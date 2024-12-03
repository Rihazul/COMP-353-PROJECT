<?php
session_start();

// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database credentials
$servername = "upc353.encs.concordia.ca";
$db_username = "upc353_2";
$db_password = "SleighParableSystem73";
$dbname = "upc353_2";

// Connect to the database using mysqli with error handling
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check database connection
if ($conn->connect_error) {
    die("<div class='message error'>Connection failed: " . htmlspecialchars($conn->connect_error) . "</div>");
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or show an error message
    header("Location: login.php");
    exit();
}

// Get user_id from session and ensure it's an integer
$user_id = (int)$_SESSION['user_id'];

// Initialize message variable
$message = "";
echo "User ID: $user_id<br>";
// Handle friend addition
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['new_friend'])) {
    $new_friend = trim($_POST['new_friend']);

    if (!empty($new_friend)) {
        // Split the full name into first and last name
        $names = explode(' ', $new_friend, 2);

        if (count($names) == 2) { // Ensure both first and last name are provided
            $first_name = $names[0];
            $last_name = $names[1];

            // Prepare statement to check if the friend exists by first and last name
            $friend_query = "SELECT MemberID FROM Member WHERE FirstName = ? AND LastName = ?";
            if ($stmt = $conn->prepare($friend_query)) {
                $stmt->bind_param("ss", $first_name, $last_name);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $stmt->bind_result($friend_id);
                    $stmt->fetch();
                    $stmt->close();

                    // Check if a friendship already exists
                    $check_friend_query = "
                        SELECT * 
                        FROM Friends 
                        WHERE (MemberID1 = ? AND MemberID2 = ?) 
                           OR (MemberID1 = ? AND MemberID2 = ?)";
                    if ($stmt = $conn->prepare($check_friend_query)) {
                        $stmt->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);
                        $stmt->execute();
                        $stmt->store_result();

                        if ($stmt->num_rows === 0) {
                            $stmt->close();

                            // Add the friend relationship with status 'Pending'
                            $add_friend_query = "
                                INSERT INTO Friends (MemberID1, MemberID2, DateStarted, Status) 
                                VALUES (?, ?, NOW(), 'Pending')";
                            if ($stmt = $conn->prepare($add_friend_query)) {
                                $stmt->bind_param("ii", $user_id, $friend_id);
                                if ($stmt->execute()) {
                                    $message = "<div class='message success'>Friend request sent to " . htmlspecialchars($new_friend) . "!</div>";
                                } else {
                                    $message = "<div class='message error'>Error adding friend: " . htmlspecialchars($stmt->error) . "</div>";
                                }
                                $stmt->close();
                            } else {
                                $message = "<div class='message error'>Error preparing friend addition statement.</div>";
                            }
                        } else {
                            $message = "<div class='message error'>You are already friends or a request is pending with " . htmlspecialchars($new_friend) . ".</div>";
                            $stmt->close();
                        }
                    } else {
                        $message = "<div class='message error'>Error preparing friendship check statement.</div>";
                    }
                } else {
                    $message = "<div class='message error'>User " . htmlspecialchars($new_friend) . " not found.</div>";
                    $stmt->close();
                }
            } else {
                $message = "<div class='message error'>Error preparing friend lookup statement.</div>";
            }
        } else {
            $message = "<div class='message error'>Please provide both first and last names.</div>";
        }
    } else {
        $message = "<div class='message error'>Friend name cannot be empty.</div>";
    }
}

// Fetch friends list
$friends_list = [];
$friends_query = "
    SELECT M.MemberID, CONCAT(M.FirstName, ' ', M.LastName) AS FullName, M.Pseudonym
    FROM Friends F
    INNER JOIN Member M 
        ON (F.MemberID1 = M.MemberID OR F.MemberID2 = M.MemberID)
    WHERE (F.MemberID1 = ? OR F.MemberID2 = ?)
      AND F.Status = 'Accepted'
      AND M.MemberID != ?";

// Prepare statement
if ($stmt = $conn->prepare($friends_query)) {
    $stmt->bind_param("iii", $user_id, $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $friends_list[] = $row;
    }
    $stmt->close();
} else {
    $message .= "<div class='message error'>Error preparing friends list statement.</div>";
}

$conn->close();
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
            padding: 20px 0;
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
            object-fit: cover;
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
        /* Responsive Design */
        @media (max-width: 600px) {
            .container {
                width: 95%;
            }
            form input[type="text"] {
                width: 100%;
                margin-bottom: 10px;
            }
            form input[type="submit"] {
                width: 100%;
            }
            .friend-card {
                flex-direction: column;
                align-items: flex-start;
            }
            .friend-card img {
                margin-bottom: 10px;
            }
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
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <?php if (!empty($message)) echo $message; ?>

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
                        <!-- Use a default image for all friends -->
                        <img src="default.jpg" alt="<?php echo htmlspecialchars($friend['FullName'], ENT_QUOTES, 'UTF-8'); ?>">
                        <a href="user_profile.php?id=<?php echo htmlspecialchars($friend['MemberID'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($friend['FullName'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Add Friend Form -->
        <form action="friends.php" method="POST" id="add-friend-form">
            <input type="text" name="new_friend" placeholder="Add a new friend (First Last)">
            <input type="submit" value="Add Friend">
        </form>
    </div>
</body>
</html>
