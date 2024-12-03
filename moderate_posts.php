<?php
session_start();

// Database connection
$servername = "upc353.encs.concordia.ca";
$username = "upc353_2";
$password = "SleighParableSystem73";
$dbname = "upc353_2";

$conn = new mysqli($servername, $username, $password, $dbname);



// Query to get pending posts
$sql = "SELECT * FROM Posts WHERE ModerationStatus = 'Pending'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Moderate Posts</title>
    <style>
        body {
            font-family: Tahoma, sans-serif;
            background-color: #fac3da;
            margin: 0;
            padding: 0;
        }

        /* Top purple bar */
        #purple_bar {
            height: 60px;
            background-color: #9e34eb;
            color: #fff;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        #purple_bar .title {
            font-size: 24px;
            font-weight: bold;
        }

        .logout-button {
            background-color: #fff;
            color: #9e34eb;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .logout-button:hover {
            background-color: #e0d4f7;
        }

        /* Admin Container */
        .admin-container {
            width: 900px;
            margin: 30px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #000;
            text-align: center;
        }

        h1 {
            color: #9e34eb;
            font-size: 2em;
            margin-bottom: 10px;
        }

        /* Post Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #9e34eb;
            color: #fff;
            font-weight: bold;
        }

        td a {
            color: #9e34eb;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        td a:hover {
            color: #7a29b8;
            text-decoration: underline;
        }

        /* Footer */
        .footer {
            text-align: center;
            color: #333;
            font-size: 0.9em;
            padding: 10px;
        }
    </style>
</head>
<body>

<!-- Top purple bar -->
<div id="purple_bar">
    <div class="title">Manage Posts</div>
    <button class="logout-button" onclick="window.location.href='login.php'">Log out</button>
</div>

<!-- Admin Container -->
<div class="admin-container">
    <h1>Moderate Posts</h1>
    <p>Below is the list of posts that need moderation. You can approve or reject posts.</p>

    <!-- Posts Table -->
    <table>
        <tr>
            <th>Post ID</th>
            <th>Author</th>
            <th>Content</th>
            <th>Actions</th>
        </tr>
        <?php
        // Fetch and display pending posts
        while ($row = $result->fetch_assoc()) {
            // Get author details
            $member_query = "SELECT FirstName, LastName FROM Member WHERE MemberID = ?";
            $member_stmt = $conn->prepare($member_query);
            $member_stmt->bind_param("i", $row['MemberID']);
            $member_stmt->execute();
            $member_result = $member_stmt->get_result();
            $member = $member_result->fetch_assoc();

            echo "<tr>";
            echo "<td>" . $row['PostID'] . "</td>";
            echo "<td>" . $member['FirstName'] . " " . $member['LastName'] . "</td>";
            echo "<td>" . substr($row['TextContent'], 0, 50) . "...</td>";
            echo "<td>
                    <a href='approve_post.php?id=" . $row['PostID'] . "'>Approve</a> | 
                    <a href='delete_post.php?id=" . $row['PostID'] . "'>Reject</a>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>

<!-- Footer -->
<div class="footer">
    © 2024 COSN Admin Dashboard
</div>

</body>
</html>

<?php
// Close the database connection

?>
