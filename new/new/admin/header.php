<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GSMXTOOL Dashboard</title>
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
    
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1.0/dist/css/adminlte.min.css">

    <!-- Custom CSS (optional) -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
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
        <img src="assets/img/logo.png" alt="Admin Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">GSMXTOOL</span>
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
                <li class="nav-item">
                    <a href="user_list.php" class="nav-link <?php echo ($current_page == 'user_list.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Manage Users</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="transfer_credit.php" class="nav-link <?php echo ($current_page == 'transfer_credit.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-money-bill"></i>
                        <p>Transfer Taka</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="withdraw_credit.php" class="nav-link <?php echo ($current_page == 'withdraw_credit.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-rupee-sign"></i>
                        <p>Withdraw Taka</p>
                    </a>
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
                <li class="nav-item">
                    <a href="https://rent.shunlocker.com/update.php" class="nav-link <?php echo ($current_page == 'ViewTools.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-history"></i>
                        <p>Used Tools</p>
                    </a>
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Popup Button Example</title>
    <style>
        /* Style for the popup */
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: white;
            border: 1px solid black;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        /* Style for the overlay behind the popup */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        /* Button styling */
        .btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <!-- Button to open the popup -->
    <button class="btn" onclick="openPopup()">Check Update</button>

    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>

    <!-- Popup -->
    <div class="popup" id="popup">
        <h3>Visit a Website</h3>
        <p>Click the link below to visit a website:</p>
        <a href="https://rent.shunlocker.com/update.php" target="_blank">Visit Example.com</a>
        <br><br>
        <button onclick="closePopup()">Close</button>
    </div>

    <script>
        // Function to open the popup
        function openPopup() {
            document.getElementById('popup').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        // Function to close the popup
        function closePopup() {
            document.getElementById('popup').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }
    </script>

</body>
</html>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
<!-- /.main-sidebar -->


</body>
</html>
