<?php
session_start();
include '../db.php'; // Ensure DB connection is established

// Enable error reporting for debugging
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$response = "";

// Handle form submission to place an order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    // Make sure service_ids is an array
    $service_ids = isset($_POST['service_ids']) ? $_POST['service_ids'] : [];
    $additional_info = trim($_POST['additional_info']);
    $user_id = $_SESSION['user_id']; // User's ID from session
    $requirements = isset($_POST['requirements']) ? json_encode($_POST['requirements']) : null;

    // Retrieve user's current credit
    $stmt = $conn->prepare("SELECT credit FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_credit = $stmt->fetchColumn();

    // Check if the user has enough credit to place the order
    $total_price = 0;
    foreach ($service_ids as $service_id) {
        $stmt = $conn->prepare("SELECT price FROM server_services WHERE id = ?");
        $stmt->execute([$service_id]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);
        $total_price += $service['price'];
    }

    // Check if user has enough credit
    if ($user_credit >= $total_price) {
        try {
            // Deduct the credit from the user's account
            $deduct_stmt = $conn->prepare("UPDATE users SET credit = credit - ? WHERE id = ?");
            $deduct_stmt->execute([$total_price, $user_id]);

            // Insert the new order into server_order table for each valid service selected
            foreach ($service_ids as $service_id) {
                // Add submit_time to the INSERT query to ensure it's saved
                $stmt = $conn->prepare("INSERT INTO server_order (user_id, service_id, additional_info, requirements, submit_time) 
                                        VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)");
                $stmt->execute([$user_id, $service_id, $additional_info, $requirements]);
            }
            $response = "Your order has been placed successfully!";
        } catch (PDOException $e) {
            $response = "Error: " . $e->getMessage();
        }
    } else {
        $response = "Insufficient credit to place the order.";
    }
}

// Handle the update action (when updating order status or rejecting it)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $replay_note = isset($_POST['replay_note']) ? trim($_POST['replay_note']) : null;

    try {
        // Fetch the price for the service associated with this order
        $stmt = $conn->prepare("SELECT price, user_id FROM server_order 
                                JOIN server_services ON server_order.service_id = server_services.id 
                                WHERE server_order.id = ?");
        $stmt->execute([$order_id]);
        $order_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order_data) {
            // If the status is 'Rejected', credit the user account
            if ($status == 'Rejected') {
                // Add the price back to the user's credit
                $credit_stmt = $conn->prepare("UPDATE users SET credit = credit + ? WHERE id = ?");
                $credit_stmt->execute([$order_data['price'], $order_data['user_id']]);
            }

            // Set replay_time to current timestamp in AM/PM format when status is updated
            $replay_time = date('Y-m-d h:i:s A'); // Format: 2024-12-06 01:30:00 PM

            // Update order status, replay note, and replay time
            $update_stmt = $conn->prepare("UPDATE server_order 
                                           SET status = ?, replay_note = ?, replay_time = ? 
                                           WHERE id = ?");
            $update_stmt->execute([$status, $replay_note, $replay_time, $order_id]);

            $response = "Order status and replay note have been updated successfully!";
        } else {
            $response = "Order not found!";
        }
    } catch (PDOException $e) {
        $response = "Error: " . $e->getMessage();
    }
}

// Fetch all orders with the corresponding service prices
try {
    $stmt = $conn->query("SELECT so.id, so.status, so.additional_info, so.replay_note, so.submit_time, so.replay_time, ss.price 
                          FROM server_order so 
                          JOIN server_services ss ON so.service_id = ss.id");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $response = "Error fetching orders: " . $e->getMessage();
}

?>

<?php include 'header.php'; // Include header ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .order-item {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .price {
            font-weight: bold;
            color: #28a745; /* green color for price */
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Manage Orders</h1>

        <?php if ($response): ?>
        <div class="alert alert-info"><?php echo $response; ?></div>
        <?php endif; ?>

        <div class="list-group">
            <?php foreach ($orders as $order): ?>
                <div class="order-item">
                    <h5>Order ID: <?php echo $order['id']; ?></h5>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
                    <p><strong>Additional Information:</strong> <?php echo htmlspecialchars($order['additional_info']); ?></p>

                    <!-- Display Submit Time -->
                    <p><strong>Submit Time:</strong> <?php echo htmlspecialchars($order['submit_time']); ?></p>

                    <!-- Display Replay Time (AM/PM format) -->
                    <p><strong>Replay Time:</strong> <?php echo htmlspecialchars($order['replay_time']) ? date('h:i A', strtotime($order['replay_time'])) : 'Not yet replayed'; ?></p>

                    <!-- Display Price -->
                    <p><strong class="price">Price:</strong> $<?php echo number_format($order['price'], 2); ?></p>

                    <!-- Update Order Status Form -->
                    <form method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <div class="mb-3">
                            <label for="status_<?php echo $order['id']; ?>" class="form-label">Change Status</label>
                            <select name="status" class="form-select" id="status_<?php echo $order['id']; ?>" required>
                                <option value="Pending" <?php echo ($order['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="In Progress" <?php echo ($order['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                                <option value="Rejected" <?php echo ($order['status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                                <option value="Success" <?php echo ($order['status'] == 'Success') ? 'selected' : ''; ?>>Success</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="replay_note_<?php echo $order['id']; ?>" class="form-label">Replay Note</label>
                            <textarea name="replay_note" class="form-control" id="replay_note_<?php echo $order['id']; ?>" rows="4"><?php echo htmlspecialchars($order['replay_note']); ?></textarea>
                        </div>

                        <button type="submit" name="update_order" class="btn btn-primary">Update Order</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <p><a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a></p>
    </div>
</body>
</html>

<?php include 'footer.php'; // Include footer ?>
