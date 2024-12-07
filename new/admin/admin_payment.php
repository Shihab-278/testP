<?php
session_start();
include '../db.php';

// Check if the user is an admin
if ($_SESSION['role'] != 'admin') {
    echo "Access denied.";
    exit;
}

// পেন্ডিং পেমেন্টস অ্যাপ্রুভ অথবা রিজেক্ট
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action']; // 'approve' বা 'reject'

    if ($action == 'approve' || $action == 'reject') {
        // Start a transaction to ensure atomicity
        $conn->begin_transaction();

        try {
            // Prepare the update query for payment status
            $stmt = $conn->prepare("UPDATE manual_payments SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $action, $id); // Use bind_param with both action and id
            if (!$stmt->execute()) {
                throw new Exception("Error updating payment status: " . $stmt->error);
            }

            // If approved, update the user's balance
            if ($action == 'approve') {
                // Get payment details
                $stmt_payment = $conn->prepare("SELECT user_id, amount FROM manual_payments WHERE id = ?");
                $stmt_payment->bind_param("i", $id);
                $stmt_payment->execute();
                $result = $stmt_payment->get_result();
                $payment = $result->fetch_assoc();

                $user_id = $payment['user_id'];
                $amount = $payment['amount'];

                // Update the user's balance
                $stmt_balance = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
                $stmt_balance->bind_param("di", $amount, $user_id);
                if (!$stmt_balance->execute()) {
                    throw new Exception("Error updating user's balance: " . $stmt_balance->error);
                }

                echo "Payment approved and funds added.";
            } else {
                echo "Payment rejected.";
            }

            // Commit the transaction
            $conn->commit();
        } catch (Exception $e) {
            // Rollback the transaction if any error occurs
            $conn->rollback();
            echo "Error: " . $e->getMessage();
        }

        // Redirect back to the pending payments page to avoid resubmission
        header("Location: admin_payment.php");
        exit;
    }
}

// Retrieve pending payments
$result = $conn->query("SELECT * FROM manual_payments WHERE status = 'pending'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
</head>
<body>
    <h2>Pending Payments</h2>
    <?php if ($result->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Transaction ID</th>
                <th>Amount</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['user_id']; ?></td>
                    <td><?php echo $row['transaction_id']; ?></td>
                    <td><?php echo $row['amount']; ?></td>
                    <td>
                        <a href="admin_payment.php?action=approve&id=<?php echo $row['id']; ?>">Approve</a> | 
                        <a href="admin_payment.php?action=reject&id=<?php echo $row['id']; ?>">Reject</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No pending payments.</p>
    <?php endif; ?>
</body>
</html>
