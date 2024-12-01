<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Admin Management</title>
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
        .admin-container {
            width: 900px;
            margin: 30px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .admin-section {
            margin-bottom: 30px;
        }
        h2 {
            color: #9e34eb;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            text-align: left;
            padding: 10px;
        }
        th {
            background-color: #9e34eb;
            color: white;
        }
        .approve-button, .disapprove-button, .edit-button, .delete-button {
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        .approve-button {
            background-color: #28a745;
            color: white;
        }
        .approve-button:hover {
            background-color: #218838;
        }
        .disapprove-button {
            background-color: #dc3545;
            color: white;
        }
        .disapprove-button:hover {
            background-color: #c82333;
        }
        .edit-button {
            background-color: #ffc107;
            color: black;
        }
        .edit-button:hover {
            background-color: #e0a800;
        }
        .delete-button {
            background-color: #dc3545;
            color: white;
        }
        .delete-button:hover {
            background-color: #c82333;
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

    <!-- Admin Container -->
    <div class="admin-container">
        <!-- User Approval Section -->
        <div class="admin-section">
            <h2>Approve or Disapprove Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example Row -->
                    <tr>
                        <td>101</td>
                        <td>John Doe</td>
                        <td>
                            <button class="approve-button" onclick="approveUser(101)">Approve</button>
                            <button class="disapprove-button" onclick="disapproveUser(101)">Disapprove</button>
                        </td>
                    </tr>
                    <tr>
                        <td>102</td>
                        <td>Jane Smith</td>
                        <td>
                            <button class="approve-button" onclick="approveUser(102)">Approve</button>
                            <button class="disapprove-button" onclick="disapproveUser(102)">Disapprove</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Post Management Section -->
        <div class="admin-section">
            <h2>Manage Group Posts</h2>
            <table>
                <thead>
                    <tr>
                        <th>Post ID</th>
                        <th>Content</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example Row -->
                    <tr>
                        <td>201</td>
                        <td>This is a post from the group.</td>
                        <td>
                            <button class="edit-button" onclick="editPost(201)">Edit</button>
                            <button class="delete-button" onclick="deletePost(201)">Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>202</td>
                        <td>Another group post content.</td>
                        <td>
                            <button class="edit-button" onclick="editPost(202)">Edit</button>
                            <button class="delete-button" onclick="deletePost(202)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function approveUser(userId) {
            // Implement AJAX call to approve the user
            alert('User ' + userId + ' approved.');
        }

        function disapproveUser(userId) {
            // Implement AJAX call to disapprove the user
            alert('User ' + userId + ' disapproved.');
        }

        function editPost(postId) {
            // Redirect to the edit post page or implement inline editing
            alert('Redirecting to edit post ' + postId);
            // Example: window.location.href = `edit_post.php?post_id=${postId}`;
        }

        function deletePost(postId) {
            // Implement AJAX call to delete the post
            if (confirm('Are you sure you want to delete post ' + postId + '?')) {
                alert('Post ' + postId + ' deleted.');
            }
        }
    </script>
</body>
</html>