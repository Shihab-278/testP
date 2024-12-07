<?php
session_start();
include 'db.php'; // Database connection file

// Redirect logged-in users to their dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: user/dashboard.php'); // or admin/dashboard.php if the user is an admin
    exit;
}

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Handle OTP verification form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputOtp = $_POST['otp'];

    // Check if the OTP is correct
    if (isset($_SESSION['otp']) && $_SESSION['otp'] == $inputOtp) {
        // OTP matches, log the user in
        $user_id = $_SESSION['user_id'];

        // Fetch user data
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role']; // Store user role for redirection purposes

            // Redirect to the respective dashboard
            if ($user['role'] == 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: user/dashboard.php');
            }
            exit;
        }
    } else {
        $error = "Invalid OTP!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2 class="my-4">OTP Verification</h2>

    <!-- Error Message -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- OTP Form -->
    <form method="POST">
        <div class="form-group">
            <label for="otp">Enter OTP</label>
            <input type="text" class="form-control" id="otp" name="otp" placeholder="Enter OTP sent to your email" required>
        </div>
        <button type="submit" class="btn btn-primary">Verify OTP</button>
    </form>
</div>
</body>
</html>
