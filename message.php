

<?php
session_start();
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log'); // Specify the log file location
error_reporting(E_ALL); // Report all PHP errors

// Mock user session for demonstration
 // Replace with dynamic session data
$user_id = $_SESSION['user_id'];

// Database connection
$servername = "upc353.encs.concordia.ca";
$username = "upc353_2";
$password = "SleighParableSystem73";
$dbname = "upc353_2";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch friends for the current user
$user_id = $_SESSION['user_id'];
$friends_query = "
    SELECT M.MemberID, M.Pseudonym 
    FROM Friends F
    INNER JOIN Member M ON (F.MemberID1 = M.MemberID OR F.MemberID2 = M.MemberID)
    WHERE (F.MemberID1 = ? OR F.MemberID2 = ?)
      AND F.Status = 'Accepted'
      AND M.MemberID != ?";
$stmt = $conn->prepare($friends_query);
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$friends_result = $stmt->get_result();
$friends = [];
$friends = [];
while ($row = $friends_result->fetch_assoc()) {
    if (!empty($row['MemberID']) && !empty($row['Pseudonym'])) {
        $friends[] = $row; // Store valid friend records
    }
}
$stmt->close(); // Explicitly close the statement


// Handle AJAX requests for chat history
if (isset($_GET['action']) && $_GET['action'] === 'fetch_chat_history') {
    header('Content-Type: application/json'); // Ensure JSON response

    $friend_id = intval($_GET['friend_id']);
    if ($friend_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid friend ID.']);
        exit();
    }

$chat_query = "
    SELECT SenderMemberID AS sender_id, RecipientMemberID AS recipient_id, Content AS message_text, DateSent AS date_sent
    FROM Messages
    WHERE (SenderMemberID = ? AND RecipientMemberID = ?)
       OR (SenderMemberID = ? AND RecipientMemberID = ?)
    ORDER BY DateSent ASC";


    $stmt = $conn->prepare($chat_query);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Database query preparation failed.']);
        error_log('Database query preparation failed: ' . $conn->error);
        exit();
    }

    $stmt->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Query execution failed.']);
        error_log('Query execution failed: ' . $stmt->error);
        exit();
    }

    $chat_result = $stmt->get_result();
    if ($chat_result === false) {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch chat history.']);
        error_log('Failed to fetch chat history: ' . $stmt->error);
        exit();
    }

    $messages = [];
    while ($row = $chat_result->fetch_assoc()) {
        $messages[] = $row;
    }

    echo json_encode($messages);
    exit();
}


// Handle AJAX requests for sending messages
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_message') {
    header('Content-Type: application/json'); // Ensure JSON response

    $recipient_id = intval($_POST['recipient_id'] ?? 0);
    $message_text = trim($_POST['message_text'] ?? '');

    if ($recipient_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid recipient ID.']);
        error_log('Invalid recipient ID: ' . $recipient_id);
        exit();
    }

    if (empty($message_text)) {
        echo json_encode(['success' => false, 'message' => 'Message text cannot be empty.']);
        error_log('Empty message text.');
        exit();
    }

