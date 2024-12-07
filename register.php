<?php
session_start();
include 'db.php'; // Ensure db.php connects to your database

// Check if the users table is empty
try {
    // Count the number of users in the users table
    $stmt = $conn->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();

    // If no users are found, redirect to install.php
    if ($userCount == 0) {
        header("Location: install.php");
        exit(); // Stop the script execution after redirection
    }

} catch (Exception $e) {
    // Handle any potential errors here, e.g., database connection issues
    die('Error: ' . $e->getMessage());
}

// Initialize response variable
$response = ""; 

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
    $error = 'Unable to fetch settings from the database.';
    $website_title = 'Default Website Title'; // Default in case of failure
    $header_content = 'Welcome to Our Website!';
    $footer_content = '© 2024 GSMXTOOL. All Rights Reserved.';
    $custom_html = ''; // Default empty custom HTML section
}

// Handle the registration logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($name) || empty($phone) || empty($address) || empty($username) || empty($email) || empty($password)) {
        $response = "<div class='alert alert-danger'>All fields are required.</div>";
    } else {
        // Check if username exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user) {
            $response = "<div class='alert alert-danger'>Username already exists!</div>";
        } else {
            // Check if email exists
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $emailExists = $stmt->fetch();

            if ($emailExists) {
                $response = "<div class='alert alert-danger'>Email already exists!</div>";
            } else {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert new user
                $stmt = $conn->prepare("INSERT INTO users (name, phone, address, username, email, password, role) VALUES (?, ?, ?, ?, ?, ?, 'user')");
                if ($stmt->execute([$name, $phone, $address, $username, $email, $hashedPassword])) {
                    $response = "<div class='alert alert-success'>Registration successful! You will be redirected shortly...</div>";
                    $_SESSION['registration_success'] = true; // Flag for redirect animation

                    // Fetch current Telegram settings from the database
                    $telegramToken = '';
                    $chatId = '';

                    // Fetch the Telegram settings from the database
                    $stmt = $conn->query("SELECT * FROM telegram_settings LIMIT 1");
                    $settings = $stmt->fetch();

                    if ($settings) {
                        $telegramToken = $settings['telegram_token'];
                        $chatId = $settings['chat_id'];
                    }

                    // Send Telegram notification only if the settings exist
                    if ($telegramToken && $chatId) {
                        $message = "New registration: \nName: $name \nUsername: $username \nEmail: $email \nPhone: $phone \nAddress: $address";
                        $url = "https://api.telegram.org/bot$telegramToken/sendMessage?chat_id=$chatId&text=" . urlencode($message);

                        // Make a request to the Telegram API to send the message
                        file_get_contents($url);
                    }

                } else {
                    $response = "<div class='alert alert-danger'>There was an error registering your account. Please try again later.</div>";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($website_title); ?> - Register</title> <!-- Dynamic Website Title -->
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            background: #f8f9fa;
        }
        .register-container {
            max-width: 500px;
            margin: auto;
            padding: 2rem;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: none;
        }
        .btn-primary {
            background: #007bff;
            border: none;
            height: 50px;
            font-size: 18px;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .register-title {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
        }
        .floating-label {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .floating-label input {
            border-radius: 0.375rem;
        }
        .floating-label label {
            position: absolute;
            top: 0.5rem;
            left: 0.75rem;
            font-size: 0.875rem;
            color: #6c757d;
            transition: all 0.2s;
        }
        .floating-label input:focus ~ label,
        .floating-label input:not(:placeholder-shown) ~ label {
            top: -0.5rem;
            left: 0.75rem;
            font-size: 0.75rem;
            color: #007bff;
        }
        .error-message {
            color: #d9534f;
        }
        .progress-container {
            display: none;
            margin-top: 20px;
        }
        .progress-bar {
            height: 10px;
            background-color: #28a745;
        }
    </style>
</head>
<body>
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="register-container">
        <h1 class="text-center register-title"><?php echo htmlspecialchars($website_title); ?> - Register</h1> <!-- Dynamic Website Title -->
        <p class="text-center text-muted"><?php echo htmlspecialchars($header_content); ?></p> <!-- Dynamic Header Content -->

        <?php if (!empty($response)) { echo $response; } ?>

        <form method="POST" action="">
            <!-- Full Name Field -->
            <div class="floating-label">
                <input type="text" name="name" class="form-control" id="name" placeholder=" " required>
                <label for="name">Full Name</label>
            </div>

            <!-- Phone Number Field -->
            <div class="floating-label">
                <input type="text" name="phone" class="form-control" id="phone" placeholder=" " required>
                <label for="phone">Phone Number</label>
            </div>

            <!-- Address Field -->
            <div class="floating-label">
                <input type="text" name="address" class="form-control" id="address" placeholder=" " required>
                <label for="address">Address</label>
            </div>

            <!-- Username Field -->
            <div class="floating-label">
                <input type="text" name="username" class="form-control" id="username" placeholder=" " required>
                <label for="username">Username</label>
            </div>

            <!-- Email Field -->
            <div class="floating-label">
                <input type="email" name="email" class="form-control" id="email" placeholder=" " required>
                <label for="email">Email</label>
            </div>

            <!-- Password Field -->
            <div class="floating-label">
                <input type="password" name="password" class="form-control" id="password" placeholder=" " required>
                <label for="password">Password</label>
                <small id="password-strength" class="form-text text-muted"></small>
                <!-- Progress Bar -->
                <div class="progress mt-2" style="height: 5px; display: none;" id="password-progress">
                    <div class="progress-bar" id="password-progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>

        <div class="mt-3 text-center">
            <p class="mb-1"><a href="login.php" class="text-decoration-none">Already have an account? Log in</a></p>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript for Progress Bar and Animation -->
<script>
    // Check if registration was successful (from PHP session)
    if (<?php echo isset($_SESSION['registration_success']) && $_SESSION['registration_success'] ? 'true' : 'false'; ?>) {
        // Show the progress container
        document.querySelector('.progress-container').style.display = 'block';

        // Fill the progress bar gradually
        let progressBar = document.querySelector('.progress-bar');
        let width = 0;
        let interval = setInterval(function() {
            if (width >= 100) {
                clearInterval(interval);
                setTimeout(function() {
                    window.location.href = 'login.php'; // Redirect to login page after the progress bar completes
                }, 500); // Wait 0.5 seconds before redirecting
            } else {
                width++;
                progressBar.style.width = width + '%';
            }
        }, 20); // Adjust the speed here
    }
</script>

</body>
</html>
