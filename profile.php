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
        .profile-name {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-top: 10px;
        }
        .profile-buttons {
            margin-top: 100px; /* Adjust margin to create space for the profile picture */
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
        .profile-content {
            width: 900px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #000;
            background-color: white;
            text-align: center;
            position: relative; /* Ensures the profile picture is positioned correctly */
        }

        .profile-content img.cover-pic {
            width: 100%;
            height: 300px; /* Adjusted height for better alignment */
            border-radius: 10px 10px 0 0;
            object-fit: cover; /* Ensures the cover image scales properly */
        }

        .profile-content img.profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            position: absolute;
            top: 200px; /* Adjusted top position to center profile picture below cover */
            left: 50%;
            transform: translateX(-50%);
            z-index: 1; /* Ensures it sits on top of other elements */
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
    </style>
</head>
<body>
    
    <!-- top purple bar -->    
    <div id="purple_bar">
        <div style="font-size: 45px; font-weight: bold;">
            COSN
        </div>
        <div class="search-wrapper">
            <input type="text" name="search" id="search_box" placeholder="Search for people">
            <div class="search-icon"></div>
        </div>
        <button class="logout-button" onclick="window.location.href='login.php'">Log out</button>
    </div>

    <!-- profile content/ cover part -->
    <div class="profile-content">
        <img src="cover.jpg" class="cover-pic" alt="Cover Photo">
        <img src="profile.jpg" class="profile-pic" alt="Profile Photo">
        <div class="profile-name">Jone Doe</div>
        <div class="profile-buttons">
            <button onclick="window.location.href='timeline.php'">Timeline</button>
            <button onclick="window.location.href='about.php'">About</button>
            <button onclick="window.location.href='friends.php'">Friends</button>
            <button onclick="window.location.href='photos.php'">Photos</button>
            <button onclick="window.location.href='settings.php'">Settings</button>
        </div>
    </div>

    <!-- profile content/ main part -->
    <div class="main-content">
        <!-- left side : friends -->
        <div class="friends-list">
            <div style="font-size: 20px; color: #9e34eb; font-weight: bold;">Friends</div>
            <div class="friend-card">
                <img src="friend1.jpg" alt="Friend 1">
                <div>Friend 1</div>
            </div>
            <div class="friend-card">
                <img src="friend2.jpg" alt="Friend 2">
                <div>Friend 2</div>
            </div>
            <div class="friend-card">
                <img src="friend3.jpg" alt="Friend 3">
                <div>Friend 3</div>
            </div>
        </div>
        
        <!-- right side : posts -->
        <div class="posts-list">
            <div style="font-size: 20px; color: #9e34eb; font-weight: bold;">Posts</div>
            <div class="post">
                <p>Post content 1</p>
            </div>
            <div class="post">
                <p>Post content 2</p>
            </div>
            <div class="post">
                <p>Post content 3</p>
            </div>
        </div>
    </div>
</body>
</html>