<?php
require 'config.php';

echo "<h2>Database Connection Test</h2>";

// Test connection
if ($conn->ping()) {
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
    exit;
}

// Check if database exists
$result = $conn->query("SHOW DATABASES LIKE 'lost_and_found'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✓ Database 'lost_and_found' exists</p>";
} else {
    echo "<p style='color: red;'>✗ Database 'lost_and_found' does not exist</p>";
}

// Check if users table exists
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✓ Table 'users' exists</p>";
    
    // Show table structure
    $result = $conn->query("DESCRIBE users");
    echo "<h3>Users Table Structure:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>✗ Table 'users' does not exist</p>";
}

// Test inserting a user
echo "<h3>Testing User Insertion:</h3>";
$test_name = "Test User";
$test_email = "test@example.com";
$test_password = password_hash("test123", PASSWORD_DEFAULT);

$sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $test_name, $test_email, $test_password);

if ($stmt->execute()) {
    echo "<p style='color: green;'>✓ Test user inserted successfully</p>";
    // Clean up test data
    $conn->query("DELETE FROM users WHERE email = 'test@example.com'");
    echo "<p style='color: blue;'>✓ Test data cleaned up</p>";
} else {
    echo "<p style='color: red;'>✗ Failed to insert test user: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?> 