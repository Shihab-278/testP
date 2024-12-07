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

$balance = $user['balance'];
$credit = $user['credit'];

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

include 'header.php'; 
$showModal = false;
$modalMessage = '';
$toolDetails = [];

// Add a function to get a detailed order status message
function getOrderStatusMessage($status) {
    switch ($status) {
        case 'Pending':
            return 'Your order is awaiting processing.';
        case 'Processing':
            return 'Your order is being processed. Please wait for further updates.';
        case 'Completed':
            return 'Your order has been successfully completed.';
        case 'Success':
            return 'Order completed successfully.';
        default:
            return 'Unknown status.';
    }
}

// Build the SQL query to fetch orders with filtering
$sql = "SELECT o.id, o.user_id, o.order_status, o.submit_time, o.replay_time, o.reply_text, o.quantity, o.total_price, s.name as service_name, u.username
        FROM orders o 
        JOIN services s ON o.service_id = s.id 
        JOIN users u ON o.user_id = u.id 
        WHERE 1";

$parameters = [];
if (isset($_GET['status']) && $_GET['status'] !== '') {
    $sql .= " AND o.order_status = :status";
    $parameters['status'] = $_GET['status'];
}
if (isset($_GET['service']) && $_GET['service'] !== '') {
    $sql .= " AND o.service_id = :service_id";
    $parameters['service_id'] = $_GET['service'];
}

if ($_SESSION['role'] !== 'admin') {
    $sql .= " AND o.user_id = :user_id";
    $parameters['user_id'] = $user_id;
}

$sql .= " ORDER BY o.submit_time DESC";

