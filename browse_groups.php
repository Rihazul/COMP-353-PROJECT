<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groups</title>
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
        .logout-button {
            background-color: #fff;
            color: #9e34eb;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .logout-button:hover {
            background-color: #e0d4f7;
        }
        .groups-container {
            width: 900px;
            margin: 30px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #9e34eb;
            text-align: center;
            margin-bottom: 20px;
        }
        .group-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
        }
        .group-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .group-image {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            object-fit: cover;
        }
        .group-details {
            font-size: 16px;
        }
        .join-button {
            background-color: #9e34eb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .join-button:hover {
            background-color: #7a29b8;
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div id="purple_bar">
        <div style="font-size: 45px; font-weight: bold;">
            COSN
        </div>
        <button class="logout-button" onclick="window.location.href='login.php'">Log out</button>
    </div>

    <!-- Groups Container -->
    <div class="groups-container">
        <h2>Available Groups</h2>
        <!-- Group 1 -->
        <div class="group-card">
            <div class="group-info">
                <img src="https://via.placeholder.com/60" class="group-image" alt="Group Image">
                <div class="group-details">
                    <strong>Group Name 1</strong>
                    <p>Description of the group. Something about its purpose.</p>
                </div>
            </div>
            <button class="join-button" onclick="joinGroup(1)">Join</button>
        </div>
        <!-- Group 2 -->
        <div class="group-card">
            <div class="group-info">
                <img src="https://via.placeholder.com/60" class="group-image" alt="Group Image">
                <div class="group-details">
                    <strong>Group Name 2</strong>
                    <p>Description of the group. Something about its purpose.</p>
                </div>
            </div>
            <button class="join-button" onclick="joinGroup(2)">Join</button>
        </div>
        <!-- Group 3 -->
        <div class="group-card">
            <div class="group-info">
                <img src="https://via.placeholder.com/60" class="group-image" alt="Group Image">
                <div class="group-details">
                    <strong>Group Name 3</strong>
                    <p>Description of the group. Something about its purpose.</p>
                </div>
            </div>
            <button class="join-button" onclick="joinGroup(3)">Join</button>
        </div>
    </div>

    <script>
        function joinGroup(groupId) {
            // Implement AJAX call to join the group
            alert('Request to join group ' + groupId + ' sent.');
        }
    </script>
</body>
</html>
