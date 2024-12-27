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
            $result = $conn->query("SELECT * FROM schedules ORDER BY start_time ASC");

            if ($result->num_rows > 0) {
                echo "<ul>";
                while ($row = $result->fetch_assoc()) {
                    echo "<li><strong>" . $row['session_title'] . "</strong> - " . $row['start_time'] . " to " . $row['end_time'] . "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No schedules available.</p>";
            }
            ?>
        </section>

        <!-- Display Proceedings -->
        <section>
            <h2>Proceedings</h2>
            <?php
            $result = $conn->query("SELECT * FROM proceedings ORDER BY paper_id ASC");

            if ($result->num_rows > 0) {
                echo "<table border='1'>
                        <tr>
                            <th>Paper ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Download</th>
                        </tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row['paper_id'] . "</td>
                            <td>" . $row['title'] . "</td>
                            <td>" . $row['author'] . "</td>
                            <td><a href='uploads/" . $row['file_path'] . "' download>Download</a></td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No proceedings available.</p>";
            }
            ?>
        </section>

        <!-- Session Registration -->
        <section>
            <h2>Session Registration</h2>
            <form action="register_session.php" method="POST">
                <label for="participant_id">Participant ID:</label>
                <input type="text" id="participant_id" name="participant_id" required><br><br>

                <label for="session_id">Select Session:</label>
                <select id="session_id" name="session_id" required>
                    <?php
                    $result = $conn->query("SELECT * FROM schedules ORDER BY start_time ASC");

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['session_title'] . " (" . $row['start_time'] . " to " . $row['end_time'] . ")</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No sessions available</option>";
                    }
                    ?>
                </select><br><br>

                <button type="submit">Register</button>
            </form>
        </section>
    </div>
</body>
</html>

<?php
$conn->close();
?>

// register_session.php
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $participant_id = $_POST['participant_id'];
    $session_id = $_POST['session_id'];

    // Validate inputs
    if (empty($participant_id) || empty($session_id)) {
        die("Participant ID and Session ID are required.");
    }

    // Check if already registered
    $stmt = $conn->prepare("SELECT * FROM session_registrations WHERE participant_id = ? AND session_id = ?");
    $stmt->bind_param("ii", $participant_id, $session_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "You are already registered for this session.";
    } else {
        // Insert registration
        $stmt = $conn->prepare("INSERT INTO session_registrations (participant_id, session_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $participant_id, $session_id);

        if ($stmt->execute()) {
            echo "Session registered successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
}
$conn->close();
?>
