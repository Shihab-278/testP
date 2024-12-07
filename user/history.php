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
$stmt = $conn->prepare("SELECT username, `group`, balance, credit FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user['username'] ?? 'Unknown User';
$group_name = $user['group'] ?? 'No Group';
$balance = $user['balance'] ?? 0;
$credit = $user['credit'] ?? 0;

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
    </section>

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


<style>
    /* Blue and Red Theme */
    body {
        color: #1c3b6f !important;
        background-color: #f2f6fc;
        font-family: 'Arial', sans-serif;
    }

    .card {
        border-radius: 10px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
    }

    .card-title {
        color: #1c3b6f !important;
    }

    .text-muted {
        color: #ff3b30 !important;
    }

    .bg-light {
        background-color: #ffffff !important;
    }

    .table-dark th {
        background-color: #1c3b6f !important;
        color: white;
    }

    .footer-section {
        background-color: #1c3b6f;
        color: white;
    }

    .footer-section p {
        margin: 0;
    }

    .footer-section .fab {
        font-size: 20px;
        transition: color 0.3s;
    }

    .footer-section .fab:hover {
        color: #007bff;
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
        const bangladeshTime = new Date().toLocaleString("en-US", { timeZone: "Asia/Dhaka" });
        document.getElementById("current-time").innerText = bangladeshTime;
    }, 1000);
</script>
