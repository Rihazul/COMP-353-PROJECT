<?php
session_start();

// Set environment ('development' or 'production')
$environment = 'development'; // Change to 'production' in live environment

if ($environment === 'production') {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
} else {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or show an error
    header("Location: login.php");
    exit();
}

// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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

// Refactored time_ago function
function time_ago($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    // Calculate total weeks from total days
    $weeks = floor($diff->days / 7);
    $days = $diff->days % 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );

    // Create an associative array with the calculated values
    $units = array(
        'y' => $diff->y,
        'm' => $diff->m,
        'w' => $weeks,
        'd' => $days,
        'h' => $diff->h,
        'i' => $diff->i,
        's' => $diff->s,
    );

    foreach ($units as $k => &$v) {
        if ($v) {
            $string[$k] = $v . ' ' . $string[$k] . ($v > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) {
        $string = array_slice($string, 0, 1);
    }

    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

// Function to fetch comments for a given post
function fetch_comments($conn, $post_id) {
    global $environment, $message;
    $comments = [];
    $fetch_comments_query = "
        SELECT C.CommentID, C.MemberID, M.FirstName, M.LastName, C.CommentText, C.DatePosted
        FROM Comments C
        INNER JOIN Member M ON C.MemberID = M.MemberID
        WHERE C.PostID = ?
        ORDER BY C.DatePosted ASC
    ";

    if ($stmt = $conn->prepare($fetch_comments_query)) {
        $stmt->bind_param("i", $post_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($comment = $result->fetch_assoc()) {
                $comments[] = $comment;
            }
        } else {
            // Log the error or handle it as needed
            if ($environment === 'development') {
                $message .= "<div class='message error'>Error executing comments query: " . htmlspecialchars($stmt->error) . "</div>";
            }
        }
        $stmt->close();
    } else {
        // Log the error or handle it as needed
        if ($environment === 'development') {
            $message .= "<div class='message error'>Error preparing comments query: " . htmlspecialchars($conn->error) . "</div>";
        }
    }

    return $comments;
}

// Function to fetch attachments for a given post (if multiple attachments are needed)
function fetch_attachments($conn, $post_id) {
    global $environment, $message;
    $attachments = [];
    $fetch_attachments_query = "
        SELECT AttachmentPath
        FROM Attachments
        WHERE PostID = ?
        ORDER BY AttachmentID ASC
    ";

    if ($stmt = $conn->prepare($fetch_attachments_query)) {
        $stmt->bind_param("i", $post_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($attachment = $result->fetch_assoc()) {
                $attachments[] = $attachment['AttachmentPath'];
            }
        } else {
            // Handle execution error
            if ($environment === 'development') {
                $message .= "<div class='message error'>Error executing attachments query: " . htmlspecialchars($stmt->error) . "</div>";
            }
        }
        $stmt->close();
    } else {
        // Handle preparation error
        if ($environment === 'development') {
            $message .= "<div class='message error'>Error preparing attachments query: " . htmlspecialchars($conn->error) . "</div>";
        }
    }

    return $attachments;
}

// Handle Form Submissions: Comments, Add Content, and Link Content
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message .= "<div class='message error'>Invalid CSRF token.</div>";
    } else {
        // Determine the type of submission based on available POST variables
        if (isset($_POST['comment_text']) && isset($_POST['post_id'])) {
            // Handle Comment Submission
            $post_id = (int)$_POST['post_id'];
            $comment_text = trim($_POST['comment_text']);

            // Validate input
            if (empty($comment_text)) {
                $message .= "<div class='message error'>Comment cannot be empty.</div>";
            } else {
                // Prepare and execute the insert statement for comments
                $insert_comment_query = "
                    INSERT INTO Comments (PostID, MemberID, CommentText, DatePosted)
                    VALUES (?, ?, ?, NOW())
                ";

                if ($stmt = $conn->prepare($insert_comment_query)) {
                    $stmt->bind_param("iis", $post_id, $user_id, $comment_text);

                    if ($stmt->execute()) {
                        $message .= "<div class='message success'>Comment added successfully.</div>";
                    } else {
                        if ($environment === 'development') {
                            $message .= "<div class='message error'>Error adding comment: " . htmlspecialchars($stmt->error) . "</div>";
                        } else {
                            $message .= "<div class='message error'>An error occurred while adding your comment.</div>";
                        }
                    }

                    $stmt->close();
                } else {
                    if ($environment === 'development') {
                        $message .= "<div class='message error'>Database error: " . htmlspecialchars($conn->error) . "</div>";
                    } else {
                        $message .= "<div class='message error'>An error occurred while processing your request.</div>";
                    }
                }
            }
        } elseif (isset($_POST['additional_content']) && isset($_POST['post_id'])) {
            // Handle Add Content Submission
            $post_id = (int)$_POST['post_id'];
            $additional_content = trim($_POST['additional_content']);

            // Handle file upload if present
            $additional_attachment = null;
            if (isset($_FILES['additional_attachment']) && $_FILES['additional_attachment']['error'] === UPLOAD_ERR_OK) {
                $file_tmp_path = $_FILES['additional_attachment']['tmp_name'];
                $file_name = basename($_FILES['additional_attachment']['name']);
                $file_size = $_FILES['additional_attachment']['size'];
                $file_type = mime_content_type($file_tmp_path);

                // Define allowed file types
                $allowed_types = [
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                    'video/mp4',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ];

                if (in_array($file_type, $allowed_types)) {
                    // Define upload directory
                    $upload_dir = 'uploads/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                    // Generate a unique file name to prevent overwriting
                    $new_file_name = uniqid() . "_" . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $file_name);
                    $dest_path = $upload_dir . $new_file_name;

                    if (move_uploaded_file($file_tmp_path, $dest_path)) {
                        $additional_attachment = $dest_path;
                    } else {
                        $message .= "<div class='message error'>Error moving the uploaded file.</div>";
                    }
                } else {
                    $message .= "<div class='message error'>Invalid file type for attachment.</div>";
                }
            }

            // Validate input
            if (empty($additional_content) && empty($additional_attachment)) {
                $message .= "<div class='message error'>Additional content or attachment is required.</div>";
            } else {
                // Prepare and execute the update statement for adding content
                $update_content_query = "
                    UPDATE Posts
                    SET AdditionalContent = ?, AdditionalAttachment = ?
                    WHERE PostID = ?
                ";

                if ($stmt = $conn->prepare($update_content_query)) {
                    // Bind parameters. If no attachment, set as NULL
                    $stmt->bind_param("ssi", $additional_content, $additional_attachment, $post_id);

                    if ($stmt->execute()) {
                        if ($stmt->affected_rows > 0) {
                            $message .= "<div class='message success'>Additional content added successfully.</div>";
                        } else {
                            $message .= "<div class='message error'>No changes made. Please ensure the post exists.</div>";
                        }
                    } else {
                        if ($environment === 'development') {
                            $message .= "<div class='message error'>Error adding content: " . htmlspecialchars($stmt->error) . "</div>";
                        } else {
                            $message .= "<div class='message error'>An error occurred while adding your content.</div>";
                        }
                    }

                    $stmt->close();
                } else {
                    if ($environment === 'development') {
                        $message .= "<div class='message error'>Database error: " . htmlspecialchars($conn->error) . "</div>";
                    } else {
                        $message .= "<div class='message error'>An error occurred while processing your request.</div>";
                    }
                }
            }
        } elseif (isset($_POST['link_url']) && isset($_POST['post_id'])) {
            // Handle Link Content Submission
            $post_id = (int)$_POST['post_id'];
            $link_url = trim($_POST['link_url']);

            // Validate URL
            if (empty($link_url)) {
                $message .= "<div class='message error'>Link URL cannot be empty.</div>";
            } elseif (!filter_var($link_url, FILTER_VALIDATE_URL)) {
                $message .= "<div class='message error'>Please enter a valid URL.</div>";
            } else {
                // Prepare and execute the update statement for linking content
                $update_link_query = "
                    UPDATE Posts
                    SET LinkURL = ?
                    WHERE PostID = ?
                ";

                if ($stmt = $conn->prepare($update_link_query)) {
                    $stmt->bind_param("si", $link_url, $post_id);

                    if ($stmt->execute()) {
                        if ($stmt->affected_rows > 0) {
                            $message .= "<div class='message success'>Link added successfully.</div>";
                        } else {
                            $message .= "<div class='message error'>No changes made. Please ensure the post exists.</div>";
                        }
                    } else {
                        if ($environment === 'development') {
                            $message .= "<div class='message error'>Error adding link: " . htmlspecialchars($stmt->error) . "</div>";
                        } else {
                            $message .= "<div class='message error'>An error occurred while adding your link.</div>";
                        }
                    }

                    $stmt->close();
                } else {
                    if ($environment === 'development') {
                        $message .= "<div class='message error'>Database error: " . htmlspecialchars($conn->error) . "</div>";
                    } else {
                        $message .= "<div class='message error'>An error occurred while processing your request.</div>";
                    }
                }
            }
        }
    }
}

