<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or show an error
    header("Location: login.php");
    exit();
}

$member_id = $_SESSION['user_id'];

// Database connection
$servername = "upc353.encs.concordia.ca";
$username = "upc353_2";
$password = "SleighParableSystem73";
$dbname = "upc353_2";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("<div class='message error'>Connection failed: " . htmlspecialchars($conn->connect_error) . "</div>");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $text_content = $_POST['text_content'];
    $attachment = null;

    // Handle file upload
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = basename($_FILES["attachment"]["name"]);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'pdf', 'doc', 'docx'];

        if (in_array($file_ext, $allowed_extensions)) {
            $target_file = $upload_dir . uniqid() . "_" . $file_name;

            if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
                $attachment = $target_file; // Save the file path to the database
            } else {
                echo "<div class='message error'>File upload failed.</div>";
            }
        } else {
            echo "<div class='message error'>Invalid file type.</div>";
        }
    }

    // Insert post into the database
    $query = "INSERT INTO Posts (MemberID, TextContent, AttachmentContent, DatePosted, ModerationStatus) VALUES (?, ?, ?, NOW(), 'Pending')";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("iss", $member_id, $text_content, $attachment);

        if ($stmt->execute()) {
            echo "<div class='message success'>Post created successfully! Redirecting to your profile...</div>";
            // Redirect to profile.php after 3 seconds
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'profile.php';
                }, 3000);
            </script>";
        } else {
            echo "<div class='message error'>Error creating post: " . htmlspecialchars($conn->error) . "</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='message error'>Database error: " . htmlspecialchars($conn->error) . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
    <style>
        /* [Your existing CSS styles] */
        body {
            font-family: Tahoma, sans-serif;
            background-color: #f4f4f4;
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
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
        }
        .form-container, .posts-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #ccc;
            margin-bottom: 20px;
        }
        .form-container h2, .posts-container h2 {
            color: #9e34eb;
            margin-top: 0;
        }
        .form-container textarea,
        .form-container input,
        .form-container button {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .form-container button {
            background-color: #9e34eb;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #7a29b8;
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
        .post {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .post:last-child {
            border-bottom: none;
        }
        .post h3 {
            margin: 0 0 5px 0;
            color: #333;
        }
        .post p {
            margin: 5px 0;
            color: #555;
        }
        .post .date {
            font-size: 0.9em;
            color: #999;
        }
        .post .attachment {
            margin-top: 10px;
        }
        .post .attachment a {
            color: #9e34eb;
            text-decoration: none;
        }
        .post .attachment a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>Create Post</h1>
    </header>
    <div class="container">
        <div class="form-container">
            <h2>Write a New Post</h2>
            <form action="" method="post" enctype="multipart/form-data">
                <textarea name="text_content" rows="4" placeholder="Write your post..." required></textarea>
                <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.gif,.mp4,.pdf,.doc,.docx">
                <!-- Removed hidden member_id input -->
                <button type="submit">Post</button>
            </form>
        </div>

        <div class="posts-container">
            <h2>Your Posts</h2>
            <?php
            // Fetch posts for the current user
            $query = "SELECT TextContent, AttachmentContent, DatePosted, ModerationStatus FROM Posts WHERE MemberID = ? ORDER BY DatePosted DESC";
            $stmt = $conn->prepare($query);
            if ($stmt) {
                $stmt->bind_param("i", $member_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='post'>";
                        echo "<h3>Post on " . htmlspecialchars(date("F j, Y, g:i a", strtotime($row['DatePosted']))) . "</h3>";
                        echo "<p>" . nl2br(htmlspecialchars($row['TextContent'])) . "</p>";
                        if (!empty($row['AttachmentContent'])) {
                            // Determine the file type for proper handling
                            $file_ext = strtolower(pathinfo($row['AttachmentContent'], PATHINFO_EXTENSION));
                            $image_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                            $video_extensions = ['mp4'];
                            $document_extensions = ['pdf', 'doc', 'docx'];

                            echo "<div class='attachment'>";
                            if (in_array($file_ext, $image_extensions)) {
                                echo "<img src='" . htmlspecialchars($row['AttachmentContent']) . "' alt='Attachment' style='max-width: 100%; height: auto;'>";
                            } elseif (in_array($file_ext, $video_extensions)) {
                                echo "<video controls style='max-width: 100%; height: auto;'><source src='" . htmlspecialchars($row['AttachmentContent']) . "' type='video/mp4'>Your browser does not support the video tag.</video>";
                            } elseif (in_array($file_ext, $document_extensions)) {
                                echo "<a href='" . htmlspecialchars($row['AttachmentContent']) . "' target='_blank'>View Attachment</a>";
                            } else {
                                echo "<a href='" . htmlspecialchars($row['AttachmentContent']) . "' download>Download Attachment</a>";
                            }
                            echo "</div>";
                        }
                        echo "<p class='date'>Status: " . htmlspecialchars($row['ModerationStatus']) . "</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>You have not created any posts yet.</p>";
                }

                $stmt->close();
            } else {
                echo "<div class='message error'>Error fetching posts: " . htmlspecialchars($conn->error) . "</div>";
            }

            $conn->close();
            ?>
        </div>
    </div>
</body>
</html>
