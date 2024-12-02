<?php
// Start session (if using authentication)
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

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the user_id from GET or POST requests
// Check if the user_id is available in PHP
if (!isset($_COOKIE['user_id'])) {
    // Inject JavaScript to retrieve user_id from localStorage and reload the page
    echo "<script>
        const userId = localStorage.getItem('user_id');
        if (userId) {
            document.cookie = 'user_id=' + userId + '; path=/';
            location.reload();
        } else {
            alert('User ID not found in local storage. Redirecting to login.');
            window.location.href = 'login.php';
        }
    </script>";
    exit();
}

// Retrieve the user_id from the cookie
$user_id = intval($_COOKIE['user_id']);
if ($user_id <= 0) {
    die("Invalid user ID. Please log in again.");
}


// Fetch pending friend requests
$friend_requests = [];
$requests_query = "
    SELECT F.MemberID1 AS requester_id, M.Pseudonym AS requester_name
    FROM Friends F
    INNER JOIN Member M ON F.MemberID1 = M.MemberID
    WHERE F.MemberID2 = $user_id AND F.Status = 'Pending'";
$result = $conn->query($requests_query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $friend_requests[] = $row;
    }
}

// Process friend request actions (approve/reject)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['request_id'])) {
    $request_id = intval($_POST['request_id']);
    $action = $_POST['action']; // "approve" or "reject"

    if ($action === 'approve') {
        // Begin transaction to ensure both operations succeed
        $conn->begin_transaction();

        try {
            // Add the two users as friends
            $add_friend_query = "
                INSERT INTO Friends (MemberID1, MemberID2, DateStarted, Status)
                VALUES ($user_id, $request_id, NOW(), 'Accepted')";
            $conn->query($add_friend_query);

            // Remove the pending request
            $remove_request_query = "
                DELETE FROM Friends 
                WHERE MemberID1 = $request_id AND MemberID2 = $user_id AND Status = 'Pending'";
            $conn->query($remove_request_query);

            // Commit the transaction
            $conn->commit();

            // Show success message
            $message = "Friend request approved!";
        } catch (Exception $e) {
            // Rollback on error
            $conn->rollback();
            $message = "Error approving request: " . $e->getMessage();
        }
    } elseif ($action === 'reject') {
        // Reject the friend request
        $reject_query = "
            DELETE FROM Friends 
            WHERE MemberID1 = $request_id AND MemberID2 = $user_id AND Status = 'Pending'";
        if ($conn->query($reject_query)) {
            $message = "Friend request rejected.";
        } else {
            $message = "Error rejecting request: " . $conn->error;
        }
    }

    // Refresh the page to reflect changes
    echo "<meta http-equiv='refresh' content='0'>";
    exit();
}
?>
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
        .message {
            text-align: center;
            font-weight: bold;
            margin: 20px;
            color: green;
        }
        .error {
            color: red;
        }
    </style>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        console.log("JavaScript is loaded and running.");

        // Check for user_id in localStorage
        const userId = localStorage.getItem("user_id");
        if (!userId) {
            console.error("No user_id found in localStorage.");
            alert("User ID not found in local storage. Please log in again.");
            window.location.href = "login.php"; // Redirect to login page
        } else {
            console.log("Retrieved user_id from localStorage:", userId);

            // Append user_id to links
            const links = document.querySelectorAll("a");
            links.forEach(link => {
                const url = new URL(link.href);
                url.searchParams.set("user_id", userId);
                link.href = url.toString();
                console.log("Updated link with user_id:", link.href);
            });

            // Append user_id to forms
            const forms = document.querySelectorAll("form");
            forms.forEach(form => {
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = "user_id";
                input.value = userId;
                form.appendChild(input);
                console.log("Form updated with user_id:", form);
            });
        }
    });
</script>


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
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <div class="requests-list">
            <?php if (empty($friend_requests)): ?>
                <p>No friend requests found.</p>
            <?php else: ?>
                <?php foreach ($friend_requests as $request): ?>
                    <div class="request-card">
                        <div class="request-info">
                            <div><strong><?php echo htmlspecialchars($request['requester_name']); ?></strong></div>
                        </div>
                        <div class="request-actions">
                            <form action="" method="post" style="display:inline;">
                                <input type="hidden" name="request_id" value="<?php echo $request['requester_id']; ?>">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="approve-button">Approve</button>
                            </form>
                            <form action="" method="post" style="display:inline;">
                                <input type="hidden" name="request_id" value="<?php echo $request['requester_id']; ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="reject-button">Reject</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
