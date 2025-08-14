<?php
session_start();
require 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$name = $email = $password = $confirm_password = '';
$identifier_type = $identifier_value = '';
$name_err = $email_err = $password_err = $confirm_password_err = $identifier_type_err = $identifier_value_err = $register_success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    error_log("Registration attempt - POST data: " . print_r($_POST, true));
    
    if (empty(trim($_POST['name']))) {
        $name_err = 'Please enter your name.';
    } else {
        $name = trim($_POST['name']);
    }

    if (empty(trim($_POST['email']))) {
        $email_err = 'Please enter your email.';
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $email_err = 'Invalid email format.';
    } else {
        $sql = 'SELECT user_id FROM users WHERE email = ?';
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            $email_err = 'Database error occurred.';
        } else {
            $stmt->bind_param('s', $_POST['email']);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $email_err = 'This email is already registered.';
            } else {
                $email = trim($_POST['email']);
            }
            $stmt->close();
        }
    }

    if (empty(trim($_POST['password']))) {
        $password_err = 'Please enter a password.';
    } elseif (strlen(trim($_POST['password'])) < 6) {
        $password_err = 'Password must be at least 6 characters.';
    } else {
        $password = trim($_POST['password']);
    }

    if (empty(trim($_POST['confirm_password']))) {
        $confirm_password_err = 'Please confirm your password.';
    } else {
        $confirm_password = trim($_POST['confirm_password']);
        if ($password !== $confirm_password) {
            $confirm_password_err = 'Passwords do not match.';
        }
    }

    // Identifier validation
    $allowed_identifier_types = ['nin','email','matric'];
    $identifier_type = isset($_POST['identifier_type']) ? strtolower(trim($_POST['identifier_type'])) : '';
    if (!in_array($identifier_type, $allowed_identifier_types, true)) {
        $identifier_type_err = 'Please select a valid identifier type.';
    }

    if ($identifier_type === 'email') {
        $identifier_value = $email;
    } else {
        $identifier_value = isset($_POST['identifier_value']) ? trim($_POST['identifier_value']) : '';
        if ($identifier_type === 'nin') {
            if (empty($identifier_value)) {
                $identifier_value_err = 'Please enter your NIN.';
            } elseif (!preg_match('/^[A-Za-z0-9\-]{6,20}$/', $identifier_value)) {
                $identifier_value_err = 'NIN must be 6-20 characters (letters, numbers, dashes).';
            }
        } elseif ($identifier_type === 'matric') {
            if (empty($identifier_value)) {
                $identifier_value_err = 'Please enter your Matriculation number.';
            } elseif (!preg_match('/^[A-Za-z0-9\/\-]{3,30}$/', $identifier_value)) {
                $identifier_value_err = 'Matriculation number must be 3-30 characters (letters, numbers, / or -).';
            }
        }
    }

    // Ensure identifier is unique if provided
    if (empty($identifier_type_err) && empty($identifier_value_err)) {
        $sql = 'SELECT user_id FROM users WHERE identifier_value = ?';
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('s', $identifier_value);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $identifier_value_err = 'This identifier is already in use.';
            }
            $stmt->close();
        }
    }

    if (empty($name_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($identifier_type_err) && empty($identifier_value_err)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = 'INSERT INTO users (name, email, password, identifier_type, identifier_value) VALUES (?, ?, ?, ?, ?)';
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed for insert: " . $conn->error);
            $register_success = 'Database error occurred. Please try again.';
        } else {
            $stmt->bind_param('sssss', $name, $email, $hashed_password, $identifier_type, $identifier_value);
            if ($stmt->execute()) {
                session_start();
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = 'user';
                header('Location: dashboard.php');
                exit;
            } else {
                error_log("Execute failed: " . $stmt->error);
                $register_success = 'Something went wrong. Please try again.';
            }
            $stmt->close();
        }
    } else {
        error_log("Registration validation failed: " . implode(", ", array_filter([$name_err, $email_err, $password_err, $confirm_password_err])));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register - Lost and Found</title>
    <link rel="stylesheet" href="./src/output.css">

  <script src="https://kit.fontawesome.com/79a49acde1.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans text-gray-800">

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
          <a href="my_claims.php" class="text-gray-800 hover:text-black font-medium transition"><i class="fas fa-hand-paper mr-1"></i>My Claims</a>
          <a href="notifications.php" class="text-gray-800 hover:text-black font-medium transition"><i class="fas fa-bell mr-1"></i>Notifications</a>
          <a href="logout.php" class="ml-2 px-4 py-2 bg-black text-white rounded-full hover:bg-gray-900 transition"><i class="fas fa-sign-out-alt mr-1"></i>Logout</a>
        <?php else: ?>
          <a href="register.php" class="ml-2 px-4 py-2 bg-black text-white rounded-full hover:bg-gray-900 transition"><i class="fas fa-user-plus mr-1"></i>Sign Up</a>
          <a href="login.php" class="ml-2 px-4 py-2 bg-gray-800 text-white rounded-full hover:bg-black transition"><i class="fas fa-sign-in-alt mr-1"></i>Login</a>
        <?php endif; ?>
        <?php if (!isset($_SESSION['admin_id'])): ?>
          <a href="admin_login.php" class="ml-2 px-4 py-2 bg-blue-700 text-white rounded-full hover:bg-blue-800 transition">Admin Login</a>
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
        <a href="my_claims.php" class="block text-gray-800 hover:text-black"><i class="fas fa-hand-paper mr-1"></i>My Claims</a>
        <a href="notifications.php" class="block text-gray-800 hover:text-black"><i class="fas fa-bell mr-1"></i>Notifications</a>
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

<!-- Registration Form -->
<main class="max-w-7xl mx-auto px-4 py-10">
<div class="flex items-center justify-center px-4">
  <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center">Create Account</h2>

    <?php if ($register_success): ?>
      <div class="mb-4 p-4 bg-green-100 text-green-700 rounded"><?= $register_success ?></div>
    <?php endif; ?>

    <form action="" method="post">
      <div class="mb-4">
        <label class="block text-sm font-medium">Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" class="mt-1 block w-full p-2 border <?= $name_err ? 'border-red-500' : 'border-gray-300' ?> rounded-md focus:ring-black focus:border-black" required>
        <?php if ($name_err): ?><p class="text-sm text-red-500 mt-1"><?= $name_err ?></p><?php endif; ?>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" class="mt-1 block w-full p-2 border <?= $email_err ? 'border-red-500' : 'border-gray-300' ?> rounded-md focus:ring-black focus:border-black" required>
        <?php if ($email_err): ?><p class="text-sm text-red-500 mt-1"><?= $email_err ?></p><?php endif; ?>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium">Select Identifier</label>
        <select name="identifier_type" id="identifier_type" class="mt-1 block w-full p-2 border <?= $identifier_type_err ? 'border-red-500' : 'border-gray-300' ?> rounded-md focus:ring-black focus:border-black" required>
          <option value="">-- Select --</option>
          <option value="nin" <?= $identifier_type === 'nin' ? 'selected' : '' ?>>National Identification Number (NIN)</option>
          <option value="email" <?= $identifier_type === 'email' ? 'selected' : '' ?>>Email</option>
          <option value="matric" <?= $identifier_type === 'matric' ? 'selected' : '' ?>>Matriculation Number</option>
        </select>
        <?php if ($identifier_type_err): ?><p class="text-sm text-red-500 mt-1"><?= $identifier_type_err ?></p><?php endif; ?>
      </div>

      <div class="mb-4" id="identifier_value_group">
        <label class="block text-sm font-medium" id="identifier_value_label">Identifier Value</label>
        <input type="text" name="identifier_value" id="identifier_value" value="<?= htmlspecialchars($identifier_value) ?>" placeholder="Enter your identifier" class="mt-1 block w-full p-2 border <?= $identifier_value_err ? 'border-red-500' : 'border-gray-300' ?> rounded-md focus:ring-black focus:border-black">
        <?php if ($identifier_value_err): ?><p class="text-sm text-red-500 mt-1"><?= $identifier_value_err ?></p><?php endif; ?>
        <p class="text-xs text-gray-500 mt-1">Used to verify identity when claiming items.</p>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium">Password</label>
        <div class="relative">
          <input type="password" name="password" id="password" class="mt-1 block w-full p-2 border <?= $password_err ? 'border-red-500' : 'border-gray-300' ?> rounded-md focus:ring-black focus:border-black pr-10" required>
          <button type="button" tabindex="-1" class="absolute right-2 top-1.5 text-gray-400 hover:text-gray-700" onclick="togglePassword('password', this)">
            <i class="fa-regular fa-eye"></i>
          </button>
        </div>
        <?php if ($password_err): ?><p class="text-sm text-red-500 mt-1"><?= $password_err ?></p><?php endif; ?>
      </div>

      <div class="mb-6">
        <label class="block text-sm font-medium">Confirm Password</label>
        <div class="relative">
          <input type="password" name="confirm_password" id="confirm_password" class="mt-1 block w-full p-2 border <?= $confirm_password_err ? 'border-red-500' : 'border-gray-300' ?> rounded-md focus:ring-black focus:border-black pr-10" required>
          <button type="button" tabindex="-1" class="absolute right-2 top-1.5 text-gray-400 hover:text-gray-700" onclick="togglePassword('confirm_password', this)">
            <i class="fa-regular fa-eye"></i>
          </button>
        </div>
        <?php if ($confirm_password_err): ?><p class="text-sm text-red-500 mt-1"><?= $confirm_password_err ?></p><?php endif; ?>
      </div>

      <button type="submit" class="w-full px-4 py-2 bg-black text-white rounded-full hover:bg-gray-900 transition duration-200">Register</button>

      <p class="mt-4 text-center text-sm text-gray-600">Already have an account? 
        <a href="login.php" class="text-black hover:underline">Login</a>
      </p>
    </form>
  </div>
</div>
</main>

<script>
    function updateIdentifierInput() {
      const typeSelect = document.getElementById('identifier_type');
      const group = document.getElementById('identifier_value_group');
      const label = document.getElementById('identifier_value_label');
      const input = document.getElementById('identifier_value');
      const type = typeSelect ? typeSelect.value : '';
      if (!typeSelect) return;
      if (type === 'email') {
        group.classList.add('hidden');
        input.value = '';
      } else {
        group.classList.remove('hidden');
        if (type === 'nin') {
          label.textContent = 'National Identification Number (NIN)';
          input.placeholder = 'e.g., A12345-6789';
        } else if (type === 'matric') {
          label.textContent = 'Matriculation Number';
          input.placeholder = 'e.g., CSC/2019/1234';
        } else {
          label.textContent = 'Identifier Value';
          input.placeholder = 'Enter your identifier';
        }
      }
    }
    document.addEventListener('DOMContentLoaded', updateIdentifierInput);
    const typeSel = document.getElementById('identifier_type');
    if (typeSel) typeSel.addEventListener('change', updateIdentifierInput);
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

    function togglePassword(fieldId, btn) {
      const input = document.getElementById(fieldId);
      const icon = btn.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }
  </script>

</body>
</html>
