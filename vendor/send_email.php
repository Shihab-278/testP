<?php
// include the Composer autoload file
require_once __DIR__ . '/../vendor/autoload.php'; // Adjust the path based on your directory structure

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendWelcomeEmail($email, $name, $smtp) {
    // Set up PHPMailer
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $smtp['host']; // SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = $smtp['username']; // SMTP username
        $mail->Password = $smtp['password']; // SMTP password
        $mail->SMTPSecure = $smtp['secure'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $smtp['port']; // SMTP port

        $mail->setFrom($smtp['username'], $smtp['from_name']);
        $mail->addAddress($email, $name);

        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Our Website';
        $mail->Body    = "Dear $name, <br>Welcome to our website! We are glad to have you with us. <br><br>Best Regards, <br>The Team";
        $mail->AltBody = "Dear $name, Welcome to our website! We are glad to have you with us.";

        $mail->send();
        return true; // Success
    } catch (Exception $e) {
        return "Mailer Error: {$mail->ErrorInfo}"; // Error
    }
}
