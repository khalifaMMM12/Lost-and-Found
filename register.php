<?php
require 'config.php';

$name = $email = $password = $confirm_password = '';
$name_err = $email_err = $password_err = $confirm_password_err = $register_success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
        $stmt->bind_param('sss', $name, $email, $hashed_password);
        if ($stmt->execute()) {
            $register_success = 'Registration successful! <a href="login.php">Login here</a>.';
            $name = $email = $password = $confirm_password = '';
        } else {
            $register_success = 'Something went wrong. Please try again.';
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
    <title>Register - Lost and Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .register-form { max-width: 400px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="register-form">
        <h2 class="mb-4 text-center">Register</h2>
        <?php if ($register_success): ?>
            <div class="alert alert-success"><?php echo $register_success; ?></div>
        <?php endif; ?>
        <form id="regForm" action="" method="post" novalidate>
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" class="form-control <?php echo $name_err ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($name); ?>">
                <div class="invalid-feedback"><?php echo $name_err; ?></div>
            </div>
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
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control <?php echo $confirm_password_err ? 'is-invalid' : ''; ?>">
                <div class="invalid-feedback"><?php echo $confirm_password_err; ?></div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
            <p class="mt-3 text-center">Already have an account? <a href="login.php">Login</a></p>
        </form>
    </div>
    <script>
    // Client-side validation
    document.getElementById('regForm').addEventListener('submit', function(e) {
        let valid = true;
        const name = this.name.value.trim();
        const email = this.email.value.trim();
        const password = this.password.value;
        const confirm_password = this.confirm_password.value;
        if (!name) valid = false;
        if (!email || !/^\S+@\S+\.\S+$/.test(email)) valid = false;
        if (!password || password.length < 6) valid = false;
        if (password !== confirm_password) valid = false;
        if (!valid) {
            e.preventDefault();
        }
    });
    </script>
</body>
</html> 