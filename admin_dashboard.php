<?php
require 'config.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
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
  <link rel="stylesheet" href="./src/output.css">
  <script src="https://kit.fontawesome.com/79a49acde1.js" crossorigin="anonymous"></script>
    <style>
        .admin-panel { max-width: 1100px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .table-img { max-width: 80px; max-height: 60px; object-fit: cover; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen font-sans text-gray-800">
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="dashboard.php" class="text-2xl font-bold text-black">Lost & Found</a>
            <div class="flex space-x-4 items-center text-sm md:text-base">
                <a href="admin_dashboard.php" class="block text-gray-800 hover:text-black"><i class="fas fa-home mr-1"></i>Dashboard</a>
                <a href="search.php" class="block text-gray-800 hover:text-black"><i class="fas fa-search mr-1"></i>Search</a>
                <a href="admin_dashboard.php" class="block text-gray-800 hover:text-black"><i class="fas fa-cog mr-1"></i>Admin Dashboard</a>
                <a href="logout.php" class="block px-4 py-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition"><i class="fas fa-sign-out-alt mr-1"></i>Admin Logout</a>
            </div>
        </div>
    </nav>
    <main class="max-w-7xl mx-auto px-4 py-10">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-center mb-2">Admin Dashboard</h1>
            <p class="text-center text-gray-500">Manage reports, users, and matches</p>
        </div>
        <div class="flex justify-center mb-8">
            <div class="inline-flex rounded-md shadow-sm" role="group">
                <button type="button" id="tab-pending" class="tab-btn px-6 py-2 text-sm font-medium border border-gray-200 bg-blue-100 text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-200 rounded-l-lg">Pending Items</button>
                <button type="button" id="tab-users" class="tab-btn px-6 py-2 text-sm font-medium border-t border-b border-gray-200 bg-white text-gray-700 focus:z-10 focus:ring-2 focus:ring-blue-200">Users</button>
                <button type="button" id="tab-matches" class="tab-btn px-6 py-2 text-sm font-medium border border-gray-200 bg-white text-gray-700 focus:z-10 focus:ring-2 focus:ring-blue-200 rounded-r-lg">Potential Matches</button>
            </div>
        </div>
        <div id="tab-content-pending">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (count($pending_items) > 0): ?>
                    <?php foreach ($pending_items as $item): ?>
                        <div class="bg-white rounded-lg shadow p-6 flex flex-col">
                            <div class="flex items-center mb-2">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold <?php echo $item['type'] === 'lost' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'; ?> mr-2 text-capitalize"><?php echo htmlspecialchars($item['type']); ?></span>
                                <span class="ml-auto text-xs text-gray-400"><?php echo htmlspecialchars($item['date']); ?></span>
                            </div>
                            <div class="mb-2 font-semibold text-gray-800 truncate">Description:</div>
                            <div class="mb-2 text-gray-700 flex-1"><?php echo htmlspecialchars($item['description']); ?></div>
                            <div class="mb-2 text-gray-500 text-sm">Location: <?php echo htmlspecialchars($item['location']); ?></div>
                            <div class="mb-4">
                                <?php if ($item['image']): ?>
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" class="w-full h-32 object-cover rounded border" alt="Item image">
                                <?php else: ?>
                                    <div class="w-full h-32 flex items-center justify-center bg-gray-100 rounded border text-gray-400">No image</div>
                                <?php endif; ?>
                            </div>
                            <div class="flex gap-2 mt-auto">
                                <a href="?approve=<?php echo $item['item_id']; ?>" class="flex-1 px-3 py-2 bg-green-500 text-white rounded hover:bg-green-600 text-center">Approve</a>
                                <a href="?delete_item=<?php echo $item['item_id']; ?>" class="flex-1 px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-center" onclick="return confirm('Delete this item?');">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center text-gray-400">No pending items.</div>
                <?php endif; ?>
            </div>
        </div>
        <div id="tab-content-users" class="hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg shadow">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">User ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="border-b last:border-0">
                                <td class="px-4 py-2"><?php echo $user['user_id']; ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($user['name']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($user['role']); ?></td>
                                <td class="px-4 py-2">
                                    <?php if ($user['role'] !== 'admin'): ?>
                                        <a href="?delete_user=<?php echo $user['user_id']; ?>" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs" onclick="return confirm('Delete this user?');">Delete</a>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">Admin</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center text-gray-400">No users found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="tab-content-matches" class="hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg shadow">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Lost Description</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Lost Location</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Found Description</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Found Location</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    $matches = get_lost_found_matches();
                    if (count($matches) > 0):
                        foreach ($matches as $match): ?>
                            <tr class="border-b last:border-0">
                                <td class="px-4 py-2"><?= htmlspecialchars($match['lost_description']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($match['lost_location']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($match['found_description']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($match['found_location']) ?></td>
                                <td class="px-4 py-2">
                                    <form method="post" action="">
                                        <input type="hidden" name="match_lost_id" value="<?= $match['lost_id'] ?>">
                                        <input type="hidden" name="match_found_id" value="<?= $match['found_id'] ?>">
                                        <button type="submit" name="approve_match" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-xs">Approve & Notify</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach;
                    else: ?>
                        <tr><td colspan="5" class="text-center text-gray-400">No matches found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script>
        // Tab switching logic
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = [
            document.getElementById('tab-content-pending'),
            document.getElementById('tab-content-users'),
            document.getElementById('tab-content-matches')
        ];
        tabBtns.forEach((btn, idx) => {
            btn.addEventListener('click', () => {
                tabBtns.forEach((b, i) => {
                    b.classList.toggle('bg-blue-100', i === idx);
                    b.classList.toggle('text-blue-700', i === idx);
                    b.classList.toggle('bg-white', i !== idx);
                    b.classList.toggle('text-gray-700', i !== idx);
                });
                tabContents.forEach((content, i) => {
                    content.classList.toggle('hidden', i !== idx);
                });
            });
        });
    </script>
</body>
</html>