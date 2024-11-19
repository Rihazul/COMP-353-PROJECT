<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "upc353.encs.concordia.ca"; // Replace with your database server
$username = "upc353_2"; // Replace with your database username
$password = "SleighParableSystem73"; // Replace with your database password
$dbname = "upc353_2"; // Replace with your database name

// Attempt to create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection and provide detailed diagnostics
if ($conn->connect_error) {
    die("Connection failed: (" . $conn->connect_errno . ") " . $conn->connect_error . 
        "<br>Host: $servername<br>Username: $username<br>Database: $dbname");
}

// If the connection is successful, output a success message
echo "Connected successfully to database '$dbname' on host '$servername' as user '$username'.";
?>
