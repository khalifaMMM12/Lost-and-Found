<?php
require_once 'config.php';
require_once 'backend_report_lost.php'; // reuse auto_match_and_notify

function handle_report_found($post, $files, $session) {
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
                $image_path = $new_name; // Only save the filename
                if (!move_uploaded_file($files['image']['tmp_name'], $upload_dir. $new_name)) {
                    $image_err = 'Failed to upload image.';
                }
            }
        }
        if (empty($category_err) && empty($description_err) && empty($location_err) && empty($date_err) && empty($image_err) && empty($contact_phone_err) && empty($contact_email_err)) {
            global $conn;
            $sql = 'INSERT INTO items (user_id, type, category, description, location, date, contact_phone, contact_email, status) VALUES (?, "found", ?, ?, ?, ?, ?, ?, "pending")';
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('issssss', $session['user_id'], $category, $description, $location, $date, $contact_phone, $contact_email);
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
                auto_match_and_notify($item_id, 'found');
                $success_msg = 'Found item reported successfully!';
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
