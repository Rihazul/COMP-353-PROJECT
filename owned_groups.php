<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Your Groups</title>
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
        .manage-button {
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            background-color: #34a7eb;
            color: white;
        }
        .manage-button:hover {
            background-color: #1d81c2;
        }
    </style>
</head>
<body>
<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Get logged-in member ID from session
$member_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
if ($member_id === 0) {
    echo "<p>You must be logged in to view this page.</p>";
    exit;
}

// Fetch manageable groups for the logged-in member
$groups_sql = "
    SELECT G.GroupID, G.GroupName, G.Description
    FROM `Groups` G
    INNER JOIN `GroupMembers` GM ON G.GroupID = GM.GroupID
    WHERE GM.MemberID = ? AND GM.Role = 'Owner'";
$groups_stmt = $conn->prepare($groups_sql);
$groups_stmt->bind_param("i", $member_id);
$groups_stmt->execute();
$groups_result = $groups_stmt->get_result();
?>

<!-- Top Bar -->
<div id="purple_bar">
    <div style="font-size: 45px; font-weight: bold;">COSN</div>
    <button class="logout-button" onclick="window.location.href='login.php'">Log out</button>
</div>

<!-- Admin Container -->
<div class="admin-container">
    <h2>Manage Your Groups</h2>
    <table>
        <thead>
            <tr>
                <th>Group ID</th>
                <th>Group Name</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($groups_result->num_rows > 0) {
                while ($row = $groups_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['GroupID']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['GroupName']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Description']) . "</td>";
                    echo "<td>";
                    echo "<a href='group_admin.php?group_id=" . $row['GroupID'] . "' class='manage-button'>Manage</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>You do not own any groups.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php
$conn->close();
?>
</body>
</html>