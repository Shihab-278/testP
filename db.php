<?php
// Database connection settings
$host = 'localhost';  // Your database host (e.g., localhost or IP address)
$dbname = 'domhoste_test';  // Your database name
$username = 'root';  // Your database username
$password = '';  // Your database password

try {
    // Create a new PDO instance and establish the connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    // Set the PDO error mode to exception (to catch and report any errors)
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Optionally, you can set the default fetch mode for results:
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // If the connection fails, catch the error and display it
    echo 'Connection failed: ' . $e->getMessage();
    exit;  // Stop the script if the connection fails
}
?>
