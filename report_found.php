<?php
require 'config.php';
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
  <link rel="stylesheet" href="./src/output.css">
    <script src="https://kit.fontawesome.com/79a49acde1.js" crossorigin="anonymous"></script>

</head>
<body class="bg-gray-100 min-h-screen">
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
          <a href="report_found.php" class="text-green-700 font-semibold border-b-2 border-green-400 font-medium"><i class="fas fa-check-circle mr-1"></i>Report Found</a>
          <a href="logout.php" class="ml-2 px-4 py-2 bg-black text-white rounded-full hover:bg-gray-900 transition"><i class="fas fa-sign-out-alt mr-1"></i>Logout</a>
        <?php else: ?>
          <a href="register.php" class="ml-2 px-4 py-2 bg-black text-white rounded-full hover:bg-gray-900 transition"><i class="fas fa-user-plus mr-1"></i>Sign Up</a>
          <a href="login.php" class="ml-2 px-4 py-2 bg-gray-800 text-white rounded-full hover:bg-black transition"><i class="fas fa-sign-in-alt mr-1"></i>Login</a>
        <?php endif; ?>
        <a href="admin_login.php" class="ml-2 px-4 py-2 bg-blue-700 text-white rounded-full hover:bg-blue-800 transition">Admin Login</a>
        <?php if (!isset($_SESSION['admin_id'])): ?>
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
      <?php if (!isset($_SESSION['admin_id'])): ?>
        <a href="admin_login.php" class="block px-4 py-2 bg-blue-700 text-white rounded-full hover:bg-blue-800 transition">Admin Login</a>
      <?php endif; ?>
    </div>
  </nav>

    <main class="max-w-7xl mx-auto px-4 py-10">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md">
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
            <div class="grid sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-green-700 mb-1">Category</label>
                    <select name="category" class="block w-full px-3 py-2 border border-green-200 bg-green-50 text-green-900 rounded-md focus:outline-none focus:ring-2 focus:ring-green-200">
                        <option value="">Select category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat ?>" <?= $category == $cat ? 'selected' : '' ?>><?= $cat ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($category_err): ?>
                        <p class="text-green-600 text-sm mt-1"><?= $category_err ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-green-700 mb-1">Date Found</label>
                    <input type="date" name="date" class="block w-full px-3 py-2 border border-green-200 bg-green-50 text-green-900 rounded-md focus:outline-none focus:ring-2 focus:ring-green-200" value="<?= htmlspecialchars($date) ?>">
                    <?php if ($date_err): ?>
                        <p class="text-green-600 text-sm mt-1"><?= $date_err ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-green-700 mb-1">Location</label>
                    <input type="text" name="location" class="block w-full px-3 py-2 border border-green-200 bg-green-50 text-green-900 rounded-md focus:outline-none focus:ring-2 focus:ring-green-200" value="<?= htmlspecialchars($location) ?>">
                    <?php if ($location_err): ?>
                        <p class="text-green-600 text-sm mt-1"><?= $location_err ?></p>
                    <?php endif; ?>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-green-700 mb-1">Image (optional)</label>
                        <div class="w-40 h-40 flex items-center justify-center border-2 border-dashed border-green-200 bg-green-50 relative cursor-pointer hover:border-green-300 transition" id="imageUploadBox">
                            <input type="file" name="image" id="imageInput" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" tabindex="-1">
                            <span id="uploadIcon" class="absolute inset-0 flex items-center justify-center text-4xl text-green-200 pointer-events-auto" style="z-index:2;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-12 h-12">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </span>
                            <img id="imagePreview" src="#" class="hidden absolute inset-0 w-full h-full object-cover rounded pointer-events-auto" alt="Preview" style="z-index:3;">
                            <button type="button" id="removeImage" class="hidden absolute top-1 right-1 bg-white border border-green-200 rounded-full text-sm px-2 py-0.5 hover:bg-green-100 pointer-events-auto" style="z-index:4;">&times;</button>
                        </div>
                        <?php if ($image_err): ?>
                            <p class="text-green-600 text-sm mt-1"><?= $image_err ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-green-700 mb-1">Description</label>
                    <textarea name="description" rows="6" class="block w-full px-3 py-2 border border-green-200 bg-green-50 text-green-900 rounded-md focus:outline-none focus:ring-2 focus:ring-green-200"><?= htmlspecialchars($description) ?></textarea>
                    <?php if ($description_err): ?>
                        <p class="text-green-600 text-sm mt-1"><?= $description_err ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Contact Details Section -->
            <div class="mt-6 border-t border-green-200 pt-6">
                <h3 class="text-lg font-semibold text-green-800 mb-4">Contact Details</h3>
                <p class="text-sm text-green-600 mb-4">Please provide at least one contact method so people can reach you about this item.</p>
                
                <div class="grid sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-green-700 mb-1">Phone Number (optional)</label>
                        <input type="tel" name="contact_phone" placeholder="+1 (555) 123-4567" class="block w-full px-3 py-2 border border-green-200 bg-green-50 text-green-900 rounded-md focus:outline-none focus:ring-2 focus:ring-green-200" value="<?= htmlspecialchars($contact_phone) ?>">
                        <?php if ($contact_phone_err): ?>
                            <p class="text-green-600 text-sm mt-1"><?= $contact_phone_err ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-green-700 mb-1">Email Address (optional)</label>
                        <input type="email" name="contact_email" placeholder="your.email@example.com" class="block w-full px-3 py-2 border border-green-200 bg-green-50 text-green-900 rounded-md focus:outline-none focus:ring-2 focus:ring-green-200" value="<?= htmlspecialchars($contact_email) ?>">
                        <?php if ($contact_email_err): ?>
                            <p class="text-green-600 text-sm mt-1"><?= $contact_email_err ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-green-100 text-green-900 rounded-md hover:bg-green-200 transition">Submit Report</button>
                <a href="dashboard.php" class="text-sm text-green-500 hover:text-green-700">← Back to Dashboard</a>
            </div>
        </form>
    </div>
    </main>

    <script>
        function resetImageUploadBox() {
            document.querySelectorAll('#uploadIcon').forEach(icon => icon.classList.remove('hidden'));
            document.querySelectorAll('#imagePreview').forEach(preview => preview.classList.add('hidden'));
            document.querySelectorAll('#removeImage').forEach(btn => btn.classList.add('hidden'));
        }
        // Always reset state on page load
        window.addEventListener('DOMContentLoaded', resetImageUploadBox);

        // Only open file dialog when clicking the plus icon or empty area
        document.getElementById('uploadIcon').addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('imageInput').click();
        });
        // Prevent preview and remove from opening file dialog
        document.getElementById('imagePreview').addEventListener('click', function(e) { e.stopPropagation(); });
        document.getElementById('removeImage').addEventListener('click', function(e) {
            e.stopPropagation();
            const input = document.getElementById('imageInput');
            input.value = '';
            resetImageUploadBox();
        });
        // Remove onclick from container
        document.getElementById('imageUploadBox').onclick = null;

        document.getElementById('imageInput').addEventListener('change', function (e) {
            const preview = document.getElementById('imagePreview');
            const uploadIcon = document.getElementById('uploadIcon');
            const removeBtn = document.getElementById('removeImage');
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    preview.src = event.target.result;
                    preview.classList.remove('hidden');
                    uploadIcon.classList.add('hidden');
                    removeBtn.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            } else {
                resetImageUploadBox();
            }
        });

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
