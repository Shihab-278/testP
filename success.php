<?php
session_start();

// Check if registration is successful
if (!isset($_SESSION['registration_success'])) {
    header('Location: installer.php'); // Redirect back to the installer if accessed directly
    exit;
}
unset($_SESSION['registration_success']); // Clear the success flag
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Complete - GSMX TOOL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            color: #fff;
        }
        .container {
            max-width: 500px;
            margin: auto;
            text-align: center;
            padding: 2rem;
            background: #fff;
            color: #000;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
        .btn-primary, .btn-secondary {
            width: 100%;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="container">
        <h1>Installation Complete!</h1>
        <p>Your installation is now complete. You can log in to the admin panel or visit the website.</p>
        <p><strong>Important:</strong> Please delete the <code>install.php</code> file from your server to ensure security and prevent unauthorized access. Afterward, you can log in to your account.</p>
        <a href="/admin/dashboard.php" class="btn btn-primary">Login to Admin Panel</a>
        <a href="index.php" class="btn btn-secondary">Visit Website</a>
    </div>
</div>
</body>
</html>
