<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COSN Photos</title>
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
        .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            background: url('search_icon.png') no-repeat center center;
            background-size: contain;
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
        header {
            text-align: center;
            padding: 20px;
            background-color: #9e34eb;
            color: white;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
        }
        nav ul li {
            display: inline;
            margin: 0 10px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .gallery {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
        }
        .gallery img {
            width: 200px;
            height: 200px;
            object-fit: cover;
            margin: 10px;
            border-radius: 10px;
            box-shadow: 0px 0px 5px 0px #ccc;
        }
    </style>
</head>
<body>
    
    <!-- top purple bar -->    
    <div id="purple_bar">
        <div style="font-size: 45px; font-weight: bold;">
            COSN
        </div>
        <div class="search-wrapper">
            <input type="text" name="search" id="search_box" placeholder="Search for photos">
            <div class="search-icon"></div>
        </div>
        <button class="logout-button" onclick="window.location.href='login.php'">Log out</button>
    </div>

    <header>
        <h1>Photo Gallery</h1>
        <nav>
            <ul>
                <li><a href="profile.php">Home</a></li>
                <li><a href="photos.php">Photos</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="gallery">
            <?php
            $photos = [
                'photo1.jpg',
                'photo2.jpg',
                'photo3.jpg',
                'photo4.jpg',
                'photo5.jpg',
                'photo6.jpg'
            ];
            foreach ($photos as $photo) {
                echo "<img src='$photo' alt='Photo'>";
            }
            ?>
        </section>
    </main>
</body>
</html>