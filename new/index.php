<?php include('header.php'); ?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Welcome to GSMXTOOL</h1>
        <p>Your one-stop solution for tool rentals and activation services.</p>
    </div>
</section>

<!-- Summary Section (Boxes above Pricing) -->
<section class="summary-section py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <!-- Box 1 -->
            <div class="col-md-3 mb-4">
                <div class="info-box p-4 bg-white shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="info-title">Service</h3>
                        <span class="badge green-badge">ONLINE</span>
                    </div>
                    <p class="info-number">SAM FRP</p>
                </div>
            </div>
            <!-- Box 2 -->
            <div class="col-md-3 mb-4">
                <div class="info-box p-4 bg-white shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="info-title">Tools</h3>
                        <span class="badge green-badge">Available</span>
                    </div>
                    <p class="info-number">10</p>
                </div>
            </div>
            <!-- Box 3 -->
            <div class="col-md-3 mb-4">
                <div class="info-box p-4 bg-white shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="info-title">User</h3>
                        <span class="badge green-badge">Active</span>
                    </div>
                    <p class="info-number">140</p>
                </div>
            </div>
            <!-- Box 4 -->
            <div class="col-md-3 mb-4">
                <div class="info-box p-4 bg-white shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="info-title">Support</h3>
                        <span class="badge green-badge">24/7</span>
                    </div>
                    <p class="info-number">Online</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section class="py-5">
    <div class="container">
        <h2 class="section-title text-center">Available Now</h2>
        <div class="row">
            <!-- Card 1 -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="img/unlocktool.png" class="card-img-top" alt="Unlock Tool">
                    <div class="card-body text-center">
                        <h5 class="card-title">Unlock Tool</h5>
                        <p class="card-text">Price: 50 Taka<br>Time: 6 Hrs</p>
                    </div>
                </div>
            </div>
            <!-- Card 2 -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="img/chimera.png" class="card-img-top" alt="AMT Tool">
                    <div class="card-body text-center">
                        <h5 class="card-title">Chimera Tool</h5>
                        <p class="card-text">Price: 50 Taka<br>Time: 2 Hrs</p>
                    </div>
                </div>
            </div>
            <!-- Card 3 -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="img/dft.png" class="card-img-top" alt="TFM Tool">
                    <div class="card-body text-center">
                        <h5 class="card-title">DFT Pro</h5>
                        <p class="card-text">Price: 100 Taka<br>Time: 6 Hrs</p>
                    </div>
                </div>
            </div>
            <!-- Card 4 -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="img/amt.png" class="card-img-top" alt="SRS Tool">
                    <div class="card-body text-center">
                        <h5 class="card-title">AMT Tool</h5>
                        <p class="card-text">Price: 100 Taka<br>Time: 2 Hrs</p>
                    </div>
                </div>
            </div>
            <!-- Card 5 -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="img/griffin.png" class="card-img-top" alt="Advanced Unlock Tool">
                    <div class="card-body text-center">
                        <h5 class="card-title">Griffin Tool</h5>
                        <p class="card-text">Price: 150 Taka<br>Time: 8 Hrs</p>
                    </div>
                </div>
            </div>
            <!-- Card 6 -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="img/chet.png" class="card-img-top" alt="Premium AMT Tool">
                    <div class="card-body text-center">
                        <h5 class="card-title">Cheetah Tool</h5>
                        <p class="card-text">Price: 150 Taka<br>Time: 4 Hrs</p>
                    </div>
                </div>
            </div>
            <!-- Card 7 -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="img/cf.png" class="card-img-top" alt="Elite TFM Tool">
                    <div class="card-body text-center">
                        <h5 class="card-title">CF Tool</h5>
                        <p class="card-text">Price: 200 Taka<br>Time: 8 Hrs</p>
                    </div>
                </div>
            </div>
            <!-- Card 8 -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="img/tf.png" class="card-img-top" alt="Pro SRS Tool">
                    <div class="card-body text-center">
                        <h5 class="card-title">TFM Tool</h5>
                        <p class="card-text">Price: 200 Taka<br>Time: 4 Hrs</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- WhatsApp Chat Button -->
<a href="https://wa.me/8801908021826" target="_blank" id="whatsapp-button">
    <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" class="whatsapp-icon">
    <span class="whatsapp-text">WhatsApp</span>
</a>

<?php include('footer.php'); ?>

<!-- CSS Styling -->
<style>
    /* Summary Section Styling */
    .info-box {
        border-radius: 8px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .info-box:hover {
        transform: translateY(-5px);
        box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.2);
    }

    .info-title {
        font-size: 1.2rem;
        color: #333;
    }

    .info-number {
        font-size: 2rem;
        font-weight: bold;
        margin-top: 10px;
        color: #28a745;
    }

    .badge {
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: bold;
    }

    .green-badge {
        background-color: #28a745;
        color: white;
        animation: fadeBadge 2s infinite;
    }

    /* Badge Animation */
    @keyframes fadeBadge {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }

    /* WhatsApp Button Styling */
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
       
