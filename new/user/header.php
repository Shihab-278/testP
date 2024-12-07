<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOM Hoster Dashboard</title>
    <link rel="icon" href="../favicon.ico" type="image/x-icon">

    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1.0/dist/css/adminlte.min.css">

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Roboto', sans-serif;
        }
        .navbar {
            background: linear-gradient(90deg, #1e293b, #334155);
        }
        .navbar-dark .navbar-nav .nav-link {
            color: #f8fafc;
        }
        .navbar-dark .navbar-nav .nav-link:hover {
            color: #60a5fa;
        }
        .btn-primary {
            background-color: #2563eb;
            border: none;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #1e40af;
        }
        .sidebar-dark-primary {
            background-color: #1f2937;
        }
        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active {
            background-color: #2563eb;
            color: #fff;
        }
        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link:hover {
            background-color: #3b82f6;
            color: #fff;
        }
        .nav-icon {
            transition: transform 0.3s ease;
        }
        .nav-link:hover .nav-icon {
            transform: scale(1.2);
        }
        .brand-link {
            background-color: #1e293b;
            border-bottom: 1px solid #374151;
        }
        .brand-text {
            color: #60a5fa;
            font-weight: bold;
        }

        /* Add styling for the Add Credit Button */
        .add-credit-btn {
            background-color: #34D399;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .add-credit-btn:hover {
            background-color: #10B981;
            transform: scale(1.05);
        }
        .add-credit-btn i {
            margin-right: 8px; /* Space between icon and text */
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- User Info -->
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-user"></i>
                        <?php echo isset($username) ? htmlspecialchars($username) : 'Guest'; ?>
                    </a>
                </li>
                <!-- User Group -->
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-users"></i>
                        <?php echo isset($group_name) && !empty($group_name) ? 'Your Package Is: ' . htmlspecialchars($group_name) : 'Your Package Is:'; ?>
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
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="#" class="brand-link">
                <img src="img/logo.png" alt="User Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text">DOM Hoster</span>
            </a>

          <!-- Sidebar Menu -->
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
        <!-- Home Menu Item -->
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-home"></i>
                <p>Home</p>
            </a>
        </li>
        <!-- Generate Tool Menu Item -->
        <li class="nav-item">
            <a href="SelectTool.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'SelectTool.php') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-random"></i>
                <p>Generate Tool</p>
            </a>
        </li>
        <!-- Profile Menu Item -->
        <li class="nav-item">
            <a href="profile.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-user-circle"></i>
                <p>Profile</p>
            </a>
        </li>
        <!-- History Menu Item -->
        <li class="nav-item">
            <a href="history.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'history.php') ? 'active' : ''; ?>">
                <i class="nav-icon fas fa-history"></i>
                <p>History</p>
            </a>
        </li>
        <!-- Add Credit Button -->
        <li class="nav-item">
            <a href="fund.php" class="nav-link">
                <button class="add-credit-btn">
                    <i class="fas fa-wallet"></i> Add Credit
                </button>
            </a>
        </li>
        <!-- Telegram Menu Item -->
        <li class="nav-item">
            <a href="/" target="_blank" class="nav-link">
                 <i class="nav-icon fas fa-home"></i> <!-- Home Icon -->
                <p>Home Page</p>
            </a>
        </li>
    </ul>
</nav>
<!-- /.sidebar-menu -->

            </div>
            <!-- /.sidebar -->
        </aside>
        <!-- /.main-sidebar -->
    </div>
</body>
</html>
