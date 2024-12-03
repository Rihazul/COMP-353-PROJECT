<?php
session_start();

// Database connection
$servername = "upc353.encs.concordia.ca";
$username = "upc353_2";
$password = "SleighParableSystem73";
$dbname = "upc353_2";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the GroupID from the URL
$groupID = isset($_GET['id']) ? $_GET['id'] : 0;

if ($groupID > 0) {
    // Delete the group from the database
    $deleteSql = "DELETE FROM `Groups` WHERE GroupID = $groupID";

    if ($conn->query($deleteSql) === TRUE) {
        echo "Group deleted successfully!";
        header("Location: manage_groups.php"); // Redirect to the manage groups page
        exit;
    } else {
        echo "Error deleting group: " . $conn->error;
    }
} else {
    echo "Invalid Group ID.";
}

$conn->close();
?>
