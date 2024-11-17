<?php
include 'config.php'; // Include database connection

// Query to fetch all user information
$sql = "SELECT * FROM Member";
$result = $conn->query($sql);

// Check if users exist and output the data
if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr>";
    echo "<th>MemberID</th>";
    echo "<th>FirstName</th>";
    echo "<th>LastName</th>";
    echo "<th>Address</th>";
    echo "<th>Email</th>";
    echo "<th>DOB</th>";
    echo "<th>Pseudonym</th>";
    echo "<th>Privilege</th>";
    echo "<th>Status</th>";
    echo "<th>PublicInfoVisibilitySettings</th>";
    echo "<th>SecretSanta</th>";
    echo "</tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['MemberID'] . "</td>";
        echo "<td>" . $row['FirstName'] . "</td>";
        echo "<td>" . $row['LastName'] . "</td>";
        echo "<td>" . $row['Address'] . "</td>";
        echo "<td>" . $row['Email'] . "</td>";
        echo "<td>" . $row['DOB'] . "</td>";
        echo "<td>" . $row['Pseudonym'] . "</td>";
        echo "<td>" . $row['Privilege'] . "</td>";
        echo "<td>" . $row['Status'] . "</td>";
        echo "<td>" . $row['PublicInfoVisibilitySettings'] . "</td>";
        echo "<td>" . ($row['SecretSanta'] ? 'Yes' : 'No') . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>No users found in the database.</p>";
}

// Close the connection
$conn->close();
?>
