<?php
session_start();
include '../db.php';

// Set default timezone to Asia/Dhaka (Bangladesh Standard Time)
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

// Get total lifetime credit transfers
$lifetime_transfer_stmt = $conn->query("SELECT SUM(amount) as lifetime_total_transfers FROM credit_transfers");
$lifetime_total_transfers = $lifetime_transfer_stmt->fetch()['lifetime_total_transfers'];
$lifetime_total_transfers = $lifetime_total_transfers ? $lifetime_total_transfers : 0;

// Get total added tools
$total_added_tools_stmt = $conn->query("SELECT COUNT(*) AS total FROM tools");
$total_added_tools = $total_added_tools_stmt->fetchColumn();

// Get total number of orders
$total_orders_stmt = $conn->query("SELECT COUNT(*) AS total_orders FROM orders");
$total_orders = $total_orders_stmt->fetchColumn();

// Get the last order update timestamp
$order_update_time_stmt = $conn->query("SELECT MAX(order_date) AS last_order_date FROM orders");
$last_order_date = $order_update_time_stmt->fetch()['last_order_date'];

// Get Pending Orders
$pending_orders_stmt = $conn->query("SELECT COUNT(*) AS total_pending FROM orders WHERE order_status = 'Pending'");
$total_pending_orders = $pending_orders_stmt->fetchColumn();

// Get Successful Orders
$successful_orders_stmt = $conn->query("SELECT COUNT(*) AS total_successful FROM orders WHERE order_status IN ('Success', 'Completed')");
$total_successful_orders = $successful_orders_stmt->fetchColumn();

