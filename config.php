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
?> 