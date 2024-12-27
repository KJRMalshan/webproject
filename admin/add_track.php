<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = ""; // Leave empty for WAMP's default setup
$database = "conference";

// Create a connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $track_name = $_POST['track_name'];
    $session_name = $_POST['session_name'];
    $speaker = $_POST['speaker'];
    $timing = $_POST['timing'];
    $venue = $_POST['venue'];

    // Prepare SQL query
    $stmt = $conn->prepare("INSERT INTO tracks_and_sessions (track_name, session_name, speaker, timing, venue) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $track_name, $session_name, $speaker, $timing, $venue);

    // Execute and check
    if ($stmt->execute()) {
        echo "Track/Session added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>
