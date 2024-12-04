<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Database connection
$servername = "upc353.encs.concordia.ca";
$username = "upc353_2";
$password = "SleighParableSystem73";
$dbname = "upc353_2";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to add a participant to a group
function addParticipant($conn, $groupID, $memberID1, $post) {
    $groupID = $conn->real_escape_string($groupID);
    $memberID1 = $conn->real_escape_string($memberID1);
    $post = $conn->real_escape_string($post);

    // Randomly select a `MemberID2` from the same group, excluding the new participant
    $result = $conn->query("SELECT MemberID1 FROM Gift_exchange WHERE groupID = '$groupID' AND MemberID1 != '$memberID1'");
    $members = $result->fetch_all(MYSQLI_ASSOC);

    $memberID2 = $members ? $members[array_rand($members)]['MemberID1'] : NULL;

    // Insert participant with `MemberID2` (or NULL if no other participants)
    $query = "INSERT INTO Gift_exchange (groupID, MemberID1, MemberID2, Post) VALUES ('$groupID', '$memberID1', " . ($memberID2 ? "'$memberID2'" : "NULL") . ", '$post')";
    return $conn->query($query);
}

// Function to assign gift receivers within a group
function assignReceivers($conn, $groupID) {
    $groupID = $conn->real_escape_string($groupID);
    $result = $conn->query("SELECT MemberID1 FROM Gift_exchange WHERE groupID = '$groupID'");
    $members = $result->fetch_all(MYSQLI_ASSOC);

    if (count($members) > 1) {
        shuffle($members);
        $count = count($members);
        for ($i = 0; $i < $count; $i++) {
            $giver = $members[$i]['MemberID1'];
            $receiver = $members[($i + 1) % $count]['MemberID1'];
            $conn->query("UPDATE Gift_exchange SET MemberID2='$receiver' WHERE groupID='$groupID' AND MemberID1='$giver'");
        }
        return true;
    }
    return false;
}

// Handle form submissions
$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['send_gift'], $_POST['groupID'], $_POST['memberID1'], $_POST['memberID2'])) {
        $groupID = $_POST['groupID'];
        $memberID1 = $_POST['memberID1'];
        $memberID2 = $_POST['memberID2'];

        // Mark the gift as sent
        $query = "UPDATE Gift_exchange SET sent_status=TRUE WHERE groupID='$groupID' AND MemberID1='$memberID1' AND MemberID2='$memberID2'";
        if ($conn->query($query)) {
            $message = "Gift sent successfully!";
        } else {
            $message = "Error sending gift.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gift Exchange - COSN</title>
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
        .container {
            width: 900px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #000;
            background-color: white;
        }
        header {
            text-align: center;
            padding: 20px;
            background-color: #9e34eb;
            color: white;
            border-radius: 10px 10px 0 0;
        }
        main {
            padding: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }
        form label {
            margin-top: 10px;
            font-weight: bold;
        }
        form input[type="text"],
        form input[type="number"],
        form textarea {
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: #f0f0f0; /* Background color for input fields */
        }
        form input[type="submit"] {
            padding: 10px;
            background: #9e34eb;
            color: #fff;
            border: 0;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
        form input[type="submit"]:hover {
            background: #7a29b8;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
            color: #333;
        }
    </style>
      <script>
        function logout() {
            localStorage.clear();
            fetch('logout.php', { method: 'POST' })
                .then(() => {
                    window.location.href = 'login.php';
                })
                .catch(error => console.error('Error logging out:', error));
        }
    </script>
</head>
<body>
    <!-- top purple bar -->    
    <div id="purple_bar">
        <div style="font-size: 45px; font-weight: bold;">
            COSN
        </div>
        <div class="search-wrapper">
            <input type="text" name="search" id="search_box" placeholder="Search for settings">
            <div class="search-icon"></div>
        </div>
        <button class="logout-button" onclick="logout()">Log out</button>
    </div>

    <div class="container">
        <header>
            <h1>Gift Exchange</h1>
        </header>
        <main>
        <form action="gift_exchange.php" method="post">
            <label for="groupID">Group ID:</label>
            <input type="number" id="groupID" name="groupID" required>

            <label for="memberID1">Your Member ID:</label>
            <input type="number" id="memberID1" name="memberID1" required>

            <label for="memberID2">Send Gift To (Receiver ID):</label>
            <input type="number" id="memberID2" name="memberID2" required>

            <label for="post">Post:</label>
            <textarea id="post" name="post" rows="4" required></textarea>

            <input type="submit" name="send_gift" value="Send Gift">
        </form>

            <form action="gift_exchange.php" method="post">
                <label for="groupID">Group ID:</label>
                <input type="number" id="groupID" name="groupID" required>

                <input type="submit" name="assign_receivers" value="Assign Gift Receivers">
            </form>

            <?php if (!empty($message)): ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>