<?php
session_start();
include '../db.php';

// Check if the admin is logged in
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $botToken = $_POST['telegram_bot_token'];
    $chatId = $_POST['telegram_chat_id'];

    // Update bot token
    $stmt = $conn->prepare("UPDATE settings SET value = ? WHERE name = 'telegram_bot_token'");
    $stmt->execute([$botToken]);

    // Update chat ID
    $stmt = $conn->prepare("UPDATE settings SET value = ? WHERE name = 'telegram_chat_id'");
    $stmt->execute([$chatId]);

    $_SESSION['success'] = 'Settings updated successfully.';
    header('Location: settings_page.php');
    exit;
}
?>
