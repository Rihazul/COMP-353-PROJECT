<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Event Organizer</title>
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
        .event-form, .vote-form {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #ccc;
            margin-bottom: 20px;
        }
        .event-form h3, .vote-form h3 {
            color: #9e34eb;
        }
        .event-form input, .event-form button, .vote-form button {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .event-form button, .vote-form button {
            background-color: #9e34eb;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        .event-form button:hover, .vote-form button:hover {
            background-color: #7a29b8;
        }
    </style>
</head>
<body>
    <header>
        <h1>Organize Group Event</h1>
    </header>

    <div class="container">
        <!-- Form for Suggesting an Event -->
        <div class="event-form">
            <h3>Suggest an Event Date/Time/Place</h3>
            <form action="suggest_event.php" method="post">
                <input type="text" name="event_name" placeholder="Event Name" required>
                <input type="date" name="event_date" placeholder="Date" required>
                <input type="time" name="event_time" placeholder="Time" required>
                <input type="text" name="event_place" placeholder="Place" required>
                <button type="submit">Suggest</button>
            </form>
        </div>

        <!-- Event Voting Section -->
        <div class="vote-form">
            <h3>Vote on Suggested Dates/Times/Places</h3>
            <?php
            // Fetch suggested events from the database
            $group_id = 1; // Replace with actual group ID
            $conn = new mysqli("localhost", "username", "password", "database");

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT id, event_name, event_date, event_time, event_place, votes FROM event_suggestions WHERE group_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $group_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($event = $result->fetch_assoc()) {
                    echo "<div>";
                    echo "<p><strong>" . htmlspecialchars($event['event_name']) . "</strong></p>";
                    echo "<p>Date: " . htmlspecialchars($event['event_date']) . "</p>";
                    echo "<p>Time: " . htmlspecialchars($event['event_time']) . "</p>";
                    echo "<p>Place: " . htmlspecialchars($event['event_place']) . "</p>";
                    echo "<p>Votes: " . $event['votes'] . "</p>";

                    // Voting button form
                    echo "<form action='vote_event.php' method='post'>";
                    echo "<input type='hidden' name='event_id' value='" . $event['id'] . "'>";
                    echo "<button type='submit'>Vote for this Event</button>";
                    echo "</form>";
                    echo "</div><hr>";
                }
            } else {
                echo "<p>No event suggestions yet. Be the first to suggest one!</p>";
            }

            $stmt->close();
            $conn->close();
            ?>
        </div>
    </div>
</body>
</html>
