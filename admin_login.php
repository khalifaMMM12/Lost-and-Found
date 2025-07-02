<?php
require 'config.php';

$email = $password = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $sql = "SELECT admin_id, name, email, password FROM admin_users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($admin_id, $name, $db_email, $db_password);
        $stmt->fetch();
        if (password_verify($password, $db_password)) {
            $_SESSION['admin_id'] = $admin_id;
            $_SESSION['admin_name'] = $name;
            $_SESSION['admin_email'] = $db_email;
            $_SESSION['role'] = 'admin';
            header('Location: admin_dashboard.php');
            exit;
        } else {
            $error = 'Invalid credentials.';
        }
    } else {
        $error = 'Invalid credentials.';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="./src/output.css">
    <script src="https://kit.fontawesome.com/79a49acde1.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans text-gray-800 flex items-center justify-center">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-700">Admin Login</h2>
        <?php if ($error): ?>
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post" action="" novalidate>
            <div class="mb-4">
                <label class="block text-sm font-medium">Email</label>
                <input type="email" name="email" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-blue-200 focus:border-blue-400" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium">Password</label>
                <input type="password" name="password" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-blue-200 focus:border-blue-400" required>
            </div>
            <button type="submit" class="w-full bg-blue-700 text-white py-2 rounded hover:bg-blue-800 transition">Login</button>
        </form>
        <div class="mt-4 text-center">
            <a href="login.php" class="text-sm text-blue-500 hover:underline">← User Login</a>
        </div>
    </div>
</body>
</html>
