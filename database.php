<?php
// Database connection
$servername = "upc353.encs.concordia.ca";
$username = "upc353_2";
$password = "SleighParableSystem73";
$dbname = "upc353_2";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("<p class='error'>Connection failed: " . htmlspecialchars($conn->connect_error) . "</p>");
}

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
    // Fetch all tables from the database
    $tables_query = "SHOW TABLES";
    $tables_result = $conn->query($tables_query);

    if ($tables_result) {
        while ($table_row = $tables_result->fetch_array()) {
            $table_name = $table_row[0];
            $display_name = htmlspecialchars($table_name); // Sanitize for display

            echo "<h2>Table: $display_name</h2>";

            // Fetch columns for the current table
            $columns_query = "SHOW COLUMNS FROM `$table_name`";
            $columns_result = $conn->query($columns_query);

            if ($columns_result) {
                $columns = [];
                while ($column = $columns_result->fetch_assoc()) {
                    $columns[] = $column['Field'];
                }

                if (count($columns) > 0) {
                    // Fetch data from the current table
                    // To handle tables with special characters or reserved words, use backticks
                    $select_query = "SELECT * FROM `$table_name`";
                    $data_result = $conn->query($select_query);

                    if ($data_result && $data_result->num_rows > 0) {
                        echo "<table>";
                        // Display table headers
                        echo "<tr>";
                        foreach ($columns as $column) {
                            echo "<th>" . htmlspecialchars($column) . "</th>";
                        }
                        echo "</tr>";

                        // Display table rows
                        while ($row = $data_result->fetch_assoc()) {
                            echo "<tr>";
                            foreach ($columns as $column) {
                                // Handle NULL values
                                $cell = isset($row[$column]) ? $row[$column] : "NULL";
                                echo "<td>" . htmlspecialchars($cell) . "</td>";
                            }
                            echo "</tr>";
                        }
                        echo "</table>";

                        // Free data result set
                        $data_result->free();
                    } else {
                        // Display message if table is empty
                        echo "<p class='empty-message'>No data found in table <strong>$display_name</strong>.</p>";
                    }

                    // Free columns result set
                    $columns_result->free();
                } else {
                    echo "<p class='empty-message'>No columns found in table <strong>$display_name</strong>.</p>";
                }
            } else {
                echo "<p class='error'>Error fetching columns for table <strong>$display_name</strong>: " . htmlspecialchars($conn->error) . "</p>";
            }
        }

        // Free tables result set
        $tables_result->free();
    } else {
        echo "<p class='error'>Error fetching tables: " . htmlspecialchars($conn->error) . "</p>";
    }

    // Close the database connection
    $conn->close();
    ?>
</body>
</html>
