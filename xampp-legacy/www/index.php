<?php
echo "<h1>XAMPP 5 (PHP 5.6) is running!</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Test MySQL connection
$host = 'localhost';
$user = 'root';
$pass = ''; // Default XAMPP has no password

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    echo "<p style='color: red;'>MySQL Connection Failed: " . $conn->connect_error . "</p>";
} else {
    echo "<p style='color: green;'>MySQL Connection Successful!</p>";
    $conn->close();
}

echo "<hr>";
phpinfo();
?>
