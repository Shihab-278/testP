<?php
session_start();
include 'db.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetch dynamic settings from the database
try {
    $stmt = $conn->prepare("SELECT name, value FROM settings WHERE name IN ('website_title', 'header_content', 'footer_content', 'custom_html')");
    $stmt->execute();
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Assign default values if settings are not available
    $website_title = $settings['website_title'] ?? 'Default Website Title';
    $header_content = $settings['header_content'] ?? 'Welcome to Our Website!';
    $footer_content = $settings['footer_content'] ?? '© 2024 GSMXTOOL. All Rights Reserved.';
    $custom_html = $settings['custom_html'] ?? ''; // Custom HTML section

} catch (Exception $e) {
    // Handle exception if needed
    $error = 'Unable to fetch settings from the database.';
    $website_title = 'Default Website Title'; // Default in case of failure
    $header_content = 'Welcome to Our Website!';
    $footer_content = '© 2024 GSMXTOOL. All Rights Reserved.';
    $custom_html = ''; // Default empty custom HTML section
}

// Fetch reCAPTCHA settings from the database
$query = "SELECT * FROM settings WHERE name IN ('recaptcha_enabled', 'recaptcha_site_key', 'recaptcha_secret_key')";
$stmt = $conn->query($query);
$settings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get current reCAPTCHA settings
$recaptchaEnabled = 0;
$recaptchaSiteKey = '';
$recaptchaSecretKey = '';
foreach ($settings as $setting) {
    if ($setting['name'] == 'recaptcha_enabled') {
        $recaptchaEnabled = (int)$setting['value'];
    } elseif ($setting['name'] == 'recaptcha_site_key') {
        $recaptchaSiteKey = $setting['value'];
    } elseif ($setting['name'] == 'recaptcha_secret_key') {
        $recaptchaSecretKey = $setting['value'];
    }
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if reCAPTCHA is enabled
    if ($recaptchaEnabled) {
        $recaptchaResponse = $_POST['g-recaptcha-response'];
        $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecretKey&response=$recaptchaResponse");
        $responseKeys = json_decode($verifyResponse, true);

        // If reCAPTCHA is invalid
        if (intval($responseKeys['success']) !== 1) {
            $error = "reCAPTCHA verification failed!";
        }
    }

    // Validate user credentials if reCAPTCHA is successful
    if (!isset($error)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user) {
            if ($user['banned']) {
                $error = "Your account is banned. Please contact support.";
            } elseif (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                // Capture user's IP address
                $user_ip = $_SERVER['REMOTE_ADDR'];

                // Save login activity to the database
                $stmt = $conn->prepare("INSERT INTO login_history (user_id, ip_address, login_time) VALUES (?, ?, NOW())");
                $stmt->execute([$user['id'], $user_ip]);

                // Redirect based on role
                if ($user['role'] == 'admin') {
                    header('Location: admin/dashboard.php');
                } else {
                    header('Location: user/dashboard.php');
                }
                exit;
            } else {
                $error = "Password is incorrect!";
            }
        } else {
            $error = "Account does not exist!";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($website_title); ?> - Login</title> <!-- Dynamic Title -->
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: #f5f7fa;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .login-title {
            font-size: 1.75rem;
            font-weight: bold;
            color: #343a40;
            margin-bottom: 1rem;
        }
        .text-muted {
            color: #6c757d;
            font-size: 1rem;
        }
        .btn-primary {
            background: #007bff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            width: 100%;
            font-size: 1rem;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .form-control {
            background-color: #f1f3f5;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 1rem;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
            padding: 0.75rem;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="login-container">
    <h1 class="login-title"><?php echo htmlspecialchars($website_title); ?> - Login</h1> <!-- Dynamic Website Title -->
    <p class="text-muted"><?php echo htmlspecialchars($header_content); ?></p> <!-- Dynamic Header Content -->

    <!-- Error Message (if any) -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Login Form -->
    <form method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
        </div>

        <!-- reCAPTCHA -->
        <?php if ($recaptchaEnabled): ?>
            <div class="mb-3">
                <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($recaptchaSiteKey); ?>"></div>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary">Sign In</button>
    </form>

    <div class="mt-3 text-muted">
        <p><a href="register.php">Create a New Account</a></p>
        <p><a href="forgot_password.php">Forgot Password?</a></p>
    </div>

    <!-- Footer Content -->
    <footer class="mt-5">
        <p class="text-muted"><?php echo htmlspecialchars($footer_content); ?></p> <!-- Dynamic Footer Content -->
    </footer>

</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- reCAPTCHA Script -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>
