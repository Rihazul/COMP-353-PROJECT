<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Admin Management</title>
    <style>
        body {
            font-family: Tahoma, sans-serif;
            background-color: #fac3da;
            margin: 0;
            padding: 0;
        }
        #purple_bar {
            height: 60px;
            background-color: #9e34eb;
            color: #fff;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logout-button {
            background-color: #fff;
            color: #9e34eb;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .logout-button:hover {
            background-color: #e0d4f7;
        }
        .admin-container {
            width: 900px;
            margin: 30px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .admin-section {
            margin-bottom: 30px;
        }
        h2 {
            color: #9e34eb;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            text-align: left;
            padding: 10px;
        }
        th {
            background-color: #9e34eb;
            color: white;
        }
        .approve-button, .disapprove-button, .edit-button, .delete-button {
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        .approve-button {
            background-color: #28a745;
            color: white;
        }
        .approve-button:hover {
            background-color: #218838;
        }
        .disapprove-button {
            background-color: #dc3545;
            color: white;
        }
        .disapprove-button:hover {
            background-color: #c82333;
        }
        .edit-button {
            background-color: #ffc107;
            color: black;
        }
        .edit-button:hover {
            background-color: #e0a800;
        }
        .delete-button {
            background-color: #dc3545;
            color: white;
        }
        .delete-button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Fetch user data
$user_sql = "SELECT MemberID, FirstName, LastName FROM Member WHERE Status = 'Inactive'";
$user_result = $conn->query($user_sql);

// Fetch post data
$post_sql = "SELECT PostID, TextContent FROM Posts WHERE ModerationStatus = 'Pending'";
$post_result = $conn->query($post_sql);
?>

<!-- Top Bar -->
<div id="purple_bar">
    <div style="font-size: 45px; font-weight: bold;">
        COSN
    </div>
    <button class="logout-button" onclick="window.location.href='login.php'">Log out</button>
</div>

<!-- Admin Container -->
<div class="admin-container">
    <!-- User Approval Section -->
    <div class="admin-section">
        <h2>Approve or Disapprove Users</h2>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($user_result->num_rows > 0) {
                    // Output data of each row
                    while($row = $user_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["MemberID"] . "</td>";
                        echo "<td>" . $row["FirstName"] . " " . $row["LastName"] . "</td>";
                        echo "<td>";
                        echo "<button class='approve-button' onclick='approveUser(" . $row["MemberID"] . ")'>Approve</button>";
                        echo "<button class='disapprove-button' onclick='disapproveUser(" . $row["MemberID"] . ")'>Disapprove</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No users to approve</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Post Management Section -->
    <div class="admin-section">
        <h2>Manage Group Posts</h2>
        <table>
            <thead>
                <tr>
                    <th>Post ID</th>
                    <th>Content</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($post_result->num_rows > 0) {
                    // Output data of each row
                    while($row = $post_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["PostID"] . "</td>";
                        echo "<td>" . $row["TextContent"] . "</td>";
                        echo "<td>";
                        echo "<button class='edit-button' onclick='editPost(" . $row["PostID"] . ")'>Edit</button>";
                        echo "<button class='delete-button' onclick='deletePost(" . $row["PostID"] . ")'>Delete</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No posts available</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</div>

    <script>
        function approveUser(userId) {
            // Implement AJAX call to approve the user
            alert('User ' + userId + ' approved.');
        }

        function disapproveUser(userId) {
            // Implement AJAX call to disapprove the user
            alert('User ' + userId + ' disapproved.');
        }

        function editPost(postId) {
            // Redirect to the edit post page or implement inline editing
            alert('Redirecting to edit post ' + postId);
            // Example: window.location.href = `edit_post.php?post_id=${postId}`;
        }

        function deletePost(postId) {
            // Implement AJAX call to delete the post
            if (confirm('Are you sure you want to delete post ' + postId + '?')) {
                alert('Post ' + postId + ' deleted.');
            }
        }
    </script>
</body>
</html>