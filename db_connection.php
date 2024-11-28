<?php
$servername = "upc353.encs.concordia.ca";
$username = "upc353_2"; // Use the username for your MySQL database
$password = "SleighParableSystem73"; // Use the password for your MySQL database
$dbname = "cosn_database"; // Replace with your database name

// Create a new connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
