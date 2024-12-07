<?php
$host = 'localhost';
$dbname = 'domhoste_test';
$username = 'domhoste_test';  // Set your DB username
$password = 'domhoste_test';  // Set your DB password

// কানেকশন তৈরি করা
$conn = new mysqli($servername, $username, $password, $dbname);

// চেক করুন কানেকশন সফল কিনা
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
