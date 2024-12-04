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


$moderatorId = 1; // Logged-in moderator's ID

if (isset($_GET['id'])) {
    $postId = intval($_GET['id']);

    // Fetch the MemberID of the post's author
    $authorQuery = "SELECT MemberID FROM Posts WHERE PostID = ?";
    $stmt = $conn->prepare($authorQuery);
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();

    if (!$post) {
        die("Error: Post not found.");
    }

    $authorId = intval($post['MemberID']);

    // Delete the post
    $deleteQuery = "DELETE FROM Posts WHERE PostID = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $postId);
    if ($stmt->execute()) {
        // Send a rejection message
        $message = "Your post with ID $postId has been rejected. Please ensure future posts adhere to community guidelines.";
        $sendMessageQuery = "INSERT INTO Messages (SenderMemberID, RecipientMemberID, Content, DateSent) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sendMessageQuery);
        $stmt->bind_param("iis", $moderatorId, $authorId, $message);
        if ($stmt->execute()) {
            echo "Post rejected, and author notified.";
        } else {
            echo "Failed to send rejection message: " . $conn->error;
        }
    } else {
        echo "Failed to reject the post: " . $conn->error;
    }
} else {
    echo "Invalid post ID.";
}

$conn->close();
?>
