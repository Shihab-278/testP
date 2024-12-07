<?php
session_start();
include '../db.php';

if (isset($_GET['id'])) {
    // Sanitize the payment_id to make sure it's an integer
    $payment_id = intval($_GET['id']);

    // Validate that the payment_id is a valid positive integer
    if ($payment_id > 0) {
        // Update payment status to confirmed
        $query = "UPDATE payments SET payment_status = 'confirmed' WHERE payment_id = $payment_id";

        if (mysqli_query($conn, $query)) {
            echo "Payment confirmed successfully!";
        } else {
            echo "Error: " . mysqli_error($conn);
            echo "<br>Query: " . $query;  // Print the SQL query for debugging purposes
        }
    } else {
        echo "Invalid payment ID!";
    }
} else {
    echo "Payment ID not provided!";
}
?>
