<?php
session_start();
include '../db.php';

// Set timezone to Asia/Kolkata (Indian Standard Time)
date_default_timezone_set('Asia/Dhaka');


if ($_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Ensure that user ID is set in the session
if (!isset($_SESSION['user_id'])) {
    die("User is not logged in.");
}

// Fetch services
$services = $conn->query("SELECT * FROM services");

// Debugging query execution
if (!$services) {
    die("Error fetching services: " . $conn->error);
}

// Fetch services
$services = $conn->query("SELECT * FROM services");

// Debugging query execution
if (!$services) {
    die("Error fetching services: " . $conn->error);
}

// Use rowCount() for PDO
if ($services->rowCount() === 0) {
    die("No services found in the database.");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $service_id = $_POST['service_id'];
    $imei = $_POST['imei'] ?? NULL;
    $email = $_POST['email'] ?? NULL;

    $sql = "INSERT INTO orders (service_id, user_id, imei, email, status, created_at)
            VALUES ('$service_id', '$user_id', '$imei', '$email', 'Pending', NOW())";

    // Debugging order query
    if ($conn->query($sql) === TRUE) {
        echo "Order submitted successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Order</title>
</head>
<body>
    <h1>User: Submit New Order</h1>
    <form method="POST">
        <label>Select Service:</label>
        <select name="service_id" id="service_id" required>
            <option value="">Select Service</option>
            <?php while ($service = $services->fetch_assoc()): ?>
                <option value="<?= $service['id']; ?>" data-requirement="<?= $service['requirement']; ?>">
                    <?= $service['service_name']; ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>
        <div id="dynamic-input"></div>
        <button type="submit">Submit Order</button>
    </form>

    <script>
        const serviceSelect = document.getElementById('service_id');
        const dynamicInput = document.getElementById('dynamic-input');

        serviceSelect.addEventListener('change', function () {
            const requirement = serviceSelect.selectedOptions[0].getAttribute('data-requirement');
            dynamicInput.innerHTML = ''; // Clear previous inputs
            if (requirement === 'IMEI') {
                dynamicInput.innerHTML = '<label>IMEI:</label><input type="text" name="imei" required>';
            } else if (requirement === 'Email') {
                dynamicInput.innerHTML = '<label>Email:</label><input type="email" name="email" required>';
            }
        });
    </script>
</body>
</html>
