<?php
session_start();
include '../db.php';

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

// Fetch pending payments and the corresponding username, along with the payment_type
$stmt = $conn->prepare("SELECT mp.id, mp.transaction_id, mp.user_id, mp.amount, mp.payment_method, mp.payment_type, u.username
                        FROM manual_payments mp
                        JOIN users u ON mp.user_id = u.id
                        WHERE mp.status = 'pending'");
$stmt->execute();
$payments = $stmt->fetchAll();

include 'header.php'; // Include the header for the page (optional)
?>

<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- Payment table -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Pending Payments</h3>
                        </div>
                        <div class="card-body">
                            <table id="pendingPaymentsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>User</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Payment Type</th> <!-- Added payment type column -->
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($payment['transaction_id']); ?></td>
                                            <td><?php echo htmlspecialchars($payment['username']); ?></td>
                                            <td><?php echo htmlspecialchars($payment['amount']); ?></td>
                                            <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                                            <td><?php echo htmlspecialchars($payment['payment_type']); ?></td> <!-- Display payment type -->
                                            <td>
                                                <a href="approve_payment.php?id=<?php echo $payment['id']; ?>" class="btn btn-success btn-sm">Approve</a>
                                                <a href="reject_payment.php?id=<?php echo $payment['id']; ?>" class="btn btn-danger btn-sm">Reject</a>
                                            </td>
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

<!-- Include any required JavaScript -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1.0/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables@1.10.21/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#pendingPaymentsTable').DataTable();
    });
</script>
</body>
</html>
