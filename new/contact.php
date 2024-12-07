<?php include('header.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f7fc;
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header with Dynamic Gradient */
        header {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            background-size: 400% 400%;
            animation: gradientAnimation 5s ease infinite;
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 1px;
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
            transition: color 0.3s ease, transform 0.3s ease;
        }

        nav ul li a:hover {
            color: #00BFFF;
            transform: scale(1.1);
        }

        /* Main Content Area */
        main {
            flex-grow: 1;
            padding: 20px;
        }

        /* Contact Form Section */
        .contact-form {
            width: 50%;
            margin: 40px auto;
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
            opacity: 0;
            animation: fadeIn 1s forwards 0.5s;
        }

        .contact-form:hover {
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.15);
        }

        .contact-form h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 28px;
            color: #333;
        }

        .contact-form input, .contact-form textarea {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease, transform 0.3s ease;
        }

        .contact-form input:focus, .contact-form textarea:focus {
            border-color: #2575fc;
            outline: none;
            transform: scale(1.05);
        }

        .contact-form textarea {
            height: 150px;
            resize: vertical;
        }

        .contact-form button {
            background-color: #2575fc;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .contact-form button:hover {
            background-color: #6a11cb;
            transform: translateY(-3px) scale(1.05);
        }

        /* Contact Info Section */
        .contact-info {
            text-align: center;
            margin-top: 40px;
            opacity: 0;
            animation: fadeIn 1s forwards 1s;
        }

        .contact-info h3 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #2575fc;
            transform: translateY(20px);
            animation: slideIn 1s forwards 1.5s;
        }

        .contact-info p {
            font-size: 18px;
            margin: 10px 0;
            transform: translateY(20px);
            animation: slideIn 1s forwards 2s;
        }

        .contact-info i {
            margin-right: 10px;
            color: #2575fc;
            font-size: 20px;
        }

        /* Footer Section */
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 15px 20px;
            font-size: 14px;
            position: relative;
            bottom: 0;
        }

        footer p {
            margin: 0;
        }

        /* Animation Definitions */
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        @keyframes slideIn {
            0% { transform: translateY(30px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .contact-form {
                width: 80%;
            }

            .contact-info {
                padding: 20px;
            }

            header h1 {
                font-size: 24px;
            }

            .contact-info i {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <header>
        <h1>Contact Us</h1>
        <nav>
            <ul>
                <li><a href="/">Home</a></li>
                <li><a href="/user/dashboard.php">My Account</a></li>
                <li><a href="/contact.php">Contact Us</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content Section -->
    <main>
        <!-- Contact Form Section -->
        <div class="contact-form">
            <h2>We'd Love to Hear From You!</h2>
            <form action="/submit_contact" method="POST">
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="email" name="email" placeholder="Your Email" required>
                <textarea name="message" placeholder="Your Message" required></textarea>
                <button type="submit">Send Message</button>
            </form>
        </div>

        <!-- Contact Information Section -->
        <div class="contact-info">
            <h3>Our Contact Information</h3>
            <p><i class="fas fa-phone"></i>Phone: +1 123 456 7890</p>
            <p><i class="fas fa-envelope"></i>Email: contact@yourwebsite.com</p>
            <p><i class="fas fa-map-marker-alt"></i>Address: 123 Main Street, Your City, Country</p>
        </div>
    </main>

</body>
<?php include('footer.php'); ?>
</html>
