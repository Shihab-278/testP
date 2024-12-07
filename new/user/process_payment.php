<?php
include '../db.php';  // Include the database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the POST data
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $transaction_id = $_POST['transaction_id'];
    $user_id = 1; // Example user ID (you can fetch it based on the session or login)

    // Debugging: Check the input values before proceeding
    echo "User ID: " . $user_id . "<br>";
    echo "Amount: " . $amount . "<br>";
    echo "Payment Method: " . $payment_method . "<br>";
    echo "Transaction ID: " . $transaction_id . "<br>";

    // Cast amount to float (if it's not already a number)
    if (!is_numeric($amount)) {
        echo "Invalid amount. Please enter a valid number.<br>";
        exit;
    }
    $amount = (float) $amount; // Ensure it's treated as a float

    // Ensure payment_method and transaction_id are strings
    $payment_method = (string) $payment_method;
    $transaction_id = (string) $transaction_id;

    // Prepare the query with placeholders
    $query = "INSERT INTO payments (user_id, amount, payment_method, transaction_id) VALUES (?, ?, ?, ?)";

    // Prepare statement
    if ($stmt = mysqli_prepare($conn, $query)) {
        // Bind parameters (i - integer, d - double, s - string)
        if (mysqli_stmt_bind_param($stmt, "idss", $user_id, $amount, $payment_method, $transaction_id)) {
            // Execute the query
            if (mysqli_stmt_execute($stmt)) {
                echo "Payment successfully submitted!";
            } else {
                echo "Error executing query: " . mysqli_stmt_error($stmt); // Execution error
            }
        } else {
            echo "Error binding parameters: " . mysqli_stmt_error($stmt); // Bind error
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing query: " . mysqli_error($conn); // Prepare error
    }
}
?>
