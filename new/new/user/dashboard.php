<?php
session_start();
include '../db.php';

// Set timezone to Asia/Kolkata (Indian Standard Time)
date_default_timezone_set('Asia/Kolkata');
// Check if the user is logged in and has the correct role
if ($_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

// Retrieve user ID from session
$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT username, `group`, balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user['username'] ?? '';
$group_name = $user['group'] ?? '';
$balance = $user['balance'] ?? 0;

// Fetch user's generated tools
$tools_stmt = $conn->prepare("SELECT tools.tool_name, tools.tool_username, tools.tool_password, user_tools.generated_at 
                              FROM user_tools 
                              JOIN tools ON user_tools.tool_id = tools.id 
                              WHERE user_tools.user_id = ?");
$tools_stmt->execute([$user_id]);
$generated_tools = $tools_stmt->fetchAll();

// Fetch today's credit usage
$today = date('Y-m-d');
$credit_stmt = $conn->prepare("SELECT COALESCE(SUM(credits_used), 0) AS total_used 
                               FROM credit_usage 
                               WHERE user_id_fk = ? AND DATE(usage_date) = ?");
$credit_stmt->execute([$user_id, $today]);
$total_today_amount = $credit_stmt->fetchColumn();

include 'header.php'; // Include user-side header
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card-box {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }
        .card-box:hover {
            transform: scale(1.05);
        }
        .icon-box {
            font-size: 2rem;
            opacity: 0.8;
        }
        .content-header h4 {
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="container my-4">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>

    <!-- Dashboard Header -->
    <div class="content-header mb-4">
        <h4><i class="fas fa-tachometer-alt"></i> User Dashboard</h4>
        <p class="text-muted">Welcome back, <?php echo htmlspecialchars($username); ?>!</p>
    </div>

    <!-- Dashboard Content -->
    <div class="row">
        <!-- Card: Account Balance -->
        <div class="col-md-4">
            <div class="card card-box bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Credits in Account</h5>
                            <h3><?php echo htmlspecialchars($balance); ?></h3>
                        </div>
                        <div class="icon-box">
                            <i class="fas fa-credit-card"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Generated Tools -->
        <div class="col-md-4">
            <div class="card card-box bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Generated Tools</h5>
                            <h3><?php echo count($generated_tools); ?></h3>
                        </div>
                        <div class="icon-box">
                            <i class="fas fa-tools"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Today's Credit Usage -->
        <div class="col-md-4">
            <div class="card card-box bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Today's Credit Usage</h5>
                            <h3><?php echo htmlspecialchars($total_today_amount); ?></h3>
                        </div>
                        <div class="icon-box">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
