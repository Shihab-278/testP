<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GSMXTOOL Tool Server</title>
    <meta name="description" content="Your one-stop solution for tool rentals and activation services.">
    <meta name="keywords" content="tool rental, activation services, GSMXTOOL, unlock tools, activation">
    <meta name="author" content="GSMXTOOL">
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        /* Blue and Red Theme */
        :root {
            --primary-blue: #0056b3;
            --secondary-red: #ff3e4d;
            --light-gray: #f9f9f9;
            --dark-gray: #212529;
        }

        /* Navbar */
        .navbar {
            background-color: var(--primary-blue);
        }

        .navbar-brand {
            color: #fff;
        }

        .navbar-nav .nav-link {
            color: #fff;
        }

        .navbar-nav .nav-link:hover {
            color: var(--secondary-red);
        }

        .btn-account {
            background-color: var(--secondary-red);
            color: #fff;
            border: none;
        }

        .btn-account:hover {
            background-color: #cc323d;
        }

        /* Hero Section with Animated Background */
        .hero {
            background: linear-gradient(45deg, var(--primary-blue), var(--secondary-red));
            background-size: 400% 400%;
            animation: gradientAnimation 10s ease infinite;
            color: #fff;
            text-align: center;
            padding: 80px 15px;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: bold;
        }

        @keyframes gradientAnimation {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Section Headers */
        .section-title {
            color: var(--primary-blue);
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 30px;
        }

        /* Cards */
        .card {
            border: 1px solid var(--primary-blue);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .card-title {
            color: var(--primary-blue);
        }

        /* Footer */
        footer {
            background-color: var(--dark-gray);
            color: #fff;
            padding: 20px 0;
        }

        footer a {
            color: var(--secondary-red);
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/index.php">
                <img src="https://rent.shunlocker.com/user/img/logo.png" alt="Logo" height="50" style="border-radius: 50%;">
            
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Home
    </a>
    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
        <li><a class="dropdown-item" href="/">Home</a></li>
         <li><a class="dropdown-item" href="/home2.php">Home2</a></li>
        <li><a class="dropdown-item" href="/home3.php">Home3</a></li>
    </ul>
</li>
                    <li class="nav-item"><a class="nav-link" href="/payment.php">Payment</a></li>
                    <li class="nav-item"><a class="nav-link" href="/contact.php">Contact</a></li>
                </ul>
                <!-- My Account Button -->
                <?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Check if the user is an admin or regular user
    if ($_SESSION['role'] == 'admin') {
        header("Location: /admin/dashboard"); // Redirect to admin dashboard
        exit;
    } else {
        header("Location: /user/dashboard"); // Redirect to user dashboard
        exit;
    }
}
?>

<!-- My Account Button -->
<a href="/user/dashboard.php#" class="btn btn-account ms-3">My Account</a>        </div>
    </nav>