// Execute the query
$stmt = $conn->prepare($sql);
$stmt->execute($parameters);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-pending { background-color: orange; color: white; font-weight: bold; }
        .status-processing { background-color: blue; color: white; font-weight: bold; }
        .status-success { background-color: green; color: white; font-weight: bold; }
        .progress-bar { transition: width 1s ease; height: 20px; }
        .modal-body p, .modal-body h6 { margin-bottom: 15px; font-size: 1.1em; }
        .modal-body pre { white-space: pre-wrap; word-wrap: break-word; background-color: #f4f4f4; padding: 10px; border-radius: 4px; }
        .modal-body .badge { font-size: 1.1em; padding: 5px 10px; }
        .modal-footer { display: flex; justify-content: flex-end; }
        .service-btn { background: none; border: none; padding: 8px 16px; text-align: left; font-size: 1rem; color: #007bff; font-weight: bold; cursor: pointer; }
        .service-btn:hover { background-color: #f0f0f0; border-radius: 5px; }
        table th, table td { vertical-align: middle; font-size: 1.2rem; }
        .modal-body h5 { font-size: 1.25rem; font-weight: bold; }
        .badge { font-size: 1.1rem; padding: 5px 10px; }
        .order-box { border: 1px solid #ddd; border-radius: 8px; padding: 20px; background-color: #f9f9f9; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Your Order History</h1>

        <!-- Filter Form -->
      <form method="GET" action="" id="filterForm">
    <div class="row mb-4">
        <div class="col-md-4">
            <label for="statusFilter" class="form-label">Filter by Order Status</label>
            <select id="statusFilter" name="status" class="form-select" onchange="this.form.submit()">
                <option value="">Select Status</option>
                <option value="Pending" <?php echo isset($_GET['status']) && $_GET['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="Processing" <?php echo isset($_GET['status']) && $_GET['status'] == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                <option value="Completed" <?php echo isset($_GET['status']) && $_GET['status'] == 'Completed' ? 'selected' : ''; ?>>Success</option>
            </select>
        </div>

        <div class="col-md-4">
            <label for="serviceFilter" class="form-label">Filter by Service</label>
            <select id="serviceFilter" name="service" class="form-select" onchange="this.form.submit()">
                <option value="">Select Service</option>
                <?php
                // Fetch distinct services to populate the dropdown
                $stmt_services = $conn->prepare("SELECT id, name FROM services");
                $stmt_services->execute();
                $services = $stmt_services->fetchAll(PDO::FETCH_ASSOC);
                foreach ($services as $service) {
                    echo "<option value=\"{$service['id']}\" " . (isset($_GET['service']) && $_GET['service'] == $service['id'] ? 'selected' : '') . ">{$service['name']}</option>";
                }
                ?>
            </select>
        </div>
    </div>


        <div class="order-box">
            <?php if (empty($orders)): ?>
            <div class="alert alert-warning">You have no orders yet.</div>
            <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <th>User</th>
                        <?php endif; ?>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Submit Time</th>
                        <th>Replay Time</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                        <th>Reply</th>
                        <th>Additional Fields</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): 
                        // Get status message for each order
                        $order_status_message = getOrderStatusMessage($order['order_status']);
                    ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <td><?php echo htmlspecialchars($order['username']); ?></td>
                        <?php endif; ?>
                        <td>
                            <button type="button" class="btn btn-info service-btn" data-bs-toggle="modal" data-bs-target="#serviceModal<?php echo $order['id']; ?>">
                                <?php echo htmlspecialchars($order['service_name']); ?>
                            </button>
                        </td>
                        <td>
                            <span class="badge 
                                <?php echo ($order['order_status'] == 'Pending') ? 'status-pending' : 
                                           ($order['order_status'] == 'Processing') ? 'status-processing' : 
                                           'status-success'; ?>">
                                <?php echo ucfirst($order['order_status'] == 'Completed' ? 'Success' : $order['order_status']); ?>
                            </span>
                            <p><small><?php echo $order_status_message; ?></small></p> <!-- Order status message -->
                        </td>
                        <td><?php echo $order['submit_time']; ?></td>
                        <td><?php echo $order['replay_time'] ? $order['replay_time'] : 'Not replied yet'; ?></td>
                        <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                        <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                        <td>
                            <?php if ($order['reply_text']): ?>
                                <strong>Reply:</strong> <?php echo htmlspecialchars($order['reply_text']); ?>
                            <?php else: ?>
                                No reply yet.
                            <?php endif; ?>
                        </td>

                        <!-- Fetch and display additional fields for each order -->
                        <td>
                            <?php
                            // Fetch additional fields for this order from the order_fields table
                            $order_id = $order['id'];
                            $stmt_fields = $conn->prepare("SELECT field_name, field_value FROM order_fields WHERE order_id = ?");
                            $stmt_fields->execute([$order_id]);
                            $fields = $stmt_fields->fetchAll(PDO::FETCH_ASSOC);

                            if ($fields): 
                                foreach ($fields as $field):
                            ?>
                                    <strong><?php echo htmlspecialchars(ucfirst($field['field_name'])); ?>:</strong> 
                                    <?php echo htmlspecialchars($field['field_value']); ?><br>
                            <?php endforeach; else: ?>
                                No additional fields provided.
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Modal for Service Details -->
                    <div class="modal fade" id="serviceModal<?php echo $order['id']; ?>" tabindex="-1" aria-labelledby="serviceModalLabel<?php echo $order['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="serviceModalLabel<?php echo $order['id']; ?>">View Order - Order ID: <?php echo $order['id']; ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h6 class="mb-3"><strong>Order ID:</strong> <?php echo $order['id']; ?></h6>

                                    <p><strong>Status:</strong> 
                                        <span class="badge 
                                            <?php echo ($order['order_status'] == 'Pending') ? 'status-pending' : 
                                                       ($order['order_status'] == 'Processing') ? 'status-processing' : 
                                                       'status-success'; ?>">
                                            <?php echo ucfirst($order['order_status'] == 'Completed' ? 'Success' : $order['order_status']); ?>
                                        </span>
                                    </p>
                                    <p><strong>Order Status Message:</strong> <?php echo $order_status_message; ?></p>

                                    <p><strong>Submit Time:</strong> <?php echo $order['submit_time']; ?></p>
                                    <p><strong>Replay Time:</strong> <?php echo $order['replay_time'] ? $order['replay_time'] : 'Not replied yet'; ?></p>

                                    <p><strong>Quantity:</strong> <?php echo $order['quantity']; ?></p>
                                    <p><strong>Total Price:</strong> $<?php echo number_format($order['total_price'], 2); ?></p>

                                    <h6>Reply:</h6>
                                    <div class="border p-3 mb-3" style="background-color: #f8f9fa;">
                                        <?php if ($order['reply_text']): ?>
                                            <pre><?php echo htmlspecialchars($order['reply_text']); ?></pre>
                                        <?php else: ?>
                                            No reply yet.
                                        <?php endif; ?>
                                    </div>

                                    <h6>Additional Fields:</h6>
                                    <?php
                                    // Display additional fields inside the modal
                                    if ($fields): 
                                        foreach ($fields as $field):
                                    ?>
                                            <strong><?php echo htmlspecialchars(ucfirst($field['field_name'])); ?>:</strong> 
                                            <?php echo htmlspecialchars($field['field_value']); ?><br>
                                    <?php endforeach; else: ?>
                                        No additional fields provided.
                                    <?php endif; ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <p><a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a></p>
    </div>

<?php include 'footer.php'; ?>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>
