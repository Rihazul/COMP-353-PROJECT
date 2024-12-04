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

// Handle signup submission
$signup_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $gender = trim($_POST['gender']);
    $date_of_birth = trim($_POST['date_of_birth']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $retyped_password = trim($_POST['retyped_password']);
    $privilege = trim($_POST['privilege']);

    // Set privilege to "junior" if the user selects "regular"
    if ($privilege == 'regular') {
        $privilege = 'junior';
    }

    // Validate form inputs
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($retyped_password)) {
        $signup_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $signup_message = "Invalid email format.";
    } elseif ($password !== $retyped_password) {
        $signup_message = "Passwords do not match.";
    } else {
        // Check if the email is already registered
        $check_email_query = "SELECT MemberID FROM Member WHERE Email = ?";
        $stmt = $conn->prepare($check_email_query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $signup_message = "This email is already registered.";
        } else {

            // Insert new user into the database
            $insert_query = "INSERT INTO Member (FirstName, LastName, Gender, DOB, Email, Password, Privilege) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            if (!$stmt) {
                die("Database error: " . $conn->error);
            }
            $stmt->bind_param("sssssss", $first_name, $last_name, $gender, $date_of_birth, $email, $password,$privilege);

            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['user_name'] = $first_name;

                // Make the new user a friend with the admin (MemberID1)
                $admin_id = 1;
                $friend_query = "INSERT INTO Friends (MemberID1, MemberID2) VALUES (?, ?)";
                $stmt = $conn->prepare($friend_query);
                if (!$stmt) {
                    die("Database error: " . $conn->error);
                }
                $stmt->bind_param("ii", $admin_id, $new_user_id);
                $stmt->execute();

                // Redirect to profile page after successful signup
                header("Location: profile.php");
                exit();
            } else {
                $signup_message = "Error registering user. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>COSN | Sign Up</title>
    <style>
        #bar {
            height: 100px;
            background-color: #9e34eb;
            color: #3f0b57;
            padding: 4px;
        }
        #signup {
            background-color: #eb3480;
            color: white;
            font-size: small;
            padding: 5px;
            border-radius: 5px;
            width: 50px;
            margin-top: 8px;
            text-align: center;
            float: right;
        }
        #bar2 {
            background-color: white;
            width: 800px;
            height: auto;
            margin: auto;
            margin-top: 50px;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #000;
            text-align: center;
        }
        #typed_email {
            width: 200px;
            height: 30px;
            border-radius: 5px;
            border: none;
            margin-top: 5px;
            padding: 5px;
            border: 1px solid black;
        }
        #submit_button {
            width: 200px;
            height: 30px;
            border-radius: 5px;
            border: none;
            padding: 5px;
            background-color: #9e34eb;
            color: white;
            font-weight: bold;
        }
        .message {
            color: red;
            font-size: small;
            font-weight: bold;
        }
    </style>
</head>
<body style="font-family: tahoma; background-color:#fac3da">
    <div id="bar">
        <div style="font-size: 40px; font-weight: bold;">COSN</div>
        <div><a href="login.php" id="signup">Log In</a></div>
    </div>

    <div id="bar2">
        <div style="font-size: 30px; color: #9e34eb; font-weight: bold; margin-top: 20px;">Sign Up to COSN</div>
        <br><br>
        <form method="post" action="">
            <input type="text" id="typed_email" placeholder="Enter your first name" name="first_name" required> <br><br>
            <input type="text" id="typed_email" placeholder="Enter your last name" name="last_name" required> <br><br>
            Gender:
            <select name="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select> <br><br>
            <input type="date" id="typed_email" name="date_of_birth" required> <br><br>
            <input type="text" id="typed_email" placeholder="Enter your email" name="email" required> <br><br>
            <input type="password" id="typed_email" placeholder="Enter your password" name="password" required> <br><br>
            <input type="password" id="typed_email" placeholder="Retype your password" name="retyped_password" required> <br><br>
            <label for="privilege">User Type:</label>
            <select name="privilege" id="privilege">
                <option value="regular">Regular</option>
                <option value="businessman">Businessman</option>
            </select> <br><br>
            <input type="submit" id="submit_button" value="Sign Up"> <br><br>
        </form>

        <?php if (!empty($signup_message)): ?>
            <div class="message"><?php echo htmlspecialchars($signup_message); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
