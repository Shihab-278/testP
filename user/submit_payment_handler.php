<?php
session_start();
include '../db.php'; // Database connection

// Function to send Telegram notification
function sendTelegramNotification($message) {
    global $conn;
    $stmt = $conn->query("SELECT * FROM telegram_settings LIMIT 1");
    $settings = $stmt->fetch();

    if ($settings) {
        $telegramToken = $settings['telegram_token'];
        $chatId = $settings['chat_id'];

        // Telegram API URL
        $url = "https://api.telegram.org/bot$telegramToken/sendMessage";

        // Prepare data to send
        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML' // You can use HTML formatting in the message
        ];

        // Use cURL to send the request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }
}

// Check if the user is logged in
if ($_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

// Retrieve user ID from session
$user_id = $_SESSION['user_id'];

// Get user information (optional: display the user's name or username)
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user['username'] ?? '';

// Optionally, fetch the latest payment record or display confirmation
$stmt = $conn->prepare("SELECT * FROM manual_payments WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$user_id]);
$payment = $stmt->fetch();

// Payment success notification message
$message = "✅ Payment Successful ✅\n\n";
$message .= "User: " . htmlspecialchars($username) . "\n"; // Add username to the Telegram message
if ($payment) {
    $message .= "Amount: " . htmlspecialchars($payment['amount']) . "\n";
    $message .= "Transaction ID: " . htmlspecialchars($payment['transaction_id']) . "\n";
    $message .= "Payment Method: " . htmlspecialchars($payment['payment_method']) . "\n";
    $message .= "Status: " . htmlspecialchars($payment['status']) . "\n";
    $message .= "Submitted At: " . date('Y-m-d H:i:s', strtotime($payment['created_at'])) . "\n";
} else {
    $message .= "No payment found.";
}

// Send Telegram notification about successful payment
sendTelegramNotification($message);

?>

<?php include 'header.php'; // Include header ?>

<div class="container mt-5">
    <!-- Success Notification Card -->
    <div class="alert alert-success text-center" role="alert">
        <h2>✅ Payment Submitted Successfully!</h2>
        <p>Thank you, <strong><?php echo htmlspecialchars($username); ?></strong>! Your payment has been successfully submitted. Our team will review the payment and update the status shortly.</p>
    </div>

    <!-- Payment Details Section -->
    <?php if ($payment): ?>
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-primary text-white">
                <h4>Payment Details</h4>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item"><strong>Amount:</strong> <?php echo htmlspecialchars($payment['amount']); ?> USD</li>
                    <li class="list-group-item"><strong>Transaction ID:</strong> <?php echo htmlspecialchars($payment['transaction_id']); ?></li>
                    <li class="list-group-item"><strong>Payment Method:</strong> <?php echo htmlspecialchars($payment['payment_method']); ?></li>
                    <li class="list-group-item"><strong>Status:</strong> <?php echo htmlspecialchars($payment['status']); ?></li>
                    <li class="list-group-item"><strong>Submitted At:</strong> <?php echo date('Y-m-d H:i:s', strtotime($payment['created_at'])); ?></li>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <div class="text-center">
        <a href="dashboard.php" class="btn btn-primary btn-lg">Back to Dashboard</a>
    </div>
</div>

<?php include 'footer.php'; // Include footer ?>
