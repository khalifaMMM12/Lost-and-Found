<?php
require 'config.php';
header('Location: dashboard.php');
exit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost and Found - Home</title>
    <!--   <link rel="stylesheet" href="./src/output.css">
 -->
    <link rel="stylesheet" href="/src/output.css">
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
                    <a href="logout.php" class="ml-2 px-4 py-2 bg-black text-white rounded-full hover:bg-gray-900 transition">Logout</a>
                <?php else: ?>
                    <a href="register.php" class="ml-2 px-4 py-2 bg-black text-white rounded-full hover:bg-gray-900 transition">Sign Up</a>
                    <a href="login.php" class="ml-2 px-4 py-2 bg-gray-800 text-white rounded-full hover:bg-black transition">Login</a>
                <?php endif; ?>
                <a href="admin_login.php" class="ml-2 px-4 py-2 bg-blue-700 text-white rounded-full hover:bg-blue-800 transition">Admin Login</a>
            </div>
        </div>
    </nav>
    <div class="max-w-xl mx-auto bg-white rounded-lg shadow p-8 text-center">
        <h1 class="mb-3 text-3xl font-bold text-blue-700">Lost and Found Portal</h1>
        <p class="mb-6 text-gray-600">A simple platform for university students and staff to report, search, and retrieve lost or found items efficiently.</p>
        <div class="flex flex-col gap-3 mb-6">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="register.php" class="px-6 py-3 bg-blue-500 text-white rounded hover:bg-blue-600 text-lg">Sign Up</a>
                <a href="login.php" class="px-6 py-3 bg-green-500 text-white rounded hover:bg-green-600 text-lg">Login</a>
            <?php endif; ?>
            <a href="search.php" class="px-6 py-3 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-lg">Search Items</a>
        </div>
        <hr class="my-6">
        <p class="text-gray-400 text-sm">For assistance, contact the university's Lost and Found office.</p>
    </div>
</body>
</html>