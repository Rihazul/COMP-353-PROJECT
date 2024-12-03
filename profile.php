<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

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

// Get user data
$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM Member WHERE MemberID = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

// If no user found, log them out
if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Get user's posts
$posts_query = "SELECT * FROM Posts WHERE MemberID = ? ORDER BY DatePosted DESC";
$post_stmt = $conn->prepare($posts_query);
$post_stmt->bind_param("i", $user_id);
$post_stmt->execute();
$posts_result = $post_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN Profile</title>
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
        .profile-content {
            width: 900px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #000;
            background-color: white;
            text-align: center;
            position: relative;
        }
        .cover-box {
            position: relative;
            width: 100%;
            height: 400px;
            border-radius: 10px 10px 0 0;
            overflow: hidden;
            border: 1px solid black;
        }
        .cover-box img.cover-pic {
            width: 100%;
            height: 100%;
            object-fit: cover;

        }
        .profile-pic-container {
            position: absolute;
            top: 200px; /* Adjust to position the profile picture */
            left: 50%;
            width: 100px;
            height: 100px;
            transform: translateX(-50%);
            text-align: center;
            border-radius: 50%;
            border: 1px solid black;
        }
        .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }

        .profile-name {
            font-size: 24px;
            font-weight: bold;
            color: #333;

        }
        .profile-buttons {
            margin-top: 20px;
        }
        .profile-buttons button {
            background-color: #9e34eb;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .profile-buttons button:hover {
            background-color: #7a29b8;
        }
        .main-content {
            width: 900px;
            margin: 100px auto 20px auto; /* Adjust margin to account for profile pic */
            display: flex;
            justify-content: space-between;
        }
        .friends-list, .posts-list {
            width: 48%;
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0px 0px 5px 0px #ccc;
        }
        .friends-list {
            margin-right: 2%;
            display: flex;
            flex-direction: column;
            gap: 10px; /* Add space between friend cards */
        }
        .friend-card {
            display: flex;
            align-items: center;
            padding: 10px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 0px 5px 0px #ccc;
        }
        .friend-card img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .friend-card div {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        .friends-list img, .posts-list img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 10px;
        }
        .post {
            background-color: white;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 10px;
            box-shadow: 0px 0px 5px 0px #ccc;
        }
        .post-form {
            margin-bottom: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0px 0px 5px 0px #ccc;
        }
        .post-form textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: none;
        }
        .post-form button {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #9e34eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .post-form button:hover {
            background-color: #7a29b8;
        }    </style>
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
<!-- Top purple bar -->
<div id="purple_bar">
    <div style="font-size: 45px; font-weight: bold;">
        COSN
    </div>
    <div class="search-wrapper">
        <input type="text" name="search" id="search_box" placeholder="Search for people">
        <div class="search-icon"></div>
    </div>
    <button class="logout-button" onclick="logout()">Log out</button>
</div>

<!-- Profile content/ cover part -->
<div class="profile-content">
    <div class="cover-box">
        <img src="<?= $user['CoverPhoto'] ?? 'https://via.placeholder.com/900x400?text=Cover+Photo' ?>"
             class="cover-pic" alt="Cover Photo">
        <div class="profile-pic-container">
            <img src="<?= $user['ProfilePhoto'] ?? '/img/default-profile-picture.png' ?>"
                 class="profile-pic" alt="Profile Photo">
            <div class="profile-name"><?= htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']) ?></div>
        </div>
    </div>
    <div class="profile-buttons">
        <button onclick="window.location.href='timeline.php'">Timeline</button>
        <button onclick="window.location.href='friends.php'">Friends</button>
        <button onclick="window.location.href='post.php'">Posts</button>
        <button onclick="window.location.href='settings.php'">Settings</button>
    </div>
</div>

<!-- Profile content/ main part -->
<div class="main-content">
    <!-- Left side: friends -->
    <div class="friends-list">
        <div style="font-size: 20px; color: #9e34eb; font-weight: bold;">Friends</div>
        <!-- You can dynamically load friends here -->
    </div>

    <!-- Right side: post form and posts -->
    <div class="posts-list">
        <div class="post-form">
            <form action="post.php" method="post">
                <textarea name="post_content" placeholder="What's on your mind?" required></textarea>
                <button type="submit">Post</button>
            </form>
        </div>
        <div style="font-size: 20px; color: #9e34eb; font-weight: bold;">Posts</div>
        <?php while ($post = $posts_result->fetch_assoc()): ?>
            <div class="post">
                <p><?= htmlspecialchars($post['TextContent']) ?></p>
                <small>Posted on <?= $post['DatePosted'] ?></small>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>
