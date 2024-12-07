<?php
session_start();
include '../db.php'; // Ensure DB connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];
$response = "";

// Fetch the orders for the logged-in user
try {
    $stmt = $conn->prepare("SELECT o.id, o.status, o.submit_time, o.replay_time, o.reply_text, s.name as service_name 
                            FROM orders o 
                            JOIN services s ON o.service_id = s.id 
                            WHERE o.user_id = ? 
                            ORDER BY o.submit_time DESC");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $response = "Error fetching orders: " . $e->getMessage();
    $orders = [];  // Ensure $orders is an empty array in case of error
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Order History</h1>

        <?php if ($response): ?>
        <div class="alert alert-info"><?php echo $response; ?></div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
        <div class="alert alert-warning">No orders found in your history.</div>
        <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Service</th>
                    <th>Submit Time</th>
                    <th>Replay Time</th>
                    <th>Status</th>
                    <th>Reply</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo htmlspecialchars($order['service_name']); ?></td>
                    <td><?php echo $order['submit_time']; ?></td>
                    <td><?php echo $order['replay_time'] ? $order['replay_time'] : 'Not replied yet'; ?></td>
                    <td><?php echo ucfirst($order['status']); ?></td>
                    <td>
                        <?php if ($order['reply_text']): ?>
                            <strong>Reply:</strong> <?php echo htmlspecialchars($order['reply_text']); ?>
                        <?php else: ?>
                            No reply yet.
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <p><a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a></p>
    </div>
</body>
</html>
