<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
    <style>
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
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #ccc;
        }
        .form-container h2 {
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
    </style>
</head>
<body>
    <header>
        <h1>Create Post</h1>
    </header>
    <div class="container">
        <?php
        // Database connection
        $servername = "upc353.encs.concordia.ca";
        $username = "upc353_2";
        $password = "SleighParableSystem73";
        $dbname = "upc353_2";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("<div class='message error'>Connection failed: " . $conn->connect_error . "</div>");
        }

        // Handle form submission
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $member_id = $_POST['member_id'];
            $text_content = $_POST['text_content'];
            $attachment = null;

            // Handle file upload
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = "uploads/";
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $file_name = basename($_FILES["attachment"]["name"]);
                $target_file = $upload_dir . uniqid() . "_" . $file_name;

                if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
                    $attachment = $target_file; // Save the file path to the database
                } else {
                    echo "<div class='message error'>File upload failed.</div>";
                }
            }

            // Insert post into the database
            $query = "INSERT INTO Posts (MemberID, TextContent, AttachmentContent, DatePosted, ModerationStatus) VALUES (?, ?, ?, NOW(), 'Pending')";
            $stmt = $conn->prepare($query);
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
                echo "<div class='message error'>Error creating post: " . $conn->error . "</div>";
            }
            $stmt->close();
        }
        ?>
        <div class="form-container">
            <h2>Write a New Post</h2>
            <form action="" method="post" enctype="multipart/form-data">
                <textarea name="text_content" rows="4" placeholder="Write your post..." required></textarea>
                <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.gif,.mp4,.pdf,.doc,.docx">
                <input type="hidden" name="member_id" value="1"> <!-- Replace with dynamic member ID -->
                <button type="submit">Post</button>
            </form>
        </div>
    </div>
    <?php $conn->close(); ?>
</body>
</html>
