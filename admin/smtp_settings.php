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

// Fetch current SMTP settings from the database
$stmt = $conn->query("SELECT * FROM smtp_settings WHERE id = 1");
$smtp = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['host'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $secure = $_POST['secure'];
    $port = $_POST['port'];
    $from_email = $_POST['from_email'];
    $from_name = $_POST['from_name'];

    // Insert or update SMTP settings in the database
    $stmt = $conn->prepare("REPLACE INTO smtp_settings (id, host, username, password, secure, port, from_email, from_name) VALUES (1, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$host, $username, $password, $secure, $port, $from_email, $from_name]);

    $success = true; // Flag to show the modal
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
                <h4><i class="fa fa-caret-right fw-r5"></i> SMTP Settings</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">SMTP Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">SMTP Settings Form</h3>
                        </div>
                        <!-- form start -->
                        <form method="POST">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="host">SMTP Host</label>
                                    <input type="text" name="host" class="form-control" id="host" placeholder="SMTP Host" value="<?php echo htmlspecialchars($smtp['host']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="username">SMTP Username</label>
                                    <input type="text" name="username" class="form-control" id="username" placeholder="SMTP Username" value="<?php echo htmlspecialchars($smtp['username']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">SMTP Password</label>
                                    <input type="password" name="password" class="form-control" id="password" placeholder="SMTP Password" value="<?php echo htmlspecialchars($smtp['password']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="secure">Encryption</label>
                                    <select class="form-control" name="secure" id="secure" required>
                                        <option value="">None</option>
                                        <option value="tls" <?php echo $smtp['secure'] === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                        <option value="ssl" <?php echo $smtp['secure'] === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="port">SMTP Port</label>
                                    <input type="number" name="port" class="form-control" id="port" placeholder="SMTP Port" value="<?php echo htmlspecialchars($smtp['port']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="from_email">Set From Email</label>
                                    <input type="email" name="from_email" class="form-control" id="from_email" placeholder="Set From Email" value="<?php echo htmlspecialchars($smtp['from_email']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="from_name">Set From Name</label>
                                    <input type="text" name="from_name" class="form-control" id="from_name" placeholder="Set From Name" value="<?php echo htmlspecialchars($smtp['from_name']); ?>" required>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save Settings</button>
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
                SMTP settings have been saved successfully!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; // AdminLTE footer ?>

<!-- Add this script to show the modal if settings are saved -->
<?php if ($success): ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#successModal').modal('show');
    });
</script>
<?php endif; ?>
