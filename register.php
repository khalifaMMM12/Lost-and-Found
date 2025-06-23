<?php
session_start();
require 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$name = $email = $password = $confirm_password = '';
$name_err = $email_err = $password_err = $confirm_password_err = $register_success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Debug: Log the POST data
    error_log("Registration attempt - POST data: " . print_r($_POST, true));
    
    // Validate name
    if (empty(trim($_POST['name']))) {
        $name_err = 'Please enter your name.';
    } else {
        $name = trim($_POST['name']);
    }

    // Validate email
    if (empty(trim($_POST['email']))) {
        $email_err = 'Please enter your email.';
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $email_err = 'Invalid email format.';
    } else {
        // Check if email already exists
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

    // Validate password
    if (empty(trim($_POST['password']))) {
        $password_err = 'Please enter a password.';
    } elseif (strlen(trim($_POST['password'])) < 6) {
        $password_err = 'Password must be at least 6 characters.';
    } else {
        $password = trim($_POST['password']);
    }

    // Validate confirm password
    if (empty(trim($_POST['confirm_password']))) {
        $confirm_password_err = 'Please confirm your password.';
    } else {
        $confirm_password = trim($_POST['confirm_password']);
        if ($password !== $confirm_password) {
            $confirm_password_err = 'Passwords do not match.';
        }
    }

    // Insert into database if no errors
    if (empty($name_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = 'INSERT INTO users (name, email, password) VALUES (?, ?, ?)';
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed for insert: " . $conn->error);
            $register_success = 'Database error occurred. Please try again.';
        } else {
            $stmt->bind_param('sss', $name, $email, $hashed_password);
            if ($stmt->execute()) {
                // Auto-login after registration
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Lost and Found</title>
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
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-center">Register</h2>
            <?php if ($register_success): ?>
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    <?php echo $register_success; ?>
                </div>
            <?php endif; ?>
            <form action="" method="post">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="name" class="mt-1 block w-full p-2 border <?php echo $name_err ? 'border-red-500' : 'border-gray-300'; ?> rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="<?php echo htmlspecialchars($name); ?>" required>
                    <?php if ($name_err): ?>
                        <p class="mt-1 text-sm text-red-500"><?php echo $name_err; ?></p>
                    <?php endif; ?>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" class="mt-1 block w-full p-2 border <?php echo $email_err ? 'border-red-500' : 'border-gray-300'; ?> rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="<?php echo htmlspecialchars($email); ?>" required>
                    <?php if ($email_err): ?>
                        <p class="mt-1 text-sm text-red-500"><?php echo $email_err; ?></p>
                    <?php endif; ?>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" class="mt-1 block w-full p-2 border <?php echo $password_err ? 'border-red-500' : 'border-gray-300'; ?> rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    <?php if ($password_err): ?>
                        <p class="mt-1 text-sm text-red-500"><?php echo $password_err; ?></p>
                    <?php endif; ?>
                </div>
                <div class="mb-6">
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="mt-1 block w-full p-2 border <?php echo $confirm_password_err ? 'border-red-500' : 'border-gray-300'; ?> rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    <?php if ($confirm_password_err): ?>
                        <p class="mt-1 text-sm text-red-500"><?php echo $confirm_password_err; ?></p>
                    <?php endif; ?>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">Register</button>
                <p class="mt-4 text-center text-sm text-gray-600">Already have an account? <a href="login.php" class="text-blue-600 hover:underline">Login</a></p>
            </form>
        </div>
    </div>
</body>
</html>