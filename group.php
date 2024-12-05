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

// Handle form submission to create a new post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_content'], $_POST['group_id'])) {
    $post_content = $conn->real_escape_string($_POST['post_content']);
    $group_id = intval($_POST['group_id']);
    $member_id = 1; // Replace with the actual logged-in MemberID

    // Handle optional media upload
    $attachment_content = NULL;
    if (!empty($_FILES['media']['tmp_name'])) {
        $attachment_content = file_get_contents($_FILES['media']['tmp_name']);
    }

    // Insert the post into the database
    $sql = "INSERT INTO `Posts` (MemberID, GroupID, TextContent, AttachmentContent, DatePosted, ModerationStatus) 
            VALUES ($member_id, $group_id, '$post_content', ?, NOW(), 'Pending')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("b", $attachment_content); // 'b' indicates blob data

    if ($stmt->execute()) {
        // Redirect to avoid form resubmission issues
        header("Location: group.php?group_id=" . $group_id);
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
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

    // Fetch posts for the group
    $posts_sql = "SELECT PostID, MemberID, TextContent, LinkURL, DatePosted, ModerationStatus, Profile FROM `Posts` WHERE GroupID = $group_id";
    $posts_result = $conn->query($posts_sql);

    $posts = [];
    if ($posts_result->num_rows > 0) {
        while ($post_row = $posts_result->fetch_assoc()) {
            $posts[] = $post_row;
        }
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
            <form action="group.php" method="post" enctype="multipart/form-data">
                <textarea name="post_content" rows="4" placeholder="What's on your mind?"></textarea>
                <input type="file" name="media" accept="image/*,video/*">
                <input type="hidden" name="group_id" value="<?php echo $group_id; ?>"> <!-- Dynamic Group ID -->
                <button type="submit">Post</button>
            </form>
        </div>

        <!-- Posts List -->
        <div class="posts-list">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-card">
                        <!--<div class="post-header">Member ID: <?php echo htmlspecialchars($post['MemberID']); ?></div>-->
                        <div class="post-content"><?php echo nl2br(htmlspecialchars($post['TextContent'])); ?></div>
                        <?php if (!empty($post['LinkURL'])): ?>
                            <a href="<?php echo htmlspecialchars($post['LinkURL']); ?>" target="_blank">View Link</a>
                        <?php endif; ?>
                        <div class="post-time">Posted on: <?php echo htmlspecialchars($post['DatePosted']); ?></div>
                        <div class="post-status">Status: <?php echo htmlspecialchars($post['ModerationStatus']); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No posts available in this group.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>