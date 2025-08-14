<?php
require_once 'config.php';

function admin_handle_actions() {
    global $conn;
    // Approve item
    if (isset($_GET['approve']) && is_numeric($_GET['approve'])) {
        $item_id = intval($_GET['approve']);
        $sql = 'UPDATE items SET status = "approved" WHERE item_id = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $item_id);
        $stmt->execute();
        $stmt->close();
    }
    // Delete item and all related data (images, notifications) via ON DELETE CASCADE
    if (isset($_GET['delete_item']) && is_numeric($_GET['delete_item'])) {
        $item_id = intval($_GET['delete_item']);
        // This will also delete related item_images and notifications due to ON DELETE CASCADE
        $sql = 'DELETE FROM items WHERE item_id = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $item_id);
        $stmt->execute();
        $stmt->close();
    }
    // Delete user
    if (isset($_GET['delete_user']) && is_numeric($_GET['delete_user'])) {
        $user_id = intval($_GET['delete_user']);
        $sql = 'DELETE FROM users WHERE user_id = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->close();
    }
    // Approve lost/found match and notify users
    if (isset($_POST['approve_match']) && is_numeric($_POST['match_lost_id']) && is_numeric($_POST['match_found_id'])) {
        $lost_id = intval($_POST['match_lost_id']);
        $found_id = intval($_POST['match_found_id']);
        // Mark both as returned
        $conn->query("UPDATE items SET status = 'returned' WHERE item_id IN ($lost_id, $found_id)");
        // Get user ids
        $lost_user = $conn->query("SELECT user_id FROM items WHERE item_id = $lost_id")->fetch_assoc();
        $found_user = $conn->query("SELECT user_id FROM items WHERE item_id = $found_id")->fetch_assoc();
        // Notify both users
        $msg = 'Your lost/found item has been matched by admin. Please check your dashboard.';
        if ($lost_user && $lost_user['user_id']) {
            $stmt = $conn->prepare('INSERT INTO notifications (item_id, recipient_id, message) VALUES (?, ?, ?)');
            $stmt->bind_param('iis', $lost_id, $lost_user['user_id'], $msg);
            $stmt->execute();
            $stmt->close();
        }
        if ($found_user && $found_user['user_id']) {
            $stmt = $conn->prepare('INSERT INTO notifications (item_id, recipient_id, message) VALUES (?, ?, ?)');
            $stmt->bind_param('iis', $found_id, $found_user['user_id'], $msg);
            $stmt->execute();
            $stmt->close();
        }
    }
}

function get_pending_items() {
    global $conn;
    $sql = 'SELECT i.item_id, i.type, i.description, i.location, i.date, i.status, img.image_path as image,
                   (SELECT COUNT(*) FROM claims c WHERE c.item_id = i.item_id AND c.status = "pending") AS pending_claims
            FROM items i
            LEFT JOIN (
                SELECT item_id, MIN(image_id) as min_image_id
                FROM item_images
                GROUP BY item_id
            ) first_img ON i.item_id = first_img.item_id
            LEFT JOIN item_images img ON img.item_id = i.item_id AND img.image_id = first_img.min_image_id
            WHERE i.status = "pending" ORDER BY i.date DESC';
    $result = $conn->query($sql);
    $items = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    // Fix image path for display
    foreach ($items as &$item) {
        if (!empty($item['image'])) {
            $item['image'] = 'uploads/' . ltrim($item['image'], '/');
        }
    }
    return $items;
}

function get_users() {
    global $conn;
    $sql = 'SELECT u.user_id, u.name, u.email, u.role, u.identifier_type, u.identifier_value,
                   (SELECT COUNT(*) FROM claims c WHERE c.user_id = u.user_id) AS claims_count,
                   (SELECT COUNT(*) FROM items i WHERE i.user_id = u.user_id) AS reports_count
            FROM users u
            ORDER BY u.user_id DESC';
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_lost_found_matches() {
    global $conn;
    // Find approved lost and found items with similar description or location
    $sql = "SELECT l.item_id AS lost_id, l.description AS lost_description, l.location AS lost_location, f.item_id AS found_id, f.description AS found_description, f.location AS found_location
            FROM items l
            JOIN items f ON l.type = 'lost' AND f.type = 'found' AND l.status = 'approved' AND f.status = 'approved'
            AND (
                l.location = f.location
                OR l.description LIKE CONCAT('%', f.description, '%')
                OR f.description LIKE CONCAT('%', l.description, '%')
            )
            WHERE l.item_id != f.item_id
            ORDER BY l.date DESC, f.date DESC
            LIMIT 30";
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
