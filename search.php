<?php
session_start();
require 'backend_search.php';

$categories = ['Electronics', 'Books', 'Clothing', 'Accessories', 'Documents', 'Other'];
$types = ['lost' => 'Lost', 'found' => 'Found'];

$filter_category = isset($_GET['category']) ? trim($_GET['category']) : '';
$filter_type = isset($_GET['type']) ? trim($_GET['type']) : '';
$filter_date = isset($_GET['date']) ? trim($_GET['date']) : '';
$filter_location = isset($_GET['location']) ? trim($_GET['location']) : '';

$items = get_search_items([
    'category' => $filter_category,
    'type' => $filter_type,
    'date' => $filter_date,
    'location' => $filter_location
]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Items - Lost and Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    <div class="search-form max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-2xl font-semibold mb-4 text-center">Search Lost & Found Items</h2>
        <form method="get" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" class="block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-500 focus:outline-none">
                    <option value="">All</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat; ?>" <?php if ($filter_category == $cat) echo 'selected'; ?>><?php echo $cat; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" class="block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-500 focus:outline-none">
                    <option value="">All</option>
                    <?php foreach ($types as $val => $label): ?>
                        <option value="<?php echo $val; ?>" <?php if ($filter_type == $val) echo 'selected'; ?>><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="date" class="block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-500 focus:outline-none" value="<?php echo htmlspecialchars($filter_date); ?>">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <input type="text" name="location" class="block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-500 focus:outline-none" placeholder="Enter location" value="<?php echo htmlspecialchars($filter_location); ?>">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">Search</button>
            </div>
        </form>
    </div>
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php if (count($items) > 0): ?>
                <?php foreach ($items as $item): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <?php if ($item['image']): ?>
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" class="w-full h-48 object-cover" alt="Item image">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/400x180?text=No+Image" class="w-full h-48 object-cover" alt="No image">
                        <?php endif; ?>
                        <div class="p-4">
                            <h5 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($item['type']); ?> Item</h5>
                            <p class="text-gray-600"><?php echo htmlspecialchars($item['description']); ?></p>
                            <p class="mt-2 text-sm text-gray-500"><strong>Location:</strong> <?php echo htmlspecialchars($item['location']); ?></p>
                            <p class="mt-1 text-sm text-gray-500"><strong>Date:</strong> <?php echo htmlspecialchars($item['date']); ?></p>
                            <div class="flex flex-wrap gap-2 mt-3">
                                <span class="bg-<?php echo $item['type'] == 'lost' ? 'red' : 'green'; ?>-100 text-<?php echo $item['type'] == 'lost' ? 'red' : 'green'; ?>-800 text-xs font-medium px-3 py-1 rounded-full"><?php echo htmlspecialchars($item['type']); ?></span>
                                <span class="bg-gray-200 text-gray-800 text-xs font-medium px-3 py-1 rounded-full"><?php echo htmlspecialchars($item['status']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">No items found matching your criteria.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>