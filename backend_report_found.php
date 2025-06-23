<?php
require_once 'config.php';

function handle_report_found($post, $files, $session) {
    $category = $description = $location = $date = '';
    $category_err = $description_err = $location_err = $date_err = $image_err = '';
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
                $image_path = $upload_dir . $new_name;
                if (!move_uploaded_file($files['image']['tmp_name'], $image_path)) {
                    $image_err = 'Failed to upload image.';
                }
            }
        }
        if (empty($category_err) && empty($description_err) && empty($location_err) && empty($date_err) && empty($image_err)) {
            global $conn;
            $sql = 'INSERT INTO items (user_id, type, description, location, date, status, image) VALUES (?, "found", ?, ?, ?, "pending", ?)';
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('issss', $session['user_id'], $description, $location, $date, $image_path);
            if ($stmt->execute()) {
                $success_msg = 'Found item reported successfully!';
                $category = $description = $location = $date = '';
            } else {
                $error_msg = 'Something went wrong. Please try again.';
            }
            $stmt->close();
        }
    }
    $categories = ['Electronics', 'Books', 'Clothing', 'Accessories', 'Documents', 'Other'];
    return compact('category', 'description', 'location', 'date', 'category_err', 'description_err', 'location_err', 'date_err', 'image_err', 'success_msg', 'error_msg', 'categories');
}
