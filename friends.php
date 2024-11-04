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
        <form action="search_friends.php" method="post">
            <input type="text" name="search" placeholder="Search for friends...">
            <input type="submit" value="Search">
        </form>
        <h2>Friends List</h2>
        <ul id="friends-list">
            <?php
            $friends = ["Alice", "Bob", "Charlie"]; // Example friends list
            foreach ($friends as $friend) {
                echo "<li>$friend</li>";
            }
            ?>
        </ul>
        <form action="friends.php" method="POST">
            <input type="text" name="new_friend" placeholder="Add a new friend">
            <input type="submit" value="Add Friend">
        </form>
    </div>
</body>
</html>