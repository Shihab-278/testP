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

        /* Hero Section */
        .hero {
            background: linear-gradient(45deg, var(--primary-blue), var(--secondary-red));
            color: #fff;
            text-align: center;
            padding: 80px 15px;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: bold;
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
            <a class="navbar-brand" href="#">
                <img src="https://rent.shunlocker.com/user/img/logo.png" alt="Logo" height="50">
                GSMXTOOL
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Payment</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                </ul>
               <!-- My Account Button -->
<a href="https://rent.shunlocker.com/login.php" class="btn btn-account ms-3">My Account</a>

            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Welcome to GSMXTOOL</h1>
            <p>Your one-stop solution for tool rentals and activation services.</p>
        </div>
    </section>

    <!-- Pricing Section (Moved to Top) -->
    <section class="py-5">
        <div class="container">
            <h2 class="section-title text-center">Pricing</h2>
            <div class="row">
                <!-- Card 1 -->
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <img src="img/tfm.jpg" class="card-img-top" alt="Unlock Tool">
                        <div class="card-body text-center">
                            <h5 class="card-title">Unlock Tool</h5>
                            <p class="card-text">Price: 50 Taka<br>Time: 6 Hrs</p>
                        </div>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <img src="img/tfm.jpg" class="card-img-top" alt="AMT Tool">
                        <div class="card-body text-center">
                            <h5 class="card-title">AMT Tool</h5>
                            <p class="card-text">Price: 50 Taka<br>Time: 2 Hrs</p>
                        </div>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <img src="img/tfm.jpg" class="card-img-top" alt="TFM Tool">
                        <div class="card-body text-center">
                            <h5 class="card-title">TFM Tool</h5>
                            <p class="card-text">Price: 100 Taka<br>Time: 6 Hrs</p>
                        </div>
                    </div>
                </div>
                <!-- Card 4 -->
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <img src="img/tfm.jpg" class="card-img-top" alt="SRS Tool">
                        <div class="card-body text-center">
                            <h5 class="card-title">SRS Tool</h5>
                            <p class="card-text">Price: 100 Taka<br>Time: 2 Hrs</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center">Our Services</h2>
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <i class="fas fa-unlock-alt fa-3x mb-3 text-primary"></i>
                    <h5>Unlock Services</h5>
                    <p>Unlock devices remotely with our trusted tools.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <i class="fas fa-tools fa-3x mb-3 text-primary"></i>
                    <h5>Tool Rentals</h5>
                    <p>Access professional tools for short-term use.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <i class="fas fa-cogs fa-3x mb-3 text-primary"></i>
                    <h5>Activation Services</h5>
                    <p>Fast activation of your tools and software.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <p>&copy; 2024 GSMXTOOL. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
