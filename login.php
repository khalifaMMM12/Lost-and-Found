<?php
session_start();
require 'config.php';

$email = $password = '';
$email_err = $password_err = $login_err = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty(trim($_POST['email']))) {
        $email_err = 'Please enter your email.';
    } else {
        $email = trim($_POST['email']);
    }

    if (empty(trim($_POST['password']))) {
        $password_err = 'Please enter your password.';
    } else {
        $password = trim($_POST['password']);
    }

    if (empty($email_err) && empty($password_err)) {
        $sql = 'SELECT user_id, name, email, password, role FROM users WHERE email = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($user_id, $name, $db_email, $db_password, $role);
            $stmt->fetch();
            if (password_verify($password, $db_password)) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $db_email;
                $_SESSION['role'] = $role;
                header('Location: dashboard.php');
                exit;
            } else {
                $login_err = 'Invalid email or password.';
            }
        } else {
            $login_err = 'Invalid email or password.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Lost and Found</title>
  <script src="https://cdn.tailwindcss.com"></script>
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


<!-- Login Form -->
<main class="max-w-7xl mx-auto px-4 py-10">
<div class="flex justify-center px-4">
  <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

    <?php if ($login_err): ?>
      <div class="mb-4 p-4 bg-red-100 text-red-700 rounded"><?php echo $login_err; ?></div>
    <?php endif; ?>

    <form id="loginForm" action="" method="post" novalidate>
      <div class="mb-4">
        <label for="email" class="block text-sm font-medium">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" class="mt-1 block w-full p-3 border <?= $email_err ? 'border-red-500' : 'border-gray-300' ?> rounded-md focus:ring-black focus:border-black" required>
        <?php if ($email_err): ?><p class="mt-1 text-sm text-red-500"><?php echo $email_err; ?></p><?php endif; ?>
      </div>

      <div class="mb-6">
        <label for="password" class="block text-sm font-medium">Password</label>
        <div class="relative">
          <input type="password" name="password" id="password" class="mt-1 block w-full p-3 border <?= $password_err ? 'border-red-500' : 'border-gray-300' ?> rounded-md focus:ring-black focus:border-black pr-10" required>
          <button type="button" tabindex="-1" class="absolute right-2 top-2 text-gray-400 hover:text-gray-700" onclick="togglePassword('password', this)">
            <i class="fa-regular fa-eye"></i>
          </button>
        </div>
        <?php if ($password_err): ?><p class="mt-1 text-sm text-red-500"><?php echo $password_err; ?></p><?php endif; ?>
      </div>

      <button type="submit" class="w-full px-4 py-2 bg-black text-white rounded-full hover:bg-gray-900 transition duration-200">Login</button>

      <p class="mt-4 text-center text-sm text-gray-600">Don't have an account? 
        <a href="register.php" class="text-black hover:underline">Register</a>
      </p>
    </form>
  </div>
</div>
</main>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    let valid = true;
    const email = this.email.value.trim();
    const password = this.password.value;
    if (!email || !/^\S+@\S+\.\S+$/.test(email)) valid = false;
    if (!password) valid = false;
    if (!valid) {
        e.preventDefault();
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
