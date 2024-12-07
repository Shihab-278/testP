<?php
include 'db.php'; // Include your database connection file

// Check if any admin or user exists
try {
    $stmt = $conn->query("SELECT COUNT(*) FROM users"); // Replace 'users' with your actual table name
    $userCount = $stmt->fetchColumn();

    if ($userCount == 0) {
        header("Location: install.php");
        exit;
    }
} catch (PDOException $e) {
    echo "Error checking user count: " . $e->getMessage();
    exit;
}

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
} catch (PDOException $e) {
    echo "Error fetching settings: " . $e->getMessage();
    exit;
}

// Fetch the images for the slider
try {
    $stmt = $conn->query("SELECT * FROM images WHERE status = 'active'"); // Replace with your actual table and column name
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching images: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($website_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        .hero {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .info-box {
            position: relative;
            border-radius: 8px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            padding: 20px;
            background: #fff;
        }
        .info-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        footer {
            background: #212529;
            color: white;
            padding: 20px 0;
        }
        footer a {
            color: #00aaff;
            text-decoration: none;
        }

        /* Slider Styles */
/* Slider Styles */
.slider-container {
    position: relative;
    max-width: 100%;
    overflow: hidden;
    display: flex;
    justify-content: center;
}

.slider {
    display: flex;
    transition: transform 0.5s ease-in-out; /* Add smooth slide transition */
    width: 100%;
}

.slide {
    min-width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.slider img {
    max-width: 100%;
    height: auto;
    border-radius: 15px;
}

/* Dot navigation */
.dot-nav {
    text-align: center;
    padding-top: 10px;
}

.dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    margin: 0 5px;
    border-radius: 50%;
    background-color: #bbb;
    transition: background-color 0.3s ease;
}

.dot:hover {
    background-color: #717171;
}


    </style>
</head>
<body>

<!-- Header -->
<?php include('header.php'); ?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1><?php echo htmlspecialchars($website_title); ?></h1>
        <p><?php echo htmlspecialchars($header_content); ?></p>
        <?php if (!empty($custom_html)): ?>
        <div>
            <?php echo $custom_html; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Image Slider Section -->
<section class="py-5">
    <div class="container">
        <div class="slider-container">
            <div class="slider">
                <?php foreach ($images as $image): ?>
                    <div class="slide">
                        <img src="../uploads/<?php echo htmlspecialchars($image['filename']); ?>" alt="<?php echo htmlspecialchars($image['filename']); ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <!-- Dot navigation -->
        <div class="dot-nav">
            <?php foreach ($images as $index => $image): ?>
                <span class="dot" onclick="showSlide(<?php echo $index; ?>)"></span>
            <?php endforeach; ?>
        </div>
    </div>
</section>



<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    AOS.init();
</script>

<script>
    let currentIndex = 0;
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    const slideCount = slides.length;

    // Function to show the slide
    function showSlide(index) {
        if (index >= slideCount) currentIndex = 0;
        if (index < 0) currentIndex = slideCount - 1;

        // Move slides to the correct position
        slides.forEach((slide, i) => {
            slide.style.transform = `translateX(-${currentIndex * 100}%)`;
        });

        // Update dots
        dots.forEach((dot, i) => {
            dot.style.backgroundColor = i === currentIndex ? '#717171' : '#bbb';
        });
    }

    // Auto-play slider
    function autoPlay() {
        setInterval(() => {
            currentIndex = (currentIndex + 1) % slideCount; // Loop through slides
            showSlide(currentIndex);
        }, 3000); // Change image every 3 seconds
    }

    // Initialize the slider to show the first slide and start auto-play
    showSlide(currentIndex);
    autoPlay();
</script>

</body>
</html>


<!-- Summary Section -->
<section class="summary-section py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <!-- Box 1 -->
            <div class="col-md-4 mb-4">
                <div class="info-box shadow p-4">
                    <h3 class="info-title text-uppercase">Service</h3>
                    <p class="info-number text-success">SAM FRP</p>
                    <span class="badge bg-success">ONLINE</span>
                </div>
            </div>
            <!-- Box 2 -->
            <div class="col-md-4 mb-4">
                <div class="info-box shadow p-4">
                    <h3 class="info-title text-uppercase">Activation</h3>
                    <p class="info-number text-primary">Tools & Dongle</p>
                    <span class="badge bg-primary">Available</span>
                </div>
            </div>
            <!-- Box 3 -->
            <div class="col-md-4 mb-4">
                <div class="info-box shadow p-4">
                    <h3 class="info-title text-uppercase">Support</h3>
                    <p class="info-number text-warning">24/7</p>
                    <span class="badge bg-warning">Active</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Available Tools Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Available Tools</h2>
        <div class="row">
            <?php
            $stmt = $conn->query("SELECT * FROM pricing_boxes LIMIT 8");
            $boxes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($boxes as $box): ?>
                <div class="col-md-3 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="<?php echo htmlspecialchars($box['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($box['title']); ?>">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo htmlspecialchars($box['title']); ?></h5>
                            <p class="card-text">Price: <?php echo htmlspecialchars($box['price']); ?> Taka<br>Time: <?php echo htmlspecialchars($box['time']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Footer -->
<footer>
    <div class="container d-flex justify-content-between">
        <p><?php echo htmlspecialchars($footer_content); ?></p>
        <p>Developed by <a href="https://domhoster.com" target="_blank">DomHoster</a></p>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    AOS.init();
</script>

<script>
let currentIndex = 0;
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');
const slideCount = slides.length;

// Function to show the slide
function showSlide(index) {
    if (index >= slideCount) currentIndex = 0;
    if (index < 0) currentIndex = slideCount - 1;

    // Move slides to the correct position (from left to right)
    slides.forEach((slide, i) => {
        slide.style.transform = `translateX(-${currentIndex * 100}%)`;  // This makes the sliding effect from left to right
    });

    // Update dots
    dots.forEach((dot, i) => {
        dot.style.backgroundColor = i === currentIndex ? '#717171' : '#bbb';
    });
}

// Auto-play slider
function autoPlay() {
    setInterval(() => {
        currentIndex = (currentIndex + 1) % slideCount; // Loop through slides
        showSlide(currentIndex);
    }, 3000); // Change image every 3 seconds
}

// Initialize the slider to show the first slide and start auto-play
showSlide(currentIndex);
autoPlay();
