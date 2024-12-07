<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="icon" href="../favicon.png" type="image/x-icon">
    
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1.0/dist/css/adminlte.min.css">

    <!-- Custom CSS (optional) -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        /* Reducing font size for text */
        body, .navbar, .sidebar, .nav-link, .brand-text, .user-panel .info, .nav-item p {
            font-size: 0.85rem; /* Smaller text size */
        }

        /* Prevent content from being covered by fixed navbar */
        .hold-transition {
            padding-top: 56px; /* Adjust this value based on your navbar height */
        }

        /* Ensuring the navbar stays on top */
        .main-header {
            z-index: 1050; /* Keep navbar on top */
        }
        
        /* Optional: If the sidebar is fixed, ensure its content doesn't overlap */
        .main-sidebar {
            z-index: 1040; /* Sidebar under the navbar */
        }

        /* Adjusting layout if needed */
        .wrapper {
            position: relative;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">

   <nav class="main-header navbar navbar-expand navbar-light bg-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- User Info with Notification -->
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-user"></i>
                <?php echo isset($username) ? htmlspecialchars($username) : 'Guest'; ?>

                <!-- Notification Badge -->
                <?php if ($notification_count > 0): ?>
                    <span class="badge badge-danger ml-2"><?php echo $notification_count; ?></span>
                <?php endif; ?>
            </a>
        </li>

        <!-- Logout Button -->
        <li class="nav-item">
            <button onclick="location.href='../logout.php';" class="btn btn-primary">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </li>
    </ul>
</nav>

    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-light-primary elevation-4">
        <!-- Brand Logo -->
        <a href="#" class="brand-link">
            <img src="assets/img/logo.png" alt="Admin Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">Admin Panel</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- User Panel -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="assets/img/user.png" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block"><?php echo htmlspecialchars($username); ?></a>
                    <span class="badge badge-success">Online</span>
                </div>
            </div>

            <!-- Search Form -->
            <div class="form-inline">
                <div class="input-group" data-widget="sidebar-search">
                    <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-sidebar">
                            <i class="fas fa-search fa-fw"></i>
                        </button>
                    </div>
                </div>
            </div>

            <?php
            $current_page = basename($_SERVER['PHP_SELF']); // Get current file name
            ?>

<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>

        <!-- Change "Manage Users" to "Client Name" -->
        <li class="nav-item">
            <a href="user_list.php" class="nav-link <?php echo ($current_page == 'user_list.php') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-users"></i>
                <p>Client Name</p>
            </a>
        </li>

        <!-- Group Transfer Credit and Withdraw Credit -->
        <li class="nav-item">
            <a href="#" class="nav-link <?php echo ($current_page == 'transfer_credit.php' || $current_page == 'withdraw_credit.php') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-money-bill"></i>
                <p>Credit Management</p>
                <i class="fas fa-angle-down float-right"></i>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="transfer_credit.php" class="nav-link <?php echo ($current_page == 'transfer_credit.php') ? 'active' : ''; ?>">
                        <i class="fas fa-money-bill-wave"></i> Transfer Credit
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin_add_credits.php" class="nav-link <?php echo ($current_page == 'admin_add_credits.php') ? 'active' : ''; ?>">
                        <i class="fas fa-money-bill-wave"></i> Transfer Server credit
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="withdraw_credit.php" class="nav-link <?php echo ($current_page == 'withdraw_credit.php') ? 'active' : ''; ?>">
                        <i class="fas fa-dollar-sign"></i> Withdraw Credit
                    </a>
                </li>
            </ul>
        </li>
 <li class="nav-item">
                        <a href="AddTool.php" class="nav-link <?php echo ($current_page == 'AddTool.php') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-plus"></i>
                            <p>Add Tools</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="ViewTools.php" class="nav-link <?php echo ($current_page == 'ViewTools.php') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Used Tools</p>
                        </a>
                    </li>

        <!-- Server Service Section -->
        <li class="nav-item">
            <a href="#" class="nav-link <?php echo ($current_page == 'server_service.php') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-server"></i>
                <p>Server Service</p>
                <i class="fas fa-angle-down float-right"></i>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="add_imei_service.php" class="nav-link <?php echo ($current_page == 'add_imei_service.php') ? 'active' : ''; ?>">
                        <i class="fas fa-cogs"></i> Add IMEI Services
                    </a>
                </li>
                <li class="nav-item">
                    <a href="add_server_service.php" class="nav-link <?php echo ($current_page == 'add_server_service.php') ? 'active' : ''; ?>">
                        <i class="fas fa-cogs"></i> Add Server Services
                    </a>
                </li>
                <li class="nav-item">
                    <a href="manage_services.php" class="nav-link <?php echo ($current_page == 'manage_services.php') ? 'active' : ''; ?>">
                        <i class="fas fa-file-alt"></i> Manage IMEI Service
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="manage_server_services.php" class="nav-link <?php echo ($current_page == 'manage_server_services.php') ? 'active' : ''; ?>">
                        <i class="fas fa-file-alt"></i> Manage Server Service
                    </a>
                </li>
            </ul>
        </li>

        <!-- Server Order Section -->
        <li class="nav-item">
            <a href="#" class="nav-link <?php echo ($current_page == 'server_orders.php') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-box"></i>
                <p>Server Orders</p>
                <i class="fas fa-angle-down float-right"></i>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="manage_orders.php" class="nav-link <?php echo ($current_page == 'manage_orders.php') ? 'active' : ''; ?>">
                        <i class="fas fa-tasks"></i> Manage Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a href="server_order.php" class="nav-link <?php echo ($current_page == 'server_order.php') ? 'active' : ''; ?>">
                        <i class="fas fa-file-alt"></i> Server Orders
                    </a>
                </li>
            </ul>
        </li>

        <!-- Payment Section -->
        <li class="nav-item">
            <a href="#" class="nav-link <?php echo ($current_page == 'manage_payments.php') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-credit-card"></i>
                <p>Payment Management</p>
                <i class="fas fa-angle-down float-right"></i>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="manage_payments.php" class="nav-link <?php echo ($current_page == 'manage_payments.php') ? 'active' : ''; ?>">
                        <i class="fas fa-history"></i> Manage Payments
                    </a>
                </li>
                <li class="nav-item">
                    <a href="pending_payments.php" class="nav-link <?php echo ($current_page == 'pending_payments.php') ? 'active' : ''; ?>">
                        <i class="fas fa-clock"></i> Pending Payments
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-item">
            <a href="categories.php" class="nav-link <?php echo ($current_page == 'categories.php') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-list-alt"></i>
                <p>Tools Categories</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="smtp_settings.php" class="nav-link <?php echo ($current_page == 'smtp_settings.php') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-tools"></i>
                <p>SMTP Settings</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="update_settings.php" class="nav-link <?php echo ($current_page == 'main_settings.php') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-cogs"></i>
                <p>Main Settings</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="update_telegram_settings.php" class="nav-link <?php echo ($current_page == 'update_telegram_settings.php') ? 'active' : ''; ?>">
                <i class="nav-icon fab fa-telegram"></i>
                <p>Telegram</p>
            </a>
        </li>

        <!-- Add Menu Manager Section -->
        <li class="nav-item">
            <a href="#" class="nav-link" id="menu-manager-toggle">
                <i class="nav-icon fas fa-th"></i>
                <p>Menu Manager</p>
                <i class="fas fa-angle-down float-right"></i>
            </a>
            <div class="menu-manager-options" id="menu-manager-options" style="display: none;">
                <div class="card bg-light mb-2">
                    <div class="card-body p-2">
                        <a href="upload_slider.php" class="btn btn-outline-primary btn-block">
                            <i class="fas fa-image"></i> Slider
                        </a>
                    </div>
                </div>
                <div class="card bg-light mb-2">
                    <div class="card-body p-2">
                        <a href="admin_pricing.php" class="btn btn-outline-warning btn-block">
                            <i class="fas fa-box"></i> Price Box
                        </a>
                    </div>
                </div>
            </div>
        </li>

        <!-- Payment Section -->
        <li class="nav-item">
            <a href="#" class="nav-link" id="payment-toggle">
                <i class="nav-icon fas fa-credit-card"></i>
                <p>Payment Settings</p>
                <i class="fas fa-angle-down float-right"></i>
            </a>
            <div class="payment-options" id="payment-options" style="display: none;">
                <div class="card bg-light mb-2">
                    <div class="card-body p-2">
                        <a href="payment_method.php" class="btn btn-outline-primary btn-block">
                            <i class="fas fa-cogs"></i> Payment Methods
                        </a>
                        
                                <!-- Move "Change Password" to the main menu -->
        <li class="nav-item">
            <a href="pass.php" class="nav-link <?php echo ($current_page == 'change_password.php') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-key text-info"></i> Change Password
            </a>
        </li>

                                <!-- Move "Check Update" to the bottom -->
        <li class="nav-item">
            <a href="update.php" class="nav-link <?php echo ($current_page == 'update.php') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-sync-alt text-warning"></i> Check Update
            </a>
        </li>
        

                    </div>
                </div>
            </div>
        </li>

    </ul>
</nav>

        </div>
    </aside>


<!-- ./wrapper -->

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1.0/dist/js/adminlte.min.js"></script>

<script>
    // Toggle visibility of Menu Manager options
    document.getElementById('menu-manager-toggle').addEventListener('click', function(event) {
        event.preventDefault();
        const menuOptions = document.getElementById('menu-manager-options');
        const angleIcon = this.querySelector('i.fas.fa-angle-down');
        if (menuOptions.style.display === 'none' || menuOptions.style.display === '') {
            menuOptions.style.display = 'block';
            angleIcon.classList.remove('fa-angle-down');
            angleIcon.classList.add('fa-angle-up');
        } else {
            menuOptions.style.display = 'none';
            angleIcon.classList.remove('fa-angle-up');
            angleIcon.classList.add('fa-angle-down');
        }
    });

    // Toggle visibility of Payment options
    document.getElementById('payment-toggle').addEventListener('click', function(event) {
        event.preventDefault();
        const paymentOptions = document.getElementById('payment-options');
        const angleIcon = this.querySelector('i.fas.fa-angle-down');
        if (paymentOptions.style.display === 'none' || paymentOptions.style.display === '') {
            paymentOptions.style.display = 'block';
            angleIcon.classList.remove('fa-angle-down');
            angleIcon.classList.add('fa-angle-up');
        } else {
            paymentOptions.style.display = 'none';
            angleIcon.classList.remove('fa-angle-up');
            angleIcon.classList.add('fa-angle-down');
        }
    });
</script>
</body>
</html>
