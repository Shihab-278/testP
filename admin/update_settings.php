<?php
session_start();
include '../db.php';

// Ensure only admin can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Retrieve user ID from session
$user_id = $_SESSION['user_id'];

// Get username from the database
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user ? $user['username'] : '';

// Database connection (replace with your own credentials)
$pdo = new PDO('mysql:host=localhost;dbname=domhoste_test', 'domhoste_test', 'domhoste_test');

// Fetch current settings from the database
$query = "SELECT * FROM settings WHERE name IN ('recaptcha_enabled', 'recaptcha_site_key', 'recaptcha_secret_key', 'website_title', 'header_content', 'footer_content', 'custom_html', 'logo', 'telegram_channel_link', 'phone', 'email', 'reseller_services')";
$stmt = $pdo->query($query);
$settings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set the current values for the settings
$recaptchaEnabled = 0;
$recaptchaSiteKey = '';
$recaptchaSecretKey = '';
$siteTitle = '';
$headerText = '';
$footerText = '';
$customHtml = '';
$logoPath = '';  // Add a variable for logo
$telegramLink = '';  // Add variable for Telegram link
$phone = '';
$email = '';
$resellerServices = '';

foreach ($settings as $setting) {
    if ($setting['name'] == 'recaptcha_enabled') {
        $recaptchaEnabled = (int)$setting['value'];
    } elseif ($setting['name'] == 'recaptcha_site_key') {
        $recaptchaSiteKey = $setting['value'];
    } elseif ($setting['name'] == 'recaptcha_secret_key') {
        $recaptchaSecretKey = $setting['value'];
    } elseif ($setting['name'] == 'website_title') {
        $siteTitle = $setting['value'];
    } elseif ($setting['name'] == 'header_content') {
        $headerText = $setting['value'];
    } elseif ($setting['name'] == 'footer_content') {
        $footerText = $setting['value'];
    } elseif ($setting['name'] == 'custom_html') {
        $customHtml = $setting['value'];
    } elseif ($setting['name'] == 'logo') {
        $logoPath = $setting['value'];  // Get the current logo path
    } elseif ($setting['name'] == 'telegram_channel_link') {
        $telegramLink = $setting['value'];  // Get the current Telegram link
    } elseif ($setting['name'] == 'phone') {
        $phone = $setting['value'];
    } elseif ($setting['name'] == 'email') {
        $email = $setting['value'];
    } elseif ($setting['name'] == 'reseller_services') {
        $resellerServices = $setting['value'];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recaptchaEnabled = isset($_POST['recaptcha_enabled']) ? 1 : 0;
    $recaptchaSiteKey = $_POST['recaptcha_site_key'];
    $recaptchaSecretKey = $_POST['recaptcha_secret_key'];
    $siteTitle = $_POST['website_title'];
    $headerText = $_POST['header_content'];
    $footerText = $_POST['footer_content'];
    $customHtml = $_POST['custom_html'];
    $telegramLink = $_POST['telegram_link'];  // Handle Telegram link
    $phone = $_POST['phone'];  // Handle phone input
    $email = $_POST['email'];  // Handle email input
    $resellerServices = $_POST['reseller_services'];  // Handle reseller services input

    // Handle file upload for logo
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $fileTmpPath = $_FILES['logo']['tmp_name'];
        $fileName = $_FILES['logo']['name'];
        $fileType = $_FILES['logo']['type'];

        // Check if the file is an image (PNG/JPG)
        $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
        if (in_array($fileType, $allowedTypes)) {
            $uploadDir = '../user/img/';
            $filePath = $uploadDir . basename($fileName);
            if (move_uploaded_file($fileTmpPath, $filePath)) {
                $logoPath = $filePath;  // Set the new logo path
            }
        }
    }

    // Save the updated settings in the database
    $stmt = $pdo->prepare("INSERT INTO settings (name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = ?");
    $stmt->execute(['recaptcha_enabled', $recaptchaEnabled, $recaptchaEnabled]);
    $stmt->execute(['recaptcha_site_key', $recaptchaSiteKey, $recaptchaSiteKey]);
    $stmt->execute(['recaptcha_secret_key', $recaptchaSecretKey, $recaptchaSecretKey]);
    $stmt->execute(['website_title', $siteTitle, $siteTitle]);
    $stmt->execute(['header_content', $headerText, $headerText]);
    $stmt->execute(['footer_content', $footerText, $footerText]);
    $stmt->execute(['custom_html', $customHtml, $customHtml]);
    if ($logoPath) {
        $stmt->execute(['logo', $logoPath, $logoPath]);  // Update the logo path
    }
    $stmt->execute(['telegram_channel_link', $telegramLink, $telegramLink]);  // Update the Telegram link
    $stmt->execute(['phone', $phone, $phone]);  // Update phone number
    $stmt->execute(['email', $email, $email]);  // Update email address
    $stmt->execute(['reseller_services', $resellerServices, $resellerServices]);  // Update reseller services

    $successMessage = 'Settings updated successfully.';
}
?>

