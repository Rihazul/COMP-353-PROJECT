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

// Fetch the total number of members
$total_members_query = "SELECT COUNT(*) AS total_members FROM Member"; // Adjust table and column name based on your schema
$total_members_result = mysqli_query($conn, $total_members_query);
$total_members = mysqli_fetch_assoc($total_members_result)['total_members'];

// Fetch the total number of groups
$total_groups_query = "SELECT COUNT(*) AS total_groups FROM `Groups`"; // Enclose Groups in backticks
$total_groups_result = mysqli_query($conn, $total_groups_query);
$total_groups = mysqli_fetch_assoc($total_groups_result)['total_groups'];

// Check if there are no groups
if ($total_groups == 0) {
    $no_groups_message = "No groups available. Please create a group.";
} else {
    $no_groups_message = ""; // Reset message if groups exist
}

// Fetch the total number of pending posts (assuming a 'ModerationStatus' column)
$pending_posts_query = "SELECT COUNT(*) AS pending_posts FROM Posts WHERE ModerationStatus = 'Pending'"; // Adjust table and column name
$pending_posts_result = mysqli_query($conn, $pending_posts_query);
$pending_posts = mysqli_fetch_assoc($pending_posts_result)['pending_posts'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - COSN</title>
    <style>
        /* General body styling */
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

        /* Admin Menu */
        .admin-menu {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
        }

        .admin-menu a {
            text-decoration: none;
            color: #ffffff;
            background-color: #9e34eb;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1em;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .admin-menu a:hover {
            background-color: #7a29b8;
        }

        /* Quick Stats */
        .admin-stats {
            background-color: #e6f2ff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .admin-stats h3 {
            color: #9e34eb;
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        .admin-stats ul {
            list-style: none;
            padding: 0;
        }

        .admin-stats li {
            font-size: 1.1em;
            color: #333;
        }

        /* Admin Actions */
        .admin-actions {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 5px 0px #ccc;
            text-align: left;
        }

        .admin-actions h3 {
            color: #333;
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        .admin-actions ul {
            list-style: none;
            padding: 0;
        }

        .admin-actions li {
            margin: 8px 0;
        }

        .admin-actions li a {
            color: #9e34eb;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        .admin-actions li a:hover {
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

        /* No Groups Alert */
        .no-groups-alert {
            background-color: #ffcccc;
            padding: 15px;
            border-radius: 5px;
            color: #d8000c;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<!-- Top purple bar -->
<div id="purple_bar">
    <div class="title">COSN Admin Dashboard</div>
    <button class="logout-button" onclick="window.location.href='login.php'">Log out</button>
</div>

<!-- Admin Container -->
<div class="admin-container">
    <h1>Admin Dashboard</h1>
    <p>Welcome, Admin. Use the dashboard to manage members, groups, and content.</p>

    <!-- No Groups Alert -->
    <?php if ($no_groups_message): ?>
        <div class="no-groups-alert">
            <?php echo $no_groups_message; ?>
        </div>
    <?php endif; ?>

    <!-- Admin Navigation Menu -->
    <div class="admin-menu">
        <a href="manage_members.php">Manage Members</a>
        <a href="manage_groups.php">Manage Groups</a>
        <a href="moderate_posts.php">Moderate Posts</a>
        <a href="reports.php">View Reports</a>
    </div>

    <!-- Quick Stats -->
    <div class="admin-stats">
        <h3>Quick Stats</h3>
        <ul>
            <li>Total Members: <?php echo $total_members; ?></li>
            <li>Total Groups: <?php echo $total_groups; ?></li>
            <li>Pending Posts: <?php echo $pending_posts; ?></li>
        </ul>
    </div>

    <!-- Actions -->
    <div class="admin-actions">
        <h3>Admin Actions</h3>
        <ul>
            <li><a href="create_member.php">Create New Member</a></li>
            <li><a href="create_group.php">Create New Group</a></li>
            <li><a href="reports.php">Generate Member Reports</a></li>
        </ul>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    © 2024 COSN Admin Dashboard
</div>

</body>
</html>

<?php
// Close the database connection
session_unset();
session_destroy();
header("Location: login.php");
exit();
$conn->close();
?>
