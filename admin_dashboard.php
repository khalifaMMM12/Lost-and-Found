<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require 'config.php';

// Approve item
if (isset($_GET['approve']) && is_numeric($_GET['approve'])) {
    $item_id = intval($_GET['approve']);
    $sql = 'UPDATE items SET status = "approved" WHERE item_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $item_id);
    $stmt->execute();
    $stmt->close();
    // Optionally, send notification to user
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
// Fetch pending items
$sql = 'SELECT item_id, type, description, location, date, status, image FROM items WHERE status = "pending" ORDER BY date DESC';
$result = $conn->query($sql);
$pending_items = $result->fetch_all(MYSQLI_ASSOC);
// Fetch users
$sql = 'SELECT user_id, name, email, role FROM users ORDER BY user_id DESC';
$result = $conn->query($sql);
$users = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Lost and Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .admin-panel { max-width: 1100px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .table-img { max-width: 80px; max-height: 60px; object-fit: cover; }
    </style>
</head>
<body>
    <div class="admin-panel">
        <h2 class="mb-4">Admin Dashboard</h2>
        <a href="dashboard.php" class="btn btn-link mb-3">Back to Dashboard</a>
        <h4>Pending Item Reports</h4>
        <div class="table-responsive mb-5">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Location</th>
                        <th>Date</th>
                        <th>Image</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($pending_items) > 0): ?>
                    <?php foreach ($pending_items as $item): ?>
                        <tr>
                            <td class="text-capitalize"><?php echo htmlspecialchars($item['type']); ?></td>
                            <td><?php echo htmlspecialchars($item['description']); ?></td>
                            <td><?php echo htmlspecialchars($item['location']); ?></td>
                            <td><?php echo htmlspecialchars($item['date']); ?></td>
                            <td>
                                <?php if ($item['image']): ?>
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" class="table-img" alt="Item image">
                                <?php else: ?>
                                    <span class="text-muted">No image</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($item['status']); ?></span></td>
                            <td>
                                <a href="?approve=<?php echo $item['item_id']; ?>" class="btn btn-success btn-sm mb-1">Approve</a>
                                <a href="?delete_item=<?php echo $item['item_id']; ?>" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Delete this item?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center">No pending items.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <h4>Users</h4>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td>
                                <?php if ($user['role'] !== 'admin'): ?>
                                    <a href="?delete_user=<?php echo $user['user_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this user?');">Delete</a>
                                <?php else: ?>
                                    <span class="text-muted">Admin</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">No users found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html> 