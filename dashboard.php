<?php
session_start();
require 'backend_dashboard.php';
$items = get_dashboard_items();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Lost and Found Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<script src="https://kit.fontawesome.com/79a49acde1.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

  <!-- Navbar -->
  <nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <a href="dashboard.php" class="text-2xl font-bold text-blue-600">Lost & Found</a>

      <!-- Desktop Menu -->
      <div class="hidden md:flex space-x-4 items-center text-sm md:text-base">
        <a href="dashboard.php" class="text-gray-700 hover:text-blue-600 font-medium transition"><i class="fas fa-home mr-1"></i>Dashboard</a>
        <a href="search.php" class="text-gray-700 hover:text-blue-600 font-medium transition"><i class="fas fa-search mr-1"></i>Search</a>
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="report_lost.php" class="text-gray-700 hover:text-blue-600 font-medium transition"><i class="fas fa-exclamation-circle mr-1"></i>Report Lost</a>
          <a href="report_found.php" class="text-gray-700 hover:text-blue-600 font-medium transition"><i class="fas fa-check-circle mr-1"></i>Report Found</a>
          <a href="logout.php" class="ml-2 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition"><i class="fas fa-sign-out-alt mr-1"></i>Logout</a>
        <?php else: ?>
          <a href="register.php" class="ml-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition"><i class="fas fa-user-plus mr-1"></i>Sign Up</a>
          <a href="login.php" class="ml-2 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition"><i class="fas fa-sign-in-alt mr-1"></i>Login</a>
        <?php endif; ?>
      </div>

      <!-- Hamburger -->
      <button class="md:hidden text-gray-700 text-2xl focus:outline-none" onclick="document.getElementById('mobileMenu').classList.toggle('hidden')">
        <i class="fas fa-bars"></i>
      </button>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="md:hidden px-4 pb-4 space-y-2 hidden">
      <a href="dashboard.php" class="block text-gray-700 hover:text-blue-600"><i class="fas fa-home mr-1"></i>Dashboard</a>
      <a href="search.php" class="block text-gray-700 hover:text-blue-600"><i class="fas fa-search mr-1"></i>Search</a>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="report_lost.php" class="block text-gray-700 hover:text-blue-600"><i class="fas fa-exclamation-circle mr-1"></i>Report Lost</a>
        <a href="report_found.php" class="block text-gray-700 hover:text-blue-600"><i class="fas fa-check-circle mr-1"></i>Report Found</a>
        <a href="logout.php" class="block px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition"><i class="fas fa-sign-out-alt mr-1"></i>Logout</a>
      <?php else: ?>
        <a href="register.php" class="block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition"><i class="fas fa-user-plus mr-1"></i>Sign Up</a>
        <a href="login.php" class="block px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition"><i class="fas fa-sign-in-alt mr-1"></i>Login</a>
      <?php endif; ?>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="max-w-7xl mx-auto px-4 py-10">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">All Lost & Found Items</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($items as $item): ?>
        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition duration-300 p-4 flex flex-col">
          <?php if (count($item['images']) > 0): ?>
            <img src="uploads/<?php echo htmlspecialchars($item['images'][0]); ?>" alt="Item Image" class="w-full h-48 object-cover rounded-lg mb-3" />
          <?php else: ?>
            <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center mb-3 text-gray-400 text-sm">
              <i class="fas fa-image mr-2"></i>No Image
            </div>
          <?php endif; ?>

          <div class="flex-1">
            <span class="inline-block mb-2 px-2 py-1 text-xs font-semibold rounded-full
              <?php echo $item['type'] === 'lost' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600'; ?>">
              <i class="fas <?php echo $item['type'] === 'lost' ? 'fa-times-circle' : 'fa-check-circle'; ?> mr-1"></i>
              <?php echo ucfirst($item['type']); ?> Item
            </span>
            <h3 class="font-semibold text-lg text-gray-800 mb-1"><?php echo htmlspecialchars($item['description']); ?></h3>
            <p class="text-sm text-gray-600 mb-1"><strong>Location:</strong> <?php echo htmlspecialchars($item['location']); ?></p>
            <p class="text-sm text-gray-500 mb-1"><strong>Date:</strong> <?php echo htmlspecialchars($item['date']); ?></p>
            <p class="text-xs text-gray-400"><strong>Status:</strong> <?php echo htmlspecialchars($item['status']); ?></p>
          </div>
        </div>
      <?php endforeach; ?>

      <?php if (count($items) === 0): ?>
        <div class="col-span-full text-center text-gray-500 text-lg">No items found.</div>
      <?php endif; ?>
    </div>
  </main>

</body>
</html>
