<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN User Profile</title>
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
            top: 200px;
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
            margin-top: 15px;
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
            margin: 100px auto 20px auto;
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
            color: #333;
        }
        .post-time {
            margin-top: 5px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    
    <!-- Top bar -->
    <div id="purple_bar">
        <div style="font-size: 45px; font-weight: bold;">
            COSN
        </div>
        <div class="search-wrapper">
            <input type="text" name="search" id="search_box" placeholder="Search for people">
        </div>
        <button class="logout-button" onclick="window.location.href='login.php'">Log out</button>
    </div>

    <!-- Profile content -->
    <div class="profile-content">
        <div class="cover-box">
            <img src="https://via.placeholder.com/900x400?text=Cover+Photo" class="cover-pic" alt="Cover Photo">
            <div class="profile-pic-container">
                <img src="/img/default-profile-picture.png" class="profile-pic" alt="Profile Photo">
                <div class="profile-name">John Doe</div>
            </div>
        </div>
        <div class="profile-buttons">
            <button onclick="window.location.href='message.php?user_id=123'">Message</button>
            <button onclick="window.location.href='block_report.php?user_id=123'">Block/Report</button>
            <button onclick="window.location.href='timeline.php'">Back to Timeline</button>
        </div>
    </div>

    <!-- Posts -->
    <div class="main-content">
        <div class="posts-list">
            <div style="font-size: 20px; color: #9e34eb; font-weight: bold;">Posts</div>
            <div class="post">
                <div class="post-header">John Doe</div>
                <div class="post-content">This is a post from John Doe.</div>
                <div class="post-time">2 hours ago</div>
            </div>
            <div class="post">
                <div class="post-header">John Doe</div>
                <div class="post-content">Another update from John Doe!</div>
                <div class="post-time">5 hours ago</div>
            </div>
        </div>
    </div>
</body>
</html>
