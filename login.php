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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .login-form { max-width: 400px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="login-form">
        <h2 class="mb-4 text-center">Login</h2>
        <?php if ($login_err): ?>
            <div class="alert alert-danger"><?php echo $login_err; ?></div>
        <?php endif; ?>
        <form id="loginForm" action="" method="post" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control <?php echo $email_err ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($email); ?>">
                <div class="invalid-feedback"><?php echo $email_err; ?></div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control <?php echo $password_err ? 'is-invalid' : ''; ?>">
                <div class="invalid-feedback"><?php echo $password_err; ?></div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
            <p class="mt-3 text-center">Don't have an account? <a href="register.php">Register</a></p>
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