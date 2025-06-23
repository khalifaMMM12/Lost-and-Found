<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require 'backend_report_lost.php';

$vars = handle_report_lost($_POST, $_FILES, $_SESSION);
extract($vars);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Lost Item - Lost and Found</title>
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
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-6 text-center">Report Lost Item</h2>
        <?php if ($success_msg): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p class="font-bold">Success!</p>
                <p><?php echo $success_msg; ?></p>
            </div>
        <?php elseif ($error_msg): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p class="font-bold">Error!</p>
                <p><?php echo $error_msg; ?></p>
            </div>
        <?php endif; ?>
        <form action="" method="post" enctype="multipart/form-data" novalidate>
            <div class="mb-4">
                <label for="category" class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                <div class="relative">
                    <select name="category" class="block appearance-none w-full bg-gray-200 border border-gray-300 text-gray-700 py-3 px-4 pr-8 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent <?php echo $category_err ? 'is-invalid' : ''; ?>">
                        <option value="">Select category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat; ?>" <?php if ($category == $cat) echo 'selected'; ?>><?php echo $cat; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width={2} d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                        </svg>
                    </div>
                </div>
                <div class="text-red-500 text-xs mt-1"><?php echo $category_err; ?></div>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                <textarea name="description" class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent <?php echo $description_err ? 'is-invalid' : ''; ?>" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
                <div class="text-red-500 text-xs mt-1"><?php echo $description_err; ?></div>
            </div>
            <div class="mb-4">
                <label for="location" class="block text-gray-700 text-sm font-bold mb-2">Location</label>
                <input type="text" name="location" class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent <?php echo $location_err ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($location); ?>">
                <div class="text-red-500 text-xs mt-1"><?php echo $location_err; ?></div>
            </div>
            <div class="mb-4">
                <label for="date" class="block text-gray-700 text-sm font-bold mb-2">Date Lost</label>
                <input type="date" name="date" class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent <?php echo $date_err ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($date); ?>">
                <div class="text-red-500 text-xs mt-1"><?php echo $date_err; ?></div>
            </div>
            <div class="mb-4">
                <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Image (optional, max 2MB)</label>
                <input type="file" name="image" class="shadow appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent <?php echo $image_err ? 'is-invalid' : ''; ?>" accept="image/*">
                <div class="text-red-500 text-xs mt-1"><?php echo $image_err; ?></div>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-opacity-50">
                    Submit
                </button>
            </div>
            <div class="mt-4 text-center">
                <a href="dashboard.php" class="text-blue-500 hover:text-blue-700 text-sm">Back to Dashboard</a>
            </div>
        </form>
    </div>
</body>
</html>