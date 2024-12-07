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

// Get user information
$stmt = $conn->prepare("SELECT username, `group`, balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user['username'] ?? '';
$group_name = $user['group'] ?? '';
$balance = $user['balance'] ?? 0;

try {
    // Fetch user name and login history for the user
    $stmt = $conn->prepare("
        SELECT u.name, lh.ip_address, lh.login_time 
        FROM users u
        JOIN login_history lh ON u.id = lh.user_id
        WHERE u.id = ?
        ORDER BY lh.login_time DESC
    ");
    $stmt->execute([$user_id]);
    $login_history = $stmt->fetchAll();

    // Fetch user name for display
    $stmt_user = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $stmt_user->execute([$user_id]);
    $user = $stmt_user->fetch();
    $user_name = $user['name'];
} catch (Exception $e) {
    die("Error fetching login history: " . $e->getMessage());
}

include 'header.php'; // Include user-side header
?>
<!-- Content Wrapper -->
<div class="content-wrapper">
    <section class="content-header text-center">
        <h2 class="text-primary fw-bold mb-3">Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>
        <p class="text-muted">Here is your login history:</p>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card bg-light shadow">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>IP Address</th>
                                            <th>Login Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($login_history)) : ?>
                                            <?php foreach ($login_history as $entry) : ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($entry['ip_address']); ?></td>
                                                    <td><?php echo htmlspecialchars(date('d M Y, h:i A', strtotime($entry['login_time']))); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">No login history found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
