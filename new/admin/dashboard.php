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

// Function to get the user's time zone based on IP
function getUserTimeZoneByIP() {
    $ip = $_SERVER['REMOTE_ADDR'];  // Get the user's IP address
    $access_key = 'YOUR_API_ACCESS_KEY';  // Replace with your IP geolocation API key
    
    // Fetch IP details from the geolocation API
    $url = "http://api.ipstack.com/$ip?access_key=$access_key";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    
    // Return timezone if available, otherwise default to Asia/Dhaka
    return isset($data['time_zone']['id']) ? $data['time_zone']['id'] : 'Asia/Dhaka';
}

// Get the user's timezone
$user_timezone = getUserTimeZoneByIP();

// Set the timezone for the application
date_default_timezone_set($user_timezone);

// Display the current time in AM/PM format
$current_time = date('Y-m-d h:i:s A');

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

            </div>
        </div>
    </section>
</div>

<!-- Footer -->
<?php include 'footer.php'; ?>

<!-- JavaScript to make time
