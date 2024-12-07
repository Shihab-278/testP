<?php
// Ensure that the 'id' parameter is passed and valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Include the database connection file
    include '../db.php';  // Assuming you have db.php for the PDO connection

    try {
        // Prepare the SQL query using a prepared statement
        $stmt = $conn->prepare("UPDATE manual_payments SET status = :status WHERE id = :id");
        
        // Bind the parameters
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Set the status value to 'rejected'
        $status = 'rejected';

        // Execute the query
        $stmt->execute();

        $message = "Payment rejected successfully.";
        $redirect_to = "manage_payments.php";  // Redirect back to the pending payments page

    } catch (PDOException $e) {
        // Handle the error and redirect to the same page with the error message
        $message = "Error rejecting the payment: " . $e->getMessage();
        $redirect_to = "manage_payments.php";  // Redirect back to the pending payments page
    }

    // Close the connection
    $conn = null;  // Close PDO connection
} else {
    $message = "Invalid or missing payment ID.";
    $redirect_to = "manage_payments.php";  // Redirect to the pending payments page
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Rejection</title>
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
