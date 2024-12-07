<?php
session_start();
include '../db.php';

// Fetch the logo URL from the database
$sql = "SELECT logo_url FROM settings WHERE id = 1 LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $logo_url = $row['logo_url'];
    $favicon_url = $row['favicon_url'];
} else {
    $logo_url = 'uploads/default_logo.png';  // Fallback to default if no logo found
    $favicon_url = 'uploads/default_favicon.ico';  // Fallback for favicon
}

// Fetch the contact email and phone number
$sql = "SELECT contact_email, contact_phone FROM settings WHERE id = 1 LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$contact_email = $row['contact_email'];
$contact_phone = $row['contact_phone'];

// Fetch extra menu items
$sql = "SELECT menu_name, menu_link FROM extra_menu";
$stmt = $conn->prepare($sql);
$stmt->execute();
$extra_menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch reseller services
$sql = "SELECT service_name, service_link FROM reseller_services";
$stmt = $conn->prepare($sql);
$stmt->execute();
$reseller_services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <!-- Add Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Website</title>
    <link rel="stylesheet" href="style.css">
<style>
    /* Resetting some default styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Arial', sans-serif;
        line-height: 1.6;
        background-color: #f9f9f9;
        color: #333;
    }

    /* Top bar styles */
.top-bar {
    background: #fff;
    color: #212529;
    padding: 10px 70px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background 0.3s ease;
}

/* Contact info styling */
.top-bar .contact-info {
    font-size: 16px;
    font-weight: 500;
}

.top-bar .contact-info div {
    display: flex;
    align-items: center;
}

.top-bar .contact-info i {
    margin-right: 5px; /* Space between icon and text */
    font-size: 16px; /* Icon size */
}

/* Styling for the welcome text and site title */
.site-title {
    font-size: 15px;
    font-weight: 700;
    text-align: right;
}

.site-title span {
    font-size: 14px;
    font-weight: normal;
    margin-right: 5px;
}

/* Icon alignment */
.top-bar .contact-info a {
    color: #0d6efd;
    text-decoration: none;
    margin-left: 10px;
}

.top-bar .contact-info a:hover {
    text-decoration: underline;
}

    /* Header Section */
    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 20px;
        background-color: #444;
        border-bottom: 2px solid #f1f1f1;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        margin-top: 0;
        transition: background 0.3s ease;
    }
    header {
    display: flex
;
    justify-content: space-between;
    align-items: center;
    padding: 20px 70px;
    background-color: #444;
    border-bottom: 2px solid #f1f1f1;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    margin-top: 0;
    transition: background 0.3s ease;
}
body {
    padding-top: 0px;
    background-color: #f5f5f5;
}
    .logo img {
        max-width: 200px;
        height: auto;
        transition: transform 0.3s ease, opacity 0.3s ease;
    }

    .logo img:hover {
        transform: scale(1.1);
        opacity: 0.9; /* Slight fade effect */
    }

    nav ul {
        list-style-type: none;
        display: flex;
        padding-left: 0;
    }

    nav ul li {
        margin-left: 30px;
        position: relative;
    }

    nav ul li a {
        text-decoration: none;
        color: #fff;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 14px;
        letter-spacing: 1px;
        transition: color 0.3s ease, transform 0.3s ease;
    }

    nav ul li a:hover {
        color: #007bff;
        transform: translateY(-5px); /* Hover animation */
    }

    /* Dropdown Menu */
    .dropdown {
        position: relative;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #444;
        min-width: 160px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        z-index: 1;
        border-radius: 5px;
        padding: 10px 0;
    }

    .dropdown-content a {
        color: #fff;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    .dropdown-content a:hover {
        background-color: #555;
    }

    .dropdown:hover .dropdown-content {
        display: block;
        opacity: 1;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        header {
            flex-direction: column;
            text-align: center;
            padding: 15px;
        }

        .logo img {
            max-width: 150px;
        }

        nav ul {
            flex-direction: column;
            margin-top: 20px;
            padding: 0;
        }

        nav ul li {
            margin: 10px 0;
        }

        .top-bar .contact-info {
            flex-direction: column;
            align-items: center;
            margin-bottom: 10px;
        }

        .top-bar .contact-info div {
            font-size: 14px;
            margin-bottom: 5px;
        }

        /* Mobile dropdown adjustments */
        .dropdown-content {
            position: static;
            box-shadow: none;
            opacity: 1;
        }

        .dropdown-content a {
            padding: 10px;
            font-size: 16px;
        }
    }
    footer {
    background-color: #040e18;
    padding: 20px 0;
    text-align: center;
    position: relative;
    bottom: 0;
    width: 100%;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
}
</style>

</head>
<body>

   <!-- Top Bar Section -->
<div class="top-bar">
    <div class="contact-info">
        <div>
            <i class="fa fa-envelope"></i> <!-- Email icon -->
            <strong></strong> <a href="mailto:<?php echo $contact_email; ?>"><?php echo $contact_email; ?></a> 
            <i class="fa fa-phone"></i> <!-- Phone icon -->
            <strong></strong> <a href="tel:<?php echo $contact_phone; ?>"><?php echo $contact_phone; ?></a>
        </div>
    </div>
    <div class="site-title">
        <span>Welcome to</span>
        <strong><?php echo htmlspecialchars($website_title); ?></strong> <!-- Dynamically show website title -->
    </div>
</div>



    <!-- Header Section -->
    <header>
        <div class="logo">
            <!-- Display logo -->
            <img src="<?php echo $logo_url; ?>" alt="Website Logo">
        </div>

        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>

                <!-- Reseller Services Dropdown -->
                <li class="dropdown">
                    <a href="reseller.php">Reseller Services</a>
                    <div class="dropdown-content">
                        <?php foreach ($reseller_services as $service): ?>
                            <a href="<?php echo $service['service_link']; ?>"><?php echo $service['service_name']; ?></a>
                        <?php endforeach; ?>
                        <a href="imei.php">IMEI Service</a>
                        <a href="server-service.php">Server Service</a>
                    </div>
                </li>
 <li><a href="/user/dashboard.php">My Account</a></li>
                <!-- Display Extra Menu Items -->
                <?php foreach ($extra_menu_items as $item): ?>
                    <li><a href="<?php echo $item['menu_link']; ?>"><?php echo $item['menu_name']; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </header>

</body>
</html>
