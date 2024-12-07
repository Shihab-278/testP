<?php
session_start();
include '../db.php';

// Check if the user is logged in (optional, but recommended)
if (!isset($_SESSION['user_id'])) {
    echo "User is not logged in.";
    exit;
}

// Payment Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_payment'])) {
    $user_id = $_SESSION['user_id']; // Get user ID from session
    $transaction_id = $_POST['transaction_id'];
    $amount = $_POST['amount'];

    // Debug: Check the form values
    echo "Transaction ID: $transaction_id<br>";
    echo "Amount: $amount<br>";

    // Prepare and insert payment data into the database
    $stmt = $conn->prepare("INSERT INTO manual_payments (user_id, transaction_id, amount, status) VALUES (?, ?, ?, 'pending')");
    if ($stmt === false) {
        // Debug: Check if prepare fails
        echo "Error preparing statement: " . $conn->error;
        exit;
    }

    $stmt->bind_param("isd", $user_id, $transaction_id, $amount);

    if ($stmt->execute()) {
        echo "Payment request submitted successfully.";
    } else {
        // Debug: Check for execution errors
        echo "Error executing statement: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Payment</title>
</head>
<body>
    <h2>Submit Payment</h2>
    <form action="user_payment.php" method="POST">
        <label for="transaction_id">Transaction ID:</label>
        <input type="text" id="transaction_id" name="transaction_id" required>
        
        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" step="0.01" required>
        
        <button type="submit" name="submit_payment">Submit</button>
    </form>
</body>
</html>
