<?php
session_start();
include '../db.php';
// Set timezone to Asia/Kolkata (Indian Standard Time)
date_default_timezone_set('Asia/Kolkata');

// Function to send Telegram notification
function sendTelegramNotification($message)
{
    $telegramBotToken = '7857067266:AAHsmiNevnfRktIswl4qtXmXibY--T8gG5Q';
    $chatId = '@DOM Hoster'; // Replace with your chat ID

    $url = "https://api.telegram.org/bot$telegramBotToken/sendMessage";

    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];

    $context  = stream_context_create($options);
    file_get_contents($url, false, $context);
}

// Check if the user is logged in and has the correct role
if ($_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

// Retrieve user ID from session
$user_id = $_SESSION['user_id'];

// Get username from the database
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user ? $user['username'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tool_id'])) {
        $tool_id = $_POST['tool_id'];

        // Get the tool details
        $stmt = $conn->prepare("SELECT * FROM tools WHERE id = ?");
        $stmt->execute([$tool_id]);
        $tool = $stmt->fetch();

        if (!$tool) {
            $response['message'] = 'Tool not found!';
        } else {
            // Check if the user has already generated this tool
            $stmt = $conn->prepare("SELECT * FROM user_tools WHERE user_id = ? AND tool_id = ?");
            $stmt->execute([$user_id, $tool_id]);
            $alreadyGenerated = $stmt->fetch();

            if ($alreadyGenerated) {
                $response['message'] = 'You have already generated this tool!';
            } else {
                // Get user info
                $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();

                // Check if the user has enough credits
                if ($user['balance'] < $tool['tool_cost']) {
                    $response['message'] = 'Not enough credit to generate this tool.';
                } else {
                    // Deduct the tool cost from the user's balance
                    $stmt = $conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
                    $stmt->execute([$tool['tool_cost'], $user_id]);

                    // Log the credit usage
                    $stmt = $conn->prepare("INSERT INTO credit_usage (user_id_fk, credits_used, usage_date) VALUES (?, ?, NOW())");
                    $stmt->execute([$user_id, $tool['tool_cost']]);

                    // Store the generated tool in the user_tools table
                    $stmt = $conn->prepare("INSERT INTO user_tools (user_id, tool_id, generated_at) VALUES (?, ?, NOW())");
                    $stmt->execute([$user_id, $tool_id]);

                    // Fetch the updated list of generated tools
                    $tools_stmt = $conn->prepare("
                        SELECT tools.tool_name, tools.tool_username, tools.tool_password, user_tools.generated_at
                        FROM tools 
                        JOIN user_tools ON tools.id = user_tools.tool_id 
                        WHERE user_tools.user_id = ?
                    ");
                    $tools_stmt->execute([$user_id]);
                    $generated_tools = $tools_stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Prepare tool details for response
                    $response = [
                        'status' => 'success',
                        'message' => '',
                        'tool' => [
                            'name' => htmlspecialchars($tool['tool_name']),
                            'username' => htmlspecialchars($tool['tool_username']),
                            'password' => htmlspecialchars($tool['tool_password']),
                        ],
                        'generated_tools' => $generated_tools // Send updated generated tools list
                    ];


                    // Send Telegram notification
                    $message = "🚨 New Tool Generated! 🚨\n\n";
                    $message .= "═══════════════════════════════════\n";
                    $message .= "     🌟 Tool Details 🌟\n";
                    $message .= "═══════════════════════════════════\n";
                    $message .= " 🛠️ Tool Name: " . $tool['tool_name'] . "\n";
                    $message .= " 👤 Tool User: " . $tool['tool_username'] . "\n";
                    $message .= " 🔑 Tool Password: " . $tool['tool_password'] . "\n";
                    $message .= "═══════════════════════════════════\n\n";
                    $message .= "👤 Generated By: " . $username; // Include the username

                    sendTelegramNotification($message);
                }
            }
        }
    } else {
        $response['message'] = 'No tool selected!';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
