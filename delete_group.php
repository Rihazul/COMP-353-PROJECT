<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "upc353.encs.concordia.ca";
$username = "upc353_2";
$password = "SleighParableSystem73";
$dbname = "upc353_2";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the GroupID from the URL
$groupID = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($groupID > 0) {
    // Step 1: Delete associated rows from Posts
    $deletePostsQuery = "DELETE FROM `Posts` WHERE GroupID = ?";
    $deletePostsStmt = $conn->prepare($deletePostsQuery);
    $deletePostsStmt->bind_param("i", $groupID);

    if (!$deletePostsStmt->execute()) {
        echo "Error deleting associated posts: " . $deletePostsStmt->error;
        exit();
    }

    // Step 2: Delete associated rows from JoinRequests
    $deleteJoinRequestsQuery = "DELETE FROM `JoinRequests` WHERE GroupID = ?";
    $deleteJoinRequestsStmt = $conn->prepare($deleteJoinRequestsQuery);
    $deleteJoinRequestsStmt->bind_param("i", $groupID);

    if (!$deleteJoinRequestsStmt->execute()) {
        echo "Error deleting associated join requests: " . $deleteJoinRequestsStmt->error;
        exit();
    }

    // Step 3: Delete the group itself
    $deleteGroupQuery = "DELETE FROM `Groups` WHERE GroupID = ?";
    $deleteGroupStmt = $conn->prepare($deleteGroupQuery);
    $deleteGroupStmt->bind_param("i", $groupID);

    if ($deleteGroupStmt->execute()) {
        echo "Group deleted successfully!";
        header("Location: manage_groups.php");
        exit();
    } else {
        echo "Error deleting group: " . $deleteGroupStmt->error;
    }

    // Close statements
    $deletePostsStmt->close();
    $deleteJoinRequestsStmt->close();
    $deleteGroupStmt->close();
} else {
    echo "Invalid Group ID.";
}

$conn->close();
?>
