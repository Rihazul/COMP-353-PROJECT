<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Details</title>
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
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
        }
        .post-container, .comments-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #ccc;
            margin-bottom: 20px;
        }
        .post-container h2, .comments-container h3 {
            color: #9e34eb;
            margin-top: 0;
        }
        .attachment {
            margin-top: 10px;
            text-align: center;
        }
        .attachment img, .attachment video {
            max-width: 100%;
            border-radius: 10px;
            margin-top: 10px;
        }
        .comments-container .comment {
            padding: 10px;
            border-bottom: 1px solid #ccc;
            margin-bottom: 10px;
        }
        .comment:last-child {
            border-bottom: none;
        }
        .comment-form {
            margin-top: 20px;
        }
        .comment-form textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: none;
        }
        .comment-form button {
            padding: 10px 20px;
            background-color: #9e34eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
        }
        .comment-form button:hover {
            background-color: #7a29b8;
        }
    </style>
</head>
<body>
    <header>
        <h1>Post Details</h1>
    </header>

    <div class="container">
        <!-- Post Content and Attachment Section -->
        <div class="post-container">
            <?php
            // Replace with actual post ID from URL parameter
            $post_id = $_GET['post_id'];

            // Connect to the database
            $conn = new mysqli("localhost", "username", "password", "database");

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch the post data
            $post_query = "SELECT title, content, attachment, created_at FROM posts WHERE id = ?";
            $stmt = $conn->prepare($post_query);
            $stmt->bind_param("i", $post_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $post = $result->fetch_assoc();

            if ($post) {
                echo "<h2>" . htmlspecialchars($post['title']) . "</h2>";
                echo "<p>" . htmlspecialchars($post['content']) . "</p>";
                echo "<p><small>Posted on: " . $post['created_at'] . "</small></p>";
                
                // Display attachment if it exists
                if (!empty($post['attachment'])) {
                    echo "<div class='attachment'>";
                    $attachment = $post['attachment'];
                    
                    // Check file type to decide how to display it
                    $file_extension = pathinfo($attachment, PATHINFO_EXTENSION);
                    if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                        echo "<img src='uploads/$attachment' alt='Attachment'>";
                    } elseif (in_array($file_extension, ['mp4', 'webm', 'ogg'])) {
                        echo "<video controls src='uploads/$attachment'></video>";
                    } else {
                        echo "<p><a href='uploads/$attachment' download>Download Attachment</a></p>";
                    }
                    echo "</div>";
                }
            } else {
                echo "<p>Post not found.</p>";
            }

            $stmt->close();
            ?>

        </div>

        <!-- Comments Section -->
        <div class="comments-container">
            <h3>Comments</h3>
            <?php
            // Fetch comments for the post
            $comments_query = "SELECT user_name, comment_text, commented_at FROM comments WHERE post_id = ? ORDER BY commented_at DESC";
            $stmt = $conn->prepare($comments_query);
            $stmt->bind_param("i", $post_id);
            $stmt->execute();
            $comments_result = $stmt->get_result();

            if ($comments_result->num_rows > 0) {
                while ($comment = $comments_result->fetch_assoc()) {
                    echo "<div class='comment'>";
                    echo "<p><strong>" . htmlspecialchars($comment['user_name']) . ":</strong></p>";
                    echo "<p>" . htmlspecialchars($comment['comment_text']) . "</p>";
                    echo "<p><small>Commented on: " . $comment['commented_at'] . "</small></p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No comments yet. Be the first to comment!</p>";
            }

            $stmt->close();
            ?>

            <!-- Comment Form -->
            <div class="comment-form">
                <form action="add_comment.php" method="post">
                    <textarea name="comment_text" rows="4" placeholder="Write a comment..." required></textarea>
                    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                    <button type="submit">Add Comment</button>
                </form>
            </div>
        </div>
    </div>

    <?php
    // Close the database connection
    $conn->close();
    ?>
</body>
</html>
