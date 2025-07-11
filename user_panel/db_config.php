<?php
// Database Configuration

$host = 'localhost';      // Database Host (Default: localhost)
$user = 'root';           // Database Username (Default: root in WAMP)
$password = '';           // Database Password (Leave blank if no password is set)
$database = 'arprimeshowroom'; // Your Database Name

// Create Connection
$conn = mysqli_connect($host, $user, $password, $database);

// Check Connection
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}

// Optional: Uncomment to display successful connection
// echo 'Connected successfully!';
?>
