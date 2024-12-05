<?php
session_start();
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

// Get the GroupID from the URL parameter
if (!isset($_GET['group_id']) || !is_numeric($_GET['group_id'])) {
    die("Invalid or missing Group ID.");
}
$group_id = intval($_GET['group_id']); // Sanitize input

// Fetch pending join requests for the specific group
$join_requests_sql = "
    SELECT JR.RequestID, JR.MemberID, M.FirstName, M.LastName, JR.RequestedAt
    FROM JoinRequests JR
    INNER JOIN Member M ON JR.MemberID = M.MemberID
    WHERE JR.GroupID = ? AND JR.Status = 'Pending'";
$join_requests_stmt = $conn->prepare($join_requests_sql);
$join_requests_stmt->bind_param("i", $group_id);
$join_requests_stmt->execute();
$join_requests_result = $join_requests_stmt->get_result();

// Fetch posts for moderation for the specific group
$post_sql = "
    SELECT PostID, TextContent 
    FROM Posts 
    WHERE ModerationStatus = 'Pending' AND GroupID = ?";
$post_stmt = $conn->prepare($post_sql);
$post_stmt->bind_param("i", $group_id);
$post_stmt->execute();
$post_result = $post_stmt->get_result();

// Handle approval or rejection of join requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['action'])) {
    $request_id = intval($_POST['request_id']);
    $action = $_POST['action'];

    if ($action === 'approve') {
        // Approve the join request
        $approve_sql = "UPDATE JoinRequests SET Status = 'Approved' WHERE RequestID = ?";
        $approve_stmt = $conn->prepare($approve_sql);
        $approve_stmt->bind_param("i", $request_id);

        if ($approve_stmt->execute()) {
            echo "<script>alert('Join request approved successfully.');</script>";
        } else {
            echo "<script>alert('Failed to approve the join request. Error: " . $approve_stmt->error . "');</script>";
        }
    } elseif ($action === 'reject') {
        // Reject the join request
        $reject_sql = "UPDATE JoinRequests SET Status = 'Rejected' WHERE RequestID = ?";
        $reject_stmt = $conn->prepare($reject_sql);
        $reject_stmt->bind_param("i", $request_id);

        if ($reject_stmt->execute()) {
            echo "<script>alert('Join request rejected successfully.');</script>";
        } else {
            echo "<script>alert('Failed to reject the join request. Error: " . $reject_stmt->error . "');</script>";
        }
    }

    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . "?group_id=" . $group_id);
    exit();
}

// Handle approval or rejection of posts
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'], $_POST['post_action'])) {
    $post_id = intval($_POST['post_id']);
    $post_action = $_POST['post_action'];

    if ($post_action === 'approve') {
        // Approve the post
        $approve_post_sql = "UPDATE Posts SET ModerationStatus = 'Approved' WHERE PostID = ?";
        $approve_post_stmt = $conn->prepare($approve_post_sql);
        $approve_post_stmt->bind_param("i", $post_id);

        if ($approve_post_stmt->execute()) {
            echo "<script>alert('Post approved successfully.');</script>";
        } else {
            echo "<script>alert('Failed to approve the post. Error: " . $approve_post_stmt->error . "');</script>";
        }
    } elseif ($post_action === 'reject') {
        // Reject the post
        $reject_post_sql = "UPDATE Posts SET ModerationStatus = 'Rejected' WHERE PostID = ?";
        $reject_post_stmt = $conn->prepare($reject_post_sql);
        $reject_post_stmt->bind_param("i", $post_id);

        if ($reject_post_stmt->execute()) {
            echo "<script>alert('Post rejected successfully.');</script>";
        } else {
            echo "<script>alert('Failed to reject the post. Error: " . $reject_post_stmt->error . "');</script>";
        }
    }

    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . "?group_id=" . $group_id);
    exit();
}
?>
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
            color: white;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logout-button {
            background-color: white;
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
        .approve-button, .reject-button {
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
    </style>
</head>
<body>
<div id="purple_bar">
    <div style="font-size: 45px; font-weight: bold;">COSN</div>
    <button class="logout-button" onclick="window.location.href='login.php'">Log out</button>
</div>

<div class="admin-container">
    <!-- Manage Join Requests -->
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
                <?php if ($join_requests_result->num_rows > 0): ?>
                    <?php while ($row = $join_requests_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['RequestID']; ?></td>
                            <td><?php echo $row['MemberID']; ?></td>
                            <td><?php echo htmlspecialchars($row['FirstName'] . " " . $row['LastName']); ?></td>
                            <td><?php echo $row['RequestedAt']; ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="request_id" value="<?php echo $row['RequestID']; ?>">
                                    <button type="submit" name="action" value="approve" class="approve-button">Approve</button>
                                    <button type="submit" name="action" value="reject" class="reject-button">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No pending join requests</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Manage Group Posts -->
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
                <?php if ($post_result->num_rows > 0): ?>
                    <?php while ($row = $post_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['PostID']; ?></td>
                            <td><?php echo htmlspecialchars($row['TextContent']); ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="post_id" value="<?php echo $row['PostID']; ?>">
                                    <button type="submit" name="post_action" value="approve" class="approve-button">Approve</button>
                                    <button type="submit" name="post_action" value="reject" class="reject-button">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3">No posts available</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>