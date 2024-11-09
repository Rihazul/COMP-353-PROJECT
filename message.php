<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
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
            display: flex;
        }
        .friends-list {
            width: 25%;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #ccc;
            padding: 10px;
            overflow-y: auto;
            max-height: 500px;
        }
        .friends-list h3 {
            color: #9e34eb;
            text-align: center;
        }
        .friend {
            padding: 10px;
            border-bottom: 1px solid #ccc;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s;
        }
        .friend:hover {
            background-color: #e0d4f7;
        }
        .chat-box {
            width: 70%;
            margin-left: 5%;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px #ccc;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            max-height: 500px;
        }
        .chat-history {
            overflow-y: auto;
            flex-grow: 1;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .message {
            padding: 10px;
            border-radius: 10px;
            margin: 5px 0;
            max-width: 70%;
        }
        .message.sent {
            background-color: #9e34eb;
            color: white;
            align-self: flex-end;
        }
        .message.received {
            background-color: #f1f1f1;
            color: #333;
            align-self: flex-start;
        }
        .message-form {
            display: flex;
            gap: 10px;
        }
        .message-form input[type="text"] {
            flex-grow: 1;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .message-form button {
            padding: 10px 20px;
            background-color: #9e34eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .message-form button:hover {
            background-color: #7a29b8;
        }
    </style>
</head>
<body>
    <header>
        <h1>Messages</h1>
    </header>

    <div class="container">
        <!-- Friends List Sidebar -->
        <div class="friends-list">
            <h3>Your Friends</h3>
            <?php
            // Replace this with actual friend data fetched from the database
            $friends = [
                ["id" => 1, "name" => "Alice"],
                ["id" => 2, "name" => "Bob"],
                ["id" => 3, "name" => "Charlie"]
            ];
            foreach ($friends as $friend) {
                echo "<div class='friend' onclick='openChat(" . $friend['id'] . ", \"" . $friend['name'] . "\")'>";
                echo "<strong>" . htmlspecialchars($friend['name']) . "</strong>";
                echo "</div>";
            }
            ?>
        </div>

        <!-- Chat Box -->
        <div class="chat-box">
            <div class="chat-history" id="chat-history">
                <!-- Chat history will be dynamically loaded here -->
            </div>

            <!-- Message Form -->
            <form class="message-form" id="message-form" action="send_message.php" method="post">
                <input type="hidden" name="recipient_id" id="recipient_id">
                <input type="text" name="message_text" id="message_text" placeholder="Type your message..." required>
                <button type="submit">Send</button>
            </form>
        </div>
    </div>

    <script>
        let currentRecipientId = null;
        let currentRecipientName = "";

        // Function to open a chat with a friend
        function openChat(friendId, friendName) {
            currentRecipientId = friendId;
            currentRecipientName = friendName;
            document.getElementById('recipient_id').value = friendId;
            document.getElementById('chat-history').innerHTML = `<h3>Chat with ${friendName}</h3>`;
            loadChatHistory(friendId);
        }

        // Load chat history for a selected friend
        function loadChatHistory(friendId) {
            fetch(`fetch_chat_history.php?friend_id=${friendId}`)
                .then(response => response.json())
                .then(messages => {
                    const chatHistory = document.getElementById('chat-history');
                    chatHistory.innerHTML = `<h3>Chat with ${currentRecipientName}</h3>`;
                    messages.forEach(message => {
                        const messageDiv = document.createElement('div');
                        messageDiv.classList.add('message');
                        messageDiv.classList.add(message.sender_id === friendId ? 'received' : 'sent');
                        messageDiv.textContent = message.message_text;
                        chatHistory.appendChild(messageDiv);
                    });
                    chatHistory.scrollTop = chatHistory.scrollHeight;
                });
        }

        // Event listener to send a message without reloading
        document.getElementById('message-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const messageText = document.getElementById('message_text').value;
            const recipientId = document.getElementById('recipient_id').value;

            fetch('send_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `recipient_id=${recipientId}&message_text=${encodeURIComponent(messageText)}`
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    document.getElementById('message_text').value = '';
                    loadChatHistory(recipientId);
                } else {
                    alert("Error sending message");
                }
            });
        });
    </script>
</body>
</html>