<?php include 'header.php'; // Include your header ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Update Website Settings</h2>

    <?php if (isset($successMessage)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <form method="post" action="update_settings.php" id="settings-form" enctype="multipart/form-data">

        <!-- Website General Settings -->
        <div class="form-section mb-4">
            <h4>Website General Settings</h4>
            <div class="form-group">
                <label for="website_title">Website Title:</label>
                <input type="text" id="website_title" name="website_title" value="<?php echo htmlspecialchars($siteTitle); ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="header_content">Header Text:</label>
                <input type="text" id="header_content" name="header_content" value="<?php echo htmlspecialchars($headerText); ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="footer_content">Footer Text:</label>
                <input type="text" id="footer_content" name="footer_content" value="<?php echo htmlspecialchars($footerText); ?>" class="form-control" required>
            </div>
        </div>

        <!-- Phone, Email, Reseller Services -->
        <div class="form-section mb-4">
            <h4>Contact Information</h4>
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="reseller_services">Reseller Services:</label>
                <textarea id="reseller_services" name="reseller_services" rows="4" class="form-control"><?php echo htmlspecialchars($resellerServices); ?></textarea>
            </div>
        </div>

        <!-- reCAPTCHA Settings Section -->
        <div class="form-section mb-4">
            <h4>reCAPTCHA Settings</h4>
            <div class="form-check">
                <input type="checkbox" id="recaptcha_enabled" name="recaptcha_enabled" class="form-check-input" <?php echo $recaptchaEnabled ? 'checked' : ''; ?> onchange="toggleRecaptchaFields()">
                <label for="recaptcha_enabled" class="form-check-label">Enable reCAPTCHA</label>
            </div>

            <div class="form-group">
                <label for="recaptcha_site_key">reCAPTCHA Site Key:</label>
                <input type="text" id="recaptcha_site_key" name="recaptcha_site_key" value="<?php echo htmlspecialchars($recaptchaSiteKey); ?>" class="form-control" <?php echo !$recaptchaEnabled ? 'disabled' : ''; ?>>
            </div>

            <div class="form-group">
                <label for="recaptcha_secret_key">reCAPTCHA Secret Key:</label>
                <input type="text" id="recaptcha_secret_key" name="recaptcha_secret_key" value="<?php echo htmlspecialchars($recaptchaSecretKey); ?>" class="form-control" <?php echo !$recaptchaEnabled ? 'disabled' : ''; ?>>
            </div>
        </div>

        <!-- Telegram Settings Section -->
        <div class="form-section mb-4">
            <h4>Telegram Channel Link</h4>
            <div class="form-group">
                <label for="telegram_link">Telegram Channel Link:</label>
                <input type="url" name="telegram_link" id="telegram_link" class="form-control" value="<?php echo htmlspecialchars($telegramLink ?? ''); ?>" required>
            </div>
        </div>

        <!-- Logo Upload Section -->
        <div class="form-section mb-4">
            <h4>Logo Settings</h4>
            <div class="form-group">
                <label for="logo">Upload Logo:</label>
                <input type="file" id="logo" name="logo" class="form-control-file">
                <?php if ($logoPath): ?>
                    <p>Current Logo: <img src="<?php echo $logoPath; ?>" alt="Logo" height="50"></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Custom HTML Settings Section -->
        <div class="form-section mb-4">
            <h4>Custom HTML Settings</h4>
            <div class="form-check">
                <input type="checkbox" id="custom_html_enabled" name="custom_html_enabled" class="form-check-input" <?php echo !empty($customHtml) ? 'checked' : ''; ?> onchange="toggleCustomHtmlField()">
                <label for="custom_html_enabled" class="form-check-label">Enable Custom HTML</label>
            </div>

            <div class="form-group">
                <label for="custom_html">Custom HTML (e.g., for header or footer):</label>
                <textarea id="custom_html" name="custom_html" rows="6" class="form-control" <?php echo empty($customHtml) ? 'disabled' : ''; ?>><?php echo htmlspecialchars($customHtml); ?></textarea>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg">Save Settings</button>
        </div>
    </form>
</div>

<script>
    function toggleRecaptchaFields() {
        const recaptchaEnabled = document.getElementById('recaptcha_enabled').checked;
        document.getElementById('recaptcha_site_key').disabled = !recaptchaEnabled;
        document.getElementById('recaptcha_secret_key').disabled = !recaptchaEnabled;
    }

    function toggleCustomHtmlField() {
        const customHtmlEnabled = document.getElementById('custom_html_enabled').checked;
        document.getElementById('custom_html').disabled = !customHtmlEnabled;
    }

    // Call the toggleRecaptchaFields function on page load to set the initial state
    window.onload = function() {
        toggleRecaptchaFields();
        toggleCustomHtmlField();
    };
</script>

<?php include 'footer.php'; // Include your footer ?>
