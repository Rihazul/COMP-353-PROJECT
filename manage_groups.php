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
    <p>Below is the list of all active groups. You can edit or delete groups as needed.</p>

    <!-- Groups Table -->
    <table>
        <tr>
            <th>Group ID</th>
            <th>Group Name</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php
        // Example data; replace with database query results
        $groups = [
            ['id' => 1, 'name' => 'Tech Enthusiasts', 'description' => 'A group for tech lovers.'],
            ['id' => 2, 'name' => 'Photography Club', 'description' => 'Share your photography skills.'],
            ['id' => 3, 'name' => 'Book Readers', 'description' => 'For people who love reading books.']
        ];

        foreach ($groups as $group) {
            echo "<tr>";
            echo "<td>" . $group['id'] . "</td>";
            echo "<td>" . $group['name'] . "</td>";
            echo "<td>" . $group['description'] . "</td>";
            echo "<td><a href='edit_group.php?id=" . $group['id'] . "'>Edit</a> | <a href='delete_group.php?id=" . $group['id'] . "'>Delete</a></td>";
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
// CloseCon($conn);
?>
