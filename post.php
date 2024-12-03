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

// Get member_id from session and ensure it's an integer
$member_id = (int)$_SESSION['user_id'];

// Database credentials
$servername = "upc353.encs.concordia.ca";
$username = "upc353_2";
$password = "SleighParableSystem73";
$dbname = "upc353_2";

// Connect to the database using mysqli with error handling
$conn = new mysqli($servername, $username, $password, $dbname);

// Check database connection
if ($conn->connect_error) {
    die("<div class='message error'>Connection failed: " . htmlspecialchars($conn->connect_error) . "</div>");
}

// Initialize message variables
$create_post_message = "";
$update_profile_message = "";

// Handle Post Creation
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['create_post'])) {
    // Retrieve and sanitize text content
    $text_content = trim($_POST['text_content']);
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
            $unique_prefix = uniqid();
            $target_file = $upload_dir . $unique_prefix . "_" . $file_name;

            if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
                $attachment = $target_file; // Save the file path to the database
            } else {
                $create_post_message = "<div class='message error'>File upload failed.</div>";
            }
        } else {
            $create_post_message = "<div class='message error'>Invalid file type.</div>";
        }
    }

    // Insert post into the database
    if (!empty($text_content)) {
        $query = "INSERT INTO Posts (MemberID, TextContent, AttachmentContent, DatePosted, ModerationStatus, Profile) 
                  VALUES (?, ?, ?, NOW(), 'Pending', 'View Only')";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("iss", $member_id, $text_content, $attachment);

            if ($stmt->execute()) {
                $create_post_message = "<div class='message success'>Post created successfully! Redirecting to your profile...</div>";
                // Redirect to profile.php after 3 seconds
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'profile.php';
                    }, 3000);
                </script>";
            } else {
                $create_post_message = "<div class='message error'>Error creating post: " . htmlspecialchars($stmt->error) . "</div>";
            }
            $stmt->close();
        } else {
            $create_post_message = "<div class='message error'>Database error: " . htmlspecialchars($conn->error) . "</div>";
        }
    } else {
        $create_post_message = "<div class='message error'>Post content cannot be empty.</div>";
    }
}

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_profile'])) {
    // Retrieve and sanitize inputs
    $post_id = (int)$_POST['post_id'];
    $new_profile = $_POST['profile'];

    // Validate the new_profile value
    $allowed_profiles = ['View Only', 'View and Comment', 'View and Add', 'View and Link'];
    if (in_array($new_profile, $allowed_profiles)) {
        // Update the Profile in the database
        $update_query = "UPDATE Posts SET Profile = ? WHERE PostID = ? AND MemberID = ?";
        if ($update_stmt = $conn->prepare($update_query)) {
            $update_stmt->bind_param("sii", $new_profile, $post_id, $member_id);

            if ($update_stmt->execute()) {
                if ($update_stmt->affected_rows > 0) {
                    $update_profile_message = "<div class='message success'>Profile updated successfully.</div>";
                } else {
                    $update_profile_message = "<div class='message error'>No changes made or invalid Post ID.</div>";
                }
            } else {
                $update_profile_message = "<div class='message error'>Error updating profile: " . htmlspecialchars($update_stmt->error) . "</div>";
            }
            $update_stmt->close();
        } else {
            $update_profile_message = "<div class='message error'>Database error: " . htmlspecialchars($conn->error) . "</div>";
        }
    } else {
        $update_profile_message = "<div class='message error'>Invalid profile classification selected.</div>";
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
        /* Existing CSS styles */
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
        .profile-buttons {
            margin-top: 10px;
        }
        .profile-buttons form {
            display: inline;
        }
        .profile-buttons button {
            padding: 5px 10px;
            margin-right: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
            font-size: 0.9em;
        }
        .btn-view-only { background-color: #007bff; }
        .btn-view-and-comment { background-color: #28a745; }
        .btn-view-and-add { background-color: #ffc107; color: #333; }
        .btn-view-and-link { background-color: #17a2b8; }
        .profile-current {
            font-weight: bold;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Create Post</h1>
    </header>
    <div class="container">
        <!-- Display Messages -->
        <?php 
            if (!empty($create_post_message)) echo $create_post_message; 
            if (!empty($update_profile_message)) echo $update_profile_message; 
        ?>

        <!-- Post Creation Form -->
        <div class="form-container">
            <h2>Write a New Post</h2>
            <form action="" method="post" enctype="multipart/form-data">
                <textarea name="text_content" rows="4" placeholder="Write your post..." required></textarea>
                <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.gif,.mp4,.pdf,.doc,.docx">
                <!-- Hidden input to identify post creation -->
                <input type="hidden" name="create_post" value="1">
                <button type="submit">Post</button>
            </form>
        </div>

        <!-- Display User's Posts -->
        <div class="posts-container">
            <h2>Your Posts</h2>
            <?php
            // Fetch posts for the current user, including PostID and Profile
            $query = "SELECT PostID, MemberID, TextContent, AttachmentContent, DatePosted, ModerationStatus, Profile 
                      FROM Posts 
                      WHERE MemberID = ? 
                      ORDER BY DatePosted DESC";

            if ($stmt = $conn->prepare($query)) {
                // Bind the member_id parameter to ensure only current user's posts are fetched
                $stmt->bind_param("i", $member_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $post_id = htmlspecialchars($row['PostID']);
                        $date_posted = htmlspecialchars(date("F j, Y, g:i a", strtotime($row['DatePosted'])));
                        $text_content = nl2br(htmlspecialchars($row['TextContent']));
                        $attachment = htmlspecialchars($row['AttachmentContent']);
                        $moderation_status = htmlspecialchars($row['ModerationStatus']);
                        $current_profile = htmlspecialchars($row['Profile'] ?? 'View Only');

                        echo "<div class='post'>";
                        echo "<h3>Post on $date_posted</h3>";
                        echo "<p>$text_content</p>";

                        if (!empty($attachment)) {
                            // Determine the file type for proper handling
                            $file_ext = strtolower(pathinfo($attachment, PATHINFO_EXTENSION));
                            $image_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                            $video_extensions = ['mp4'];
                            $document_extensions = ['pdf', 'doc', 'docx'];

                            echo "<div class='attachment'>";
                            if (in_array($file_ext, $image_extensions)) {
                                echo "<img src='$attachment' alt='Attachment' style='max-width: 100%; height: auto;'>";
                            } elseif (in_array($file_ext, $video_extensions)) {
                                echo "<video controls style='max-width: 100%; height: auto;'>
                                        <source src='$attachment' type='video/mp4'>
                                        Your browser does not support the video tag.
                                      </video>";
                            } elseif (in_array($file_ext, $document_extensions)) {
                                echo "<a href='$attachment' target='_blank'>View Attachment</a>";
                            } else {
                                echo "<a href='$attachment' download>Download Attachment</a>";
                            }
                            echo "</div>";
                        }

                        echo "<p class='date'>Status: $moderation_status</p>";
                        echo "<p class='profile-current'>Current Profile: $current_profile</p>";

                        // Profile Buttons
                        echo "<div class='profile-buttons'>";
                            // Define profiles and their corresponding button classes for styling
                            $profiles = [
                                'View Only' => 'btn-view-only',
                                'View and Comment' => 'btn-view-and-comment',
                                'View and Add' => 'btn-view-and-add',
                                'View and Link' => 'btn-view-and-link'
                            ];

                            foreach ($profiles as $profile_label => $btn_class) {
                                echo "<form method='post' action='' style='display:inline; margin-right:5px;'>
                                        <input type='hidden' name='post_id' value='$post_id'>
                                        <input type='hidden' name='profile' value='$profile_label'>
                                        <button type='submit' name='update_profile' class='$btn_class'>$profile_label</button>
                                      </form>";
                            }
                        echo "</div>";

                        echo "</div>";
                    }
                } else {
                    echo "<p>You have not created any posts yet.</p>";
                }

                $stmt->close();
            } else {
                echo "<div class='message error'>Error fetching posts: " . htmlspecialchars($conn->error) . "</div>";
            }

            // Close the database connection
            $conn->close();
            ?>
        </div>
    </div>
</body>
</html>
