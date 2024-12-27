<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "conference";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <h1>Admin Dashboard</h1>

    <section id="participant-management">
        <h2>Participant Management</h2>
        <table border="1">
            <tr>
                <th>Participant_ID</th>
                <th>Name</th>
                <th>Email</th>
               <!-- <th>Qr code</th> -->
                <th>Session Preferences</th>
                <th>Actions</th>
            </tr>
            <?php
            $result = $conn->query("SELECT id, name, email, session FROM registration");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                   // echo "<td>" . $row['QR_code'] . "</td>";
                    echo "<td>" . $row['session'] . "</td>";
                    echo "<td><a href='delete_track.php?id=" . $row['id'] . "'>Delete</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No participants found</td></tr>";
            }
            ?>
        </table>
    </section>

    <section id="track-management">
        <h2>Track and Session Management</h2>
        <form action="add_track.php" method="POST">
            <h3>Add Track/Session</h3>
            <label for="track_name">Track Name:</label>
            <input type="text" id="track_name" name="track_name" required><br>

            <label for="track_id">Track ID:</label>
            <input type="text" id="track_id" name="track_id" required><br>


            <label for="session_name">Session Name:</label>
            <input type="text" id="session_name" name="session_name" required><br>

            <label for="speaker">Speaker:</label>
            <input type="text" id="speaker" name="speaker" required><br>

            <label for="timing">Timing:</label>
            <input type="datetime-local" id="timing" name="timing" required><br>

            <label for="venue">Venue:</label>
            <input type="text" id="venue" name="venue" required><br>

            <button type="submit">Add Track/Session</button>
        </form>

        <h3>Existing Tracks and Sessions</h3>
        <table border="1">
            <tr>
                <th>Session_ID</th>
                <th>Session_name</th>
                <th>Track_Name</th>
                <th>Speaker</th>
                <th>Timing</th>
                <th>Venue</th>
                <th>Actions</th>
            </tr>
            <?php
            $result = $conn->query("SELECT * FROM tracks_and_sessions");

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['session_id'] . "</td>";
                    echo "<td>" . $row['session_name'] . "</td>";
                    echo "<td>" . $row['track_name'] . "</td>";
                    echo "<td>" . $row['speaker'] . "</td>";
                    echo "<td>" . $row['timing'] . "</td>";
                    echo "<td>" . $row['venue'] . "</td>";
                    echo "<td><a href='delete_track.php?id=" . $row['track_id'] . "'>Delete</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No tracks/sessions found</td></tr>";
            }
            ?>
        </table>
    </section>

    <section id="proceedings">
        <h2>Proceedings Sharing</h2>
        <form action="upload_proceeding.php" method="POST" enctype="multipart/form-data">
            <label for="proceeding">Upload Proceedings (PDF):</label>
            <input type="file" id="proceeding" name="proceeding" accept="application/pdf" required><br>
            <button type="submit">Upload</button>
        </form>

        <h3>Available Proceedings</h3>
        <ul>
            <?php
            $dir = 'proceedings/';
            if (is_dir($dir)) {
                $files = scandir($dir);
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        echo "<li><a href='$dir$file' target='_blank'>$file</a></li>";
                    }
                }
            } else {
                echo "<li>No proceedings uploaded yet</li>";
            }
            ?>
        </ul>
    </section>
</body>
<footer>
    <div class="end">
        <a href="../login.html">Logout</a>
        <a href="../index.html">Home</a>
    </div>
</footer>
</html>

<?php
$conn->close();

?>