// Fetch all approved posts ordered by DatePosted descending
$fetch_posts_query = "
    SELECT P.PostID, P.MemberID, M.FirstName, M.LastName, P.TextContent, P.AttachmentContent, P.AdditionalContent, P.AdditionalAttachment, P.LinkURL, P.DatePosted, P.ModerationStatus, P.Profile 
    FROM Posts P
    INNER JOIN Member M ON P.MemberID = M.MemberID
    WHERE P.ModerationStatus = 'Approved'
    ORDER BY P.DatePosted DESC
";

if ($result = $conn->query($fetch_posts_query)) {
    while ($row = $result->fetch_assoc()) {
        // Fetch comments for this post
        $row['Comments'] = fetch_comments($conn, $row['PostID']);
        // Fetch attachments for this post (if multiple attachments are needed)
        // Uncomment the following line if using a separate Attachments table
        // $row['Attachments'] = fetch_attachments($conn, $row['PostID']);
        $posts[] = $row;
    }
    $result->free();
} else {
    if ($environment === 'development') {
        $message .= "<div class='message error'>Error fetching posts: " . htmlspecialchars($conn->error) . "</div>";
    } else {
        $message .= "<div class='message error'>An error occurred while fetching posts.</div>";
    }
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- [Existing Head Content] -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN Timeline</title>
    <style>
        /* [Your existing CSS styles here] */
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
        .post-additional-content {
            margin-top: 10px;
        }
        .post-additional-content img,
        .post-additional-content video {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
        }
        .post-additional-content a {
            display: block;
            margin-top: 10px;
            color: #17a2b8;
            text-decoration: none;
        }
        .post-additional-content a:hover {
            text-decoration: underline;
        }
        .post-linked-content {
            margin-top: 10px;
        }
        .post-linked-content a {
            color: #1a0dab; /* Standard link color */
            text-decoration: none;
            font-weight: bold;
        }
        .post-linked-content a:hover {
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
        /* Comment Section Styles */
        .comments-section {
            margin-top: 15px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        .comment {
            margin-bottom: 10px;
        }
        .comment-author {
            font-weight: bold;
            color: #9e34eb;
        }
        .comment-content {
            margin-left: 20px;
            color: #333;
        }
        .comment-time {
            margin-left: 20px;
            font-size: 10px;
            color: #999;
        }
        .add-comment {
            margin-top: 10px;
        }
        .add-comment textarea {
            width: 100%;
            height: 60px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 5px;
            resize: vertical;
        }
        .add-comment button {
            margin-top: 5px;
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 14px;
        }
        .add-comment button:hover {
            background-color: #218838;
        }
        /* Add Content Section Styles */
        .add-content-section {
            margin-top: 10px;
        }
        .add-content-section textarea {
            width: 100%;
            height: 80px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 5px;
            resize: vertical;
            margin-bottom: 5px;
        }
        .add-content-section input[type="file"] {
            margin-bottom: 5px;
        }
        .add-content-section button {
            padding: 5px 10px;
            border: none;
            background-color: #ffc107;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .add-content-section button:hover {
            background-color: #e0a800;
        }
        /* Link Content Section Styles */
        .link-content-section {
            margin-top: 10px;
        }
        .link-content-section input[type="url"] {
            width: 80%;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 5px;
        }
        .link-content-section button {
            padding: 5px 10px;
            border: none;
            background-color: #17a2b8;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .link-content-section button:hover {
            background-color: #117a8b;
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
        <button onclick="window.location.href='browse_groups.php'">Groups</button>
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
                        <?php if (!empty($post['AdditionalContent']) || !empty($post['AdditionalAttachment'])): ?>
                            <div class="post-additional-content">
                                <?php if (!empty($post['AdditionalContent'])): ?>
                                    <p><?php echo nl2br(htmlspecialchars($post['AdditionalContent'])); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($post['AdditionalAttachment'])): ?>
                                    <?php
                                    $add_file_ext = strtolower(pathinfo($post['AdditionalAttachment'], PATHINFO_EXTENSION));
                                    $add_image_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                                    $add_video_extensions = ['mp4'];
                                    $add_document_extensions = ['pdf', 'doc', 'docx'];

                                    if (in_array($add_file_ext, $add_image_extensions)) {
                                        echo "<img src='" . htmlspecialchars($post['AdditionalAttachment']) . "' alt='Additional Attachment'>";
                                    } elseif (in_array($add_file_ext, $add_video_extensions)) {
                                        echo "<video controls>
                                                <source src='" . htmlspecialchars($post['AdditionalAttachment']) . "' type='video/mp4'>
                                                Your browser does not support the video tag.
                                              </video>";
                                    } elseif (in_array($add_file_ext, $add_document_extensions)) {
                                        echo "<a href='" . htmlspecialchars($post['AdditionalAttachment']) . "' target='_blank'>View Additional Attachment</a>";
                                    } else {
                                        echo "<a href='" . htmlspecialchars($post['AdditionalAttachment']) . "' download>Download Additional Attachment</a>";
                                    }
                                    ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($post['LinkURL'])): ?>
                            <div class="post-linked-content">
                                <a href="<?php echo htmlspecialchars($post['LinkURL']); ?>" target="_blank">🔗 View Linked Content</a>
                            </div>
                        <?php endif; ?>

                        <div class="post-time">
                            <?php
                            // Calculate time difference using the refactored time_ago function
                            echo htmlspecialchars(time_ago($post['DatePosted']));
                            ?>
                        </div>

                        <!-- Comments Section -->
                        <?php if ($post['Profile'] === 'View and Comment'): ?>
                            <div class="comments-section" id="comments-<?php echo htmlspecialchars($post['PostID']); ?>">
                                <!-- Existing Comments -->
                                <?php
                                if (!empty($post['Comments'])) {
                                    foreach ($post['Comments'] as $comment) {
                                        echo "<div class='comment'>";
                                        echo "<span class='comment-author'>" . htmlspecialchars($comment['FirstName'] . ' ' . $comment['LastName']) . ":</span> ";
                                        echo "<span class='comment-content'>" . nl2br(htmlspecialchars($comment['CommentText'])) . "</span>";
                                        echo "<div class='comment-time'>" . htmlspecialchars(time_ago($comment['DatePosted'])) . "</div>";
                                        echo "</div>";
                                    }
                                } else {
                                    echo "<p>No comments yet.</p>";
                                }
                                ?>

                                <!-- Add Comment Form -->
                                <div class="add-comment">
                                    <form action="timeline.php" method="post">
                                        <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['PostID']); ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                        <textarea name="comment_text" placeholder="Write a comment..." required></textarea>
                                        <button type="submit">Submit Comment</button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Add Content Section -->
                        <?php if ($post['Profile'] === 'View and Add'): ?>
                            <div class="add-content-section" id="add-content-<?php echo htmlspecialchars($post['PostID']); ?>">
                                <!-- Add Content Form -->
                                <div class="add-content">
                                    <form action="timeline.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['PostID']); ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                        <textarea name="additional_content" placeholder="Add your content here..." required></textarea>
                                        <input type="file" name="additional_attachment" accept=".jpg,.jpeg,.png,.gif,.mp4,.pdf,.doc,.docx">
                                        <button type="submit">Add Content</button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Link Content Section -->
                        <?php if ($post['Profile'] === 'Link to Other Contents'): ?>
                            <div class="link-content-section" id="link-content-<?php echo htmlspecialchars($post['PostID']); ?>">
                                <!-- Link Content Form -->
                                <div class="link-content">
                                    <form action="timeline.php" method="post">
                                        <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['PostID']); ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                        <input type="url" name="link_url" placeholder="Enter URL to link" required>
                                        <button type="submit">Link Content</button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Removed JavaScript Functions for Toggling Sections -->
</body>
</html>
