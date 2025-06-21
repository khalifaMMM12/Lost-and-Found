<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require 'config.php';

$category = $description = $location = $date = '';
$category_err = $description_err = $location_err = $date_err = $image_err = '';
$success_msg = $error_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate category
    if (empty(trim($_POST['category']))) {
        $category_err = 'Please select a category.';
    } else {
        $category = trim($_POST['category']);
    }
    // Validate description
    if (empty(trim($_POST['description']))) {
        $description_err = 'Please enter a description.';
    } else {
        $description = trim($_POST['description']);
    }
    // Validate location
    if (empty(trim($_POST['location']))) {
        $location_err = 'Please enter the location.';
    } else {
        $location = trim($_POST['location']);
    }
    // Validate date
    if (empty(trim($_POST['date']))) {
        $date_err = 'Please select the date.';
    } else {
        $date = trim($_POST['date']);
    }
    // Handle image upload
    $image_path = NULL;
    if (isset($_FILES['image']) && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            $image_err = 'Only JPG, PNG, and GIF files are allowed.';
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $image_err = 'Image size must be less than 2MB.';
        } else {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_name = uniqid('img_', true) . '.' . $ext;
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $image_path = $upload_dir . $new_name;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                $image_err = 'Failed to upload image.';
            }
        }
    }
    // Insert into database if no errors
    if (empty($category_err) && empty($description_err) && empty($location_err) && empty($date_err) && empty($image_err)) {
        $sql = 'INSERT INTO items (user_id, type, description, location, date, status, image) VALUES (?, "lost", ?, ?, ?, "pending", ?)';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('issss', $_SESSION['user_id'], $description, $location, $date, $image_path);
        if ($stmt->execute()) {
            $success_msg = 'Lost item reported successfully!';
            $category = $description = $location = $date = '';
        } else {
            $error_msg = 'Something went wrong. Please try again.';
        }
        $stmt->close();
    }
}
// Example categories (can be expanded or fetched from DB)
$categories = ['Electronics', 'Books', 'Clothing', 'Accessories', 'Documents', 'Other'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Lost Item - Lost and Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .report-form { max-width: 500px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="report-form">
        <h2 class="mb-4 text-center">Report Lost Item</h2>
        <?php if ($success_msg): ?>
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        <?php elseif ($error_msg): ?>
            <div class="alert alert-danger"><?php echo $error_msg; ?></div>
        <?php endif; ?>
        <form action="" method="post" enctype="multipart/form-data" novalidate>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select name="category" class="form-select <?php echo $category_err ? 'is-invalid' : ''; ?>">
                    <option value="">Select category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat; ?>" <?php if ($category == $cat) echo 'selected'; ?>><?php echo $cat; ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback"><?php echo $category_err; ?></div>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" class="form-control <?php echo $description_err ? 'is-invalid' : ''; ?>" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
                <div class="invalid-feedback"><?php echo $description_err; ?></div>
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" name="location" class="form-control <?php echo $location_err ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($location); ?>">
                <div class="invalid-feedback"><?php echo $location_err; ?></div>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Date Lost</label>
                <input type="date" name="date" class="form-control <?php echo $date_err ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($date); ?>">
                <div class="invalid-feedback"><?php echo $date_err; ?></div>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image (optional, max 2MB)</label>
                <input type="file" name="image" class="form-control <?php echo $image_err ? 'is-invalid' : ''; ?>" accept="image/*">
                <div class="invalid-feedback"><?php echo $image_err; ?></div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Submit</button>
            <a href="dashboard.php" class="btn btn-link w-100 mt-2">Back to Dashboard</a>
        </form>
    </div>
</body>
</html> 