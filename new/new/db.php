<?php
$host = 'localhost';
$dbname = 'domhoste_rent';
$username = 'domhoste_rent';  // Set your DB username
$password = 'domhoste_rent';  // Set your DB password

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
