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

// Fetch Groups Data
$groupsQuery = "SELECT GroupID, GroupName, Description, CreationDate FROM `Groups`";
$result = $conn->query($groupsQuery);

if ($result->num_rows > 0) {
    $groups = [];
    while ($row = $result->fetch_assoc()) {
        $groupID = $row['GroupID'];

        // Fetch the member names
        $membersQuery = "
            SELECT CONCAT(u.FirstName, ' ', u.LastName) AS MemberName
            FROM `GroupMembers` gm
            JOIN `Member` u ON gm.MemberID = u.MemberID
            WHERE gm.GroupID = $groupID
        ";
        $membersResult = $conn->query($membersQuery);
        $members = [];

        if ($membersResult->num_rows > 0) {
            while ($member = $membersResult->fetch_assoc()) {
                $members[] = $member['MemberName'];
            }
        }

        $row['Members'] = $members;
        $groups[] = $row;
    }
} else {
    die("No group data found.");
}

// Prepare text file content
$content = "Group Report\n\n";
foreach ($groups as $group) {
    $content .= "Group ID: " . $group['GroupID'] . "\n";
    $content .= "Group Name: " . $group['GroupName'] . "\n";
    $content .= "Description: " . $group['Description'] . "\n";
    $content .= "Creation Date: " . $group['CreationDate'] . "\n";
    $content .= "Members: " . implode(', ', $group['Members']) . "\n";
    $content .= str_repeat("-", 40) . "\n";
}

// Set filename
$filename = "Groups_Report_" . date('Y-m-d') . ".txt";

// Send file as a download
header('Content-Type: text/plain');
header("Content-Disposition: attachment; filename=\"{$filename}\"");
header('Cache-Control: max-age=0');

echo $content;

// Close connection
$conn->close();
exit;
?>
