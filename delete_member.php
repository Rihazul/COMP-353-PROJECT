<?php
session_start();

// Database connection
$servername = "upc353.encs.concordia.ca";
$username = "upc353_2";
$password = "SleighParableSystem73";
$dbname = "upc353_2";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the MemberID from the URL
$member_id = $_GET['id'];

// Assume admin privilege is 'Administrator'
$admin_privilege = 'Administrator';  // This is the privilege for admin users

// Check if the member to delete is an admin
$check_privilege_query = "SELECT Privilege FROM Member WHERE MemberID = $member_id";
$check_privilege_result = mysqli_query($conn, $check_privilege_query);
$member = mysqli_fetch_assoc($check_privilege_result);

if ($member['Privilege'] == $admin_privilege) {
    echo "You cannot delete the admin user.";
    exit;
}

// First, delete any rows in the Friends table that reference this member
$delete_friends_query = "DELETE FROM Friends WHERE MemberID1 = $member_id OR MemberID2 = $member_id";
if (!mysqli_query($conn, $delete_friends_query)) {
    echo "Error deleting related friends: " . mysqli_error($conn);
    exit;
}

// Now, delete the member from the Member table
$delete_member_query = "DELETE FROM Member WHERE MemberID = $member_id";
if (mysqli_query($conn, $delete_member_query)) {
    // Redirect to the admin dashboard after successful deletion
    header("Location: manage_members.php");
} else {
    echo "Error deleting member: " . mysqli_error($conn);
}

// Close the database connection
$conn->close();
?>
