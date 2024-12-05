<?php
session_start();

// Database connection
// Database connection
$servername = "upc353.encs.concordia.ca";
$username = "upc353_2";
$password = "SleighParableSystem73";
$dbname = "upc353_2";

$conn = new mysqli($servername, $username, $password, $dbname);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Groups - COSN</title>
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

        /* Group Table */
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
    <div class="title">Manage Groups</div>
    <button class="logout-button" onclick="window.location.href='login.php'">Log out</button>
</div>

<!-- Admin Container -->
<div class="admin-container">
    <h1>Manage Groups</h1>
    <p>Below is the list of all active groups and their members. You can edit or delete groups as needed.</p>

    <!-- Groups Table -->
    <table>
        <tr>
            <th>Group ID</th>
            <th>Group Name</th>
            <th>Description</th>
            <th>Members</th>
            <th>Actions</th>
        </tr>
        <?php
        // Fetch group information
        $sql_groups = "SELECT * FROM `Groups`";
        $result_groups = $conn->query($sql_groups);

        if ($result_groups->num_rows > 0) {
            while ($group = $result_groups->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $group['GroupID'] . "</td>";
                echo "<td>" . $group['GroupName'] . "</td>";
                echo "<td>" . $group['Description'] . "</td>";

                // Fetch members for each group
                $group_id = $group['GroupID'];
                $sql_members = "SELECT MemberID, Role FROM `GroupMembers` WHERE GroupID = $group_id";
                $result_members = $conn->query($sql_members);

                echo "<td>";
                if ($result_members->num_rows > 0) {
                    echo "<ul>";
                    while ($member = $result_members->fetch_assoc()) {
                        echo "<li>Member ID: " . $member['MemberID'] . " (" . $member['Role'] . ")</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "No members";
                }
                echo "</td>";

                echo "<td>
                <a href='edit_group.php?id=" . $group['GroupID'] . "'>Edit</a> |
                <a href='delete_group.php?id=" . $group['GroupID'] . "'>Delete</a>
              </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No groups found</td></tr>";
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
CloseCon($conn);
?>
