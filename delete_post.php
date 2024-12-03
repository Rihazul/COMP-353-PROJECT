<?php
session_start();

// Database connection
$servername = "upc353.encs.concordia.ca";
$username = "upc353_2";
$password = "SleighParableSystem73";
$dbname = "upc353_2";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check if post ID is passed
if (isset($_GET['id'])) {
    $postID = $_GET['id'];

    // Delete the post from the database
    $sql = "DELETE FROM Posts WHERE PostID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $postID);

    if ($stmt->execute()) {
        // Redirect back to the manage posts page
        header("Location: moderate_posts.php");
        exit();
    } else {
        echo "Error deleting the post.";
    }
}

// Close the database connection
$conn->close();
?>
