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

$success = false;
$users = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiver_username = $_POST['receiver_username'];
    $amount = $_POST['amount'];

    // Get receiver ID from username
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$receiver_username]);
    $receiver = $stmt->fetch();
    $receiver_id = $receiver ? $receiver['id'] : null;

    if ($receiver_id) {
        // Update receiver's balance
        $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$amount, $receiver_id]);

        // Log the transfer
        $stmt = $conn->prepare("INSERT INTO credit_transfers (sender_id, receiver_id, amount) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $receiver_id, $amount]);

        $success = true; // Flag to show the modal
    } else {
        echo 'Receiver not found.';
    }
}

// Fetch all users for dropdown
try {
    $users_stmt = $conn->query("SELECT username FROM users WHERE role='user'");
    $users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Database error: ' . $e->getMessage();
}

include 'header.php'; // AdminLTE header, sidebar, and navbar
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h4><i class="fa fa-caret-right fw-r5"></i> Transfer Credit</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Transfer Credit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Transfer Credit Form</h3>
                        </div>
                        <!-- form start -->
                        <form method="POST">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="receiverUsername">Receiver Username</label>
                                    <select name="receiver_username" class="form-control" id="receiverUsername" required>
                                        <option value="">Select User</option>
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?php echo htmlspecialchars($user['username']); ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="amount">Amount</label>
                                    <input type="number" name="amount" class="form-control" id="amount" placeholder="Enter Amount" required>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Success</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Transfer successful!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; // AdminLTE footer ?>

<!-- Add this script to show the modal if transfer is successful -->
<?php if ($success): ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#successModal').modal('show');
    });
</script>
<?php endif; ?>
