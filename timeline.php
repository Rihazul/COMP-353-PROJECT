<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN Timeline</title>
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
        .navigation-buttons {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .navigation-buttons button {
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
        .navigation-buttons button:hover {
            background-color: #7a29b8;
        }
        .timeline-content {
            width: 900px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #000;
            background-color: white;
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
        }
        .posts-list {
            margin-top: 20px;
        }
        .post {
            background-color: white;
            padding: 15px;
            margin-bottom: 15px;
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
    
    <!-- top purple bar -->    
    <div id="purple_bar">
        <div style="font-size: 45px; font-weight: bold;">
            COSN
        </div>
        <div class="search-wrapper">
            <input type="text" name="search" id="search_box" placeholder="Search for people">
        </div>
        <button class="logout-button" onclick="window.location.href='login.php'">Log out</button>
    </div>

    <!-- Navigation Buttons -->
    <div class="navigation-buttons">
        <button onclick="window.location.href='profile.php'">Profile</button>
        <button onclick="window.location.href='notification.php'">Notifications</button>
        <button onclick="window.location.href='message.php'">Messages</button>
        <button onclick="window.location.href='manage_groups.php'">Groups</button>
        <button onclick="window.location.href='event.php'">Events</button>
    </div>

    <!-- timeline content -->
    <div class="timeline-content">
        <div class="post-form">
            <form action="create_post.php" method="post">
                <textarea name="post_content" rows="4" placeholder="What's on your mind?"></textarea>
                <button type="submit">Post</button>
            </form>
        </div>
        <div class="posts-list">
            <!-- Sample Post 1 -->
            <div class="post">
                <div class="post-header">John Doe</div>
                <div class="post-content">This is my first post! Welcome to COSN!</div>
                <div class="post-time">2 hours ago</div>
            </div>
            <!-- Sample Post 2 -->
            <div class="post">
                <div class="post-header">Jane Smith</div>
                <div class="post-content">Excited for the weekend event. Who’s joining?</div>
                <div class="post-time">5 hours ago</div>
            </div>
            <!-- Sample Post 3 -->
            <div class="post">
                <div class="post-header">Alice Johnson</div>
                <div class="post-content">Check out these photos from yesterday’s meetup!</div>
                <div class="post-time">1 day ago</div>
            </div>
        </div>
    </div>
</body>
</html>
