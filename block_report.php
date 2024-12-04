<?php
session_start();

// Check if user is logged in
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

$message = ""; // Feedback message

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentUserID = $_SESSION['user_id'];
    $targetUserID = intval($_POST['target_user']);
    $actionType = $_POST['action_type'];
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : null;

    if ($actionType === "block") {
        // Block the user
        $stmt = $conn->prepare("INSERT IGNORE INTO Block (BlockerMemberID, BlockedMemberID) VALUES (?, ?)");
        $stmt->bind_param("ii", $currentUserID, $targetUserID);

        if ($stmt->execute()) {
            $message = "User successfully blocked.";
        } else {
            $message = "Error: Could not block user.";
        }
        $stmt->close();
    } elseif ($actionType === "report") {
        // Report the user
        $stmt = $conn->prepare("INSERT INTO Report (ReporterMemberID, ReportedEntityID, EntityType, ReportType, Description) VALUES (?, ?, 'Member', 'User Issue', ?)");
        $stmt->bind_param("iis", $currentUserID, $targetUserID, $reason);

        if ($stmt->execute()) {
            $message = "Report submitted successfully.";
        } else {
            $message = "Error: Could not submit report.";
        }
        $stmt->close();
    } else {
        $message = "Invalid action type.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Block or Report User</title>
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
        .block-report-content {
            width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            color: #9e34eb;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        label {
            margin: 10px 0 5px;
            font-weight: bold;
        }
        input, select, textarea, .submit-button, .back-button {
            width: 80%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        button {
            background-color: #9e34eb;
            color: white;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #7a29b8;
        }
        .back-button {
            margin-top: 20px;
            background-color: #fff;
            color: #9e34eb;
            border: 2px solid #9e34eb;
            font-weight: bold;
            cursor: pointer;
        }
        .back-button:hover {
            background-color: #e0d4f7;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
            color: #333;
            font-weight: bold;
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

    <!-- Block/Report Content -->
    <div class="block-report-content">
        <h2>Block or Report User</h2>
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form action="" method="post">
            <label for="target_user">User ID:</label>
            <input type="number" id="target_user" name="target_user" placeholder="Enter User ID" required>
            
            <label for="action_type">Action:</label>
            <select id="action_type" name="action_type" required>
                <option value="block">Block User</option>
                <option value="report">Report User</option>
            </select>
            
            <label for="reason">Reason (for reporting only):</label>
            <textarea id="reason" name="reason" rows="4" placeholder="Enter reason (optional for blocking)"></textarea>
            
            <button class="submit-button" type="submit">Submit</button>
        </form>
        <button class="back-button" onclick="window.location.href='user_profile.php'">Back to Profile</button>
    </div>
</body>
</html>
