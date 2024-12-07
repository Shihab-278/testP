<?php
session_start();
include 'db.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Fetch SMTP settings from database
$stmt = $conn->query("SELECT * FROM smtp_settings WHERE id = 1");
$smtp = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate a unique token for password reset
        $token = bin2hex(random_bytes(16));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Store the token and expiry in the database
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expiry) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expiry]);

        // Get the current site URL dynamically
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $baseURL = "$protocol://$host";

        // Create the reset link
        $resetLink = "$baseURL/reset_password.php?token=$token";

        // Set up PHPMailer
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $smtp['host']; // SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = $smtp['username']; // SMTP username
            $mail->Password = $smtp['password']; // SMTP password
            $mail->SMTPSecure = $smtp['secure'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $smtp['port']; // Port

            $mail->setFrom($smtp['username'], $smtp['from_name']);
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Please click the following link to reset your password: <a href='$resetLink'>$resetLink</a>";
            $mail->AltBody = "Please use the following link to reset your password: $resetLink";

            $mail->send();
            $message = 'Password reset email has been sent.';
        } catch (Exception $e) {
            $message = "Password reset email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $message = 'No user found with that email address.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - GSMXTool</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .form-container {
            max-width: 400px;
            margin: 60px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h3 class="text-center">Reset Your Password</h3>
        <p class="text-muted text-center">Enter your email address to receive a password reset link.</p>
        
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
        </form>
        
        <?php if (isset($message)): ?>
            <div class="alert alert-info mt-3">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="text-center mt-3">
            <a href="login.php" class="text-decoration-none">Back to Login</a>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
