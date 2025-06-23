<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
require 'backend_admin_dashboard.php';
admin_handle_actions();
$pending_items = get_pending_items();
$users = get_users();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Lost and Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .admin-panel { max-width: 1100px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .table-img { max-width: 80px; max-height: 60px; object-fit: cover; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow mb-8">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="dashboard.php" class="text-2xl font-bold text-blue-700">Lost & Found</a>
            <div class="flex space-x-4 items-center">
                <a href="dashboard.php" class="text-gray-700 hover:text-blue-700 font-medium">Dashboard</a>
                <a href="search.php" class="text-gray-700 hover:text-blue-700 font-medium">Search</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="report_lost.php" class="text-gray-700 hover:text-blue-700 font-medium">Report Lost</a>
                    <a href="report_found.php" class="text-gray-700 hover:text-blue-700 font-medium">Report Found</a>
                    <a href="logout.php" class="ml-4 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Logout</a>
                <?php else: ?>
                    <a href="register.php" class="ml-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Sign Up</a>
                    <a href="login.php" class="ml-2 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
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