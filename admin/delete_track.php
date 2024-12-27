<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "conference";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the ID of the row to delete
$id = $_GET['id'];

// SQL to delete a record
$sql = "DELETE FROM tracks_and_sessions WHERE track_id = $id";

if ($conn->query($sql) === TRUE) {
    echo "Record deleted successfully";
} else {
    echo "Error deleting record: " . $conn->error;
}

$conn->close();

// Redirect back to the previous page
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>
