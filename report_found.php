<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require 'backend_report_found.php';

$vars = handle_report_found($_POST, $_FILES, $_SESSION);
extract($vars);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Found Item - Lost and Found</title>
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
        <h2 class="text-2xl font-semibold mb-6 text-center">Report Found Item</h2>
        <?php if ($success_msg): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline"><?php echo $success_msg; ?></span>
            </div>
        <?php elseif ($error_msg): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline"><?php echo $error_msg; ?></span>
            </div>
        <?php endif; ?>
        <form action="" method="post" enctype="multipart/form-data" novalidate>
            <div class="mb-4">
                <label for="category" class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                <div class="relative">
                    <select name="category" class="form-select block w-full px-3 py-2 border <?php echo $category_err ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat; ?>" <?php if ($category == $cat) echo 'selected'; ?>><?php echo $cat; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($category_err): ?>
                        <p class="mt-2 text-sm text-red-600"><?php echo $category_err; ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                <textarea name="description" class="form-textarea block w-full px-3 py-2 border <?php echo $description_err ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring focus:ring-blue-500 focus:border-blue-500" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
                <?php if ($description_err): ?>
                    <p class="mt-2 text-sm text-red-600"><?php echo $description_err; ?></p>
                <?php endif; ?>
            </div>
            <div class="mb-4">
                <label for="location" class="block text-gray-700 text-sm font-bold mb-2">Location</label>
                <input type="text" name="location" class="form-input block w-full px-3 py-2 border <?php echo $location_err ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring focus:ring-blue-500 focus:border-blue-500" value="<?php echo htmlspecialchars($location); ?>">
                <?php if ($location_err): ?>
                    <p class="mt-2 text-sm text-red-600"><?php echo $location_err; ?></p>
                <?php endif; ?>
            </div>
            <div class="mb-4">
                <label for="date" class="block text-gray-700 text-sm font-bold mb-2">Date Found</label>
                <input type="date" name="date" class="form-input block w-full px-3 py-2 border <?php echo $date_err ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring focus:ring-blue-500 focus:border-blue-500" value="<?php echo htmlspecialchars($date); ?>">
                <?php if ($date_err): ?>
                    <p class="mt-2 text-sm text-red-600"><?php echo $date_err; ?></p>
                <?php endif; ?>
            </div>
            <div class="mb-4">
                <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Image (optional, max 2MB)</label>
                <input type="file" name="image" class="form-input block w-full px-3 py-2 border <?php echo $image_err ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring focus:ring-blue-500 focus:border-blue-500" accept="image/*">
                <?php if ($image_err): ?>
                    <p class="mt-2 text-sm text-red-600"><?php echo $image_err; ?></p>
                <?php endif; ?>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white font-bold rounded-md hover:bg-blue-600 transition duration-200">Submit</button>
            </div>
            <div class="mt-4 text-center">
                <a href="dashboard.php" class="text-blue-500 hover:text-blue-700 text-sm">Back to Dashboard</a>
            </div>
        </form>
    </div>
</body>
</html>