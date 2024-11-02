<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN | Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fac3da; /* Consistent background color */
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        header {
            background: #9e34eb; /* Consistent header color */
            color: #fff;
            padding-top: 30px;
            min-height: 70px;
            border-bottom: #77aaff 3px solid;
        }
        header a {
            color: #fff;
            text-decoration: none;
            text-transform: uppercase;
        }
        .profile-header {
            text-align: center;
            margin-top: 50px;
        }
        .profile-header img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
        }
        .profile-header h2 {
            color: #9e34eb;
            font-size: 2em;
        }
        .profile-content {
            text-align: center;
            margin-top: 20px;
        }
    </style>

</head>
<body>
    <header>
        <div class="container">
            <h1>COSN Profile</h1>
            <a href="login.php" style="float: right; margin-top: -50px;">Logout</a>
        </div>
    </header>
    <div class="container">
        <div class="profile-header">
            <img src="path/to/profile-picture.jpg" alt="Profile Picture">
            <h2>Welcome, [User's Name]!</h2>
        </div>
        <div class="profile-content">
            <p>Here you can view and edit your profile information.</p>
            <!-- Additional profile content goes here -->
        </div>
    </div>
</body>
</html>
<?php
// Sample data, replace with actual data retrieval logic
$name = "John Doe";
$email = "john.doe@example.com";
$role = "Student";
$department = "Computer Science";
?>

<div class="container">
    <div class="profile">
        <h2>Profile Information</h2>
        <p>Name: <?php echo htmlspecialchars($name); ?></p>
        <p>Email: <?php echo htmlspecialchars($email); ?></p>
        <p>Role: <?php echo htmlspecialchars($role); ?></p>
        <p>Department: <?php echo htmlspecialchars($department); ?></p>
    </div>
</div>