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

// Get the selected status filter (if any)
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

// Prepare the query with named parameters
$sql = "
    SELECT 
        o.id, 
        s.name AS service, 
        ss.price AS service_price,  
        o.submit_time, 
        o.replay_time, 
        o.additional_info, 
        o.requirements, 
        o.status, 
        o.replay_note, 
        o.created_at 
    FROM 
        server_order o
    JOIN 
        services s 
    ON 
        o.service_id = s.id
    JOIN 
        server_services ss  
    ON 
        o.service_id = ss.id
    WHERE 
        o.user_id = :user_id";

if (!empty($statusFilter)) {
    $sql .= " AND o.status = :status";
}

$sql .= " ORDER BY o.submit_time DESC";

// Prepare the statement
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT); 

if (!empty($statusFilter)) {
    $stmt->bindParam(':status', $statusFilter, PDO::PARAM_STR); 
}

// Execute the query
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to calculate time difference in minutes
function getTimeDifference($submitTime, $replayTime) {
    $submitDateTime = new DateTime($submitTime);
    $replayDateTime = new DateTime($replayTime);
    $interval = $submitDateTime->diff($replayDateTime);
    return $interval->format('%h hours %i minutes'); 
}
?>

<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .order-table th, .order-table td {
            text-align: center;
        }
        .order-table thead {
            background-color: #f8f9fa;
        }
        .order-table th {
            font-size: 1.1rem;
            color: #495057;
        }
        .order-table td {
            font-size: 1rem;
        }
        .alert-warning {
            font-size: 1.1rem;
        }
        .replay-note {
            word-wrap: break-word;
        }
        .modal-content {
            max-width: 800px;
            margin: 0 auto;
        }
        .badge-custom {
            font-size: 0.9rem;
        }
        .service-column {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .filter-form select {
            width: auto;
            padding: 0.5rem;
            border-radius: 0.375rem;
        }
    </style>
</head>
<body>

<div class="container my-5">
    <h1 class="text-center mb-4">Order History</h1>

    <!-- Filter Form -->
    <form method="GET" action="" class="mb-4 filter-form">
        <div class="row justify-content-center">
            <div class="col-auto">
                <label for="status" class="form-label">Filter by Status:</label>
                <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                    <option value="">--Select Status--</option>
                    <option value="Pending" <?php echo $statusFilter == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="In Progress" <?php echo $statusFilter == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="Rejected" <?php echo $statusFilter == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                    <option value="Success" <?php echo $statusFilter == 'Success' ? 'selected' : ''; ?>>Success</option>
                </select>
            </div>
        </div>
    </form>

    <?php if (empty($orders)): ?>
        <div class="alert alert-warning text-center">You have not placed any orders yet.</div>
    <?php else: ?>
    <table class="table table-bordered table-striped order-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Service</th>
                <th>Price</th>
                <th>Submit Time</th>
                <th>Replay Time</th>
                <th>Total Working Time</th>  
                <th>Additional Info</th>
                <th>Requirements</th>
                <th>Status</th>
                <th>Replay Note</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?php echo htmlspecialchars($order['id']); ?></td>
                <td class="service-column" title="<?php echo htmlspecialchars($order['service']); ?>">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#serviceModal<?php echo $order['id']; ?>">
                        <?php echo htmlspecialchars($order['service']); ?>
                    </a>
                </td>
                <td><?php echo '$' . number_format($order['service_price'], 2); ?></td>
                <td><?php echo date("h:i A", strtotime($order['submit_time'])); ?></td>
                <td><?php echo !empty($order['replay_time']) ? date("h:i A", strtotime($order['replay_time'])) : 'Not yet replayed'; ?></td>
                
                <td>
                    <?php
                        if (!empty($order['replay_time'])) {
                            echo getTimeDifference($order['submit_time'], $order['replay_time']);
                        } else {
                            echo 'N/A';  
                        }
                    ?>
                </td>

                <td><?php echo htmlspecialchars($order['additional_info']); ?></td>
                <td><?php echo htmlspecialchars($order['requirements']); ?></td>
                <td>
                    <?php 
                        $status = htmlspecialchars($order['status']);
                        echo $status == 'Pending' ? "<span class='badge bg-warning text-dark badge-custom'>$status</span>" :
                             ($status == 'In Progress' ? "<span class='badge bg-info text-white badge-custom'>$status</span>" :
                             ($status == 'Rejected' ? "<span class='badge bg-danger text-white badge-custom'>$status</span>" :
                             "<span class='badge bg-success badge-custom'>$status</span>"));
                    ?>
                </td>
                <td class="replay-note">
                    <?php 
                        echo !empty($order['replay_note']) ? htmlspecialchars($order['replay_note']) : 'No replay note.';
                    ?>
                </td>
                <td><?php echo htmlspecialchars($order['created_at']); ?></td>
            </tr>
            
            <!-- Modal for Service Details -->
            <div class="modal fade" id="serviceModal<?php echo $order['id']; ?>" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="serviceModalLabel"><?php echo htmlspecialchars($order['service']); ?> Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Service:</strong> <?php echo htmlspecialchars($order['service']); ?></p>
                            <p><strong>Price:</strong> $<?php echo number_format($order['service_price'], 2); ?></p>
                            <p><strong>Submit Time:</strong> <?php echo date("h:i A", strtotime($order['submit_time'])); ?></p>
                            <p><strong>Replay Time:</strong> <?php echo !empty($order['replay_time']) ? date("h:i A", strtotime($order['replay_time'])) : 'Not yet replayed'; ?></p>
                            <p><strong>Total Working Time:</strong> 
                                <?php 
                                    if (!empty($order['replay_time'])) {
                                        echo getTimeDifference($order['submit_time'], $order['replay_time']);
                                    } else {
                                        echo 'N/A';
                                    }
                                ?>
                            </p>
                            <p><strong>Additional Info:</strong> <?php echo htmlspecialchars($order['additional_info']); ?></p>
                            <p><strong>Requirements:</strong> <?php echo htmlspecialchars($order['requirements']); ?></p>
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
    
    <div class="text-center">
        <a href="dashboard.php" class="btn btn-secondary mt-4">Back to Dashboard</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'footer.php'; ?>
