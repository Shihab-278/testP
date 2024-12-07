<?php
include '../db.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Fetch SMTP settings from the database
$stmt = $conn->query("SELECT * FROM smtp_settings WHERE id = 1");
$smtpSettings = $stmt->fetch();

// Set up PHPMailer
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = $smtpSettings['host'];
    $mail->SMTPAuth = $smtpSettings['auth'];
    $mail->Username = $smtpSettings['username'];
    $mail->Password = $smtpSettings['password'];
    $mail->SMTPSecure = $smtpSettings['secure'];
    $mail->Port = $smtpSettings['port'];

    $mail->CharSet = $smtpSettings['charset'];
    $mail->setFrom($smtpSettings['from_email'], $smtpSettings['from_name']);
    $mail->addReplyTo($smtpSettings['reply_to_email'], $smtpSettings['reply_to_name']);
    // Add recipients, subject, body etc.

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
}
