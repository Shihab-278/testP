<?php
session_start();
include '../db.php';

// Set timezone to Asia/Dhaka (Bangladesh Standard Time)
date_default_timezone_set('Asia/Dhaka');

// Check if the user is logged in and has the correct role
if ($_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

// Retrieve user ID from session
$user_id = $_SESSION['user_id'];

// Get user information
$stmt = $conn->prepare("SELECT username, `group`, balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user['username'] ?? '';
$group_name = $user['group'] ?? '';
$balance = $user['balance'] ?? 0;

include 'header.php'; // Include user-side header
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        body {
            background-color: #f5f8fb;
            font-family: 'Arial', sans-serif;
        }
        .card {
            border: none;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Welcome, <?= htmlspecialchars($username) ?></h1>
    <p class="text-center text-muted">Group: <?= htmlspecialchars($group_name) ?> | Balance: <?= htmlspecialchars($balance) ?></p>
    
    <!-- Check License Section -->
    <div class="col-12 mt-4">
        <div class="card p-3">
            <p class="mb-0 fw-bold h4 text-center">Check License</p>
            <div class="text-center mt-3">
                <button class="btn btn-success" id="checkLicense">Check License</button>
                <p id="licenseKeyOutput" class="mt-3 text-success fw-bold"></p>
            </div>
        </div>
    </div>
</div>

<script>
    // Handle Check License button click
    $(document).ready(function() {
        $('#checkLicense').click(function() {
            $.ajax({
                url: 'generate_license.php', // Call the PHP file
                method: 'GET',
                success: function(response) {
                    $('#licenseKeyOutput').text(response); // Show the generated license key
                },
                error: function() {
                    $('#licenseKeyOutput').text('Error generating license key!');
                }
            });
        });
    });
</script>
</body>
</html>
