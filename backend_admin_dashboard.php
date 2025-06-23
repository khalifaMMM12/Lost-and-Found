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
    // Delete item
    if (isset($_GET['delete_item']) && is_numeric($_GET['delete_item'])) {
        $item_id = intval($_GET['delete_item']);
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
}

function get_pending_items() {
    global $conn;
    $sql = 'SELECT item_id, type, description, location, date, status, image FROM items WHERE status = "pending" ORDER BY date DESC';
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_users() {
    global $conn;
    $sql = 'SELECT user_id, name, email, role FROM users ORDER BY user_id DESC';
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}
