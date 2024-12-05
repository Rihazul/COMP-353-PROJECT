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

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user role from the `Members` table
$user_id = $_SESSION['user_id'];
$user_role_query = "SELECT Privilege FROM Member WHERE MemberID = ?";
$stmt = $conn->prepare($user_role_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_role = $row['Privilege'];
} else {
    header("Location: login.php");
    exit();
}

// Initialize message variable
$message = "";

// Check user role and allow access only for Admin or Senior
if ($user_role !== 'Admin' && $user_role !== 'Senior') {
    $message = "You do not have permission to create a group. Only senior-level members or administrators can create groups.";
} else {
    // Handle form submission for creating a new group
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $group_name = trim($_POST['group_name']);
        $group_description = trim($_POST['group_description']);

        if (!empty($group_name)) {
            // Start transaction to ensure both INSERT operations succeed
            $conn->begin_transaction();
            try {
                // Insert new group into the Groups table
                $insert_group_query = "INSERT INTO `Groups` (GroupName, Description, CreationDate) VALUES (?, ?, NOW())";
                $stmt = $conn->prepare($insert_group_query);
                $stmt->bind_param("ss", $group_name, $group_description);

                if ($stmt->execute()) {
                    // Get the newly created GroupID
                    $group_id = $conn->insert_id;

                    // Insert the creator as the Owner into the GroupMembers table
                    $insert_member_query = "INSERT INTO `GroupMembers` (GroupID, MemberID, Role, DateJoined) VALUES (?, ?, 'Owner', NOW())";
                    $stmt = $conn->prepare($insert_member_query);
                    $stmt->bind_param("ii", $group_id, $user_id);

                    if ($stmt->execute()) {
                        // Commit transaction if both inserts succeed
                        $conn->commit();
                        $message = "Group successfully created, and you are now the owner!";
                    } else {
                        // Rollback if the second insert fails
                        $conn->rollback();
                        $message = "Error adding owner to group: " . $stmt->error;
                    }
                } else {
                    // Rollback if the first insert fails
                    $conn->rollback();
                    $message = "Error creating group: " . $stmt->error;
                }
            } catch (Exception $e) {
                $conn->rollback();
                $message = "Transaction failed: " . $e->getMessage();
            }
        } else {
            $message = "Group name is required.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Group</title>
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
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
            margin-top: 20px;
        }

        form label {
            font-size: 1.1em;
            color: #333;
            margin-bottom: 10px;
        }

        form input, form textarea {
            font-size: 1em;
            padding: 10px;
            margin-bottom: 20px;
            width: 80%;
            border-radius: 5px;
            border: 1px solid #ddd;
            transition: border 0.3s;
        }

        form input[type="submit"] {
            background-color: #9e34eb;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        form input[type="submit"]:hover {
            background-color: #7a29b8;
        }

        .message {
            font-size: 1.2em;
            margin-top: 20px;
            color: #d8000c;
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- Top purple bar -->
<div id="purple_bar">
    <div class="title">COSN</div>
    <button class="logout-button" onclick="window.location.href='login.php'">Log out</button>
</div>

<!-- Admin Container -->
<div class="admin-container">
    <h1>Create New Group</h1>

    <?php if ($user_role !== 'Admin' && $user_role !== 'Senior') { ?>
        <p class="message"><?php echo $message; ?></p>
    <?php } else { ?>
        <!-- Form for creating a new group -->
        <form method="post" action="">
            <label for="group_name">Group Name: </label>
            <input type="text" name="group_name" id="group_name" required>

            <label for="group_description">Group Description: </label>
            <textarea name="group_description" id="group_description" required></textarea>

            <input type="submit" value="Create Group">
        </form>
    <?php } ?>

    <?php if (isset($message)) { echo "<p class='message'>$message</p>"; } ?>
</div>

</body>
</html>

<?php
$conn->close();
?>