<?php include('header.php'); ?>

<!-- Hero Section -->
<section class="hero bg-dark text-white text-center py-5">
    <div class="container">
        <h1 class="animate-fade-in">Welcome to GSMXTOOL</h1>
        <p class="animate-slide-up">Your one-stop solution for tool rentals and activation services.</p>
        <a href="#pricing-section" class="btn btn-primary mt-3 animate-fade-in" style="animation-delay: 0.5s;">Explore Tools</a>
    </div>
</section>

<!-- Pricing Section -->
<section id="pricing-section" class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5 animate-fade-in">Available Now</h2>
        <div class="row g-4">
            <!-- Dynamic Tool Cards -->
            <?php
            $tools = [
                ["Unlock Tool", "50 Taka", "6 Hrs", "unlocktool.png"],
                ["Chimera Tool", "50 Taka", "2 Hrs", "chimera.png"],
                ["DFT Pro", "100 Taka", "6 Hrs", "dft.png"],
                ["AMT Tool", "100 Taka", "2 Hrs", "amt.png"],
                ["Griffin Tool", "150 Taka", "8 Hrs", "griffin.png"],
                ["Cheetah Tool", "150 Taka", "4 Hrs", "chet.png"],
                ["CF Tool", "200 Taka", "8 Hrs", "cf.png"],
                ["TFM Tool", "200 Taka", "4 Hrs", "tf.png"]
            ];

            foreach ($tools as $index => $tool) {
                echo '
                    <div class="col-lg-3 col-md-4 col-sm-6 animate-card" style="animation-delay: ' . ($index * 0.2) . 's;">
                        <div class="card shadow-sm h-100">
                            <img src="img/' . $tool[3] . '" class="card-img-top" alt="' . $tool[0] . '">
                            <div class="card-body text-center">
                                <h5 class="card-title">' . $tool[0] . '</h5>
                                <p class="card-text">
                                    <strong>Price:</strong> ' . $tool[1] . '<br>
                                    <strong>Time:</strong> ' . $tool[2] . '
                                </p>
                                <a href="/user/dashboard.php" class="btn btn-success btn-sm">Rent Now</a>
                            </div>
                        </div>
                    </div>
                ';
            }
            ?>
        </div>
    </div>
</section>

<!-- WhatsApp Chat Button -->
<a href="https://wa.me/8801908021826" target="_blank" id="whatsapp-button">
    <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" class="whatsapp-icon">
    <span id="whatsapp-text">WhatsApp</span>
</a>

<!-- Styling and Animations -->
<style>
    /* Hero Section */
    .hero {
        background: linear-gradient(45deg, #25D366, #128C7E);
        color: white;
    }

    /* Section Animations */
    .animate-fade-in {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeIn 1s ease-out forwards;
    }

    .animate-slide-up {
        opacity: 0;
        transform: translateY(40px);
        animation: slideUp 1.2s ease-out forwards;
    }

    .animate-card {
        opacity: 0;
        transform: scale(0.95);
        animation: cardPop 0.8s ease-in-out forwards;
    }

    /* Pricing Section */
    .card {
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    /* WhatsApp Button */
    #whatsapp-button {
        position: fixed;
        bottom: 20px;
        right: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 8px 15px;
        border-radius: 30px;
        font-family: Arial, sans-serif;
        text-decoration: none;
        color: white;
        font-weight: bold;
        font-size: 14px;
        z-index: 1000;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        background-image: linear-gradient(45deg, #25D366, #128C7E);
        background-size: 200% 200%;
        animation: gradientBackground 3s ease infinite;
    }

    #whatsapp-button:hover {
        transform: translateY(-5px);
        background-position: right center;
    }

    .whatsapp-icon {
        width: 30px;
        height: 30px;
        margin-right: 10px;
    }

    /* Keyframes for Animations */
    @keyframes fadeIn {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideUp {
        0% {
            opacity: 0;
            transform: translateY(40px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes cardPop {
        0% {
            opacity: 0;
            transform: scale(0.95);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes gradientBackground {
        0% {
            background-position: left center;
        }
        50% {
            background-position: right center;
        }
        100% {
            background-position: left center;
        }
    }
</style>

<?php include('footer.php'); ?>
