<?php
include '../db.php';

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Fetch the order details for the given order ID
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        ?>
        <form method="POST" action="manage_orders.php">
            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
