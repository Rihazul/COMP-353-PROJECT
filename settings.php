<?php
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
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
        }
        form input[type="submit"] {
            padding: 10px;
            background: #9e34eb;
            color: #fff;
            border: 0;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
        form input[type="submit"]:hover {
            background: #7a29b8;
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
        <button class="logout-button" onclick="window.location.href='logout.php'">Log out</button>
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
                <form action="update_settings.php" method="post">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="current_username" required>
                    
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="current_email@example.com" required>
                    
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    
                    <input type="submit" value="Update Settings">
                </form>
            </section>
            <section>
                <h2>Notification Settings</h2>
                <form action="update_notifications.php" method="post">
                    <label for="email_notifications">Email Notifications:</label>
                    <input type="checkbox" id="email_notifications" name="email_notifications" checked>
                    
                    <label for="sms_notifications">SMS Notifications:</label>
                    <input type="checkbox" id="sms_notifications" name="sms_notifications">
                    
                    <button type="submit">Save Changes</button>
                </form>
            </section>
        </main>
        <footer>
            <p>&copy; 2023 COSN. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>