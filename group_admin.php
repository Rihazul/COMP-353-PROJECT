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
        .approve-button, .reject-button, .edit-button, .delete-button {
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
        .reject-button {
            background-color: #dc3545;
            color: white;
        }
        .reject-button:hover {
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

// Fetch pending join requests
$group_id = 8; // Replace with the group ID for the admin
$join_requests_sql = "
    SELECT JR.RequestID, JR.MemberID, M.FirstName, M.LastName, JR.RequestedAt
    FROM JoinRequests JR
    INNER JOIN Member M ON JR.MemberID = M.MemberID
    WHERE JR.GroupID = ? AND JR.Status = 'Pending'";
$join_requests_stmt = $conn->prepare($join_requests_sql);
$join_requests_stmt->bind_param("i", $group_id);
$join_requests_stmt->execute();
$join_requests_result = $join_requests_stmt->get_result();

// Fetch posts for moderation
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
    <!-- Pending Join Requests Section -->
    <div class="admin-section">
        <h2>Manage Join Requests</h2>
        <table>
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Requested At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($join_requests_result->num_rows > 0) {
                    while ($row = $join_requests_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["RequestID"] . "</td>";
                        echo "<td>" . $row["MemberID"] . "</td>";
                        echo "<td>" . $row["FirstName"] . " " . $row["LastName"] . "</td>";
                        echo "<td>" . $row["RequestedAt"] . "</td>";
                        echo "<td>";
                        echo "<form method='post' style='display: inline;'>
                                <input type='hidden' name='request_id' value='" . $row["RequestID"] . "'>
                                <button type='submit' name='action' value='approve' class='approve-button'>Approve</button>
                                <button type='submit' name='action' value='reject' class='reject-button'>Reject</button>
                              </form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No pending join requests</td></tr>";
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
                    while ($row = $post_result->fetch_assoc()) {
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
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Handle approval or rejection of join requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['action'])) {
    $request_id = intval($_POST['request_id']);
    $action = $_POST['action'];

    if ($action === 'approve') {
        $update_sql = "UPDATE JoinRequests SET Status = 'Approved' WHERE RequestID = ?";
    } elseif ($action === 'reject') {
        $update_sql = "UPDATE JoinRequests SET Status = 'Rejected' WHERE RequestID = ?";
    }

    if (!empty($update_sql)) {
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $request_id);
        if ($update_stmt->execute()) {
            echo "<script>alert('Request has been processed successfully.'); window.location.reload();</script>";
        } else {
            echo "<script>alert('Failed to process the request.');</script>";
        }
    }
}

$conn->close();
?>

<script>
    function editPost(postId) {
        alert('Redirecting to edit post ' + postId);
        // Redirect or handle editing logic
    }

    function deletePost(postId) {
        if (confirm('Are you sure you want to delete post ' + postId + '?')) {
            alert('Post ' + postId + ' deleted.');
            // Add AJAX or server-side logic to delete the post
        }
    }
</script>
</body>
</html>