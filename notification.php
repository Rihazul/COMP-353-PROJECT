<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
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
            // Example demo notifications
            $notifications = [
                [
                    "type" => "Friend Request",
                    "message" => "John Doe sent you a friend request.",
                    "time" => "2 hours ago"
                ],
                [
                    "type" => "Message",
                    "message" => "Alice sent you a message.",
                    "time" => "5 hours ago"
                ],
                [
                    "type" => "Event Reminder",
                    "message" => "Don't forget the group meeting tomorrow at 10 AM.",
                    "time" => "1 day ago"
                ]
            ];

            if (!empty($notifications)) {
                foreach ($notifications as $notification) {
                    echo "<div class='notification'>";
                    echo "<strong>" . htmlspecialchars($notification['type']) . "</strong>";
                    echo "<p>" . htmlspecialchars($notification['message']) . "</p>";
                    echo "<p><em>" . htmlspecialchars($notification['time']) . "</em></p>";
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
