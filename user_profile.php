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

// Fetch friend's MemberID from the friend table
$main_user_id = $_SESSION['user_id'];
$friend_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$friend_query = "SELECT m.FirstName, m.LastName FROM Friends f JOIN Member m ON f.MemberID2 = m.MemberID WHERE f.MemberID1 = ? AND f.MemberID2 = ? AND f.Status = 'Accepted'";
$stmt = $conn->prepare($friend_query);
$stmt->bind_param("ii", $main_user_id, $friend_id);
$stmt->execute();
$result = $stmt->get_result();
$friend = $result->fetch_assoc();

if (!$friend) {
    echo "<h2 style='color: red;'>Friend not found or you are not connected.</h2>";
    exit();
}

// Fetch friend's posts from the database
$posts_query = "SELECT TextContent, AttachmentContent, DatePosted FROM Posts WHERE MemberID = ? ORDER BY DatePosted DESC";
$posts_stmt = $conn->prepare($posts_query);
$posts_stmt->bind_param("i", $friend_id);
$posts_stmt->execute();
$posts_result = $posts_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($friend['FirstName'] . ' ' . $friend['LastName']) ?>'s Profile</title>
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
        }
        .cover-box img.cover-pic {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-pic-container {
            position: absolute;
            top: 50px; /* Adjust to position the profile picture */
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
        }
        .profile-pic-container img.profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            box-shadow: 0 0 0 5px #9e34eb; /* Add purple contour */
        }
        .profile-name {
            font-size: 24px;
            font-weight: bold;
            color: white;
            background-color: #9e34eb; /* Purple background */
            padding: 5px 10px;
            border-radius: 10px;
            display: inline-block;
            margin-top: 10px;
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
        .posts-list {
            width: 100%;
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0px 0px 5px 0px #ccc;
        }
        .post {
            background-color: white;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 10px;
            box-shadow: 0px 0px 5px 0px #ccc;
        }
        .post-header {
            font-weight: bold;
            color: #9e34eb;
        }
        .post-content {
            margin-top: 10px;
        }
        .post-time {
            margin-top: 10px;
            font-size: 0.9em;
            color: #777;
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
        <img src="<?= htmlspecialchars($friend['CoverPhoto'] ?? 'https://via.placeholder.com/900x400?text=Cover+Photo') ?>"
             class="cover-pic" alt="Cover Photo">
        <div class="profile-pic-container">
            <img src="<?= htmlspecialchars($friend['ProfilePhoto'] ?? '/img/default-profile-picture.png') ?>"
                 class="profile-pic" alt="Profile Photo">
            <div class="profile-name"><?= htmlspecialchars($friend['FirstName'] . ' ' . $friend['LastName']) ?></div>
        </div>
    </div>
    <div class="profile-buttons">
        <button onclick="window.location.href='message.php?user_id=<?= $friend_id ?>'">Message</button>
        <button onclick="window.location.href='block_report.php?user_id=<?= $friend_id ?>'">Block/Report</button>
        <button onclick="window.location.href='timeline.php'">Back to Timeline</button>
    </div>
</div>

<!-- Profile content/ main part -->
<div class="main-content">
    <!-- Posts -->
    <div class="posts-list">
        <div style="font-size: 20px; color: #9e34eb; font-weight: bold;">Posts</div>
        <?php while ($post = $posts_result->fetch_assoc()): ?>
            <div class="post">
                <div class="post-header"><?= htmlspecialchars($friend['FirstName'] . ' ' . $friend['LastName']) ?></div>
                <div class="post-content"><?= htmlspecialchars($post['TextContent']) ?></div>
                <div class="post-time"><?= htmlspecialchars($post['DatePosted']) ?></div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>
