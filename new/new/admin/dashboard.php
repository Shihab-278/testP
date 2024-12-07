<?php
session_start();
include '../db.php';

// Set timezone to Asia/Dhaka (Bangladesh Standard Time)
date_default_timezone_set('Asia/Dhaka');

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

// Get user information from the database
$stmt = $conn->prepare("SELECT username, `group` FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user ? $user['username'] : '';
$group_name = $user ? $user['group'] : '';

// Get total number of users
$user_count_stmt = $conn->query("SELECT COUNT(*) as total_users FROM users WHERE role='user'");
$total_users = $user_count_stmt->fetch()['total_users'];

// Get total number of generated tools
$generated_tools_stmt = $conn->query("SELECT COUNT(*) as total_generated_tools FROM user_tools");
$total_generated_tools = $generated_tools_stmt->fetch()['total_generated_tools'];

// Get total credit transfers for today
$today = date('Y-m-d'); // Format: YYYY-MM-DD
$transfer_stmt = $conn->prepare("SELECT SUM(amount) as total_today_transfers FROM credit_transfers WHERE DATE(transfer_date) = ?");
$transfer_stmt->execute([$today]);
$total_today_transfers = $transfer_stmt->fetch()['total_today_transfers'];

// Ensure we default to 0 if no transfers were made today
$total_today_transfers = $total_today_transfers ? $total_today_transfers : 0;

$total_added_tools_stmt = $conn->query("SELECT COUNT(*) AS total FROM tools");
$total_added_tools = $total_added_tools_stmt->fetchColumn();

include 'header.php'; // Admin header
?>


<div class="content-wrapper">
    <section class="content-header">
        <h4><i class="fa fa-caret-right fw-r5"></i> Admin Dashboard</h4>
    </section>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Admin Dashboard</li>
        </ol>
    </nav>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Total Users -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow" style="border-radius: 10px; transition: transform 0.3s; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);">
                        <span class="info-box-icon bg-info" style="border-radius: 10px 0 0 10px;">
                            <i class="fas fa-users"></i>
                        </span>
                        <div class="info-box-content">
                            <h3><?php echo $total_users; ?></h3>
                            <p>Total Users</p>
                        </div>
                    </div>
                </div>

                <!-- Total Generated Tools -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow" style="border-radius: 10px; transition: transform 0.3s; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);">
                        <span class="info-box-icon bg-success" style="border-radius: 10px 0 0 10px;">
                            <i class="fas fa-tools"></i>
                        </span>
                        <div class="info-box-content">
                            <h3><?php echo $total_generated_tools; ?></h3>
                            <p>Generated Tools (Today)</p>
                        </div>
                    </div>
                </div>

                <!-- Total Credit Transfers Today -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow" style="border-radius: 10px; transition: transform 0.3s; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);">
                        <span class="info-box-icon bg-danger" style="border-radius: 10px 0 0 10px;">
                            <i class="fas fa-credit-card"></i>
                        </span>
                        <div class="info-box-content">
                            <h3><?php echo $total_today_transfers; ?></h3>
                            <p>Credit Transfers (Today)</p>
                        </div>
                    </div>
                </div>
                
                <!-- Total Added Tools -->
<div class="col-12 col-sm-6 col-md-3">
    <div class="info-box shadow" style="border-radius: 10px; transition: transform 0.3s; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);">
        <span class="info-box-icon bg-primary" style="border-radius: 10px 0 0 10px;">
            <i class="fas fa-tools"></i>
        </span>
        <div class="info-box-content">
            <h3><?php echo number_format(htmlspecialchars($total_added_tools)); ?></h3>
            <p>Total Added Tools</p>
        </div>
    </div>
</div>

               

            </div>
        </div>
    </section>
</div>



<!-- /.content-wrapper -->
<?php include 'footer.php'; // Admin footer 
?>