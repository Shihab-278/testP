<?php
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $transaction_id = $_POST['transaction_id'];
    $amount = $_POST['amount'];

    $stmt = $conn->prepare("INSERT INTO manual_payments (user_id, transaction_id, amount, status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("isd", $user_id, $transaction_id, $amount);
    
    if ($stmt->execute()) {
        echo "Payment request submitted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
