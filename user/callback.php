<?php
// Start the session
session_start();

// Include database connection
include '../db.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from the POST request
    $user_id = $_POST['user_id'] ?? null;
    $transaction_id = $_POST['transaction_id'] ?? null;
    $amount = $_POST['amount'] ?? null;
    $invoiceID = $_POST['invoice_id'] ?? null;

    if (!$user_id || !$transaction_id || !$amount || !$invoiceID) {
        echo "Error: Missing required parameters.";
        exit;
    }

    // Payment gateway API details
    $apiUrl = 'https://securepay.crabdance.com/api/verify-payment';
    $apiKey = 'AV1TNgQ0nRGinrvLc2m1L7cwd'; // Replace with your actual API key

    // Prepare the data for the verification request
    $postData = [
        'invoice_id' => $invoiceID
    ];

    // Initialize cURL session
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'app-key: ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL request
    $response = curl_exec($ch);

    if ($response === false) {
        // Log cURL error and output error message
        $error_message = 'cURL error: ' . curl_error($ch);
        logCallback($error_message);
        curl_close($ch);
        echo "Error: Payment verification failed.";
        exit;
    }

    // Close cURL session
    curl_close($ch);

    // Decode the JSON response
    $responseData = json_decode($response, true);

    // Log the API response for debugging
    logCallback("Payment verification response: " . print_r($responseData, true));

    // Handle the response
    if (isset($responseData['status']) && $responseData['status'] == 1) {
        // Payment verified successfully

        // Insert the verified payment into the database
        $stmt = $conn->prepare("INSERT INTO manual_payments (user_id, transaction_id, amount, status) VALUES (?, ?, ?, 'completed')");
        $stmt->bind_param("isd", $user_id, $transaction_id, $amount);

        if ($stmt->execute()) {
            echo "Payment verified and recorded successfully.";
        } else {
            logCallback("Database error: " . $stmt->error);
            echo "Error: Failed to record payment.";
        }

        $stmt->close();
    } else {
        // Payment verification failed
        logCallback("Payment verification failed or payment not completed.");
        echo "Error: Payment verification failed or payment not completed.";
    }
}

/**
 * Log callback responses or errors for debugging.
 *
 * @param string $message The message to log.
 */
function logCallback($message) {
    $logFile = '../logs/callback_log.txt'; // Ensure the logs directory exists and is writable
    $logMessage = date('[Y-m-d H:i:s]') . " " . $message . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}
?>