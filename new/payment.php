<?php include('header.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Methods</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: #f8f9fa;
            color: #333;
        }

        /* Header Styles with Color Animation */
        header {
            color: white;
            padding: 30px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-bottom: 5px solid #00BFFF;
            background: linear-gradient(45deg, #ff7e5f, #feb47b, #00BFFF); /* Added blue color */
            background-size: 400% 400%;
            animation: gradientShift 10s ease infinite; /* Smooth color transition */
        }

        /* Keyframes for Background Color Animation */
        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }
            25% {
                background-position: 100% 50%;
            }
            50% {
                background-position: 0% 50%;
            }
            75% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        header h1 {
            margin: 0;
            font-size: 36px;
            font-weight: bold;
            text-transform: uppercase;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            margin-top: 10px;
        }

        nav ul li {
            display: inline;
            margin-right: 25px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        nav ul li a:hover {
            color: #00BFFF;
        }

        /* Main Content Section */
        #payment-methods {
            text-align: center;
            padding: 80px 20px;
            background-color: #fff;
            margin-top: 30px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
        }

        #payment-methods h2 {
            font-size: 32px;
            margin-bottom: 50px;
            color: #333;
        }

        .payment-logo {
            display: inline-block;
            margin: 20px;
            text-align: center;
            border-radius: 15px;
            padding: 30px;
            background-color: #f9f9f9;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            width: 220px;
            transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
        }

        .payment-logo:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.2);
            background-color: #f0f8ff;
        }

        .payment-logo img {
            width: 90px;
            height: 90px; /* Ensure images are square */
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        .payment-logo p {
            margin-top: 20px;
            font-size: 16px;
            color: #555;
            letter-spacing: 1px;
        }

        .payment-logo img:hover {
            transform: scale(1.1);
        }

        /* Footer Section */
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 25px;
            position: fixed;
            width: 100%;
            bottom: 0;
            font-size: 14px;
            letter-spacing: 1px;
        }

        footer p {
            margin: 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .payment-logo {
                width: 180px;
                padding: 20px;
            }

            header h1 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
<!-- Header Section -->
<header>
    <h1>Your Payment Methods</h1>
    <nav>
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="/user/dashboard.php">My Account</a></li>
        </ul>
    </nav>
</header>

<!-- Main Content Section -->
<section id="payment-methods">
    <h2>Select a Payment Method</h2>
    <div class="payment-logo">
        <img src="img/bkash.png" alt="bKash Logo" />
        <p>Payment Address: 123456789</p>
    </div>
    <div class="payment-logo">
        <img src="img/nagad.png" alt="Nagad Logo" />
        <p>Payment Address: 987654321</p>
    </div>
    <div class="payment-logo">
        <img src="img/upay.png" alt="Rocket Logo" />
        <p>Payment Address: 112233445</p>
    </div>
    <div class="payment-logo">
        <img src="img/binance.png" alt="Binance Logo" />
        <p>Payment Address: abcdefghijklmnop</p>
    </div>
</section>

<!-- Footer Section -->
<?php include('footer.php'); ?>
</body>
</html>
