<?php
session_start();

// Database connection
// include 'db_connection.php';
// $conn = OpenCon();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Posts - COSN</title>
    <style>
        /* General body styling */
        body {
            font-family: Tahoma, sans-serif;
            background-color: #fac3da;
            margin: 0;
            padding: 0;
        }

        /* Top purple bar */
        #purple_bar {
            height: 60px;
            background-color: #9e34eb;
            color: #fff;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        #purple_bar .title {
            font-size: 24px;
            font-weight: bold;
        }

        .logout-button {
            background-color: #fff;
            color: #9e34eb;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .logout-button:hover {
            background-color: #e0d4f7;
        }

        /* Admin Container */
        .admin-container {
            width: 900px;
            margin: 30px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #000;
            text-align: center;
        }

        h1 {
            color: #9e34eb;
            font-size: 2em;
            margin-bottom: 10px;
        }

        /* Post Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #9e34eb;
            color: #fff;
            font-weight: bold;
        }

        td a {
            color: #9e34eb;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        td a:hover {
            color: #7a29b8;
            text-decoration: underline;
        }

        /* Footer */
        .footer {
            text-align: center;
            color: #333;
            font-size: 0.9em;
            padding: 10px;
        }
    </style>
</head>
<body>

<!-- Top purple bar -->
<div id="purple_bar">
    <div class="title">Manage Posts</div>
    <button class="logout-button" onclick="window.location.href='login.php'">Log out</button>
</div>

<!-- Admin Container -->
<div class="admin-container">
    <h1>Manage Posts</h1>
    <p>Below is the list of posts. You can approve, edit, or delete posts as necessary.</p>

    <!-- Posts Table -->
    <table>
        <tr>
            <th>Post ID</th>
            <th>Author</th>
            <th>Content</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php
        // Example data; replace with database query results
        $posts = [
            ['id' => 1, 'author' => 'John Doe', 'content' => 'This is a sample post content.', 'status' => 'Pending'],
            ['id' => 2, 'author' => 'Jane Smith', 'content' => 'Another example of a post.', 'status' => 'Approved'],
            ['id' => 3, 'author' => 'Alice Brown', 'content' => 'This is yet another post content.', 'status' => 'Pending']
        ];

        foreach ($posts as $post) {
            echo "<tr>";
            echo "<td>" . $post['id'] . "</td>";
            echo "<td>" . $post['author'] . "</td>";
            echo "<td>" . substr($post['content'], 0, 50) . "...</td>";
            echo "<td>" . $post['status'] . "</td>";
            echo "<td>
                    <a href='approve_post.php?id=" . $post['id'] . "'>Approve</a> | 
                    <a href='edit_post.php?id=" . $post['id'] . "'>Edit</a> | 
                    <a href='delete_post.php?id=" . $post['id'] . "'>Delete</a>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>

<!-- Footer -->
<div class="footer">
    © 2024 COSN Admin Dashboard
</div>

</body>
</html>

<?php
// Close the database connection
// CloseCon($conn);
?>
