<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require 'config.php';

// Fetch notifications for the logged-in user
$user_id = $_SESSION['user_id'];
$notifications = [];
$sql = 'SELECT message, timestamp FROM notifications WHERE recipient_id = ? ORDER BY timestamp DESC LIMIT 5';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($message, $timestamp);
while ($stmt->fetch()) {
    $notifications[] = ['message' => $message, 'timestamp' => $timestamp];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Lost and Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .dashboard { max-width: 700px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="dashboard">
        <h2 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
        <div class="mb-4">
            <a href="report_lost.php" class="btn btn-outline-primary me-2">Report Lost Item</a>
            <a href="report_found.php" class="btn btn-outline-success me-2">Report Found Item</a>
            <a href="search.php" class="btn btn-outline-secondary me-2">Search Items</a>
            <a href="logout.php" class="btn btn-outline-danger">Logout</a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin_dashboard.php" class="btn btn-warning ms-2">Admin Panel</a>
            <?php endif; ?>
        </div>
        <h4>Recent Notifications</h4>
        <?php if (count($notifications) > 0): ?>
            <ul class="list-group mb-3">
                <?php foreach ($notifications as $note): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo htmlspecialchars($note['message']); ?>
                        <span class="badge bg-secondary rounded-pill"><?php echo date('M d, H:i', strtotime($note['timestamp'])); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="alert alert-info">No notifications yet.</div>
        <?php endif; ?>
    </div>
</body>
</html> 