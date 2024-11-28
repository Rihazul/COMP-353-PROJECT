<?php
session_start();

// Mock user session for demonstration
$_SESSION['user_id'] = 1; // Replace with dynamic session data

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
while ($row = $friends_result->fetch_assoc()) {
    $friends[] = $row;
}

// Handle AJAX requests for chat history
if (isset($_GET['action']) && $_GET['action'] === 'fetch_chat_history') {
    $friend_id = intval($_GET['friend_id']);

    $chat_query = "
        SELECT SenderMemberID AS sender_id, RecipientMemberID AS recipient_id, MessageText AS message_text, DateSent AS date_sent
        FROM Messages
        WHERE (SenderMemberID = ? AND RecipientMemberID = ?)
           OR (SenderMemberID = ? AND RecipientMemberID = ?)
        ORDER BY DateSent ASC";
    $stmt = $conn->prepare($chat_query);
    $stmt->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);
    $stmt->execute();
    $chat_result = $stmt->get_result();

    $messages = [];
    while ($row = $chat_result->fetch_assoc()) {
        $messages[] = $row;
    }

    echo json_encode($messages);
    exit();
}

// Handle AJAX requests for sending messages
if (isset($_POST['action']) && $_POST['action'] === 'send_message') {
    $recipient_id = intval($_POST['recipient_id']);
    $message_text = trim($_POST['message_text']);

    if (!empty($message_text)) {
        $send_query = "INSERT INTO Messages (SenderMemberID, RecipientMemberID, MessageText, DateSent) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($send_query);
        $stmt->bind_param("iis", $user_id, $recipient_id, $message_text);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send message.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Message text is empty.']);
    }
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
        .message.sent { background-color: #9e34eb; color: white; align-self: flex-end; }
        .message.received { background-color: #f1f1f1; color: #333; align-self: flex-start; }
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
            <?php foreach ($friends as $friend): ?>
                <div class="friend" onclick="openChat(<?php echo $friend['MemberID']; ?>, '<?php echo htmlspecialchars($friend['Pseudonym']); ?>')">
                    <strong><?php echo htmlspecialchars($friend['Pseudonym']); ?></strong>
                </div>
            <?php endforeach; ?>
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
        let currentRecipientId = null;

        function openChat(friendId, friendName) {
            currentRecipientId = friendId;
            document.getElementById('recipient_id').value = friendId;
            loadChatHistory(friendId);
        }

        function loadChatHistory(friendId) {
            fetch(`?action=fetch_chat_history&friend_id=${friendId}`)
                .then(response => response.json())
                .then(messages => {
                    const chatHistory = document.getElementById('chat-history');
                    chatHistory.innerHTML = `<h3>Chat with ${friendName}</h3>`;
                    messages.forEach(msg => {
                        const div = document.createElement('div');
                        div.className = `message ${msg.sender_id === friendId ? 'received' : 'sent'}`;
                        div.textContent = msg.message_text;
                        chatHistory.appendChild(div);
                    });
                    chatHistory.scrollTop = chatHistory.scrollHeight;
                });
        }

        document.getElementById('message-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const messageText = document.getElementById('message_text').value;

            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=send_message&recipient_id=${currentRecipientId}&message_text=${encodeURIComponent(messageText)}`
            })
            .then(response => response.json())
            .then(result => {
                const chatHistory = document.getElementById('chat-history');
                if (result.success) {
                    const div = document.createElement('div');
                    div.className = 'message sent';
                    div.textContent = messageText;
                    chatHistory.appendChild(div);
                    chatHistory.scrollTop = chatHistory.scrollHeight;
                    document.getElementById('message_text').value = '';
                } else {
                    const div = document.createElement('div');
                    div.className = 'message error';
                    div.textContent = result.message || 'Failed to send message.';
                    chatHistory.appendChild(div);
                }
            });
        });
    </script>
</body>
</html>
