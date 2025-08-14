<?php
require_once 'config.php';

function handle_report_lost($post, $files, $session) {
    $category = $description = $location = $date = $contact_phone = $contact_email = '';
    $category_err = $description_err = $location_err = $date_err = $image_err = $contact_phone_err = $contact_email_err = '';
    $success_msg = $error_msg = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (empty(trim($post['category']))) {
            $category_err = 'Please select a category.';
        } else {
            $category = trim($post['category']);
        }
        if (empty(trim($post['description']))) {
            $description_err = 'Please enter a description.';
        } else {
            $description = trim($post['description']);
        }
        if (empty(trim($post['location']))) {
            $location_err = 'Please enter the location.';
        } else {
            $location = trim($post['location']);
        }
        if (empty(trim($post['date']))) {
            $date_err = 'Please select the date.';
        } else {
            $date = trim($post['date']);
        }
        
        // Contact details validation
        $contact_phone = trim($post['contact_phone'] ?? '');
        $contact_email = trim($post['contact_email'] ?? '');
        
        // At least one contact method is required
        if (empty($contact_phone) && empty($contact_email)) {
            $contact_phone_err = 'Please provide either a phone number or email address for contact.';
        }
        
        // Validate phone number if provided
        if (!empty($contact_phone)) {
            if (!preg_match('/^[\+]?[0-9\s\-\(\)]{10,15}$/', $contact_phone)) {
                $contact_phone_err = 'Please enter a valid phone number.';
            }
        }
        
        // Validate email if provided
        if (!empty($contact_email)) {
            if (!filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
                $contact_email_err = 'Please enter a valid email address.';
            }
        }
        
        $image_path = NULL;
        if (isset($files['image']) && $files['image']['error'] != UPLOAD_ERR_NO_FILE) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($files['image']['type'], $allowed_types)) {
                $image_err = 'Only JPG, PNG, and GIF files are allowed.';
            } elseif ($files['image']['size'] > 2 * 1024 * 1024) {
                $image_err = 'Image size must be less than 2MB.';
            } else {
                $ext = pathinfo($files['image']['name'], PATHINFO_EXTENSION);
                $new_name = uniqid('img_', true) . '.' . $ext;
                $upload_dir = 'uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $image_path = $new_name; // Only save the filename, not the full path
                if (!move_uploaded_file($files['image']['tmp_name'], $upload_dir . $new_name)) {
                    $image_err = 'Failed to upload image.';
                }
            }
        }
        if (empty($category_err) && empty($description_err) && empty($location_err) && empty($date_err) && empty($image_err) && empty($contact_phone_err) && empty($contact_email_err)) {
            global $conn;
            $sql = 'INSERT INTO items (user_id, type, category, description, location, date, contact_phone, contact_email, status) VALUES (?, "lost", ?, ?, ?, ?, ?, ?, "pending")';
            $userId = isset($session['user_id']) ? $session['user_id'] : NULL;
            // Use i (int) for user_id when present, and null otherwise via bind_param with null requires work-around; use dynamic SQL
            if ($userId === NULL) {
                $sql = 'INSERT INTO items (user_id, type, category, description, location, date, contact_phone, contact_email, status) VALUES (NULL, "lost", ?, ?, ?, ?, ?, ?, "pending")';
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ssssss', $category, $description, $location, $date, $contact_phone, $contact_email);
            } else {
                $stmt = $conn->prepare('INSERT INTO items (user_id, type, category, description, location, date, contact_phone, contact_email, status) VALUES (?, "lost", ?, ?, ?, ?, ?, ?, "pending")');
                $stmt->bind_param('issssss', $userId, $category, $description, $location, $date, $contact_phone, $contact_email);
            }
            if ($stmt->execute()) {
                $item_id = $stmt->insert_id;
                // Insert image if uploaded
                if ($image_path) {
                    $img_sql = 'INSERT INTO item_images (item_id, image_path) VALUES (?, ?)';
                    $img_stmt = $conn->prepare($img_sql);
                    $img_stmt->bind_param('is', $item_id, $image_path);
                    $img_stmt->execute();
                    $img_stmt->close();
                }
                // Trigger automated matching & notifications
                auto_match_and_notify($item_id, 'lost');
                $success_msg = 'Lost item reported successfully!';
                $category = $description = $location = $date = $contact_phone = $contact_email = '';
            } else {
                $error_msg = 'Something went wrong. Please try again.';
            }
            $stmt->close();
        }
    }
    $categories = ['Electronics', 'Books', 'Clothing', 'Accessories', 'Documents', 'Other'];
    return compact('category', 'description', 'location', 'date', 'contact_phone', 'contact_email', 'category_err', 'description_err', 'location_err', 'date_err', 'image_err', 'contact_phone_err', 'contact_email_err', 'success_msg', 'error_msg', 'categories');
}

function auto_match_and_notify($new_item_id, $new_item_type) {
    global $conn;
    // Fetch new item details
    $stmt = $conn->prepare('SELECT item_id, type, category, description, location, date, user_id FROM items WHERE item_id = ?');
    $stmt->bind_param('i', $new_item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $newItem = $result->fetch_assoc();
    $stmt->close();
    if (!$newItem) return;

    $targetType = $new_item_type === 'lost' ? 'found' : 'lost';

    // Simple matching: same category, location substring match, description keyword overlap
    $query = "SELECT i.item_id, i.user_id, i.description, i.location, i.category FROM items i
              WHERE i.type = ? AND i.status IN ('approved','pending')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $targetType);
    $stmt->execute();
    $res = $stmt->get_result();
    $candidates = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $newDesc = strtolower($newItem['description']);
    $newLoc = strtolower($newItem['location']);
    $newCat = strtolower((string)$newItem['category']);

    $matches = [];
    foreach ($candidates as $cand) {
        $score = 0;
        if ($newCat && strtolower((string)$cand['category']) === $newCat) $score += 2;
        if ($newLoc && strpos(strtolower($cand['location']), $newLoc) !== false) $score += 1;
        // keyword overlap
        $newWords = array_unique(array_filter(preg_split('/\W+/', $newDesc)));
        $candWords = array_unique(array_filter(preg_split('/\W+/', strtolower($cand['description']))));
        $overlap = count(array_intersect($newWords, $candWords));
        if ($overlap >= 2) $score += 2;
        if ($score >= 3) { // threshold
            $matches[] = $cand;
        }
    }

    if (empty($matches)) return;

    $message = $new_item_type === 'lost'
        ? 'A potential match for your found item has been posted.'
        : 'A potential match for your lost item has been posted.';

    foreach ($matches as $m) {
        if (!empty($m['user_id'])) {
            $stmt = $conn->prepare('INSERT INTO notifications (item_id, recipient_id, message) VALUES (?, ?, ?)');
            $stmt->bind_param('iis', $new_item_id, $m['user_id'], $message);
            $stmt->execute();
            $stmt->close();
        }
    }
}
