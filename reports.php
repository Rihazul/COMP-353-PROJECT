<?php
session_start();

// Database connection
// include 'db_connection.php';
// $conn = OpenCon();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Reports - COSN</title>
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

        /* Report Actions */
        .report-actions {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 5px 0px #ccc;
            text-align: left;
        }

        .report-actions h3 {
            color: #333;
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        .report-actions ul {
            list-style: none;
            padding: 0;
        }

        .report-actions li {
            margin: 8px 0;
        }

        .report-actions li a {
            color: #9e34eb;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        .report-actions li a:hover {
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
    <div class="title">Generate Reports</div>
    <button class="logout-button" onclick="window.location.href='login.php'">Log out</button>
</div>

<!-- Admin Container -->
<div class="admin-container">
    <h1>Generate Reports</h1>
    <p>Select a report type to generate insights into user activities, group statistics, and platform interactions.</p>

    <!-- Report Actions -->
    <div class="report-actions">
        <h3>Report Types</h3>
        <ul>
            <li><a href="user_activity_report.php" target="_blank">User Activity Report</a> - View user login history, actions, and engagement metrics.</li>
            <li><a href="group_statistics_report.php" target="_blank">Group Statistics Report</a> - Analyze group memberships, active groups, and content contributions.</li>
            <li><a href="post_interaction_report.php" target="_blank">Post Interaction Report</a> - Review post activity, likes, comments, and sharing statistics.</li>
            <li><a href="content_moderation_report.php" target="_blank">Content Moderation Report</a> - Summary of flagged posts, moderation actions, and outcomes.</li>
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
// CloseCon($conn);
?>