$send_query = "INSERT INTO Messages (SenderMemberID, RecipientMemberID, Content, DateSent) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($send_query);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Database query preparation failed.']);
        error_log('Database query preparation failed: ' . $conn->error);
        exit();
    }

    $stmt->bind_param("iis", $user_id, $recipient_id, $message_text);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Failed to send message.']);
        error_log('Failed to execute query: ' . $stmt->error);
        exit();
    }

    echo json_encode(['success' => true]);
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <style>
        body { font-family: Tahoma, sans-serif; background-color: #fac3da; margin: 0; padding: 0; }
        header { background-color: #9e34eb; color: white; padding: 20px; text-align: center; }
        header h1 { margin: 0; }
        .container { width: 80%; margin: auto; padding: 20px; display: flex; }
        .friends-list { width: 25%; background-color: white; border-radius: 10px; box-shadow: 0px 0px 10px 0px #ccc; padding: 10px; overflow-y: auto; max-height: 500px; }
        .friends-list h3 { color: #9e34eb; text-align: center; }
        .friend { padding: 10px; border-bottom: 1px solid #ccc; cursor: pointer; text-align: center; transition: background-color 0.3s; }
        .friend:hover { background-color: #e0d4f7; }
        .chat-box { width: 70%; margin-left: 5%; background-color: white; border-radius: 10px; box-shadow: 0px 0px 10px 0px #ccc; padding: 20px; display: flex; flex-direction: column; justify-content: space-between; max-height: 500px; }
        .chat-history { overflow-y: auto; flex-grow: 1; border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-bottom: 10px; }
        .message { padding: 10px; border-radius: 10px; margin: 5px 0; max-width: 70%; }
.message.sent {
    background-color: #9e34eb; /* Your chosen color for sent messages */
    color: white;
    align-self: flex-end;
}

.message.received {
    background-color: #f1f1f1; /* Grey color for received messages */
    color: #333;
    align-self: flex-start;
}
        .message.error { background-color: #ff4d4d; color: white; align-self: flex-end; font-style: italic; }
        .message-form { display: flex; gap: 10px; }
        .message-form input[type="text"] { flex-grow: 1; padding: 10px; border-radius: 5px; border: 1px solid #ccc; }
        .message-form button { padding: 10px 20px; background-color: #9e34eb; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .message-form button:hover { background-color: #7a29b8; }
		
    </style>
</head>
<body>
    <header>
        <h1>Messages</h1>
    </header>

    <div class="container">
        <div class="friends-list">
            <h3>Your Friends</h3>
<?php if (!empty($friends)): ?>
    <?php foreach ($friends as $friend): ?>
        <div class="friend" onclick="openChat(<?php echo $friend['MemberID']; ?>, '<?php echo htmlspecialchars($friend['Pseudonym']); ?>')">
            <strong><?php echo htmlspecialchars($friend['Pseudonym']); ?></strong>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No friends to display. Start adding friends to chat!</p>
<?php endif; ?>
        </div>

        <div class="chat-box">
            <div class="chat-history" id="chat-history"></div>
            <form class="message-form" id="message-form">
                <input type="hidden" name="recipient_id" id="recipient_id">
                <input type="text" name="message_text" id="message_text" placeholder="Type your message..." required>
                <button type="submit">Send</button>
            </form>
        </div>
    </div>

    <script>
        const currentUserId = <?php echo json_encode($user_id); ?>;

        function openChat(friendId, friendName) {
            currentRecipientId = friendId;
            document.getElementById('recipient_id').value = friendId;
            loadChatHistory(friendId, friendName);
        }

function loadChatHistory(friendId, friendName) {
    fetch(`?action=fetch_chat_history&friend_id=${friendId}`)
        .then(response => response.json())
        .then(messages => {
            const chatHistory = document.getElementById('chat-history');
            chatHistory.innerHTML = `<h3>Chat with ${friendName}</h3>`;
            messages.forEach(msg => {
                const div = document.createElement('div');
                div.className = `message ${msg.sender_id === currentUserId ? 'sent' : 'received'}`;
                div.textContent = msg.message_text;
                chatHistory.appendChild(div);
            });
            chatHistory.scrollTop = chatHistory.scrollHeight;
        });
}


document.getElementById('message-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const messageText = document.getElementById('message_text').value;
    const recipientId = currentRecipientId;

    if (!messageText || !recipientId) {
        alert('Message text or recipient is missing!');
        return;
    }

    fetch('', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=send_message&recipient_id=${recipientId}&message_text=${encodeURIComponent(messageText)}`
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            const chatHistory = document.getElementById('chat-history');
            const div = document.createElement('div');
            div.className = 'message sent';
            div.textContent = messageText;
            chatHistory.appendChild(div);
            chatHistory.scrollTop = chatHistory.scrollHeight;
            document.getElementById('message_text').value = ''; // Clear input
        } else {
            alert(result.message || 'Failed to send message.');
        }
    })
    .catch(error => console.error('Error sending message:', error));
});

    </script>
</body>
</html>
