<?php
session_start();
include '../db.php';

// Set timezone to Asia/Dhaka (Bangladesh Standard Time)
date_default_timezone_set('Asia/Dhaka');

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

// Retrieve user ID from session
$user_id = $_SESSION['user_id'] ?? null;

// Fetch user details
$stmt = $conn->prepare("SELECT username, `group`, balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user['username'] ?? '';
$group_name = $user['group'] ?? '';
$balance = $user['balance'] ?? 0;

// Fetch user's generated tools
$tools_stmt = $conn->prepare("
    SELECT tools.tool_name, tools.tool_username, tools.tool_password, user_tools.generated_at 
    FROM user_tools 
    JOIN tools ON user_tools.tool_id = tools.id 
    WHERE user_tools.user_id = ?
");
$tools_stmt->execute([$user_id]);
$generated_tools = $tools_stmt->fetchAll();

// Fetch today's credit usage
$today = date('Y-m-d');
$credit_stmt = $conn->prepare("
    SELECT COALESCE(SUM(credits_used), 0) AS total_used 
    FROM credit_usage 
    WHERE user_id_fk = ? AND DATE(usage_date) = ?
");
$credit_stmt->execute([$user_id, $today]);
$total_today_amount = $credit_stmt->fetchColumn();

// Handle "Add Funds" form submission
$response = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount'])) {
    $amount = filter_var($_POST['amount'], FILTER_VALIDATE_FLOAT);

    if ($amount && $amount > 0) {
        $response = redirect_to_payment_gateway($amount, $username);
    } else {
        $response = "Please enter a valid amount to add funds.";
    }
}

/**
 * Redirect to payment gateway function
 */
function redirect_to_payment_gateway($amount, $username)
{
    $apiUrl = 'https://securepay.crabdance.com/api/checkout-v1';
    $apiKey = 'AV1TNgQ0nRGinrvLc2m1L7cwd';

    // Prepare API request data
    $postData = [
        'cus_name' => $username,
        'cus_email' => 'user@example.com', // Replace with user's actual email
        'amount' => $amount,
        'conversion' => 'BDT',
        'success_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/callback.php',
        'metadata' => json_encode(['username' => $username]),
    ];

    // Initialize cURL
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'app-key: ' . $apiKey,
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL request
    $response = curl_exec($ch);
    $curlError = curl_error($ch); // Capture cURL error
    curl_close($ch);

    // Decode response
    $responseData = json_decode($response, true);

    // Handle response
    if ($curlError) {
        return 'cURL Error: ' . htmlspecialchars($curlError, ENT_QUOTES);
    }

    if (isset($responseData['host_url'])) {
        header('Location: ' . $responseData['host_url']);
        exit;
    } else {
        return 'Error: Unable to retrieve payment link. ' . htmlspecialchars($responseData['message'] ?? 'Unknown error', ENT_QUOTES);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Funds</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <!-- User Details -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h3>Welcome, <?php echo htmlspecialchars($username); ?></h3>
                <p>Group: <?php echo htmlspecialchars($group_name); ?> | Balance: $<?php echo number_format($balance, 2); ?></p>
            </div>
        </div>

        <!-- Add Fund Form -->
        <div class="col-12 mt-4">
            <div class="card p-3">
                <h4 class="fw-bold text-center">Add Funds</h4>
                <form action="" method="POST" class="mt-3">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" id="amount" name="amount" class="form-control" placeholder="Enter amount to add" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Funds</button>
                </form>
                <?php if ($response): ?>
                    <div class="mt-3 alert alert-warning"><?php echo htmlspecialchars($response); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
