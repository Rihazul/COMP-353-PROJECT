<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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
        .notifications {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #ccc;
        }
        .notifications h3 {
            color: #9e34eb;
            margin-bottom: 20px;
        }
        .notification {
            padding: 15px;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .notification:last-child {
            border-bottom: none;
        }
        .notification strong {
            color: #333;
        }
        .notification p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>
    <header>
        <h1>Notifications</h1>
    </header>

    <div class="container">
        <div class="notifications">
            <h3>Recent Notifications</h3>
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

            // Fetch notifications from the da  tabase
            $sql = "SELECT NotificationContent, Date FROM Notifications";
            $result = $conn->query($sql);

            $notifications = [];
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $notifications[] = $row;
                }
            }

            $conn->close();

            if (!empty($notifications)) {
                foreach ($notifications as $notification) {
                    echo "<div class='notification'>";
                    echo "<p>" . htmlspecialchars($notification['NotificationContent']) . "</p>";
                    echo "<p><em>" . htmlspecialchars($notification['Date']) . "</em></p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No notifications yet.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
