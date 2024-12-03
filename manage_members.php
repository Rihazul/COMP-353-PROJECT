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

// Fetch members from the database
$members_query = "SELECT MemberID, FirstName, LastName, Email FROM Member"; // Adjust the table and column names if necessary
$members_result = mysqli_query($conn, $members_query);

if (!$members_result) {
    die("Error fetching members: " . mysqli_error($conn));
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Members - COSN</title>
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

        /* Member Table */
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
    <div class="title">Manage Members</div>
    <button class="logout-button" onclick="window.location.href='login.php'">Log out</button>
</div>

<!-- Admin Container -->
<div class="admin-container">
    <h1>Manage Members</h1>
    <p>Below is the list of all registered members. You can edit or remove members as needed.</p>

    <!-- Members Table -->
    <table>
        <tr>
            <th>Member ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php
        // Display each member from the database
        while ($member = mysqli_fetch_assoc($members_result)) {
            echo "<tr>";
            echo "<td>" . $member['MemberID'] . "</td>";
            echo "<td>" . $member['FirstName'] . "</td>";
            echo "<td>" . $member['LastName'] . "</td>";
            echo "<td>" . $member['Email'] . "</td>";
            echo "<td>
                    <a href='edit_member.php?id=" . $member['MemberID'] . "'>Edit</a> | 
                    <a href='delete_member.php?id=" . $member['MemberID'] . "'>Delete</a>
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
$conn->close();
?>
