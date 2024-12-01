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
        .event-form, .vote-form, .event-list {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #ccc;
            margin-bottom: 20px;
        }
        .event-form h3, .vote-form h3, .event-list h3 {
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
 <div class="container">
        <!-- Form for Suggesting an Event -->
        <div class="event-form">
            <h3>Suggest an Event Date/Time/Place</h3>
            <?php
            // Handle form submission
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['event_name'])) {
                $servername = "upc353.encs.concordia.ca";
                $username = "upc353_2";
                $password = "SleighParableSystem73";
                $dbname = "upc353_2";

                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Retrieve form data
                $event_id = intval($_POST['event_id']);
                $event_name = $conn->real_escape_string($_POST['event_name']);
                $event_date = $conn->real_escape_string($_POST['event_date']);
                $event_time = $conn->real_escape_string($_POST['event_time']);
                $event_place = $conn->real_escape_string($_POST['event_place']);
                $member_id = 1; // Replace with actual logged-in member ID
                $votes = 0;

                // Validate Event ID
                $check_event_sql = "SELECT COUNT(*) AS count FROM Event WHERE Event = $event_id";
                $result = $conn->query($check_event_sql);
                $row = $result->fetch_assoc();

                if ($row['count'] > 0) {
                    // Insert into suggestions table
                    $sql = "INSERT INTO suggestions (MemberID, Event, Suggestion, Date, Time, Votes)
                            VALUES ($member_id, $event_id, '$event_name - $event_place', '$event_date', '$event_time', $votes)";
                    if ($conn->query($sql) === TRUE) {
                        // Redirect to the same page after successful submission
                        header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
                        exit();
                    } else {
                        echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
                    }
                } else {
                    echo "<p style='color: red;'>Error: Event ID $event_id does not exist.</p>";
                }

                $conn->close();
            }

            // Display success message if redirected
            if (isset($_GET['success']) && $_GET['success'] == 1) {
                echo "<p style='color: green;'>Suggestion added successfully!</p>";
            }
            ?>
            <form method="post">
                <input type="number" name="event_id" placeholder="Event ID (must exist)" required>
                <input type="text" name="event_name" placeholder="Event Name" required>
                <input type="date" name="event_date" placeholder="Date" required>
                <input type="time" name="event_time" placeholder="Time" required>
                <input type="text" name="event_place" placeholder="Place" required>
                <button type="submit">Suggest</button>
            </form>
        </div>
    </div>


        <!-- Event Voting Section -->
<div class="vote-form">
    <h3>Vote on Suggested Dates/Times/Places</h3>
    <?php
    session_start(); // Start the session to store the token

    $servername = "upc353.encs.concordia.ca";
    $username = "upc353_2";
    $password = "SleighParableSystem73";
    $dbname = "upc353_2";
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Generate a unique token for form validation
    if (!isset($_SESSION['token'])) {
        $_SESSION['token'] = bin2hex(random_bytes(32));
    }
    $token = $_SESSION['token'];

    // Process votes
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['vote']) && isset($_POST['suggestion_id']) && isset($_POST['token'])) {
        // Verify the token to ensure the vote was intentional
        if (hash_equals($_SESSION['token'], $_POST['token'])) {
            $suggestion_id = intval($_POST['suggestion_id']);
            $vote_sql = "UPDATE suggestions SET Votes = Votes + 1 WHERE SuggestionID = ?";
            $stmt = $conn->prepare($vote_sql);
            $stmt->bind_param("i", $suggestion_id);

            if ($stmt->execute()) {
                // Regenerate the token to prevent reuse
                $_SESSION['token'] = bin2hex(random_bytes(32));
                // Redirect to avoid form resubmission
                header("Location: " . $_SERVER['PHP_SELF'] . "?vote_success=1");
                exit();
            } else {
                echo "<p style='color: red;'>Error recording vote: " . $stmt->error . "</p>";
            }
        } else {
            echo "<p style='color: red;'>Invalid token. Vote not recorded.</p>";
        }
    }

    // Display success message if redirected after voting
    if (isset($_GET['vote_success']) && $_GET['vote_success'] == 1) {
        echo "<p style='color: green;'>Vote recorded successfully!</p>";
    }

    // Fetch data from the suggestions table, ordered by newest first
    $sql = "SELECT SuggestionID, Event, Suggestion, Date, Time, Votes, MemberID FROM suggestions ORDER BY SuggestionID DESC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($suggestion = $result->fetch_assoc()) {
            echo "<div>";
            echo "<p><strong>Event ID: </strong>" . htmlspecialchars($suggestion['Event']) . "</p>";
            echo "<p><strong>Suggestion: </strong>" . htmlspecialchars($suggestion['Suggestion']) . "</p>";
            echo "<p><strong>Date: </strong>" . htmlspecialchars($suggestion['Date']) . "</p>";
            echo "<p><strong>Time: </strong>" . htmlspecialchars($suggestion['Time']) . "</p>";
            echo "<p><strong>Votes: </strong>" . htmlspecialchars($suggestion['Votes']) . "</p>";
            echo "<p><strong>Suggested By (MemberID): </strong>" . htmlspecialchars($suggestion['MemberID']) . "</p>";

            // Voting button form
            echo "<form method='post'>";
            echo "<input type='hidden' name='suggestion_id' value='" . $suggestion['SuggestionID'] . "'>";
            echo "<input type='hidden' name='token' value='" . $token . "'>";
            echo "<button type='submit' name='vote'>Vote for this Suggestion</button>";
            echo "</form>";
            echo "</div><hr>";
        }
    } else {
        echo "<p>No suggestions available. Be the first to suggest an event!</p>";
    }

    $conn->close();
    ?>
</div>




</body>
</html>