include 'header.php'; // Admin header
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Background color for the whole page */
        body {
            background-color: #f4f6f9;
            font-family: Arial, sans-serif;
        }

        /* Hover animation for info boxes */
        .info-box {
            transition: all 0.3s ease-in-out;
            cursor: pointer;
        }

        .info-box:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        /* Info box styles */
        .info-box {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .info-box .info-box-icon {
            font-size: 2.5rem;
            border-radius: 10px 0 0 10px;
        }

        .info-box-content h3 {
            font-size: 2.2rem;
            font-weight: bold;
        }

        .info-box-content p {
            font-size: 1.2rem;
        }

        /* Gradient Backgrounds for cards */
        .bg-gradient-primary {
            background: linear-gradient(45deg, #007bff, #00aaff);
        }
        .bg-gradient-success {
            background: linear-gradient(45deg, #28a745, #34c759);
        }
        .bg-gradient-danger {
            background: linear-gradient(45deg, #dc3545, #ff4b5c);
        }
        .bg-gradient-info {
            background: linear-gradient(45deg, #17a2b8, #20c3f3);
        }

        /* Text color inside info-box */
        .text-white {
            color: white !important;
        }

        /* Live Time Section */
        .live-time {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            margin-top: 20px;
        }

        /* Fade effect for live time updates */
        .fade-update {
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        /* Animation for count-up effect */
        .count-up {
            font-size: 2.2rem;
            font-weight: bold;
            transition: all 1s ease-out;
        }
        <!-- Tooltip Container -->
<div class="tooltip-container">
    <div class="tooltip-content">
        <div class="tooltip-title">Access From</div>
        <div class="tooltip-data">
            <div class="tooltip-item">
                <span class="tooltip-dot"></span>
                <span class="tooltip-label">Direct</span>
                <span class="tooltip-value">735</span>
            </div>
        </div>
    </div>
</div>

<!-- Styles for Tooltip -->
<style>
    .tooltip-container {
        position: absolute;
        display: block;
        z-index: 9999999;
        top: 0;
        left: 0;
        box-shadow: rgba(0, 0, 0, 0.2) 1px 2px 10px;
        transition: opacity 0.2s cubic-bezier(0.23, 1, 0.32, 1), visibility 0.2s cubic-bezier(0.23, 1, 0.32, 1);
        background-color: rgb(255, 255, 255);
        border: 1px solid rgb(145, 204, 117);
        border-radius: 4px;
        color: rgb(102, 102, 102);
        font-family: "Microsoft YaHei", sans-serif;
        font-size: 14px;
        padding: 10px;
        visibility: hidden;  /* Initially hidden */
        opacity: 0;
        pointer-events: none;
        transform: translate3d(30px, 269px, 0);
    }

    .tooltip-container.visible {
        visibility: visible;
        opacity: 1;
    }

    .tooltip-content {
        margin: 0;
    }

    .tooltip-title {
        font-size: 14px;
        color: #666;
        font-weight: 400;
    }

    .tooltip-data {
        margin-top: 10px;
    }

    .tooltip-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        line-height: 1;
    }

    .tooltip-dot {
        display: inline-block;
        margin-right: 4px;
        border-radius: 10px;
        width: 10px;
        height: 10px;
        background-color: #91cc75;
    }

    .tooltip-label {
        font-size: 14px;
        color: #666;
        font-weight: 400;
    }

    .tooltip-value {
        font-size: 14px;
        color: #666;
        font-weight: 900;
    }
</style>

<!-- Example JavaScript to show/hide the tooltip -->
<script>
    // Example to toggle tooltip visibility
    setTimeout(function() {
        document.querySelector('.tooltip-container').classList.add('visible');
    }, 500);  // Show tooltip after 500ms

    // Example to hide the tooltip
    setTimeout(function() {
        document.querySelector('.tooltip-container').classList.remove('visible');
    }, 5000);  // Hide tooltip after 5 seconds
</script>

    </style>
</head>
<body>

<!-- Main Content -->
<div class="content-wrapper">
    <section class="content-header">
        <h4><i class="fa fa-caret-right fw-r5"></i> Admin Dashboard</h4>
    </section>

    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Admin Dashboard</li>
        </ol>
    </nav>

    <!-- Dashboard Stats Section -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <!-- Total Users -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow-lg mb-4 p-4 bg-gradient-primary rounded">
                        <span class="info-box-icon text-white">
                            <i class="fas fa-users"></i>
                        </span>
                        <div class="info-box-content">
                            <h3 class="text-white"><?php echo $total_users; ?></h3>
                            <p class="text-white">Total Users</p>
                        </div>
                    </div>
                </div>

                <!-- Total Generated Tools -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow-lg mb-4 p-4 bg-gradient-success rounded">
                        <span class="info-box-icon text-white">
                            <i class="fas fa-cogs"></i>
                        </span>
                        <div class="info-box-content">
                            <h3 class="text-white"><?php echo $total_generated_tools; ?></h3>
                            <p class="text-white">Generated Tools (Today)</p>
                        </div>
                    </div>
                </div>

                <!-- Total Credit Transfers Today -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow-lg mb-4 p-4 bg-gradient-danger rounded">
                        <span class="info-box-icon text-white">
                            <i class="fas fa-credit-card"></i>
                        </span>
                        <div class="info-box-content">
                            <h3 class="text-white"><?php echo $total_today_transfers; ?></h3>
                            <p class="text-white">Credit Transfers (Today)</p>
                        </div>
                    </div>
                </div>

                <!-- Lifetime Credit Transfers -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow-lg mb-4 p-4 bg-gradient-info rounded">
                        <span class="info-box-icon text-white">
                            <i class="fas fa-dollar-sign"></i>
                        </span>
                        <div class="info-box-content">
                            <h3 class="text-white"><?php echo number_format(htmlspecialchars($lifetime_total_transfers)); ?></h3>
                            <p class="text-white">Lifetime Credit Transfers</p>
                        </div>
                    </div>
                </div>

                <!-- Total Added Tools -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow-lg mb-4 p-4 bg-gradient-info rounded">
                        <span class="info-box-icon text-white">
                            <i class="fas fa-wrench"></i>
                        </span>
                        <div class="info-box-content">
                            <h3 class="text-white"><?php echo number_format(htmlspecialchars($total_added_tools)); ?></h3>
                            <p class="text-white">Total Added Tools</p>
                        </div>
                    </div>
                </div>

                <!-- Total Orders -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow-lg mb-4 p-4 bg-gradient-primary rounded">
                        <span class="info-box-icon text-white">
                            <i class="fas fa-box"></i>
                        </span>
                        <div class="info-box-content">
                            <h3 class="text-white count-up" id="order-count">0</h3>
                            <p class="text-white">Total Orders</p>
                            <p class="text-white small">Last updated: <?php echo $last_order_date; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Pending Orders -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow-lg mb-4 p-4 bg-gradient-danger rounded">
                        <span class="info-box-icon text-white">
                            <i class="fas fa-hourglass-half"></i>
                        </span>
                        <div class="info-box-content">
                            <h3 class="text-white"><?php echo $total_pending_orders; ?></h3>
                            <p class="text-white">Pending Orders</p>
                        </div>
                    </div>
                </div>

                <!-- Successful Orders -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow-lg mb-4 p-4 bg-gradient-success rounded">
                        <span class="info-box-icon text-white">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        <div class="info-box-content">
                            <h3 class="text-white"><?php echo $total_successful_orders; ?></h3>
                            <p class="text-white">Successful Orders</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Live Time Section -->
            <div class="live-time" id="live-time">
                <?php echo date('Y-m-d h:i:s A'); ?>
            </div>

        </div>
    </section>
</div>

<!-- Footer -->
<?php include 'footer.php'; ?>

<!-- JavaScript to animate the "Total Orders" count-up effect -->
<script>
    var totalOrders = <?php echo $total_orders; ?>;
    var orderCountElement = document.getElementById('order-count');

    let count = 0;
    let interval = setInterval(function() {
        count++;
        orderCountElement.textContent = count;
        if (count === totalOrders) {
            clearInterval(interval);
        }
    }, 5); // Speed of count-up, adjust as necessary
</script>

<!-- JavaScript to make the time live with fade effect -->
<script>
    setInterval(function() {
        var currentTime = new Date();
        var formattedTime = currentTime.toLocaleString('en-GB', {
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit'
        });

        var liveTimeElement = document.getElementById('live-time');
        liveTimeElement.textContent = formattedTime;

        // Add fade effect
        liveTimeElement.classList.add('fade-update');
        setTimeout(function() {
            liveTimeElement.classList.remove('fade-update');
        }, 500);
    }, 1000);
</script>

</body>
</html>
