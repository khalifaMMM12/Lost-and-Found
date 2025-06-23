<?php
session_start();
require 'config.php';

$email = $password = '';
$email_err = $password_err = $login_err = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate email
    if (empty(trim($_POST['email']))) {
        $email_err = 'Please enter your email.';
    } else {
        $email = trim($_POST['email']);
    }
    // Validate password
    if (empty(trim($_POST['password']))) {
        $password_err = 'Please enter your password.';
    } else {
        $password = trim($_POST['password']);
    }
    // Check credentials
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
                // Password correct, start session
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Lost and Found</title>
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
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-center mb-6">Login</h2>
        <?php if ($login_err): ?>
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <?php echo $login_err; ?>
            </div>
        <?php endif; ?>
        <form id="loginForm" action="" method="post" novalidate>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" class="mt-1 block w-full p-3 border <?php echo $email_err ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo htmlspecialchars($email); ?>">
                <?php if ($email_err): ?>
                    <p class="mt-2 text-sm text-red-600"><?php echo $email_err; ?></p>
                <?php endif; ?>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" class="mt-1 block w-full p-3 border <?php echo $password_err ? 'border-red-500' : 'border-gray-300'; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <?php if ($password_err): ?>
                    <p class="mt-2 text-sm text-red-600"><?php echo $password_err; ?></p>
                <?php endif; ?>
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">Login</button>
            <p class="mt-4 text-center text-sm text-gray-600">Don't have an account? <a href="register.php" class="text-blue-600 hover:underline">Register</a></p>
        </form>
    </div>
    <script>
    // Client-side validation
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
    </script>
</body>
</html>