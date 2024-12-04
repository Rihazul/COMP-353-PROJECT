<?php
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

// Fetch all groups
$sql = "SELECT GroupID, GroupName, Description FROM `Groups`";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Groups</title>
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
        .group-container {
            width: 900px;
            margin: 30px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .group-item {
            padding: 15px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
        }
        .group-item h3 {
            margin: 0;
            color: #9e34eb;
        }
        .group-item p {
            margin: 5px 0;
            color: #555;
        }
        .group-item a {
            text-decoration: none;
            color: white;
            background-color: #9e34eb;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .group-item a:hover {
            background-color: #7a29b8;
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div id="purple_bar">
        <div style="font-size: 45px; font-weight: bold;">
            COSN
        </div>
        <button class="logout-button" onclick="window.location.href='login.php'">Log out</button>
    </div>

    <!-- Group List Container -->
    <div class="group-container">
        <h2>Browse Groups</h2>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="group-item">
                    <h3><?php echo htmlspecialchars($row['GroupName']); ?></h3>
                    <p><?php echo htmlspecialchars($row['Description']); ?></p>
                    <a href="group.php?group_id=<?php echo $row['GroupID']; ?>">View Group</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No groups found.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>