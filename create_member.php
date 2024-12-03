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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $address = trim($_POST['address']);
    $dob = $_POST['dob'];
    $pseudonym = trim($_POST['pseudonym']);
    $privilege = $_POST['privilege']; // Privilege (e.g., Administrator, Senior, Junior)
    $status = $_POST['status']; // Member Status (Active, Inactive, Suspended)
    $publicInfo = isset($_POST['publicInfo']) ? trim($_POST['publicInfo']) : ''; // Public Info Visibility (optional)
    $secretSanta = isset($_POST['secretSanta']) ? 1 : 0; // Secret Santa (checkbox, 1 if checked)

    // Validate required fields
    if (!empty($firstName) && !empty($lastName) && !empty($email) && !empty($password) && !empty($address) && !empty($dob) && !empty($pseudonym) && !empty($privilege) && !empty($status)) {
        // Prepare the SQL query to insert the new member
        $insert_query = "INSERT INTO `Member` (FirstName, LastName, Email, Password, Address, DOB, Pseudonym, Privilege, Status, PublicInfoVisibilitySettings, SecretSanta) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($insert_query);

        // Bind parameters (11 variables)
        $stmt->bind_param("ssssssssssi", $firstName, $lastName, $email, $password, $address, $dob, $pseudonym, $privilege, $status, $publicInfo, $secretSanta);

        if ($stmt->execute()) {
            $success_message = "New member created successfully!";
        } else {
            $error_message = "Error creating member: " . $stmt->error;
        }
    } else {
        $error_message = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Member - COSN</title>
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
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        #purple_bar .title {
            font-size: 24px;
            font-weight: bold;
        }

        .form-container {
            width: 600px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #000;
            text-align: center;
        }

        .form-container h1 {
            color: #9e34eb;
            margin-bottom: 20px;
        }

        .form-container input, .form-container select, .form-container textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-container button {
            background-color: #9e34eb;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #7a29b8;
        }

        .footer {
            text-align: center;
            color: #333;
            font-size: 0.9em;
            padding: 10px;
        }

        .message {
            font-size: 1.2em;
            margin: 10px;
        }

        .error-message {
            color: red;
        }

        .success-message {
            color: green;
        }
    </style>
</head>
<body>

<div id="purple_bar">
    <div class="title">Create New Member</div>
</div>

<div class="form-container">
    <h1>Create New Member</h1>

    <?php if (isset($error_message)): ?>
        <div class="message error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if (isset($success_message)): ?>
        <div class="message success-message"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <!-- Form for creating member -->
    <form method="POST">
        <input type="text" name="firstName" placeholder="First Name" required><br>
        <input type="text" name="lastName" placeholder="Last Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="text" name="address" placeholder="Address" required><br>
        <input type="date" name="dob" placeholder="Date of Birth" required><br>
        <input type="text" name="pseudonym" placeholder="Pseudonym" required><br>

        <!-- Privilege -->
        <select name="privilege" required>
            <option value="">Select Privilege</option>
            <option value="Administrator">Administrator</option>
            <option value="Senior">Senior</option>
            <option value="Junior">Junior</option>
        </select><br>

        <!-- Status -->
        <select name="status" required>
            <option value="">Select Status</option>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
            <option value="Suspended">Suspended</option>
        </select><br>

        <!-- Public Info Visibility (Optional) -->
        <textarea name="publicInfo" placeholder="Public Info Visibility Settings (optional)"></textarea><br>

        <!-- Secret Santa -->
        <label for="secretSanta">Secret Santa:</label>
        <input type="checkbox" name="secretSanta" value="1"><br>

        <button type="submit">Create Member</button>
    </form>
</div>

<div class="footer">
    © 2024 COSN
</div>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
