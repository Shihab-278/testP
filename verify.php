<?php
session_start();
include 'db.php'; // Ensure this includes your database connection

// Get the verification code from the URL
if (isset($_GET['code'])) {
    $verification_code = $_GET['code'];

    // Check if the verification code exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE verification_code = ? AND is_verified = 0");
    $stmt->execute([$verification_code]);
    $user = $stmt->fetch();

    if ($user) {
        // Code is valid, update user to verified
        $stmt = $conn->prepare("UPDATE users SET is_verified = 1, verification_code = NULL WHERE verification_code = ?");
        if ($stmt->execute([$verification_code])) {
            $_SESSION['verification_success'] = true;
            header("Location: login.php"); // Redirect to login page
            exit();
        } else {
            echo "Error verifying the account. Please try again.";
        }
    } else {
        echo "Invalid or expired verification code.";
    }
} else {
    echo "No verification code provided.";
}
?>
