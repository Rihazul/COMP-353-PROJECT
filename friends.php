<?php
// Start session (if needed for user authentication)
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database credentials
$servername = "upc353.encs.concordia.ca";
$username = "upc353_2";
$password = "SleighParableSystem73";
$dbname = "upc353_2";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set user ID (replace with dynamic session data in production)
$user_id = 1;

// Handle friend addition
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['new_friend'])) {
    $new_friend = trim($_POST['new_friend']);

    if (!empty($new_friend)) {
        // Check if the friend exists in the Member table
        $friend_query = "SELECT MemberID FROM Member WHERE Pseudonym = '$new_friend'";
        $friend_result = $conn->query($friend_query);

        if ($friend_result && $friend_result->num_rows > 0) {
            $friend_data = $friend_result->fetch_assoc();
            $friend_id = $friend_data['MemberID'];

            // Check if a friendship already exists
            $check_friend_query = "
                SELECT * 
                FROM Friends 
                WHERE (MemberID1 = $user_id AND MemberID2 = $friend_id) 
                   OR (MemberID1 = $friend_id AND MemberID2 = $user_id)";
            $check_friend_result = $conn->query($check_friend_query);

            if ($check_friend_result && $check_friend_result->num_rows === 0) {
                // Add the friend relationship with status 'Pending'
                $add_friend_query = "
                    INSERT INTO Friends (MemberID1, MemberID2, DateStarted, Status) 
                    VALUES ($user_id, $friend_id, NOW(), 'Pending')";
                if ($conn->query($add_friend_query)) {
                    $message = "<div class='message success'>Friend request sent to $new_friend!</div>";
                } else {
                    $message = "<div class='message error'>Error adding friend: " . $conn->error . "</div>";
                }
            } else {
                $message = "<div class='message error'>You are already friends or a request is pending with $new_friend.</div>";
            }
        } else {
            $message = "<div class='message error'>User $new_friend not found.</div>";
        }
    }
}

// Fetch friends list
$friends_list = [];
$friends_query = "
    SELECT M.MemberID, M.Pseudonym
    FROM Friends F
    INNER JOIN Member M 
        ON (F.MemberID1 = M.MemberID OR F.MemberID2 = M.MemberID)
    WHERE (F.MemberID1 = $user_id OR F.MemberID2 = $user_id) 
      AND F.Status = 'Accepted' 
      AND M.MemberID != $user_id";
$result = $conn->query($friends_query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $friends_list[] = $row;
    }
}
?>
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
            max-height: 400px;
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
        .message {
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
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
        <?php if (isset($message)) echo $message; ?>

        <!-- Search Form -->
        <form action="search_friends.php" method="post">
            <input type="text" name="search" placeholder="Search for friends...">
            <input type="submit" value="Search">
        </form>
        
        <!-- Friends List Section -->
        <h2>Friends List</h2>
        <div class="friends-list" id="friends-list">
            <?php if (empty($friends_list)): ?>
                <p>No friends found.</p>
            <?php else: ?>
                <?php foreach ($friends_list as $friend): ?>
                    <div class="friend-card">
                        <img src="<?php echo htmlspecialchars($friend['ProfilePic'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($friend['Pseudonym']); ?>">
                        <a href="user_profile.php?id=<?php echo $friend['MemberID']; ?>"><?php echo htmlspecialchars($friend['Pseudonym']); ?></a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Add Friend Form -->
        <form action="friends.php" method="POST">
            <input type="text" name="new_friend" placeholder="Add a new friend">
            <input type="submit" value="Add Friend">
        </form>
    </div>
</body>
</html>
