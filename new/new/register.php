<?php
session_start();
include 'db.php'; // Ensure db.php connects to your database

$response = ""; // Initialize response variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($username) || empty($email) || empty($password)) {
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
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
                if ($stmt->execute([$username, $email, $hashedPassword])) {
                    $response = "<div class='alert alert-success'>Registration successful! You can now <a href='login.php'>log in</a>.</div>";
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
    <title>Register - GSMX TOOL</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Custom CSS -->
    <style>
        body {
            background: #f8f9fa;
        }
        .register-container {
            max-width: 400px;
            margin: auto;
            padding: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: none;
        }
        .btn-primary {
            background: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .error-message {
            color: #d9534f;
        }
        .register-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        .floating-label {
            position: relative;
            margin-bottom: 1rem;
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
    </style>
</head>
<body>
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="register-container">
        <h1 class="text-center register-title">Create an Account</h1>
        <p class="text-center text-muted">Register to GSMX TOOL</p>

        <?php if (!empty($response)) { echo $response; } ?>

        <form method="POST" action="">
            <div class="floating-label">
                <input type="text" name="username" class="form-control" id="username" placeholder=" " required>
                <label for="username">Username</label>
            </div>
            <div class="floating-label">
                <input type="email" name="email" class="form-control" id="email" placeholder=" " required>
                <label for="email">Email</label>
            </div>
            <div class="floating-label">
                <input type="password" name="password" class="form-control" id="password" placeholder=" " required>
                <label for="password">Password</label>
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
</body>
</html>
