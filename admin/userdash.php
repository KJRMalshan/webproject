<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$database = "conference";

// Create a connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participant Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Participant Dashboard</h1>

        <!-- Display Schedules -->
        <section>
            <h2>Schedules</h2>
            <?php
            $result = $conn->query("SELECT * FROM tracks_and_sessions ORDER BY timing ASC");

            if ($result->num_rows > 0) {
                echo "<ul>";
                while ($row = $result->fetch_assoc()) {
                    echo "<li><strong>" . $row['session_name'] . "</strong> - " . $row['timing'] . " | Title: " . $row['title'] . " | Speaker: " . $row['speaker'] . " | Venue: " . $row['venue'] . "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No schedules available.</p>";
            }
            ?>
        </section>       
    </div>
</body>
</html>

<?php
$conn->close();
?>

