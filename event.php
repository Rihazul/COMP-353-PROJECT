<?php
session_start();

// Database connection
$servername = "upc353.encs.concordia.ca";
$username = "upc353_2";
$password = "SleighParableSystem73";
$dbname = "upc353_2";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Generate CSRF token for secure form submissions
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['token'];

// Assume the logged-in user ID is stored in session
// Replace this with actual session user ID retrieval
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

// Handle form submissions for event suggestions
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['event_name'])) {
    $group_id = intval($_POST['group_id']);
    $event_name = $conn->real_escape_string($_POST['event_name']);
    $event_date = $conn->real_escape_string($_POST['event_date']);
    $event_time = $conn->real_escape_string($_POST['event_time']);
    $event_place = $conn->real_escape_string($_POST['event_place']);
    $votes = 0;

    // Validate Group ID
    $check_group_sql = "SELECT COUNT(*) AS count FROM `Groups` WHERE GroupID = ?";
    $stmt = $conn->prepare($check_group_sql);
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        // Insert the suggestion into the database
        $insert_sql = "INSERT INTO suggestions (MemberID, Event, Suggestion, Date, Time, Votes) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $suggestion = "$event_name - $event_place";
        $stmt->bind_param("iisssi", $user_id, $group_id, $suggestion, $event_date, $event_time, $votes);

        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit();
        } else {
            $message = "Error: " . $stmt->error;
        }
    } else {
        $message = "Error: Group ID $group_id does not exist.";
    }
}

// Handle voting
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['vote']) && isset($_POST['suggestion_id']) && isset($_POST['token'])) {
    if (hash_equals($_SESSION['token'], $_POST['token'])) {
        $suggestion_id = intval($_POST['suggestion_id']);
        $vote_sql = "UPDATE suggestions SET Votes = Votes + 1 WHERE SuggestionID = ?";
        $stmt = $conn->prepare($vote_sql);
        $stmt->bind_param("i", $suggestion_id);

        if ($stmt->execute()) {
            $_SESSION['token'] = bin2hex(random_bytes(32)); // Regenerate token
            header("Location: " . $_SERVER['PHP_SELF'] . "?vote_success=1");
            exit();
        } else {
            $message = "Error recording vote: " . $stmt->error;
        }
    } else {
        $message = "Invalid token. Vote not recorded.";
    }
}

// Fetch suggestions for groups the user is part of
$suggestions_sql = "
    SELECT 
        S.SuggestionID, 
        S.Event, 
        S.Suggestion, 
        S.Date, 
        S.Time, 
        S.Votes, 
        S.MemberID
    FROM 
        suggestions S
    INNER JOIN 
        GroupMembers GM ON S.Event = GM.GroupID
    WHERE 
        GM.MemberID = ?
    ORDER BY 
        S.SuggestionID DESC
";
$stmt = $conn->prepare($suggestions_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$suggestions_result = $stmt->get_result();
?>
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
        h3 {
            color: #9e34eb;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #9e34eb;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #7a29b8;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <header>
        <h1>Organize Group Events</h1>
    </header>
    <div class="container">
        <!-- Suggest an Event -->
        <div class="event-form">
            <h3>Suggest an Event</h3>
            <?php if (isset($_GET['success'])): ?>
                <p class="success">Suggestion added successfully!</p>
            <?php elseif (isset($message)): ?>
                <p class="error"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>
            <form method="post">
                <input type="number" name="group_id" placeholder="Group ID" required>
                <input type="text" name="event_name" placeholder="Event Name" required>
                <input type="date" name="event_date" required>
                <input type="time" name="event_time" required>
                <input type="text" name="event_place" placeholder="Event Place" required>
                <button type="submit">Suggest</button>
            </form>
        </div>

        <!-- Vote on Suggestions -->
        <div class="vote-form">
            <h3>Vote on Suggestions</h3>
            <?php if (isset($_GET['vote_success'])): ?>
                <p class="success">Vote recorded successfully!</p>
            <?php endif; ?>
            <?php if ($suggestions_result->num_rows > 0): ?>
                <?php while ($row = $suggestions_result->fetch_assoc()): ?>
                    <div>
                        <p><strong>Group ID:</strong> <?= htmlspecialchars($row['Event']) ?></p>
                        <p><strong>Suggestion:</strong> <?= htmlspecialchars($row['Suggestion']) ?></p>
                        <p><strong>Date:</strong> <?= htmlspecialchars($row['Date']) ?></p>
                        <p><strong>Time:</strong> <?= htmlspecialchars($row['Time']) ?></p>
                        <p><strong>Votes:</strong> <?= htmlspecialchars($row['Votes']) ?></p>
                        <p><strong>Suggested By (Member ID):</strong> <?= htmlspecialchars($row['MemberID']) ?></p>
                        <form method="post">
                            <input type="hidden" name="suggestion_id" value="<?= $row['SuggestionID'] ?>">
                            <input type="hidden" name="token" value="<?= $token ?>">
                            <button type="submit" name="vote">Vote for this Suggestion</button>
                        </form>
                    </div>
                    <hr>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No suggestions available for your groups.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
