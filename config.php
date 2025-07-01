<?php
// Database configuration
$host = 'localhost';
$db   = 'lost_and_found';
$user = 'root'; // Change as needed
$pass = '';     // Change as needed

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Session timeout logic (20 minutes)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$timeout_duration = 1200; // 20 minutes in seconds
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time();
?>