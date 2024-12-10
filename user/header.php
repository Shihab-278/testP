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
        .nav-button {
            padding: 8px 25px !important;
            font-size: 14px;
            border-radius: 20px;
            background-color: #2563eb;
            color: white;
            border: none;
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
    </style>
</head>

<body>
    <div class="top-header">
        <span>Welcome to Our Dashboard! <a href="contact.php">Contact Support</a> | <a href="faq.php">FAQ</a></span>
    </div>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="../user/img/logo.png" alt="Logo" class="img-fluid rounded-circle" width="40" height="24">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse flex-grow-0" id="navbarSupportedContent">
                <span class="user-name me-2">
                    <?php
                    // Assuming username and credit are set in the session or database
                    $username = isset($username) ? htmlspecialchars($username) : 'Guest';
                    $credit = isset($credit) ? number_format($credit, 2) : '0.00'; // Format credit
                    $balance = isset($balance) ? number_format($balance, 2) : '0.00'; // Format credit
                    echo "$username | Credit: $credit";
                    echo " | Tool: $balance";

                    ?>      
                </span>
                
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item me-2">
                        <a class="nav-link nav-button" aria-current="page" href="dashboard.php" class="btn btn-outline-primary me-2">Home</a>
                    </li>

                    <li class="nav-item me-2 dropdown">
                        <a class="nav-link dropdown-toggle nav-button" href="#" role="button" id="orderHistoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-history"></i> Order History
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="orderHistoryDropdown">
                            <li><a class="dropdown-item" href="order_history.php"><i class="fas fa-history"></i> View Orders</a></li>
                            <li><a class="dropdown-item" href="server_order-history.php"><i class="fas fa-history"></i> View Server</a></li>
                            <li><a class="dropdown-item" href="history.php"><i class="fas fa-hourglass-start"></i> View Tools</a></li>

                        </ul>
                    </li>

                    <li class="nav-item me-2 dropdown">
                        <a class="nav-link dropdown-toggle nav-button" href="#" role="button" id="placeOrderDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cart-plus"></i> Place Order
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="placeOrderDropdown">
                            <li><a class="dropdown-item" href="SelectTool.php"><i class="fas fa-cogs"></i> Tool Generate</a></li>
                            <li><a class="dropdown-item" href="place_order.php"><i class="fas fa-sim-card"></i> IMEI Service</a></li>
                            <!-- New Section for Server Service -->
                            <li><a class="dropdown-item" href="server_service.php"><i class="fas fa-server"></i> Server Service</a></li>
                        </ul>
                    </li>


                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle nav-button" href="#" role="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle"></i> Profile
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item" href="submit_payment.php"><i class="fas fa-plus-circle"></i> Add Fund</a></li>
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-lock"></i> Edit Profile</a></li>
                            <li><a class="dropdown-item" href="login_history.php"><i class="fas fa-history"></i> Login History</a></li>
                            <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>

                </ul>
            </div>
        </div>
    </nav>




    <!-- Bootstrap 5 JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>