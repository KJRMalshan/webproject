<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QR Code Generator</title>
  <link rel="stylesheet" href="../qr/qr.css">
  <script type="module" src="../qr/qrmain.js"></script>
</head>
<body>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
    
    // Set default role
     $role = 'user';

    // Get form data
    $name = $_POST['name'];
    $category = $_POST['category'];
    $email = $_POST['email'];
    $userpwd = $_POST['userpwd'];
    $nic = $_POST['nic'];
    $mobile = $_POST['mobile'];
    $country = $_POST['country'];
    $paper_id = $_POST['paper_id'];
    $tracks = isset($_POST['tracks']) ? implode(", ", $_POST['tracks']) : "";
    $food_preference = $_POST['food_preference'];

    // Check for duplicate entry
    $check_stmt = $conn->prepare("SELECT * FROM registration WHERE email = ? OR nic = ?");
    $check_stmt->bind_param("ss", $email, $nic);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "Error: A user with the same email or NIC already exists.";
        $check_stmt->close();
        $conn->close();
        exit();
    }
    $check_stmt->close();

    // Handle file upload
    $payment_proof = '';
    if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] == 0) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = basename($_FILES['payment_proof']['name']);
        $target_file = $upload_dir . time() . "_" . $file_name;

        if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $target_file)) {
            $payment_proof = $target_file;
        } else {
            die("Error uploading payment proof.");
        }
    }

    // Prepare SQL query
    // Hash the password before storing it
    //$hashed_password = password_hash($userpwd, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO registration (role, name, category, email, userpwd, nic, mobile, country, paper_id, tracks, food_preference, payment_proof) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssss", $role, $name, $category, $email, $userpwd, $nic, $mobile, $country, $paper_id, $tracks, $food_preference, $payment_proof);

    // Execute and check
    if ($stmt->execute()) {
        $last_id = $conn->insert_id; // Get the last inserted ID

        // Generate QR data
        $qrData = htmlspecialchars("http://localhost/conference/details.php?id=$last_id", ENT_QUOTES, 'UTF-8');

        echo "Data submitted successfully!<br>";
        echo "<canvas id='qrcode'></canvas><br>";
        echo "<a id='downloadQR' href='#' download='qr_code.png'>Download QR Code</a><br>";
        echo "<script>window.qrData = '$qrData';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();

    // Display the ID
    $id = $last_id;
    $stmt = $conn->prepare("SELECT id FROM registration WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<strong>ID:</strong> " . $row['id'] . "<br>";
    } else {
        echo "No record found for ID: $id";
    }

    $stmt->close();
}
$conn->close();
?>
<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
<script>
    const qrData = window.qrData;
    const qrCanvas = document.getElementById("qrcode");
    const downloadLink = document.getElementById("downloadQR");

    if (qrData) {
        QRCode.toCanvas(qrCanvas, qrData, { width: 256 }, function (error) {
            if (!error) {
                const qrImage = qrCanvas.toDataURL("image/png");
                downloadLink.href = qrImage;
            }
        });
    }
</script>

<div class="container">
    <header class="header">
      <button id="btn-back" class="btn btn-back">
        ← Back
      </button>
      <button class="btn btn-login">
        Login →
      </button>
    </header>

    <main class="content">
      <h1 class="title">Your QR Code</h1>
      <p class="id">ID: <span id="qr-id"></span></p>
      
      <div class="qr-container">
        <img id="qr-image" alt="QR Code" class="qr-code">
      </div>

      <button id="btn-download" class="btn btn-download">
        ↓ Download QR Code
      </button>
    </main>
  </div>
</body>
</html>

