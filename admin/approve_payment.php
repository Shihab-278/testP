<?php
// Ensure that the 'id' parameter is passed and valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Connect to the database
    $conn = new mysqli('localhost', 'domhoste_test', 'domhoste_test', 'domhoste_test');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Prepare the query to fetch payment details
        $stmt = $conn->prepare("SELECT user_id, amount FROM manual_payments WHERE id = ? AND status = 'pending'");
        $stmt->bind_param('i', $id); // 'i' stands for integer
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch the payment details
            $row = $result->fetch_assoc();
            $user_id = $row['user_id'];
            $amount = $row['amount'];

            // Update the payment status to 'approved'
            $update_payment_stmt = $conn->prepare("UPDATE manual_payments SET status = 'approved' WHERE id = ?");
            $update_payment_stmt->bind_param('i', $id);
            $update_payment_stmt->execute();

            // Add the amount to the user's account balance
            $update_user_balance_stmt = $conn->prepare("UPDATE users SET funds = funds + ? WHERE id = ?");
            $update_user_balance_stmt->bind_param('di', $amount, $user_id); // 'd' for double, 'i' for integer
            $update_user_balance_stmt->execute();

            // Commit the transaction
            $conn->commit();

            $message = "Payment approved and funds added to the user's account.";
            $redirect_to = "manage_payments.php"; // Redirect to dashboard or previous page
        } else {
            $message = "Payment not found or already processed.";
            $redirect_to = "manage_payments.php"; // Adjust this to the page where you want to go back
        }

    } catch (Exception $e) {
        // If there's an error, rollback the transaction
        $conn->rollback();
        $message = "Error: " . $e->getMessage();
        $redirect_to = "manage_payments.php"; // Redirect to the same page in case of error
    }

    // Close the connection
    $conn->close();
} else {
    $message = "Invalid or missing payment ID.";
    $redirect_to = "manage_payments.php"; // Redirect to the pending payments list or previous page
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Approval</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .alert-box {
            margin-top: 50px;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            font-size: 18px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="alert-box">
        <?php if (isset($message)): ?>
            <?php if (strpos($message, 'Error') === false): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php else: ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Redirect script -->
<script>
    setTimeout(function() {
        window.location.href = '<?php echo $redirect_to; ?>';
    }, 1000);  // Redirect after 1 second
</script>

</body>
</html>
