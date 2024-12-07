<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="icon" href="../favicon.png" type="image/x-icon">

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Roboto', sans-serif;
            margin: 0;
        }

        /* Top Header */
        .top-header {
            background-color: #2563eb;
            color: white;
            padding: 10px 0;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
        }

        .top-header a {
            color: white;
            text-decoration: none;
        }

        .top-header a:hover {
            color: #ffd700;
        }

        /* Navbar */
        .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar .navbar-brand img {
            max-height: 50px;
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            margin-left: auto;
        }

        .nav-link {
            font-weight: 600;
            color: #374151;
        }

        .nav-link:hover {
            color: #2563eb;
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-left: 20px;
        }

        .user-info .user-name {
            font-size: 16px;
            font-weight: bold;
            margin-right: 15px;
        }

        .user-info .btn {
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 20px;
            background-color: #2563eb;
            color: white;
            border: none;
        }

        .user-info .btn:hover {
            background-color: #1e40af;
        }

        .navbar-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 10px 30px;
        }

        /* Dropdown style */
        .dropdown-menu {
            min-width: 200px;
        }

        .dropdown-item {
            font-size: 14px;
            font-weight: 500;
        }

        /* Profile and Order History Dropdowns */
        .profile-dropdown,
        .order-history-dropdown {
            position: relative;
            z-index: 1001;
        }

        .dropdown-toggle::after {
            content: none; /* Remove the default dropdown icon */
        }

        /* Container with max-width to keep the content centered */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding-left: 15px;
            padding-right: 15px;
        }

        /* Center the content on mobile */
        @media (max-width: 768px) {
            .navbar-custom {
                flex-direction: column;
                align-items: flex-start;
            }

            .user-info {
                margin-top: 10px;
                margin-left: 0;
            }

            .navbar-nav {
                flex-direction: column;
                align-items: flex-start;
                width: 100%;
            }

            .user-info .user-name {
                margin-right: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Top Header Section -->
    <div class="top-header">
        <span>Welcome to Our Dashboard! <a href="contact.php">Contact Support</a> | <a href="faq.php">FAQ</a></span>
    </div>

    <!-- Main Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="navbar-custom">
            <!-- Logo on the left side -->
            <a class="navbar-brand" href="#">
                <img src="../user/img/logo.png" alt="Logo" class="img-fluid rounded-circle">
            </a>

            <!-- Right side with username, home link, and dropdowns -->
            <div class="user-info">
                <!-- Display Username and Account Balance -->
                <span class="user-name">
                    <?php 
                        // Assuming username and credit are set in the session or database
                        $username = isset($username) ? htmlspecialchars($username) : 'Guest';
                        $credit = isset($credit) ? number_format($credit, 2) : '0.00'; // Format credit
                         $balance = isset($balance) ? number_format($balance, 2) : '0.00'; // Format credit
                        echo "$username | Credit: $credit";
                        echo " | Tool: $balance";
                        
                    ?>
                </span>
                
                <a href="dashboard.php" class="btn btn-outline-primary me-2">Home</a>

                <!-- Order History Dropdown -->
                <div class="dropdown ms-2">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="orderHistoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-history"></i> Order History
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="orderHistoryDropdown">
                        <li><a class="dropdown-item" href="order_history.php"><i class="fas fa-history"></i> View Orders</a></li>
                        <li><a class="dropdown-item" href="server_order-history.php"><i class="fas fa-history"></i> View Server</a></li>
                        <li><a class="dropdown-item" href="history.php"><i class="fas fa-hourglass-start"></i> View Tools</a></li>
                    </ul>
                    
                </div>

                <!-- Place Order Dropdown -->
<div class="dropdown ms-2">
    <button class="btn btn-primary dropdown-toggle" type="button" id="placeOrderDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-cart-plus"></i> Place Order
    </button>
    <ul class="dropdown-menu" aria-labelledby="placeOrderDropdown">
        <li><a class="dropdown-item" href="SelectTool.php"><i class="fas fa-cogs"></i> Tool Generate</a></li>
        <li><a class="dropdown-item" href="place_order.php"><i class="fas fa-sim-card"></i> IMEI Service</a></li>
        <!-- New Section for Server Service -->
        <li><a class="dropdown-item" href="server_service.php"><i class="fas fa-server"></i> Server Service</a></li>
    </ul>
</div>


                <!-- Profile Dropdown -->
                <div class="dropdown ms-2">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i> Profile
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="submit_payment.php"><i class="fas fa-plus-circle"></i> Add Fund</a></li>
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-lock"></i> Edit Profile</a></li>
                        <li><a class="dropdown-item" href="login_history.php"><i class="fas fa-history"></i> Login History</a></li>
                        <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Bootstrap 5 JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
