<?php
session_start();
include '../db.php';

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

// Set default timezone to Asia/Dhaka (fallback)
date_default_timezone_set('Asia/Dhaka');

// Ensure only admin can access this page
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Retrieve user ID from session
$user_id = $_SESSION['user_id'];

// Get username from the database
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user ? $user['username'] : '';

// Initialize response variable
$response = "";

// Get the status filter if set
$status_filter = isset($_GET['status_filter']) ? $_GET['status_filter'] : '';

// Build the SQL query
$sql = "
    SELECT o.id, o.order_status, o.submit_time, o.replay_time, o.reply_text, o.quantity, o.total_price, u.username AS user_name, s.name as service_name, o.additional_info, o.user_id
    FROM orders o 
    JOIN services s ON o.service_id = s.id 
    JOIN users u ON o.user_id = u.id
";

// Apply filter if status is provided
if ($status_filter) {
    $sql .= " WHERE o.order_status = :status_filter ";
}

$sql .= " ORDER BY o.submit_time DESC";

// Fetch all orders with service details, user_name, submit_time, replay_time, and additional fields
try {
    $stmt = $conn->prepare($sql);

    // Bind status filter if present
    if ($status_filter) {
        $stmt->bindParam(':status_filter', $status_filter);
    }

    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch the additional fields for each order
    foreach ($orders as &$order) {
        $order_id = $order['id'];
        $stmt_fields = $conn->prepare("SELECT field_name, field_value FROM order_fields WHERE order_id = ?");
        $stmt_fields->execute([$order_id]);
        $order['additional_fields'] = $stmt_fields->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $response = "Error fetching orders: " . $e->getMessage();
    $orders = [];  // Ensure $orders is an empty array in case of error
}

// Update order status and reply text if the form is submitted
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status']; // Get the updated status
    $reply_text = $_POST['reply_text']; // Get the reply text

    // Update the order status and reply text
    try {
        $stmt = $conn->prepare("UPDATE orders SET order_status = ?, reply_text = ?, replay_time = NOW() WHERE id = ?");
        if ($stmt->execute([$status, $reply_text, $order_id])) {
            $response = "Order status and reply updated successfully!";

            // Check if the order is rejected and update user's credit accordingly
            if ($status === 'Rejected') {
                // Fetch the order details to get the user_id and total_price
                $order_stmt = $conn->prepare("SELECT user_id, total_price FROM orders WHERE id = ?");
                $order_stmt->execute([$order_id]);
                $order = $order_stmt->fetch(PDO::FETCH_ASSOC);

                if ($order) {
                    $user_id = $order['user_id'];
                    $total_price = $order['total_price'];

                    // Update the user's credit by adding the total price back to their balance
                    $stmt_update_credit = $conn->prepare("UPDATE users SET credit = credit + ? WHERE id = ?");
                    if ($stmt_update_credit->execute([$total_price, $user_id])) {
                        // Send a message to Telegram when the order is rejected and credit is updated
                        $message = "⚠️ Order Rejected ⚠️\n\n";
                        $message .= "Order ID: " . $order_id . "\n";
                        $message .= "User ID: " . $user_id . "\n";
                        $message .= "Service: " . htmlspecialchars($order['service_name']) . "\n";
                        $message .= "Total Price Added to Credit: $" . number_format($total_price, 2) . "\n";
                        $message .= "User's Credit has been updated.";

                        // Send the notification to Telegram
                        sendTelegramNotification($message);
                    }
                }
            }

            // Send Telegram Notification (always for every status update)
            $order_stmt = $conn->prepare("SELECT o.id, u.username, s.name as service_name, o.order_status, o.total_price FROM orders o 
                                         JOIN users u ON o.user_id = u.id 
                                         JOIN services s ON o.service_id = s.id WHERE o.id = ?");
            $order_stmt->execute([$order_id]);
            $order = $order_stmt->fetch(PDO::FETCH_ASSOC);

            if ($order) {
                // Prepare message to send via Telegram
                $message = "✅ Order Updated ✅\n\n";
                $message .= "Order ID: " . $order['id'] . "\n";
                $message .= "User: " . htmlspecialchars($order['username']) . "\n";
                $message .= "Service: " . htmlspecialchars($order['service_name']) . "\n";
                $message .= "Status: " . ucfirst($status) . "\n";
                $message .= "Reply: " . htmlspecialchars($reply_text) . "\n";
                $message .= "Total Price: $" . number_format($order['total_price'], 2) . "\n";
                $message .= "Please review the order details.";

                // Send the notification to Telegram
                sendTelegramNotification($message);
            }
        } else {
            $response = "Failed to update order status and reply.";
        }
    } catch (PDOException $e) {
        $response = "Error updating order status and reply: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'header.php'; // Include your header ?>

<div class="container my-5">
    <h1 class="text-center">Manage Orders</h1>

    <?php if ($response): ?>
    <div class="alert alert-info"><?php echo $response; ?></div>
    <?php endif; ?>

    <form method="GET" class="mb-3">
        <label for="status_filter" class="form-label">Filter by Status</label>
        <select name="status_filter" id="status_filter" class="form-control">
            <option value="">All</option>
            <option value="Pending" <?php echo $status_filter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
            <option value="Processing" <?php echo $status_filter === 'Processing' ? 'selected' : ''; ?>>Processing</option>
            <option value="Completed" <?php echo $status_filter === 'Completed' ? 'selected' : ''; ?>>Completed</option>
            <option value="Rejected" <?php echo $status_filter === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
        </select>
        <button type="submit" class="btn btn-primary mt-2">Filter</button>
    </form>

    <?php if (empty($orders)): ?>
    <div class="alert alert-warning">No orders found.</div>
    <?php else: ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Service</th>
                <th>User Name</th>
                <th>Submit Time</th>
                <th>Replay Time</th>
                <th>Status</th>
                <th>Reply</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Additional Info</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?php echo $order['id']; ?></td>
                <td><?php echo $order['service_name']; ?></td>
                <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                <td><?php echo $order['submit_time']; ?></td>
                <td><?php echo $order['replay_time'] ? $order['replay_time'] : 'Not replied yet'; ?></td>
                <td><?php echo ucfirst($order['order_status']); ?></td>
                <td>
                    <?php if ($order['reply_text']): ?>
                        <strong>Reply:</strong> <?php echo htmlspecialchars($order['reply_text']); ?>
                    <?php else: ?>
                        No reply yet.
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                <td>
                    <?php echo htmlspecialchars($order['additional_info']); ?>

                    <!-- Display additional fields dynamically -->
                    <?php if (!empty($order['additional_fields'])): ?>
                        <ul>
                            <?php foreach ($order['additional_fields'] as $field): ?>
                                <li><strong><?php echo htmlspecialchars(ucfirst($field['field_name'])); ?>:</strong> <?php echo htmlspecialchars($field['field_value']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        No additional fields.
                    <?php endif; ?>
                </td>
                <td>
                    <!-- Button to trigger the modal for editing the order -->
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editOrderModal<?php echo $order['id']; ?>">Edit</button>
                </td>
            </tr>

            <!-- Modal for editing the order -->
            <div class="modal fade" id="editOrderModal<?php echo $order['id']; ?>" tabindex="-1" aria-labelledby="editOrderModalLabel<?php echo $order['id']; ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editOrderModalLabel<?php echo $order['id']; ?>">Edit Order #<?php echo $order['id']; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="Pending" <?php echo $order['order_status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Processing" <?php echo $order['order_status'] === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="Completed" <?php echo $order['order_status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="Rejected" <?php echo $order['order_status'] === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="reply_text" class="form-label">Reply</label>
                                    <textarea name="reply_text" class="form-control" rows="3" required><?php echo htmlspecialchars($order['reply_text']); ?></textarea>
                                </div>
                                <button type="submit" name="update_status" class="btn btn-primary mt-2">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <p><a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a></p>
</div>

<?php include 'footer.php'; // Include your footer ?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>

</body>
</html>
