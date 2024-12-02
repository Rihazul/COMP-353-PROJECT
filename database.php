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
    "Notifications" => ["ContentID", "MessageID", "Date", "MemberID", "NotificationContent"],
    "`Event`" => ["Event", "MemberID"],
    "suggestions" => ["SuggestionID", "MemberID", "Event", "Suggestion", "Date", "Time", "Votes"],
    "Gift_exchange" => ["GroupID", "MemberID1", "MemberID2", "Post"],
];

// Handle SQL Command Execution
$sql_output = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['sql_command'])) {
    $sql_command = trim($_POST['sql_command']);

    if (!empty($sql_command)) {
        // Execute the SQL command
        if ($result = $conn->query($sql_command)) {
            // Check if the query returns a result set (e.g., SELECT)
            if ($result instanceof mysqli_result) {
                // Start building the HTML table
                $sql_output .= "<h3>Query Results:</h3>";
                $sql_output .= "<table>";
                
                // Fetch and display column headers
                $sql_output .= "<tr>";
                while ($field = $result->fetch_field()) {
                    $sql_output .= "<th>" . htmlspecialchars($field->name) . "</th>";
                }
                $sql_output .= "</tr>";
                
                // Fetch and display rows
                while ($row = $result->fetch_assoc()) {
                    $sql_output .= "<tr>";
                    foreach ($row as $cell) {
                        $sql_output .= "<td>" . htmlspecialchars($cell) . "</td>";
                    }
                    $sql_output .= "</tr>";
                }
                $sql_output .= "</table>";

                // Free result set
                $result->free();
            } else {
                // For queries that do not return a result set (e.g., INSERT, UPDATE)
                $sql_output .= "<p class='success'>Query executed successfully. Affected Rows: " . $conn->affected_rows . "</p>";
            }
        } else {
            // If the query failed, display the error
            $sql_output .= "<p class='error'>Error executing query: " . htmlspecialchars($conn->error) . "</p>";
        }
    } else {
        $sql_output .= "<p class='error'>Please enter an SQL command.</p>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Viewer with SQL Command Line</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f2f2f2; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #4CAF50; color: white; }
        h1, h2, h3 { color: #333; }
        .empty-message { color: red; font-style: italic; }
        .sql-section { background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .sql-section textarea { width: 100%; height: 100px; padding: 10px; border: 1px solid #ccc; border-radius: 4px; resize: vertical; }
        .sql-section input[type="submit"] { padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .sql-section input[type="submit"]:hover { background-color: #45a049; }
        .message { padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        /* Responsive Design */
        @media (max-width: 600px) {
            th, td { font-size: 14px; }
            .sql-section textarea { height: 80px; }
        }
    </style>
</head>
<body>
    <h1>Database Viewer</h1>
    
    <!-- SQL Command-Line Interface -->
    <div class="sql-section">
        <h2>SQL Command Line</h2>
        <form method="POST" action="">
            <textarea name="sql_command" placeholder="Enter your SQL command here..."><?php if(isset($_POST['sql_command'])) echo htmlspecialchars($_POST['sql_command']); ?></textarea><br><br>
            <input type="submit" value="Execute SQL">
        </form>
        <?php
            if (!empty($sql_output)) {
                echo $sql_output;
            }
        ?>
    </div>

    <!-- Display Tables and Their Data -->
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
