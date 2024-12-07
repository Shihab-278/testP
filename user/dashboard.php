<?php
session_start();
include '../db.php';

// Set timezone to Asia/Dhaka (Bangladesh Standard Time)
date_default_timezone_set('Asia/Dhaka');

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

// Retrieve user ID from session
$user_id = $_SESSION['user_id'];

// Fetch user information including phone number
$stmt = $conn->prepare("SELECT username, full_name, email, `group`, balance, credit, telegram_channel, profile_picture, phone FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user['username'] ?? 'Unknown User';
$full_name = $user['full_name'] ?? 'No Full Name';
$email = $user['email'] ?? 'No Email';
$group_name = $user['group'] ?? 'No Group';
$balance = $user['balance'] ?? 0;
$credit = $user['credit'] ?? 0;
$telegram_channel = $user['telegram_channel'] ?? '';
$profile_picture = $user['profile_picture'] ?? 'default-profile.jpg';
$phone = $user['phone'] ?? 'No Phone Number';

// Fetch user's generated tools
$tools_stmt = $conn->prepare("SELECT tools.tool_name, tools.tool_username, tools.tool_password, user_tools.generated_at 
                              FROM user_tools 
                              JOIN tools ON user_tools.tool_id = tools.id 
                              WHERE user_tools.user_id = ?");
$tools_stmt->execute([$user_id]);
$generated_tools = $tools_stmt->fetchAll();

// Fetch Pending and Successful Orders
$pending_orders_stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND order_status = 'Pending'");
$pending_orders_stmt->execute([$user_id]);
$pending_orders_count = $pending_orders_stmt->fetchColumn();

$successful_orders_stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND (order_status = 'Success' OR order_status = 'Completed')");
$successful_orders_stmt->execute([$user_id]);
$successful_orders_count = $successful_orders_stmt->fetchColumn();

// Fetch first 6 announcements
$announcements_stmt = $conn->prepare("SELECT title, description, created_at FROM announcements ORDER BY created_at DESC LIMIT 6");
$announcements_stmt->execute();
$announcements = $announcements_stmt->fetchAll();

// Fetch total number of announcements
$total_announcements_stmt = $conn->prepare("SELECT COUNT(*) FROM announcements");
$total_announcements_stmt->execute();
$total_announcements_count = $total_announcements_stmt->fetchColumn();

// Include header
include 'header.php';
?>

<div class="container-fluid bg-light text-dark">
    <div class="content-wrapper">
        <div class="row">
            <!-- Left Column: User Info, Orders, Tools, and Telegram Channel -->
            <div class="col-lg-8 col-md-12">
                <div class="row g-4">

                    <!-- Profile Section -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card custom-bg-color shadow-sm border-light">
                            <div class="card-body text-center">
                                <img src="../uploads/<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                                <h5 class="card-title text-dark"><?php echo htmlspecialchars($full_name); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($username); ?></p>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($email); ?></p>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($phone); ?></p> <!-- Display Phone Number -->
                            </div>
                        </div>
                    </div>

                    <!-- Credits in Account Card -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card custom-bg-color shadow-sm border-light" style="background-color: #24283a; color: #fff;">
                            <div class="card-body">
                                <h5 class="card-title text-primary">Credits in Account</h5>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-text">Balance</h6>
                                        <h3 class="card-text" id="balanceNumber"><?php echo htmlspecialchars($balance); ?></h3>
                                    </div>
                                    <i class="fas fa-wallet fa-2x text-primary"></i>
                                </div>

                                <div class="mt-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-text">Credit</h6>
                                            <h3 class="card-text" id="creditAmount"><?php echo htmlspecialchars($credit); ?></h3>
                                        </div>
                                        <i class="fas fa-credit-card fa-2x text-success"></i>
                                    </div>
                                    <div class="mt-3">
                                        <a href="submit_payment.php" class="btn btn-light w-100">Add Fund</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Orders Card -->
                    <div class="col-lg-4 col-md-6">
                        <a href="/user/order_history.php?status=Pending&service=" class="text-decoration-none">
                            <div class="card custom-bg-color shadow-sm border-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title text-primary">Pending Orders</h5>
                                            <h3 class="card-text"><?php echo $pending_orders_count; ?></h3>
                                        </div>
                                        <i class="fas fa-hourglass-half fa-3x text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Successful Orders Card -->
                    <div class="col-lg-4 col-md-6">
                        <a href="/user/order_history.php?status=Completed&service=" class="text-decoration-none">
                            <div class="card custom-bg-color shadow-sm border-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title text-primary">Successful Orders</h5>
                                            <h3 class="card-text"><?php echo $successful_orders_count; ?></h3>
                                        </div>
                                        <i class="fas fa-check-circle fa-3x text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Generated Tools Card -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card custom-bg-color shadow-sm border-light">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title text-black">Generated Tools</h5>
                                        <h3 class="card-text text-black"><?php echo count($generated_tools); ?></h3>
                                    </div>
                                    <i class="fas fa-tools fa-3x"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Telegram Channel Card -->
                    <div class="col-lg-4 col-md-6">
                        <?php if (!empty($telegram_channel)): ?>
                            <div class="card custom-bg-color shadow-sm border-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title text-success">Join Our Telegram Channel</h5>
                                        </div>
                                        <a href="<?php echo htmlspecialchars($telegram_channel); ?>" target="_blank" class="btn btn-info">
                                            Join Now <i class="fab fa-telegram-plane"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div> <!-- End of Left Column -->

            <!-- Right Column: Announcements -->
            <div class="col-lg-4 col-md-12">
                <div class="card custom-bg-color shadow-sm border-light">
                    <div class="card-body">
                        <h5 class="card-title text-dark">Announcements</h5>
                        <div class="list-group" id="announcement-list">
                            <?php if ($announcements) : ?>
                                <?php foreach ($announcements as $announcement) : ?>
                                    <div class="list-group-item">
                                        <h6 class="font-weight-bold text-dark"><?php echo htmlspecialchars($announcement['title']); ?></h6>
                                        <p class="mb-1 text-muted"><?php echo htmlspecialchars($announcement['description']); ?></p>
                                        <small class="text-muted"><?php echo date('F j, Y', strtotime($announcement['created_at'])); ?></small>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <div class="list-group-item">
                                    <p class="text-center text-muted">No announcements available</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div id="loading" class="text-center" style="display: none;">
                            <p>Loading more announcements...</p>
                        </div>
                    </div>
                </div>
            </div> <!-- End of Right Column -->

        </div> <!-- End of Row -->
    </div> <!-- End of Content Wrapper -->
</div> <!-- End of Container -->

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script>
// JS to animate balance and credit numbers
setInterval(() => {
    const bangladeshTime = new Date().toLocaleString("en-US", { timeZone: "Asia/Dhaka" });
    document.getElementById('bangladesh-time').textContent = bangladeshTime;
}, 1000);

function animateNumber(elementId, startValue, endValue, duration) {
    let startTime = null;
    const step = (timestamp) => {
        if (!startTime) startTime = timestamp;
        const progress = timestamp - startTime;
        const currentValue = Math.min(startValue + (endValue - startValue) * (progress / duration), endValue);
        document.getElementById(elementId).textContent = currentValue.toFixed(2);
        if (progress < duration) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}

animateNumber("creditAmount", 0, <?php echo $credit; ?>, 2000);
animateNumber("balanceNumber", 0, <?php echo $balance; ?>, 2000);

// JS for loading more announcements on scroll
let loadedAnnouncements = 6;
let totalAnnouncements = <?php echo $total_announcements_count; ?>;

window.onscroll = function() {
    if (document.documentElement.scrollTop + window.innerHeight >= document.documentElement.scrollHeight) {
        if (loadedAnnouncements < totalAnnouncements) {
            document.getElementById('loading').style.display = 'block';
            fetch('load_announcements.php?start=' + loadedAnnouncements)
                .then(response => response.json())
                .then(data => {
                    const announcementList = document.getElementById('announcement-list');
                    data.announcements.forEach(announcement => {
                        const listItem = document.createElement('div');
                        listItem.className = 'list-group-item';
                        listItem.innerHTML = `
                            <h6 class="font-weight-bold text-dark">${announcement.title}</h6>
                            <p class="mb-1 text-muted">${announcement.description}</p>
                            <small class="text-muted">${announcement.date}</small>
                        `;
                        announcementList.appendChild(listItem);
                    });
                    loadedAnnouncements += data.announcements.length;
                    document.getElementById('loading').style.display = 'none';
                });
        }
    }
};
</script>




<style>
    .card:hover {
        transform: scale(1.05);
        transition: transform 0.3s ease-in-out;
    }

    .pending-order-card, .successful-order-card {
        transition: transform 0.3s ease;
    }

    .pending-order-card:hover, .successful-order-card:hover {
        transform: none;
    }

    .list-group-item {
        transition: background-color 0.3s ease;
    }

    .list-group-item:hover {
        background-color: #f1f1f1;
    }

    .container-fluid {
        padding: 15px 70px;
    }

    .row {
        margin-bottom: 20px;
    }

    .col-lg-4, .col-md-6 {
        padding: 10px;
    }

    .card-body {
        padding: 1.5rem;
    }

    #loading {
        text-align: center;
    }
    
    
</style>
