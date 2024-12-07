<?php
session_start();

// Initialize response message
$response = "";

// URL of the domain list (You can replace this URL with the actual domain list)
$domainListUrl = "https://shunlocker.com/domainlist.php";

// Function to fetch allowed domains using cURL
function fetchAllowedDomains($url)
{
    $allowedDomains = [];
    try {
        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout after 30 seconds

        // Execute cURL request
        $data = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            throw new Exception("Failed to fetch domain list: " . curl_error($ch));
        }

        // Close cURL
        curl_close($ch);

        // Process the data
        if ($data === false) {
            throw new Exception("Failed to fetch domain list.");
        }

        // Split data by newlines and remove extra spaces
        $allowedDomains = array_map('trim', explode("\n", $data));

    } catch (Exception $e) {
        die("<div class='alert alert-danger'>Failed to fetch domain list. Please contact support.</div>");
    }
    return $allowedDomains;
}

// Fetch allowed domains
$allowedDomains = fetchAllowedDomains($domainListUrl);

// Domain verification and registration logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['verify_domain'])) {
        $domain = trim($_POST['domain']);
        if (empty($domain)) {
            $response = "<div class='alert alert-danger'>Please enter a domain.</div>";
        } elseif (!in_array($domain, $allowedDomains)) {
            $response = "<div class='alert alert-danger'>Domain not allowed. Please contact support.</div>";
        } else {
            $_SESSION['verified_domain'] = $domain;
            $response = "<div class='alert alert-success'>Domain verified! Proceed to create your account.</div>";
        }
    } elseif (isset($_POST['register_account']) && isset($_SESSION['verified_domain'])) {
        include 'db.php'; // Include your database connection file

        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        if (empty($username) || empty($email) || empty($password)) {
            $response = "<div class='alert alert-danger'>All fields are required.</div>";
        } else {
            // Check if the username or email already exists
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $response = "<div class='alert alert-danger'>Username already exists.</div>";
            } else {
                $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $response = "<div class='alert alert-danger'>Email already exists.</div>";
                } else {
                    // Hash password before storing
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
                    if ($stmt->execute([$username, $email, $hashedPassword])) {
                        $_SESSION['registration_success'] = true;
                        header('Location: success.php'); // Redirect to success page
                        exit;
                    } else {
                        $response = "<div class='alert alert-danger'>Error during registration. Try again.</div>";
                    }
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
    <title>Installer - My Tool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            color: #fff;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 2rem;
            background: #fff;
            color: #000;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="container">
        <h1 class="text-center">Installer - My Tool</h1>
        <p class="text-center">Follow the steps to complete installation.</p>
        <?php if (!empty($response)) echo $response; ?>

        <?php if (!isset($_SESSION['verified_domain'])): ?>
            <!-- Domain verification form -->
            <form method="POST" action="" id="domainForm">
                <div class="mb-3">
                    <label for="domain" class="form-label">Verify Your Domain</label>
                    <input type="text" name="domain" id="domain" class="form-control" placeholder="example.com" required>
                </div>
                <button type="submit" name="verify_domain" class="btn btn-primary w-100">Verify Domain</button>
            </form>
        <?php else: ?>
            <!-- Account registration form -->
            <form method="POST" action="">
                <h4 class="text-center">Create Your Account</h4>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" name="register_account" class="btn btn-primary w-100">Register</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Any additional JavaScript (e.g. validation or progress bar) can be added here
    });
</script>
</body>
</html>
