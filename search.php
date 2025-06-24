<?php
session_start();
require 'backend_search.php';

$categories = ['Electronics', 'Books', 'Clothing', 'Accessories', 'Documents', 'Other'];
$types = ['lost' => 'Lost', 'found' => 'Found'];

$filter_category = $_GET['category'] ?? '';
$filter_type = $_GET['type'] ?? '';
$filter_date = $_GET['date'] ?? '';
$filter_location = $_GET['location'] ?? '';

$items = get_search_items([
    'category' => trim($filter_category),
    'type' => trim($filter_type),
    'date' => trim($filter_date),
    'location' => trim($filter_location)
]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Search Items - Lost and Found</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://kit.fontawesome.com/79a49acde1.js" crossorigin="anonymous"></script>
</head>
<body class="bg-neutral-100 min-h-screen font-sans">

   <!-- Navbar -->
  <nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <a href="dashboard.php" class="text-2xl font-bold text-black">Lost & Found</a>

      <!-- Desktop Menu -->
      <div class="hidden md:flex space-x-4 items-center text-sm md:text-base">
        <a href="dashboard.php" class="text-gray-800 hover:text-black font-medium transition"><i class="fas fa-home mr-1"></i>Dashboard</a>
        <a href="search.php" class="text-gray-800 hover:text-black font-medium transition"><i class="fas fa-search mr-1"></i>Search</a>
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="report_lost.php" class="text-gray-800 hover:text-black font-medium transition"><i class="fas fa-exclamation-circle mr-1"></i>Report Lost</a>
          <a href="report_found.php" class="text-gray-800 hover:text-black font-medium transition"><i class="fas fa-check-circle mr-1"></i>Report Found</a>
          <a href="logout.php" class="ml-2 px-4 py-2 bg-black text-white rounded-full hover:bg-gray-900 transition"><i class="fas fa-sign-out-alt mr-1"></i>Logout</a>
        <?php else: ?>
          <a href="register.php" class="ml-2 px-4 py-2 bg-black text-white rounded-full hover:bg-gray-900 transition"><i class="fas fa-user-plus mr-1"></i>Sign Up</a>
          <a href="login.php" class="ml-2 px-4 py-2 bg-gray-800 text-white rounded-full hover:bg-black transition"><i class="fas fa-sign-in-alt mr-1"></i>Login</a>
        <?php endif; ?>
      </div>

      <!-- Hamburger -->
      <button id="hamburgerBtn" class="md:hidden text-gray-800 text-2xl focus:outline-none">
        <i class="fas fa-bars"></i>
      </button>
    </div>

    <!-- Animated Mobile Menu -->
    <div id="mobileMenu" class="md:hidden overflow-hidden max-h-0 opacity-0 transition-all duration-300 ease-in-out px-4 space-y-2">
      <a href="dashboard.php" class="block text-gray-800 hover:text-black"><i class="fas fa-home mr-1"></i>Dashboard</a>
      <a href="search.php" class="block text-gray-800 hover:text-black"><i class="fas fa-search mr-1"></i>Search</a>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="report_lost.php" class="block text-gray-800 hover:text-black"><i class="fas fa-exclamation-circle mr-1"></i>Report Lost</a>
        <a href="report_found.php" class="block text-gray-800 hover:text-black"><i class="fas fa-check-circle mr-1"></i>Report Found</a>
        <a href="logout.php" class="block px-4 py-2 bg-black text-white rounded-full hover:bg-gray-900 transition"><i class="fas fa-sign-out-alt mr-1"></i>Logout</a>
      <?php else: ?>
        <a href="register.php" class="block px-4 py-2 bg-black text-white rounded-full hover:bg-gray-900 transition"><i class="fas fa-user-plus mr-1"></i>Sign Up</a>
        <a href="login.php" class="block px-4 py-2 bg-gray-800 text-white rounded-full hover:bg-black transition"><i class="fas fa-sign-in-alt mr-1"></i>Login</a>
      <?php endif; ?>
    </div>
  </nav>

<main class="max-w-7xl mx-auto px-4 py-10">
  <!-- Search Filters -->
  <div class="max-w-7xl mx-auto bg-white p-6 rounded-xl shadow-md mb-8">
    <h2 class="text-2xl font-semibold mb-4 text-center">🔍 Search Lost & Found Items</h2>
    <form method="get" class="flex flex-wrap gap-4 items-end">
      <div class="flex-1 min-w-[180px]">
        <label class="block text-sm font-medium mb-1"><i class="fas fa-list mr-1"></i>Category</label>
        <select name="category" class="w-full p-2 border border-gray-300 rounded-md focus:ring-black focus:outline-none">
          <option value="">All</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat ?>" <?= $filter_category == $cat ? 'selected' : '' ?>><?= $cat ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="flex-1 min-w-[180px]">
        <label class="block text-sm font-medium mb-1"><i class="fas fa-tag mr-1"></i>Type</label>
        <select name="type" class="w-full p-2 border border-gray-300 rounded-md focus:ring-black focus:outline-none">
          <option value="">All</option>
          <?php foreach ($types as $val => $label): ?>
            <option value="<?= $val ?>" <?= $filter_type == $val ? 'selected' : '' ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="flex-1 min-w-[180px]">
        <label class="block text-sm font-medium mb-1"><i class="fas fa-calendar-alt mr-1"></i>Date</label>
        <input type="date" name="date" value="<?= htmlspecialchars($filter_date) ?>" class="w-full p-2 border border-gray-300 rounded-md focus:ring-black focus:outline-none">
      </div>

      <div class="flex-1 min-w-[180px]">
        <label class="block text-sm font-medium mb-1"><i class="fas fa-map-marker-alt mr-1"></i>Location</label>
        <input type="text" name="location" placeholder="Enter location" value="<?= htmlspecialchars($filter_location) ?>" class="w-full p-2 border border-gray-300 rounded-md focus:ring-black focus:outline-none">
      </div>

      <div class="flex space-x-2">
        <button type="submit" class="px-4 py-2 bg-black text-white rounded-full hover:bg-gray-900 transition">
          <i class="fas fa-search mr-1"></i>Search
        </button>
        <a href="search.php" class="px-4 py-2 bg-gray-300 text-black rounded-full hover:bg-gray-400 transition">
          <i class="fas fa-sync-alt mr-1"></i>Reset
        </a>
      </div>
    </form>
  </div>

  <!-- Results -->
  <div class="container mx-auto px-4 pb-10">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php if (count($items) > 0): ?>
        <?php foreach ($items as $item): ?>
          <div class="bg-white shadow-md hover:shadow-lg rounded-xl overflow-hidden transition duration-300">
            <?php if ($item['image']): ?>
              <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="Item image" class="w-full h-48 object-cover">
            <?php else: ?>
              <img src="https://via.placeholder.com/400x180?text=No+Image" alt="No image" class="w-full h-48 object-cover">
            <?php endif; ?>
            <div class="p-4">
              <h5 class="text-lg font-semibold capitalize"><?= htmlspecialchars($item['type']) ?> Item</h5>
              <p class="text-gray-600"><?= htmlspecialchars($item['description']) ?></p>
              <p class="mt-2 text-sm text-gray-500"><strong>Location:</strong> <?= htmlspecialchars($item['location']) ?></p>
              <p class="mt-1 text-sm text-gray-500"><strong>Date:</strong> <?= htmlspecialchars($item['date']) ?></p>
              <div class="flex flex-wrap gap-2 mt-3">
                <span class="bg-<?= $item['type'] === 'lost' ? 'red' : 'green' ?>-100 text-<?= $item['type'] === 'lost' ? 'red' : 'green' ?>-700 text-xs font-medium px-3 py-1 rounded-full">
                  <?= htmlspecialchars($item['type']) ?>
                </span>
                <span class="bg-gray-200 text-gray-800 text-xs font-medium px-3 py-1 rounded-full">
                  <?= htmlspecialchars($item['status']) ?>
                </span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-span-full text-center text-gray-500 text-lg">No items found matching your criteria.</div>
      <?php endif; ?>
    </div>
  </div>
</main>

<script>
    const btn = document.getElementById('hamburgerBtn');
    const menu = document.getElementById('mobileMenu');

    btn.addEventListener('click', () => {
      const isHidden = menu.classList.contains('max-h-0');
      if (isHidden) {
        menu.classList.remove('max-h-0', 'opacity-0');
        menu.classList.add('max-h-screen', 'opacity-100');
      } else {
        menu.classList.remove('max-h-screen', 'opacity-100');
        menu.classList.add('max-h-0', 'opacity-0');
      }
    });
  </script>

</body>
</html>
