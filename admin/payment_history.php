<?php
// Start session and include database connection
session_start();
include '../db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Retrieve the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch payment history for the logged-in user
$stmt = $conn->prepare("SELECT * FROM manual_payments WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$payments = $stmt->fetchAll();

include 'header.php'; // Include the header for the page (optional)
?>

<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- Payment History Table -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Payment History</h3>
                        </div>
                        <div class="card-body">
                            <table id="paymentHistoryTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($payment['transaction_id']); ?></td>
                                            <td><?php echo htmlspecialchars($payment['amount']); ?></td>
                                            <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                                            <td>
                                                <?php
                                                // Display status with different colors
                                                switch ($payment['status']) {
                                                    case 'approved':
                                                        echo '<span class="badge badge-success">Approved</span>';
                                                        break;
                                                    case 'rejected':
                                                        echo '<span class="badge badge-danger">Rejected</span>';
                                                        break;
                                                    case 'pending':
                                                        echo '<span class="badge badge-warning">Pending</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="badge badge-secondary">Unknown</span>';
                                                        break;
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($payment['created_at']); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Include footer (optional) -->
<?php include 'footer.php'; ?>

<!-- Include necessary JavaScript -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1.0/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables@1.10.21/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#paymentHistoryTable').DataTable();
    });
</script>

</body>
</html>
