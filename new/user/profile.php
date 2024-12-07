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

// Get user information, including email
$stmt = $conn->prepare("SELECT username, `group`, balance, name, address, phone, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user['username'] ?? '';
$group_name = $user['group'] ?? '';
$balance = $user['balance'] ?? 0;
$name = $user['name'] ?? '';
$address = $user['address'] ?? '';
$phone = $user['phone'] ?? '';
$email = $user['email'] ?? ''; // Fetch email from the database

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

// Password change logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Fetch current password from the database
        $user_stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $user_stmt->execute([$user_id]);
        $user_data = $user_stmt->fetch();
        $hashed_password = $user_data['password'];

        // Verify current password
        if (password_verify($current_password, $hashed_password)) {
            if ($new_password === $confirm_password) {
                // Hash new password
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update password in the database
                $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_stmt->execute([$new_hashed_password, $user_id]);

                $success_message = "Password updated successfully!";
            } else {
                $error_message = "New password and confirm password do not match.";
            }
        } else {
            $error_message = "Current password is incorrect.";
        }
    }

    // Update profile details logic
    if (isset($_POST['update_profile'])) {
        $new_name = $_POST['name'];
        $new_address = $_POST['address'];
        $new_phone = $_POST['phone'];

        // Update profile in the database
        $update_profile_stmt = $conn->prepare("UPDATE users SET name = ?, address = ?, phone = ? WHERE id = ?");
        $update_profile_stmt->execute([$new_name, $new_address, $new_phone, $user_id]);

        $profile_success_message = "Profile updated successfully!";
    }
}

include 'header.php'; // Include user-side header
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <section class="content-header text-center">
        <h2 class="fw-bold">Welcome, <?php echo htmlspecialchars($name); ?> (<?php echo htmlspecialchars($email); ?>)</h2>
        <p>Group: <?php echo htmlspecialchars($group_name); ?></p>
        <p>Balance: ৳<?php echo htmlspecialchars($balance); ?></p>
        <p>Today's Credit Usage: ৳<?php echo htmlspecialchars($total_today_amount); ?></p>
    </section>

    <!-- Profile and Password Change Forms Side by Side -->
    <div class="row mt-5">
        <!-- Profile Update Form (Left) -->
        <div class="col-lg-6">
            <div class="card bg-light shadow-sm p-3 rounded-3 animate__animated animate__fadeInUp">
                <div class="card-body">
                    <h5 class="card-title text-dark fw-bold mb-3">Update Profile</h5>
                    
                    <?php if (isset($profile_success_message)): ?>
                        <div class="alert alert-success"><?php echo $profile_success_message; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control form-control-sm" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control form-control-sm" id="address" name="address" required><?php echo htmlspecialchars($address); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control form-control-sm" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary w-100 btn-sm">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Password Change Form (Right) -->
        <div class="col-lg-6">
            <div class="card bg-light shadow-sm p-3 rounded-3 animate__animated animate__fadeInUp">
                <div class="card-body">
                    <h5 class="card-title text-dark fw-bold mb-3">Change Password</h5>
                    
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                    <?php elseif (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control form-control-sm" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control form-control-sm" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control form-control-sm" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-primary w-100 btn-sm">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Smaller and unique card design */
    .content-wrapper {
        margin: 20px;
    }
    .card {
        border-radius: 12px; /* More rounded corners */
        padding: 15px; /* Reduced padding */
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        transition: transform 0.3s ease-in-out;
    }
    .card:hover {
        transform: scale(1.02); /* Subtle zoom effect on hover */
    }
    .btn-sm {
        padding: 10px; /* Slightly smaller button */
        font-size: 14px; /* Adjusted font size */
    }
    .form-control-sm {
        font-size: 14px; /* Adjusted form input size */
    }
</style>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>

<script>
    setInterval(() => {
        const bangladeshTime = new Date().toLocaleString("en-US", { timeZone:
