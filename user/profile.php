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
$stmt = $conn->prepare("SELECT username, `group`, balance, name, address, phone, email, profile_picture FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user['username'] ?? '';
$group_name = $user['group'] ?? '';
$balance = $user['balance'] ?? 0;
$name = $user['name'] ?? '';
$address = $user['address'] ?? '';
$phone = $user['phone'] ?? '';
$email = $user['email'] ?? ''; // Fetch email from the database
$profile_picture = $user['profile_picture'] ?? 'default-profile.jpg'; // Profile picture

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
    // Update profile and handle profile picture upload
    if (isset($_POST['update_profile'])) {
        $new_name = $_POST['name'];
        $new_address = $_POST['address'];
        $new_phone = $_POST['phone'];
        $new_profile_picture = $profile_picture; // Default to current profile picture

        // Handle Profile Picture Upload
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $file_tmp = $_FILES['profile_picture']['tmp_name'];
            $file_name = $_FILES['profile_picture']['name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png'];
            
            if (in_array($file_ext, $allowed_ext)) {
                $new_file_name = uniqid('profile_', true) . '.' . $file_ext;
                $upload_dir = '../uploads/';
                if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                    // Set new profile picture name
                    $new_profile_picture = $new_file_name;
                } else {
                    $error_message = "Error uploading the file.";
                }
            } else {
                $error_message = "Only JPG, JPEG, PNG files are allowed.";
            }
        }

        // Update profile in the database
        $update_profile_stmt = $conn->prepare("UPDATE users SET name = ?, address = ?, phone = ?, profile_picture = ? WHERE id = ?");
        $update_profile_stmt->execute([$new_name, $new_address, $new_phone, $new_profile_picture, $user_id]);

        $profile_success_message = "Profile updated successfully!";
    }
}

include 'header.php'; // Include user-side header
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <section class="content-header text-center">
        <h2 class="fw-bold text-primary">Welcome, <?php echo htmlspecialchars($name); ?> (<?php echo htmlspecialchars($email); ?>)</h2>
        <p class="text-muted">Group: <?php echo htmlspecialchars($group_name); ?></p>
        <p class="text-success">Balance: ৳<?php echo htmlspecialchars($balance); ?></p>
        <p class="text-info">Today's Credit Usage: ৳<?php echo htmlspecialchars($total_today_amount); ?></p>
    </section>

    <!-- Profile and Password Change Forms Side by Side -->
    <div class="row mt-5">
        <!-- Profile Update Form (Left) -->
        <div class="col-lg-6">
            <div class="card shadow-lg p-3 rounded-4 animate__animated animate__fadeInUp">
                <div class="card-body">
                    <h5 class="card-title text-dark fw-bold mb-3">Update Profile</h5>
                    
                    <?php if (isset($profile_success_message)): ?>
                        <div class="alert alert-success"><?php echo $profile_success_message; ?></div>
                    <?php endif; ?>

                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" required><?php echo htmlspecialchars($address); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                        </div>
                        
                        <!-- Profile Picture Upload -->
                        <div class="mb-3">
                            <label for="profile_picture" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                            <small class="text-muted">Upload an image (JPG, PNG, JPEG only)</small>
                        </div>

                        <button type="submit" name="update_profile" class="btn btn-primary w-100">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Password Change Form (Right) -->
        <div class="col-lg-6">
            <div class="card shadow-lg p-3 rounded-4 animate__animated animate__fadeInUp">
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
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-primary w-100">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles for a Modern UI -->
<style>
    /* Body and overall layout tweaks */
    .content-wrapper {
        margin: 30px;
    }
    .card {
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease-in-out;
    }
    .card:hover {
        transform: scale(1.05);
    }
    .btn {
        padding: 12px;
        font-size: 16px;
        border-radius: 8px;
        text-transform: uppercase;
    }
    .form-control {
        font-size: 14px;
        border-radius: 8px;
        padding: 12px;
    }
    .alert {
        margin-bottom: 20px;
        font-size: 14px;
    }
    .text-primary {
        color: #007bff !important;
    }
    .text-muted {
        color: #6c757d !important;
    }
    .text-success {
        color: #28a745 !important;
    }
    .text-info {
        color: #17a2b8 !important;
    }
</style>

<?php include 'footer.php'; ?>
