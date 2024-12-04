<?php
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }

       // Database connection
        $servername = "upc353.encs.concordia.ca";
        $username = "upc353_2";
        $password= "SleighParableSystem73";
        $dbname = "upc353_2";
        
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }


// Fetch current user settings from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT Email, FirstName, LastName, Family, Friends, Colleagues FROM Member WHERE MemberID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();


        
        // Handle form submission to update user settings
        $update_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_settings'])) {
    $email = trim($_POST['email']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $password = trim($_POST['password']);
    $family = trim($_POST['family']); // New field
    $friends = trim($_POST['friends']); // New field
    $colleagues = trim($_POST['colleagues']); // New field

    // Validate form inputs
    if (empty($email) || empty($first_name) || empty($last_name) || empty($password)) {
        $update_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $update_message = "Invalid email format.";
    } else {
        // Update user settings in the database, including new fields
        $update_query = "UPDATE Member 
                         SET Email = ?, FirstName = ?, LastName = ?, Password = ?, 
                             Family = ?, Friends = ?, Colleagues = ?
                         WHERE MemberID = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sssssssi", 
            $email, 
            $first_name, 
            $last_name, 
            $password, 
            $family, 
            $friends, 
            $colleagues, 
            $user_id
        );

        if ($stmt->execute()) {
            $update_message = "Settings updated successfully.";
        } else {
            $update_message = "Error updating settings. Please try again.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - COSN</title>
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
        .search-wrapper {
            position: relative;
            width: 400px;
            margin-left: 20px;
        }
        #search_box {
            width: 100%;
            border-radius: 5px;
            border: none;
            padding: 4px 4px 4px 30px;
            font-size: 17px;
            height: 25px;
        }
        .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            background: url('search_icon.png') no-repeat center center;
            background-size: contain;
        }
        .logout-button {
            background-color: #fff;
            color: #9e34eb;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-left: auto;
        }
        .logout-button:hover {
            background-color: #e0d4f7;
        }
        .container {
            width: 900px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #000;
            background-color: white;
        }
        header {
            text-align: center;
            padding: 20px;
            background-color: #9e34eb;
            color: white;
            border-radius: 10px 10px 0 0;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
            text-align: center;
            background-color: #9e34eb;
            border-radius: 0 0 10px 10px;
        }
        nav ul li {
            display: inline;
            margin: 0 10px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        nav ul li a.active {
            text-decoration: underline;
        }
        main {
            padding: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        form label {
            margin-top: 10px;
            font-weight: bold;
        }
        form input[type="text"],
        form input[type="email"],
        form input[type="password"] {
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: #f0f0f0; /* Background color for input fields */
        }
        form input[type="submit"],
        form button {
            padding: 10px;
            background: #9e34eb;
            color: #fff;
            border: 0;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
        form input[type="submit"]:hover,
        form button:hover {
            background: #7a29b8;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
            color: #333;
        }
    </style>
    <script>
        function logout() {
            localStorage.clear();
            fetch('logout.php', { method: 'POST' })
                .then(() => {
                    window.location.href = 'login.php';
                })
                .catch(error => console.error('Error logging out:', error));
        }
    </script>
</head>
<body>
    <!-- top purple bar -->    
    <div id="purple_bar">
        <div style="font-size: 45px; font-weight: bold;">
            COSN
        </div>
        <div class="search-wrapper">
            <input type="text" name="search" id="search_box" placeholder="Search for settings">
            <div class="search-icon"></div>
        </div>
        <button class="logout-button" onclick="logout()">Log out</button>
    </div>

    <div class="container">
        <header>
            <h1>COSN Settings</h1>
        </header>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="settings.php" class="active">Settings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
        <main>
            <section>
                <h2>Account Settings</h2>
<form action="settings.php" method="post">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
    
    <label for="first_name">First Name:</label>
    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['FirstName']); ?>" required>

    <label for="last_name">Last Name:</label>
    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['LastName']); ?>" required>
    
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>

    <!-- New fields -->
    <label for="family">Family:</label>
    <input type="text" id="family" name="family" 
           value="<?php echo htmlspecialchars($user['Family'] ?? ''); ?>" 
           placeholder="Enter family members">

    <label for="friends">Friends:</label>
    <input type="text" id="friends" name="friends" 
           value="<?php echo htmlspecialchars($user['Friends'] ?? ''); ?>" 
           placeholder="Enter friends">

    <label for="colleagues">Colleagues:</label>
    <input type="text" id="colleagues" name="colleagues" 
           value="<?php echo htmlspecialchars($user['Colleagues'] ?? ''); ?>" 
           placeholder="Enter colleagues">

    <input type="submit" name="update_settings" value="Update Settings">
</form>


                <?php if (!empty($update_message)): ?>
                    <div class="message"><?php echo htmlspecialchars($update_message); ?></div>
                <?php endif; ?>
            </section>
            <section>
                <h2>Notification Settings</h2>
                <form action="update_notifications.php" method="post">
                    <label for="email_notifications">Email Notifications:</label>
                    <input type="checkbox" id="email_notifications" name="email_notifications" checked>
                    
                    <label for="sms_notifications">SMS Notifications:</label>
                    <input type="checkbox" id="sms_notifications" name="sms_notifications">
                    
                    <button type="submit"        
                </form>
            </section>
        </main>
        <footer>
            <p>&copy; 2023 COSN. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>