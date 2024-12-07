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

<!-- Content Wrapper -->
<div class="content-wrapper">
    <section class="content-header text-center">
        <h2 class="text-primary fw-bold mb-3 animate__animated animate__fadeIn">Welcome to Your Dashboard, <?php echo htmlspecialchars($username); ?>!</h2>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row g-4">
                <!-- Card: Current Time -->
                <div class="col-lg-4 col-md-6">
                    <div class="card text-white bg-gradient-primary shadow animate__animated animate__fadeInUp">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Bangladesh Time</h5>
                                    <h3 class="card-text" id="bangladesh-time"><?php echo date('h:i A, d M Y'); ?></h3>
                                </div>
                                <i class="fas fa-clock fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Account Balance -->
                <div class="col-lg-4 col-md-6">
                    <div class="card text-white bg-gradient-danger shadow animate__animated animate__fadeInUp">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Credits in Account</h5>
                                    <h3 class="card-text"><?php echo htmlspecialchars($balance); ?></h3>
                                </div>
                                <i class="fas fa-wallet fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Generated Tools -->
                <div class="col-lg-4 col-md-6">
                    <div class="card text-white bg-gradient-primary shadow animate__animated animate__fadeInUp">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Generated Tools</h5>
                                    <h3 class="card-text"><?php echo count($generated_tools); ?></h3>
                                </div>
                                <i class="fas fa-tools fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Today's Credit Usage -->
                <div class="col-lg-6">
                    <div class="card text-white bg-danger shadow animate__animated animate__fadeInUp">
                        <div class="card-body">
                            <h5 class="card-title">Today's Credit Usage</h5>
                            <div class="progress mt-3" style="height: 25px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                                     role="progressbar" 
                                     style="width: <?php echo min(100, ($total_today_amount / max(1, $balance)) * 100); ?>%;" 
                                     aria-valuenow="<?php echo $total_today_amount; ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="<?php echo $balance; ?>">
                                    <?php echo round(min(100, ($total_today_amount / max(1, $balance)) * 100), 2); ?>%
                                </div>
                            </div>
                            <p class="mt-2">Used <?php echo $total_today_amount; ?> out of <?php echo $balance; ?> credits.</p>
                        </div>
                    </div>
                </div>
            </div>
            

            <!-- Recent Activity Section -->
            <div class="row mt-5">
                <div class="col-lg-12">
                    <div class="card bg-light shadow animate__animated animate__fadeInUp">
                        <div class="card-body">
                            <h5 class="card-title text-dark fw-bold mb-3">Recent Activity</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Tool Name</th>
                                            <th>Username</th>
                                            <th>Password</th>
                                            <th>Generated At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($generated_tools)) : ?>
                                            <?php foreach ($generated_tools as $index => $tool) : ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td><?php echo htmlspecialchars($tool['tool_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($tool['tool_username']); ?></td>
                                                    <td><?php echo htmlspecialchars($tool['tool_password']); ?></td>
                                                    <td><?php echo htmlspecialchars(date('d M Y, h:i A', strtotime($tool['generated_at']))); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No recent activity found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Recent Activity Section -->
        </div>
    </section>
</div>

<style>
    /* Blue and Red Theme */
    body {
        color: #1c3b6f !important;
        background-color: #f2f6fc;
    }

    .card {
        border-radius: 10px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-title {
        color: #1c3b6f !important;
    }

    .text-muted {
        color: #ff3b30 !important;
    }

    .bg-gradient-primary {
        background: linear-gradient(45deg, #1c3b6f, #007bff);
    }

    .bg-gradient-danger {
        background: linear-gradient(45deg, #ff3b30, #e63946);
    }

    .bg-light {
        background-color: #ffffff !important;
    }

    .table-dark th {
        background-color: #1c3b6f !important;
        color: white;
    }

    .progress-bar {
        background-color: #ff3b30 !important;
    }

    /* Add fade-in effect to the card */
    .animate__animated {
        animation-duration: 1s;
    }

    .animate__fadeIn {
        animation-name: fadeIn;
    }

    .animate__fadeInUp {
        animation-name: fadeInUp;
    }

    @keyframes fadeIn {
        0% {
            opacity: 0;
        }
        100% {
            opacity: 1;
        }
    }

    @keyframes fadeInUp {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<?php include 'footer.php'; ?>

<!-- Add Bootstrap 5 JS and FontAwesome -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>

<script>
    // Live Bangladesh Time Update
    setInterval(() => {
        const bangladeshTime = new
