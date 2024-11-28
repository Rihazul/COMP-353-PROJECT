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

// Tables to fetch
$tables = [
    "Member" => ["MemberID", "Password", "FirstName", "LastName", "Address", "Email", "DOB", "Pseudonym", "Privilege", "Status", "PublicInfoVisibilitySettings", "SecretSanta"],
    "`Groups`" => ["GroupID", "GroupName", "Description", "CreationDate"],
    "GroupMembers" => ["GroupID", "MemberID", "Role", "DateJoined"],
    "Messages" => ["MessageID", "SenderMemberID", "RecipientMemberID", "Content", "DateSent"],
    "Posts" => ["PostID", "MemberID", "GroupID", "TextContent", "AttachmentContent", "DatePosted", "ModerationStatus"],
    "Comments" => ["CommentID", "PostID", "MemberID", "Content", "DatePosted"],
    "Friends" => ["MemberID1", "MemberID2", "DateStarted", "Status"],
    "Notifications" => ["ContentID", "MessageID", "Date", "MemberID"],
    "`Event`" => ["EventID", "CreationDate", "MemberID"],
    "Event_vote" => ["EventID", "VoteDate", "Suggestions", "MemberID"],
    "Gift_exchange" => ["GroupID", "MemberID1", "MemberID2", "Post"],
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Viewer</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        h1, h2 { color: #333; }
        .empty-message { color: red; font-style: italic; }
    </style>
</head>
<body>
    <h1>Database Viewer</h1>
    <?php
    foreach ($tables as $table_name => $columns) {
        $display_name = str_replace("`", "", $table_name); // Remove backticks for display
        echo "<h2>Table: $display_name</h2>";

        // Fetch data from the current table
        $query = "SELECT " . implode(", ", $columns) . " FROM $table_name";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            echo "<table>";
            // Display table headers
            echo "<tr>";
            foreach ($columns as $column) {
                echo "<th>" . htmlspecialchars($column) . "</th>";
            }
            echo "</tr>";

            // Display table rows
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($columns as $column) {
                    echo "<td>" . htmlspecialchars($row[$column] ?? "") . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            // Display message if table is empty
            echo "<p class='empty-message'>No data found in table $display_name.</p>";
        }
    }

    $conn->close();
    ?>
</body>
</html>
