<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GSMXTOOL Login</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
        }
        .login-container {
            max-width: 400px;
            margin: auto;
            padding: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #007bff;
        }
        .btn-primary {
            background: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .btn-secondary {
            color: #007bff;
            background: none;
            border: none;
        }
        .btn-secondary:hover {
            text-decoration: underline;
        }
        .error-message {
            color: #d9534f;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
        .login-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            color: #333;
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
    <div class="login-container">
        <h1 class="text-center login-title">Welcome Back</h1>
        <p class="text-center text-muted">Sign in to your GSMXTOOL account</p>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="floating-label">
                <input type="text" name="username" class="form-control" id="username" placeholder=" " required>
                <label for="username">Username</label>
            </div>
            <div class="floating-label">
                <input type="password" name="password" class="form-control" id="password" placeholder=" " required>
                <label for="password">Password</label>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="rememberMe">
                <label class="form-check-label" for="rememberMe">Remember Me</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">Sign In</button>
        </form>

        <div class="mt-3 text-center">
            <p class="mb-1"><a href="register.php" class="text-decoration-none">Create a New Account</a></p>
            <p><a href="forgot_password.php" class="text-decoration-none">Forgot Password?</a></p>
            <p><a href="/" class="btn btn-secondary">Back to Home</a></p>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
