<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN Friend Requests</title>
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
        .requests-list {
            max-height: 500px;
            overflow-y: auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #ccc;
            margin-top: 20px;
        }
        .request-card {
            display: flex;
            align-items: center;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0px 0px 5px 0px #ccc;
            justify-content: space-between;
        }
        .request-card img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .request-info {
            display: flex;
            align-items: center;
        }
        .request-actions {
            display: flex;
            gap: 10px;
        }
        .request-actions button {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .approve-button {
            background-color: #9e34eb;
            color: white;
        }
        .reject-button {
            background-color: #ff4d4d;
            color: white;
        }
        .approve-button:hover {
            background-color: #7a29b8;
        }
        .reject-button:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>COSN Friend Requests</h1>
            <div>
                <a href="profile.php">Profile</a>
                <a href="friends.php">Friends</a>
                <a href="requests.php">Friend Requests</a>
                <a href="login.php">Logout</a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <h2>Friend Requests</h2>
        <div class="requests-list">
            <?php
            // Simulated friend requests array (replace with actual database query)
            $friend_requests = [
                ["id" => 1, "name" => "Alice", "profile_pic" => "alice.jpg"],
                ["id" => 2, "name" => "Bob", "profile_pic" => "bob.jpg"],
                ["id" => 3, "name" => "Charlie", "profile_pic" => "charlie.jpg"]
            ];

            foreach ($friend_requests as $request) {
                echo "<div class='request-card'>";
                echo "<div class='request-info'>";
                echo "<img src='" . $request['profile_pic'] . "' alt='" . $request['name'] . "'>";
                echo "<div><strong>" . $request['name'] . "</strong></div>";
                echo "</div>";
                echo "<div class='request-actions'>";
                echo "<form action='approve_request.php' method='post' style='display:inline;'>";
                echo "<input type='hidden' name='request_id' value='" . $request['id'] . "'>";
                echo "<button type='submit' class='approve-button'>Approve</button>";
                echo "</form>";
                echo "<form action='reject_request.php' method='post' style='display:inline;'>";
                echo "<input type='hidden' name='request_id' value='" . $request['id'] . "'>";
                echo "<button type='submit' class='reject-button'>Reject</button>";
                echo "</form>";
                echo "</div>";
                echo "</div>";
            }
            ?>
        </div>
    </div>
</body>
</html>
