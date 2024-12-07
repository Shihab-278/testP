<?php
session_start();
include '../db.php';

// Set timezone to Asia/Dhaka (Bangladesh Standard Time)
date_default_timezone_set('Asia/Dhaka');

// Check if the user is logged in and has the correct role
if ($_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

// Retrieve user ID from session
$user_id = $_SESSION['user_id'];

// Get user information
$stmt = $conn->prepare("SELECT username, `group`, balance, credit FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user['username'] ?? 'Unknown User';
$group_name = $user['group'] ?? 'No Group';
$balance = $user['balance'] ?? 0;
$credit = $user['credit'] ?? 0;

// Get user information
$stmt = $conn->prepare("SELECT username, `group`, balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user['username'] ?? '';
$group_name = $user['group'] ?? '';
$balance = $user['balance'] ?? 0;

// Fetch payment history for the user
$sql_history = "SELECT amount, transaction_id, payment_method, status, created_at FROM manual_payments WHERE user_id = ? ORDER BY created_at DESC";
$stmt_history = $conn->prepare($sql_history);
$stmt_history->execute([$user_id]);
$payments = $stmt_history->fetchAll();

// Fetch boxes from the database
$sql_boxes = "SELECT * FROM boxes ORDER BY created_at DESC";
$stmt_boxes = $conn->prepare($sql_boxes);
$stmt_boxes->execute();
$boxes = $stmt_boxes->fetchAll();

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $amount = $_POST['amount'];
    $transaction_id = $_POST['transaction_id'];
    $payment_method = $_POST['payment_method'];
    $payment_type = $_POST['payment_type'];  // Payment type: 'server_credit' or 'tool_credit'

    // Validate the input
    if (empty($amount) || empty($transaction_id) || empty($payment_method) || empty($payment_type)) {
        echo "<script>alert('All fields are required.');</script>";
    } else {
        // Insert payment into the database
        $stmt_payment = $conn->prepare("INSERT INTO manual_payments (user_id, amount, transaction_id, payment_method, payment_type, status, created_at) VALUES (?, ?, ?, ?, ?, 'Pending', NOW())");
        $stmt_payment->execute([$user_id, $amount, $transaction_id, $payment_method, $payment_type]);

        // Redirect or show a success message
        echo "<script>alert('Payment submitted successfully.'); window.location.href='';</script>";
    }
}
?>

<?php include 'header.php'; // Include header ?>

<div class="container">
    <!-- Box Section -->
    <h3>Payment Method</h3>
    <?php if (count($boxes) > 0): ?>
        <div class="boxes-container">
            <?php foreach ($boxes as $box): ?>
                <div class="box-card">
                    <h4><?php echo htmlspecialchars($box['title']); ?></h4>
                    <?php if ($box['image']): ?>
                        <img src="../admin/<?php echo htmlspecialchars($box['image']); ?>" alt="Box Image" class="box-image">
                    <?php endif; ?>
                    <p class="box-text"><?php echo htmlspecialchars($box['text']); ?></p> <!-- Add class for bold text -->
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No boxes available.</p>
    <?php endif; ?>

    <h2>Submit Payment</h2>

    <!-- Payment Form -->
    <form action="" method="POST">
        <label for="amount">Amount:</label>
        <input type="number" step="0.01" name="amount" required>

        <label for="transaction_id">Transaction ID:</label>
        <input type="text" name="transaction_id" required>

        <label for="payment_method">Payment Method:</label>
        <input type="text" name="payment_method" required>

        <!-- Dropdown for Payment Type -->
        <label for="payment_type">Payment Type:</label>
        <select name="payment_type" required>
            <option value="server_credit">Server Credit</option>
            <option value="tool_credit">Tool Credit</option>
        </select>

        <button type="submit">Submit Payment</button>
    </form>

    <!-- Payment History -->
    <h3>Payment History</h3>
    <?php if (count($payments) > 0): ?>
        <table class="history-table">
            <thead>
                <tr>
                    <th>Amount</th>
                    <th>Transaction ID</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($payment['amount']); ?></td>
                        <td><?php echo htmlspecialchars($payment['transaction_id']); ?></td>
                        <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                        <td><?php echo htmlspecialchars($payment['status']); ?></td>
                        <td><?php echo date('Y-m-d H:i:s', strtotime($payment['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No payment history found.</p>
    <?php endif; ?>
</div>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f7f8fa;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .container {
        width: 90%;
        max-width: 900px;
        margin: 40px auto;
        padding: 20px;
        background-color: #ffffff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    h2, h3, h4 {
        color: #333;
        font-size: 24px;
        text-align: center;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        font-size: 16px;
        color: #555;
        margin-bottom: 5px;
    }

    input {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f9f9f9;
        transition: border-color 0.3s;
    }

    input:focus {
        border-color: #007bff;
        background-color: #fff;
    }

    button {
        width: 100%;
        padding: 12px;
        background-color: #007bff;
        color: white;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #0056b3;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 30px;
    }

    .history-table th, .history-table td {
        padding: 12px;
        text-align: left;
        border: 1px solid #ddd;
    }

    .history-table th {
        background-color: #007bff;
        color: white;
    }

    .history-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    /* Box Section Styles */
    .boxes-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 40px;
    }

  .box-card {
    background-color: #ffffff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 10px;
    border-radius: 8px;
    width: 45%;  /* Reduced width */
    box-sizing: border-box;
    margin-bottom: 20px; /* Spacing between boxes */
    text-align: center;  /* Center-align text inside the box */
}

.box-card h4 {
    font-weight: bold;  /* Make the title text bold */
    margin-bottom: 10px;  /* Space between title and text */
}

.box-card p {
    text-align: center;  /* Center the paragraph text */
}


   .box-image {
    width: 80%;  /* Reduced width */
    height: auto;
    max-width: 150px;  /* Further reduced max width */
    border-radius: 8px;
    margin-bottom: 15px;
    display: block;
    margin-left: auto;
    margin-right: auto;  /* Center image */
}

    footer {
        background-color: #007bff;
        color: white;
        text-align: center;
        padding: 15px;
        position: relative;
        bottom: 0;
        width: 100%;
    }
</style>

<?php include 'footer.php'; // Include footer ?>

<?php
// Close the database connection
$conn = null;
?>
