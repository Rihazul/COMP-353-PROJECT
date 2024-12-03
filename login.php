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

// Redirect already logged-in users
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_privilege'] === 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: profile.php");
    }
    exit();
}

$login_message = "";

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        // Query to verify user credentials
        $query = "SELECT MemberID, Pseudonym, Password, Privilege FROM Member WHERE Email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password (plaintext comparison, consider hashing for better security)
            if ($password === $user['Password']) { 
                // Set session variables
                $_SESSION['user_id'] = $user['MemberID'];
                $_SESSION['user_name'] = $user['Pseudonym'];
                $_SESSION['user_privilege'] = $user['Privilege'];

                // Output JavaScript to store user ID in local storage and redirect
                $redirect_url = $user['Privilege'] === 'Administrator' ? "admin.php" : "profile.php";
                echo "
                <script>
                    localStorage.setItem('user_id', " . json_encode($user['MemberID']) . ");
                    localStorage.setItem('user_name', " . json_encode($user['Pseudonym']) . ");
                    setTimeout(() => {
                        window.location.href = '" . $redirect_url . "';
                    }, 100);
                </script>";
                exit();
            } else {
                $login_message = "Incorrect email or password.";
            }
        } else {
            $login_message = "Incorrect email or password.";
        }
    } else {
        $login_message = "Please fill in all fields.";
    }
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>COSN | Log In</title>
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
            height: 400px;
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
            margin-top: 20px;
            padding: 5px;
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
        <div><a href="signup.php" id="signup">Sign Up</a></div>
    </div>

    <div id="bar2">
        <div style="font-size: 30px; color: #9e34eb; font-weight: bold; margin-top: 20px;">Log in to COSN</div>
        <br><br>
        <form method="post" action="">
            <input type="text" id="typed_email" placeholder="Enter your email" name="email" required> <br><br>
            <input type="password" id="typed_email" placeholder="Enter your password" name="password" required> <br><br>
            <input type="submit" id="submit_button" value="Log in"> <br><br>
        </form>

        <a href="#" style="text-decoration: none; color: #9e34eb; font-size: small;">Forgot your password?</a>

        <?php if (!empty($login_message)): ?>
            <div class="message"><?php echo htmlspecialchars($login_message); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
