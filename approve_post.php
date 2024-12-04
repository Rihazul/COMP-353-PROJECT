<?php
session_start();

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
    $post_stmt->execute();
    $post_result = $post_stmt->get_result();
    $post_data = $post_result->fetch_assoc();
    $member_id = $post_data['MemberID'];

    // Update the post status to 'Approved'
    $approve_query = "UPDATE Posts SET ModerationStatus = 'Approved' WHERE PostID = ?";
    $approve_stmt = $conn->prepare($approve_query);
    $approve_stmt->bind_param("i", $post_id);
    $approve_stmt->execute();

    // Update the strike count and check suspension
    $strike_query = "SELECT StrikeCount FROM UserStrikes WHERE MemberID = ?";
    $strike_stmt = $conn->prepare($strike_query);
    $strike_stmt->bind_param("i", $member_id);
    $strike_stmt->execute();
    $strike_result = $strike_stmt->get_result();
    $strike_data = $strike_result->fetch_assoc();
    $strike_count = $strike_data['StrikeCount'];

    // If the strike count reaches 1, suspend the user and update status to 'Inactive'
    if ($strike_count >= 1) {
        // Update suspension status in Member table
        $suspend_query = "UPDATE Member SET Status = 'Inactive' WHERE MemberID = ?";
        $suspend_stmt = $conn->prepare($suspend_query);
        $suspend_stmt->bind_param("i", $member_id);
        $suspend_stmt->execute();
    }
}

$conn->close();
?>
