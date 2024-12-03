<?php
session_start();

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

// Get the GroupID from the URL
$groupID = isset($_GET['id']) ? $_GET['id'] : 0;

if ($groupID > 0) {
    // Fetch the group data from the database
    $sql = "SELECT * FROM `Groups` WHERE GroupID = $groupID";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $group = $result->fetch_assoc();
    } else {
        echo "Group not found.";
        exit;
    }
} else {
    echo "Invalid Group ID.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $groupName = $_POST['GroupName'];
    $description = $_POST['Description'];

    // Update the group details in the database
    $updateSql = "UPDATE `Groups` SET GroupName = '$groupName', Description = '$description' WHERE GroupID = $groupID";

    if ($conn->query($updateSql) === TRUE) {
        echo "Group updated successfully!";
        header("Location: manage_groups.php"); // Redirect to the manage groups page
        exit;
    } else {
        echo "Error updating group: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Group - COSN</title>
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

        /* Form Styling */
        form {
            width: 60%;
            margin: 0 auto;
            text-align: left;
        }

        label {
            font-weight: bold;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            background-color: #9e34eb;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #7a29b8;
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
    <div class="title">Edit Group</div>
    <button class="logout-button" onclick="window.location.href='login.php'">Log out</button>
</div>

<!-- Admin Container -->
<div class="admin-container">
    <h1>Edit Group</h1>
    <form method="POST">
        <label for="GroupName">Group Name:</label>
        <input type="text" name="GroupName" id="GroupName" value="<?php echo htmlspecialchars($group['GroupName']); ?>" required><br>

        <label for="Description">Description:</label><br>
        <textarea name="Description" id="Description" rows="4" cols="50" required><?php echo htmlspecialchars($group['Description']); ?></textarea><br>

        <button type="submit">Update Group</button>
    </form>
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
