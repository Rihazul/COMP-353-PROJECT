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

// Fetch user details for editing
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $select_query = "SELECT * FROM Member WHERE MemberID = ?";
    $stmt = $conn->prepare($select_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
} else {
    die("User ID not provided.");
}

// Handle form submission for editing user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $dob = $_POST['dob'];
    $pseudonym = trim($_POST['pseudonym']);
    $privilege = $_POST['privilege'];
    $status = $_POST['status'];
    $secret_santa = isset($_POST['secret_santa']) ? 1 : 0;

    $update_query = "UPDATE Member SET FirstName = ?, LastName = ?, Email = ?, DOB = ?, Pseudonym = ?, Privilege = ?, Status = ?, SecretSanta = ? WHERE MemberID = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssssssi", $first_name, $last_name, $email, $dob, $pseudonym, $privilege, $status, $secret_santa, $user_id);

    if ($stmt->execute()) {
        $message = "User successfully updated!";
    } else {
        $message = "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - COSN</title>
    <style>
        /* General body styling */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        /* Top purple bar */
        #purple_bar {
            height: 60px;
            background-color: #9e34eb;
            color: #fff;
            padding: 10px 20px;
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
            padding: 8px 16px;
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
            width: 800px;
            margin: 30px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: #9e34eb;
            font-size: 2.5em;
            margin-bottom: 20px;
            font-weight: bold;
        }

        /* Form Styling */
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f5f5f5;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
            margin-top: 20px;
        }

        form label {
            font-size: 1.2em;
            color: #333;
            margin-bottom: 10px;
        }

        form input, form textarea, form select {
            font-size: 1em;
            padding: 12px;
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
    <div class="title">Edit User - COSN</div>
    <button class="logout-button" onclick="window.location.href='login.php'">Log out</button>
</div>

<!-- Admin Container -->
<div class="admin-container">
    <h1>Edit User</h1>

    <!-- Form for editing user -->
    <form method="post" action="">
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" id="first_name" value="<?php echo $user['FirstName']; ?>" required>

        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" id="last_name" value="<?php echo $user['LastName']; ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo $user['Email']; ?>" required>

        <label for="dob">Date of Birth:</label>
        <input type="date" name="dob" id="dob" value="<?php echo $user['DOB']; ?>" required>

        <label for="pseudonym">Pseudonym:</label>
        <input type="text" name="pseudonym" id="pseudonym" value="<?php echo $user['Pseudonym']; ?>">

        <label for="privilege">Privilege:</label>
        <select name="privilege" id="privilege" required>
            <option value="Administrator" <?php echo ($user['Privilege'] == 'Administrator') ? 'selected' : ''; ?>>Administrator</option>
            <option value="Senior" <?php echo ($user['Privilege'] == 'Senior') ? 'selected' : ''; ?>>Senior</option>
            <option value="Junior" <?php echo ($user['Privilege'] == 'Junior') ? 'selected' : ''; ?>>Junior</option>
        </select>

        <label for="status">Status:</label>
        <select name="status" id="status" required>
            <option value="Active" <?php echo ($user['Status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
            <option value="Inactive" <?php echo ($user['Status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
            <option value="Suspended" <?php echo ($user['Status'] == 'Suspended') ? 'selected' : ''; ?>>Suspended</option>
        </select>

        <label for="secret_santa">Secret Santa:</label>
        <input type="checkbox" name="secret_santa" id="secret_santa" <?php echo ($user['SecretSanta'] == 1) ? 'checked' : ''; ?>>

        <input type="submit" value="Update User">
    </form>

    <?php if (isset($message)) { echo "<p class='message'>$message</p>"; } ?>
</div>

</body>
</html>

<?php
$conn->close();
?>
