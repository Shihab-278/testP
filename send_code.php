<?php
// PHPMailer use korar jonne
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';  // PHPMailer er library load

// Function to generate random verification code
function generate_verification_code() {
    return rand(100000, 999999);  // Simple random code
}

// User er email nite hobe
$email = $_POST['email'];

// Verification code generate kora
$code = generate_verification_code();

<?php
session_start();
include 'db.php'; // Ensure db.php connects to your database

// Connection create kora
$conn = new mysqli($servername, $username, $password, $dbname);

// Connection check kora
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// User er verification code database e update kora
$sql = "UPDATE users SET verification_code = ?, is_verified = 0 WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $code, $email);
$stmt->execute();

// Send email with verification code
$mail = new PHPMailer(true);
try {
    //Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.yourmailprovider.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'your_email@example.com';
    $mail->Password   = 'your_password';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    //Recipients
    $mail->setFrom('your_email@example.com', 'Your Website');
    $mail->addAddress($email);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Your Verification Code';
    $mail->Body    = 'Your verification code is: <strong>' . $code . '</strong>';

    $mail->send();
    echo 'Verification code sent!';
} catch (Exception $e) {
    echo "Email send failed. Error: {$mail->ErrorInfo}";
}

$conn->close();
?>
