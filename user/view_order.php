<?php
session_start();
include '../db.php'; // Ensure DB connection is established

// Check if the user is logged in (for user order view)
if (!isset($_SESSION['user_id']) && $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Get the order ID from the URL
$order_id = $_GET['order_id'] ?? 0;

// Fetch order details including requirements
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found.");
}

$requirements = json_decode($order['requirements'], true);
?>

<?php include 'header.php'; // User/Admin header ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Order Details</h1>

        <h4>Service: <?php echo htmlspecialchars($order['service_name']); ?></h4>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
        <p><strong>Order Date:</strong> <?php echo $order['order_date']; ?></p>

        <h4>Requirements Provided:</h4>
        <ul>
            <?php
            if ($requirements) {
                foreach ($requirements as $key => $value) {
                    echo "<li><strong>" . htmlspecialchars($key) . ":</strong> " . htmlspecialchars($value) . "</li>";
                }
            } else {
                echo "<li>No requirements provided for this service.</li>";
            }
            ?>
        </ul>

        <p><a href="user_orders.php" class="btn btn-secondary mt-3">Back to Orders</a></p>
    </div>
</body>
</html>

<?php include 'footer.php'; // User/Admin footer ?>
