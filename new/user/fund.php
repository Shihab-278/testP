<?php
session_start();
include '../db.php';

// Set timezone to Asia/Dhaka (Bangladesh Standard Time)
date_default_timezone_set('Asia/Dhaka');

// Check if the user is logged in and has the correct role
if ($_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

// Retrieve user ID from session
$user_id = $_SESSION['user_id'];

// Get user information
$stmt = $conn->prepare("SELECT username, `group`, balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user['username'] ?? '';
$group_name = $user['group'] ?? '';
$balance = $user['balance'] ?? 0;

// Fetch user's generated tools
$tools_stmt = $conn->prepare("SELECT tools.tool_name, tools.tool_username, tools.tool_password, user_tools.generated_at 
                              FROM user_tools 
                              JOIN tools ON user_tools.tool_id = tools.id 
                              WHERE user_tools.user_id = ?");
$tools_stmt->execute([$user_id]);
$generated_tools = $tools_stmt->fetchAll();

// Fetch today's credit usage
$today = date('Y-m-d');
$credit_stmt = $conn->prepare("SELECT COALESCE(SUM(credits_used), 0) AS total_used 
                               FROM credit_usage 
                               WHERE user_id_fk = ? AND DATE(usage_date) = ?");
$credit_stmt->execute([$user_id, $today]);
$total_today_amount = $credit_stmt->fetchColumn();

include 'header.php'; // Include user-side header
?>

<!-- Add Bootstrap 5 JS and FontAwesome -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>


<div class="container mt-5">
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card p-4 box">
                <div class="img-box text-center">
                    <img src="/img/binance.png" alt="Visa Logo" class="img-fluid" />
                </div>
                <div class="info-box" id="info-1" style="display:none;">
                    <p class="fw-bold">Visa Payment Method</p>
                    <p class="text-muted">Make secure payments with your Visa card. Details can be provided after selection.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card p-4 box">
                <div class="img-box text-center">
                    <img src="/img/bkash.png" alt="Visa Logo" class="img-fluid" />
                </div>
                <div class="info-box" id="info-2" style="display:none;">
                    <p class="fw-bold">MasterCard Payment Method</p>
                    <p class="text-muted">Pay securely with MasterCard. More details available after selection.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card p-4 box">
                <div class="img-box text-center">
                    <img src="/img/nagad.png" alt="Visa Logo" class="img-fluid" />
                </div>
                <div class="info-box" id="info-3" style="display:none;">
                    <p class="fw-bold">PayPal Payment Method</p>
                    <p class="text-muted">Fast and easy payments with PayPal. More details can be provided after selection.</p>
                </div>
            </div>
        </div>

        <!-- Payment Methods Section -->
<div class="col-12 mt-4">
    <div class="card p-3">
        <p class="mb-0 fw-bold h4">Payment Methods</p>
    </div>
</div>

<!-- Payment Method 1: Bkash -->
<div class="col-12 mb-4">
    <div class="card p-3">
        <div class="card-body border p-0">
            <p>
                <a class="btn btn-primary w-100 h-100 d-flex align-items-center justify-content-between"
                    data-bs-toggle="collapse" href="#bkashCollapse" role="button" aria-expanded="true"
                    aria-controls="bkashCollapse">
                    <span class="fw-bold">Bkash</span>
                    <span class="fab fa-bkash"></span>
                </a>
            </p>
            <div class="collapse p-3 pt-0" id="bkashCollapse">
                <div class="row">
                    <div class="col-8">
                        <p class="h4 mb-0">Bkash Personal</p>
                        <p class="mb-0"><span class="fw-bold">Number:</span><span class="text-success">01908021826</span></p>
                        <p class="mb-0"><span class="fw-bold">Add Limit:</span><span class="text-success"> 100-50000</span></p>
                        <p class="mb-0">After Send Payment Please Contact With us.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Method 2: Nagad -->
<div class="col-12 mb-4">
    <div class="card p-3">
        <div class="card-body border p-0">
            <p>
                <a class="btn btn-warning w-100 h-100 d-flex align-items-center justify-content-between"
                    data-bs-toggle="collapse" href="#nagadCollapse" role="button" aria-expanded="false"
                    aria-controls="nagadCollapse">
                    <span class="fw-bold">Nagad</span>
                    <span class="fas fa-money-check"></span>
                </a>
            </p>
            <div class="collapse p-3 pt-0" id="nagadCollapse">
                <div class="row">
                    <div class="col-8">
                        <p class="h4 mb-0">Nagad Payment</p>
                        <p class="mb-0"><span class="fw-bold">Product:</span><span class="text-success"> Name of product</span></p>
                        <p class="mb-0"><span class="fw-bold">Price:</span><span class="text-success"> $300.00</span></p>
                        <p class="mb-0">Pay using Nagad for quick and easy transactions.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Method 3: Rocket -->
<div class="col-12 mb-4">
    <div class="card p-3">
        <div class="card-body border p-0">
            <p>
                <a class="btn btn-secondary w-100 h-100 d-flex align-items-center justify-content-between"
                    data-bs-toggle="collapse" href="#rocketCollapse" role="button" aria-expanded="false"
                    aria-controls="rocketCollapse">
                    <span class="fw-bold">Rocket</span>
                    <span class="fas fa-university"></span>
                </a>
            </p>
            <div class="collapse p-3 pt-0" id="rocketCollapse">
                <div class="row">
                    <div class="col-8">
                        <p class="h4 mb-0">Rocket Payment</p>
                        <p class="mb-0"><span class="fw-bold">Product:</span><span class="text-success"> Name of product</span></p>
                        <p class="mb-0"><span class="fw-bold">Price:</span><span class="text-success"> $200.00</span></p>
                        <p class="mb-0">Enjoy secure and reliable payment through Rocket.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Method 4: PayPal -->
<div class="col-12 mb-4">
    <div class="card p-3">
        <div class="card-body border p-0">
            <p>
                <a class="btn btn-info w-100 h-100 d-flex align-items-center justify-content-between"
                    data-bs-toggle="collapse" href="#paypalCollapse" role="button" aria-expanded="false"
                    aria-controls="paypalCollapse">
                    <span class="fw-bold">PayPal</span>
                    <span class="fab fa-paypal"></span>
                </a>
            </p>
            <div class="collapse p-3 pt-0" id="paypalCollapse">
                <div class="row">
                    <div class="col-8">
                        <p class="h4 mb-0">PayPal Payment</p>
                        <p class="mb-0"><span class="fw-bold">Product:</span><span class="text-success"> Name of product</span></p>
                        <p class="mb-0"><span class="fw-bold">Price:</span><span class="text-success"> $500.00</span></p>
                        <p class="mb-0">Quick and easy payments with PayPal.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-12 mt-4">
    <div class="card p-3">
        <p class="mb-0 fw-bold h4 text-center">Need Add Credit Please Contact.</p>
    </div>
</div>

<?php
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // Validate fields
    if (empty($name) || empty($email) || empty($message)) {
        $response = "All fields are required.";
    } else {
        // Email details
        $to = "shakilhossian2236@gmail.com"; // Replace with your Gmail address
        $subject = "New Contact Form Submission";
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        // Email body
        $email_body = "You have received a new message from your contact form.\n\n";
        $email_body .= "Name: $name\n";
        $email_body .= "Email: $email\n";
        $email_body .= "Message:\n$message\n";

        // Send email
        if (mail($to, $subject, $email_body, $headers)) {
            $response = "Your message has been sent successfully!";
        } else {
            $response = "There was an error sending your message. Please try again.";
        }
    }
}
?>


</html>


<style>
    body {
        background-color: #f5f8fb;
        font-family: 'Arial', sans-serif;
    }

    .box {
        cursor: pointer;
        border-radius: 12px;
        transition: transform 0.3s ease-in-out;
    }

    .box:hover {
        transform: scale(1.05
