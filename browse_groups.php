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

// Fetch all groups
$sql = "SELECT GroupID, GroupName, Description FROM `Groups`";
$result = $conn->query($sql);

// Fetch groups the user has joined
$user_id = $_SESSION['user_id'];
$joined_groups_sql = "
    SELECT G.GroupID, G.GroupName, G.Description 
    FROM `Groups` G
    INNER JOIN `GroupMembers` GM ON G.GroupID = GM.GroupID
    WHERE GM.MemberID = ?";
$joined_stmt = $conn->prepare($joined_groups_sql);
$joined_stmt->bind_param("i", $user_id);
$joined_stmt->execute();
$joined_groups_result = $joined_stmt->get_result();

// Handle leave group action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['leave_group_id'])) {
    $leave_group_id = intval($_POST['leave_group_id']);

    // Remove user from the group
    $leave_group_sql = "DELETE FROM GroupMembers WHERE GroupID = ? AND MemberID = ?";
    $leave_stmt = $conn->prepare($leave_group_sql);
    $leave_stmt->bind_param("ii", $leave_group_id, $user_id);

    if ($leave_stmt->execute()) {
        echo "<script>alert('You have left the group successfully.');</script>";
    } else {
        echo "<script>alert('Failed to leave the group. Please try again.');</script>";
    }
}
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
        .group-item form {
            display: inline-block;
        }
        .group-item button {
            background-color: #34eb9e;
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .group-item button.leave-btn {
            background-color: #eb3434;
        }
        .group-item button.leave-btn:hover {
            background-color: #b82c2c;
        }
        .group-item button:hover {
            background-color: #29b87a;
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
        .create-group-container {
            text-align: center;
            margin: 20px;
        }
        .create-group-container button {
            background-color: #9e34eb;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin: 0 10px;
        }
        .create-group-container button:hover {
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
        <h2>All Groups</h2>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="group-item">
                    <h3><?php echo htmlspecialchars($row['GroupName']); ?></h3>
                    <p><?php echo htmlspecialchars($row['Description']); ?></p>
                    <form method="POST" action="">
                        <input type="hidden" name="group_id" value="<?php echo $row['GroupID']; ?>">
                        <button type="submit">Request to Join</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No groups found.</p>
        <?php endif; ?>
    </div>

    <!-- Joined Groups Container -->
    <div class="group-container">
        <h2>Your Groups</h2>
        <?php if ($joined_groups_result->num_rows > 0): ?>
            <?php while ($row = $joined_groups_result->fetch_assoc()): ?>
                <div class="group-item">
                    <h3><?php echo htmlspecialchars($row['GroupName']); ?></h3>
                    <p><?php echo htmlspecialchars($row['Description']); ?></p>
                    <a href="group.php?group_id=<?php echo $row['GroupID']; ?>">View Group</a>
                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="leave_group_id" value="<?php echo $row['GroupID']; ?>">
                        <button type="submit" class="leave-btn">Leave</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>You have not joined any groups yet.</p>
        <?php endif; ?>
    </div>

    <!-- Create and Manage Group Buttons -->
    <div class="create-group-container">
        <button onclick="window.location.href='create_group.php'">Create a Group</button>
        <button onclick="window.location.href='group_admin.php'">Manage Your Group</button>
    </div>
</body>
</html>
