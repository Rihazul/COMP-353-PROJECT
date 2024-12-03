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

// Initialize posts array and message variable
$posts = [];
$message = "";

// Fetch all posts ordered by DatePosted descending
$fetch_posts_query = "
    SELECT P.PostID, P.MemberID, M.FirstName, M.LastName, P.TextContent, P.AttachmentContent, P.DatePosted, P.ModerationStatus 
    FROM Posts P
    INNER JOIN Member M ON P.MemberID = M.MemberID
    ORDER BY P.DatePosted DESC
";

if ($result = $conn->query($fetch_posts_query)) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    $result->free();
} else {
    $message .= "<div class='message error'>Error fetching posts: " . htmlspecialchars($conn->error) . "</div>";
}

// Close the database connection
$conn->close();
?>
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
        /* Removed Post Creation Form Styles */
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
        .post-attachment img,
        .post-attachment video {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
        }
        .post-attachment a {
            display: block;
            margin-top: 10px;
            color: #9e34eb;
            text-decoration: none;
        }
        .post-attachment a:hover {
            text-decoration: underline;
        }
        /* Responsive Design */
        @media (max-width: 950px) {
            .timeline-content {
                width: 95%;
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

    <!-- Timeline Content -->
    <div class="timeline-content">
        <!-- Display Messages -->
        <?php if (!empty($message)) echo $message; ?>

        <!-- Posts List -->
        <div class="posts-list">
            <?php if (empty($posts)): ?>
                <p>No posts to display.</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post">
                        <div class="post-header">
                            <?php echo htmlspecialchars($post['FirstName'] . ' ' . $post['LastName']); ?>
                        </div>
                        <div class="post-content">
                            <?php echo nl2br(htmlspecialchars($post['TextContent'])); ?>
                        </div>
                        <?php if (!empty($post['AttachmentContent'])): ?>
                            <div class="post-attachment">
                                <?php
                                $file_ext = strtolower(pathinfo($post['AttachmentContent'], PATHINFO_EXTENSION));
                                $image_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                                $video_extensions = ['mp4'];
                                $document_extensions = ['pdf', 'doc', 'docx'];

                                if (in_array($file_ext, $image_extensions)) {
                                    echo "<img src='" . htmlspecialchars($post['AttachmentContent']) . "' alt='Attachment'>";
                                } elseif (in_array($file_ext, $video_extensions)) {
                                    echo "<video controls>
                                            <source src='" . htmlspecialchars($post['AttachmentContent']) . "' type='video/mp4'>
                                            Your browser does not support the video tag.
                                          </video>";
                                } elseif (in_array($file_ext, $document_extensions)) {
                                    echo "<a href='" . htmlspecialchars($post['AttachmentContent']) . "' target='_blank'>View Attachment</a>";
                                } else {
                                    echo "<a href='" . htmlspecialchars($post['AttachmentContent']) . "' download>Download Attachment</a>";
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                        <div class="post-time">
                            <?php
                            // Calculate time difference
                            $post_time = strtotime($post['DatePosted']);
                            $current_time = time();
                            $diff_seconds = $current_time - $post_time;

                            if ($diff_seconds < 60) {
                                $time_ago = $diff_seconds . ' second' . ($diff_seconds != 1 ? 's' : '') . ' ago';
                            } elseif ($diff_seconds < 3600) {
                                $minutes = floor($diff_seconds / 60);
                                $time_ago = $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
                            } elseif ($diff_seconds < 86400) {
                                $hours = floor($diff_seconds / 3600);
                                $time_ago = $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
                            } else {
                                $days = floor($diff_seconds / 86400);
                                $time_ago = $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
                            }

                            echo htmlspecialchars($time_ago);
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
