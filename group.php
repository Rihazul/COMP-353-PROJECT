<?php
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

// Fetch group details
$group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;
if ($group_id > 0) {
    $sql = "SELECT GroupName, Description FROM `Groups` WHERE GroupID = $group_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch the group data
        $row = $result->fetch_assoc();
        $group_name = $row['GroupName'];
        $group_description = $row['Description'];
    } else {
        echo "Group not found.";
        exit;
    }
} else {
    echo "Invalid group ID.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Page</title>
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
        .logout-button {
            background-color: #fff;
            color: #9e34eb;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .logout-button:hover {
            background-color: #e0d4f7;
        }
        .group-container {
            width: 900px;
            margin: 30px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .group-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .post-form {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
        }
        .post-form textarea, .post-form input[type="file"] {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }
        .post-form button {
            background-color: #9e34eb;
            color: white;
            border: none;
            padding: 10px 20px;
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
        .post-card {
            background-color: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
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
        .comment-section {
            margin-top: 10px;
            padding-left: 20px;
        }
        .comment-section form {
            margin-top: 10px;
        }
        .comment-section textarea {
            width: 90%;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .comment {
            margin-top: 10px;
            padding: 5px 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div id="purple_bar">
        <div style="font-size: 45px; font-weight: bold;">
            COSN
        </div>
        <button class="logout-button" onclick="window.location.href='login.php'">Log out</button>
    </div>

    <!-- Group Container -->
    <div class="group-container">
        <!-- Group Header -->
        <div class="group-header">
            <h2><?php echo htmlspecialchars($group_name); ?></h2>
            <p>Group Description: <?php echo htmlspecialchars($group_description); ?></p>
        </div>

        <!-- Post Creation Form -->
        <div class="post-form">
            <form action="create_post.php" method="post" enctype="multipart/form-data">
                <textarea name="post_content" rows="4" placeholder="What's on your mind?"></textarea>
                <input type="file" name="media" accept="image/*,video/*">
                <input type="hidden" name="group_id" value="<?php echo $group_id; ?>"> <!-- Dynamic Group ID -->
                <button type="submit">Post</button>
            </form>
        </div>

        <!-- Posts List -->
        <div class="posts-list">
            <!-- Example Post -->
            <div class="post-card">
                <div class="post-header">John Doe</div>
                <div class="post-content">This is a post with an image.</div>
                <img src="/uploads/sample-image.jpg" alt="Post Media" style="width: 50%; margin-top: 10px;">
                <div class="post-time">2 hours ago</div>
                <!-- Comments Section -->
                <div class="comment-section">
                    <strong>Comments:</strong>
                    <div class="comment">Jane Smith: Nice post!</div>
                    <form action="add_comment.php" method="post">
                        <textarea name="comment_content" rows="2" placeholder="Add a comment..."></textarea>
                        <input type="hidden" name="post_id" value="1">
                        <button type="submit">Comment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>