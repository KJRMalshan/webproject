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
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL query
    $stmt = $conn->prepare("SELECT * FROM registration WHERE email = ?");
    $stmt->bind_param("s", $email);

    // Execute and check
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User found
        $user = $result->fetch_assoc();
        if ($password == $user['userpwd']) { // Compare plain text passwords
            if ($user['role'] == 'admin' || $password == 'admin') {
                header("Location: ../admin/ademinu.php");
                exit();
            } else {
                // Redirect to user.php
                header("Location: ../dashboard/user.php");
                exit();
            }
        } else {
            echo "Invalid email or password.";
        }
    } else {
        // User not found
        echo "Invalid email or password.";
    }

    $stmt->close();
}

$conn->close();
?>
