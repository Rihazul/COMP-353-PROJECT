<?php
session_start();

// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or show an error
    header("Location: login.php");
    exit();
}

// Get user_id from session and ensure it's an integer
$user_id = (int)$_SESSION['user_id'];

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

// Initialize message variable
$message = "";

// Fetch the number of approved posts made by the user
$fetch_posts_query = "
    SELECT COUNT(*) AS post_count 
    FROM Posts 
    WHERE MemberID = ? AND ModerationStatus = 'Approved'
";

// Prepare the statement to prevent SQL injection
if ($stmt = $conn->prepare($fetch_posts_query)) {
    // Bind the user_id parameter
    $stmt->bind_param("i", $user_id);
    
    // Execute the query
    if ($stmt->execute()) {
        // Bind the result to the variable
        $stmt->bind_result($post_count);
        $stmt->fetch();
        
        // Calculate the total amount
        $charge_per_post = 1; // $1 per approved post
        $total_amount = $post_count * $charge_per_post;
        
        // Free the result and close the statement
        $stmt->free_result();
        $stmt->close();
    } else {
        $message .= "<div class='message error'>Error executing query: " . htmlspecialchars($stmt->error) . "</div>";
    }
} else {
    $message .= "<div class='message error'>Database error: " . htmlspecialchars($conn->error) . "</div>";
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN Billing</title>
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
        .billing-content {
            width: 900px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #000;
            background-color: white;
        }
        .billing-info {
            margin-top: 20px;
            padding: 20px;
            background-color: #fefefe;
            border-radius: 10px;
            box-shadow: 0px 0px 5px 0px #ccc;
        }
        .billing-info h2 {
            color: #9e34eb;
            margin-top: 0;
        }
        .billing-details {
            font-size: 18px;
            color: #333;
        }
        .billing-details p {
            margin: 10px 0;
        }
        /* Responsive Design */
        @media (max-width: 950px) {
            .billing-content {
                width: 95%;
            }
            .search-wrapper {
                width: 200px;
            }
        }
        /* Message Styles */
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
    
    <!-- Top Purple Bar -->    
    <div id="purple_bar">
        <div style="font-size: 45px; font-weight: bold;">
            COSN
        </div>
        <div class="search-wrapper">
            <input type="text" name="search" id="search_box" placeholder="Search for people">
        </div>
        <button class="logout-button" onclick="window.location.href='logout.php'">Log out</button>
    </div>

    <!-- Navigation Buttons -->
    <div class="navigation-buttons">
        <button onclick="window.location.href='profile.php'">Profile</button>
        <button onclick="window.location.href='notification.php'">Notifications</button>
        <button onclick="window.location.href='message.php'">Messages</button>
        <button onclick="window.location.href='manage_groups.php'">Groups</button>
        <button onclick="window.location.href='event.php'">Events</button>
    </div>

    <!-- Billing Content -->
    <div class="billing-content">
        <!-- Display Messages -->
        <?php if (!empty($message)) echo $message; ?>

        <!-- Billing Information -->
        <div class="billing-info">
            <h2>Your Billing Information</h2>
            <div class="billing-details">
                <p><strong>Number of Approved Posts:</strong> <?php echo htmlspecialchars($post_count); ?></p>
                <p><strong>Charge per Approved Post:</strong> $<?php echo number_format($charge_per_post, 2); ?></p>
                <p><strong>Total Amount:</strong> $<?php echo number_format($total_amount, 2); ?></p>
            </div>
        </div>
    </div>
</body>
</html>
