<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN Friends</title>
    <style>
        body {
            font-family: Tahoma, sans-serif;
            background-color: #fac3da;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #9e34eb;
            color: white;
            padding: 20px;
            text-align: center;
        }
        header h1 {
            margin: 0;
        }
        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold;
        }
        header a:hover {
            text-decoration: underline;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        form {
            padding: 20px;
            margin-top: 20px;
            border: #ccc 1px solid;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #ccc;
        }
        form input[type="text"] {
            padding: 10px;
            width: 80%;
            margin-right: 10px;
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
        }
        form input[type="submit"]:hover {
            background: #7a29b8;
        }
        .friends-list {
            max-height: 400px; /* Set max height for scroll */
            overflow-y: auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #ccc;
            margin-top: 20px;
        }
        .friend-card {
            display: flex;
            align-items: center;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0px 0px 5px 0px #ccc;
            transition: background-color 0.3s;
        }
        .friend-card:hover {
            background-color: #e0d4f7;
        }
        .friend-card img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .friend-card a {
            text-decoration: none;
            color: #333;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>COSN</h1>
            <div>
                <a href="profile.php">Profile</a>
                <a href="friends.php">Friends</a>
                <a href="photos.php">Photos</a>
                <a href="login.php">Logout</a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <!-- Search Form -->
        <form action="search_friends.php" method="post">
            <input type="text" name="search" placeholder="Search for friends...">
            <input type="submit" value="Search">
        </form>
        
        <!-- Friends List Section -->
        <h2>Friends List</h2>
        <div class="friends-list" id="friends-list">
            <?php
            // Example friends data with profile picture URLs and IDs
            $friends = [
                ["name" => "Alice", "profile_pic" => "alice.jpg", "id" => 1],
                ["name" => "Bob", "profile_pic" => "bob.jpg", "id" => 2],
                ["name" => "Charlie", "profile_pic" => "charlie.jpg", "id" => 3]
            ];
            
            foreach ($friends as $friend) {
                echo "<div class='friend-card'>";
                echo "<img src='" . $friend['profile_pic'] . "' alt='" . $friend['name'] . "'>";
                echo "<a href='user_profile.php?id=" . $friend['id'] . "'>" . $friend['name'] . "</a>";
                echo "</div>";
            }
            ?>
        </div>
        
        <!-- Add Friend Form -->
        <form action="friends.php" method="POST">
            <input type="text" name="new_friend" placeholder="Add a new friend">
            <input type="submit" value="Add Friend">
        </form>
    </div>
</body>
</html>
