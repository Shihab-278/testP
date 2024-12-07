<?php include('header.php'); ?>

<!-- Hero Section -->
<section class="hero text-center text-white py-5">
    <div class="container position-relative">
        <div class="hero-overlay"></div>
        <h1 class="animate-fade-in">Welcome to <span class="brand-highlight">GSMXTOOL</span></h1>
        <p class="animate-slide-up">Your one-stop solution for tool rentals and activation services.</p>
        <a href="#pricing-section" class="btn btn-gradient mt-3 animate-bounce">Explore Tools</a>
    </div>
</section>

<!-- Pricing Section -->
<section id="pricing-section" class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5 animate-fade-in">Available Tools</h2>
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
                        <div class="card shadow-sm h-100 card-hover gradient-border">
                            <div class="card-image">
                                <img src="img/' . $tool[3] . '" class="card-img-top" alt="' . $tool[0] . '">
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title">' . $tool[0] . '</h5>
                                <p class="card-text">
                                    <strong>Price:</strong> ' . $tool[1] . '<br>
                                    <strong>Time:</strong> ' . $tool[2] . '
                                </p>
                                <a href="#contact" class="btn btn-gradient btn-sm">Rent Now</a>
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
    /* General Reset */
    body {
        font-family: 'Arial', sans-serif;
    }

    /* Hero Section with Dynamic Gradient */
    .hero {
        background: linear-gradient(90deg, #6a11cb, #2575fc, #ff5e62);
        background-size: 300% 300%;
        animation: gradientShift 6s infinite;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .hero .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.2));
        z-index: -1;
    }

    @keyframes gradientShift {
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

    .brand-highlight {
        background: linear-gradient(45deg, #ff9a9e, #fad0c4);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .btn-gradient {
        display: inline-block;
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 16px;
        text-decoration: none;
        color: white;
        background: linear-gradient(90deg, #ff9966, #ff5e62);
        transition: transform 0.3s, background 0.3s;
        overflow: hidden;
        position: relative;
    }

    .btn-gradient:hover {
        background: linear-gradient(90deg, #6a11cb, #2575fc);
        transform: scale(1.05);
    }

    /* Sliding Headline Section */
    .headline-section {
        position: relative;
        overflow: hidden;
    }

    .headline-slide {
        font-size: 1.5rem;
        font-weight: bold;
        white-space: nowrap;
        display: inline-block;
        position: relative;
        animation: slideLeft 10s linear infinite;
    }

    @keyframes slideLeft {
        0% {
            transform: translateX(100%);
        }
        100% {
            transform: translateX(-100%);
        }
    }

    /* WhatsApp Button */
    #whatsapp-button {
        position: fixed;
        bottom: 20px;
        right: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px 18px;
        border-radius: 50px;
        text-decoration: none;
        color: white;
        font-weight: bold;
        background: linear-gradient(90deg, #25D366, #128C7E);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        animation: pulse 2s infinite;
        transition: transform 0.3s;
    }

    #whatsapp-button:hover {
        transform: translateY(-5px);
        background: linear-gradient(90deg, #128C7E, #25D366);
    }

    .whatsapp-icon {
        width: 30px;
        height: 30px;
        margin-right: 10px;
    }

    /* Animations */
    @keyframes pulse {
        0% {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }
        50% {
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }
        100% {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }
    }
</style>

<?php include('footer.php'); ?>
