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

    <!-- Custom CSS (optional) -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark bg-dark">
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
                <img src="img/logo.jpg" alt="User Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">DOM Hoster</span>
            </a>

<!-- Sidebar -->
<div class="sidebar">
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
            <!-- Buy and Help Menu Item -->
            <li class="nav-item">
                <a href="https://wa.me/1" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'buy_and_help.php') ? 'active' : ''; ?>">
                    <i class="nav-icon fas fa-question-circle"></i>
                    <p>Buy and Help</p>
                </a>
            </li>
             <!-- Buy and Help Menu Item -->
            <li class="nav-item">
                <a href="https://wa.me/1" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'buy_and_help.php') ? 'active' : ''; ?>">
                    <i class="fab fa-telegram"></i>
                    <p>  Telegram</p>
                </a>
            </li>
            
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>
<!-- /.sidebar -->
</aside>
<!-- /.main-sidebar -->
