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

if (isset($_GET['id'])) {
    $post_id = $_GET['id'];

    // Fetch the post details to get the MemberID
    $post_query = "SELECT MemberID FROM Posts WHERE PostID = ?";
    $post_stmt = $conn->prepare($post_query);
    $post_stmt->bind_param("i", $post_id);
    if (!$post_stmt->execute()) {
        echo "Error executing post query: " . $post_stmt->error;
        exit();
    }
    $post_result = $post_stmt->get_result();
    $post_data = $post_result->fetch_assoc();

    if (!$post_data) {
        echo "No post found for this ID.";
        exit();
    }

    $member_id = $post_data['MemberID'];

    // Update the post status to 'Rejected'
    $reject_query = "UPDATE Posts SET ModerationStatus = 'Rejected' WHERE PostID = ?";
    $reject_stmt = $conn->prepare($reject_query);
    $reject_stmt->bind_param("i", $post_id);
    if (!$reject_stmt->execute()) {
        echo "Error rejecting post: " . $reject_stmt->error;
        exit();
    }

    // Update the strike count in UserStrikes table
    $strike_query = "SELECT StrikeCount FROM UserStrikes WHERE MemberID = ?";
    $strike_stmt = $conn->prepare($strike_query);
    $strike_stmt->bind_param("i", $member_id);
    if (!$strike_stmt->execute()) {
        echo "Error fetching strike count: " . $strike_stmt->error;
        exit();
    }
    $strike_result = $strike_stmt->get_result();
    $strike_data = $strike_result->fetch_assoc();
    $strike_count = $strike_data['StrikeCount'];

    // Increment the strike count
    $strike_count++;

    // Update the strike count in the UserStrikes table
    $update_strike_query = "UPDATE UserStrikes SET StrikeCount = ? WHERE MemberID = ?";
    $update_strike_stmt = $conn->prepare($update_strike_query);
    $update_strike_stmt->bind_param("ii", $strike_count, $member_id);
    if (!$update_strike_stmt->execute()) {
        echo "Error updating strike count: " . $update_strike_stmt->error;
        exit();
    }

    // If the strike count reaches 1, suspend the user and update status to 'Inactive'
    if ($strike_count >= 1) {
        $suspend_query = "UPDATE Member SET Status = 'Inactive' WHERE MemberID = ?";
        $suspend_stmt = $conn->prepare($suspend_query);
        $suspend_stmt->bind_param("i", $member_id);
        if (!$suspend_stmt->execute()) {
            echo "Error suspending user: " . $suspend_stmt->error;
            exit();
        }
    }

    echo "Post rejected and user status updated.";
}

$conn->close();
?>
