<?php
// connect.php
$host = 'localhost';
$dbname = 'schooldb'; // Change to your database name
$username = 'sawda'; // Change if you're not using the default MySQL root user
$password = '123'; // Add your MySQL password if applicable

$conn = new mysqli($host, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
