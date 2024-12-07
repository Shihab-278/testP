<?php
session_start();
include '../db.php'; // Ensure db connection is established

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "You must be logged in as an admin to update orders.";
    exit();
}

// Check if order ID is provided
if (!isset($_GET['id'])) {
    echo "Order ID is required.";
    exit();
}

$order_id = $_GET['id'];

// Fetch order details
$stmt = $conn->prepare("SELECT orders.*, users.name AS user_name, services.name AS service_name 
                        FROM orders 
                        JOIN users ON orders.user_id = users.id 
                        JOIN services ON orders.service_id = services.id 
                        WHERE orders.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "Order not found.";
    exit();
}

// Handle form submission for order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['order_status'];
    
    // Update the order status
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    if ($stmt->execute([$new_status, $order_id])) {
        $response = "Order status updated successfully!";
    } else {
        $response = "Failed to update order status.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Update Order Status</h1>

        <?php if (isset($response)): ?>
            <div class="alert alert-info"><?php echo $response; ?></div>
        <?php endif; ?>

        <h3>Order Details</h3>
        <ul>
            <li><strong>Order ID:</strong> <?php echo $order['id']; ?></li>
            <li><strong>User Name:</strong> <?php echo htmlspecialchars($order['user_name']); ?></li>
            <li><strong>Service:</strong> <?php echo htmlspecialchars($order['service_name']); ?></li>
            <li><strong>Quantity:</strong> <?php echo $order['quantity']; ?></li>
            <li><strong>Total Price:</strong> $<?php echo number_format($order['total_price'], 2); ?></li>
            <li><strong>Status:</strong> <?php echo ucfirst($order['order_status']); ?></li>
        </ul>

        <h3>Update Status</h3>
        <form method="POST">
            <div class="mb-3">
                <label for="order_status" class="form-label">Order Status</label>
                <select name="order_status" id="order_status" class="form-control" required>
                    <option value="pending" <?php echo ($order['order_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="processing" <?php echo ($order['order_status'] == 'processing') ? 'selected' : ''; ?>>Processing</option>
                    <option value="completed" <?php echo ($order['order_status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo ($order['order_status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Status</button>
        </form>

        <p><a href="manage_orders.php" class="btn btn-secondary">Back to Orders</a></p>
    </div>
</body>
</html>
