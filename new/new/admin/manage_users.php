<?php
session_start();
include '../db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if (isset($_GET['user_id']) && isset($_GET['group'])) {
    $user_id = intval($_GET['user_id']);
    $group = $_GET['group'];

    // Validate the group value
    $valid_groups = ['reseller', 'dealer', 'vip'];
    if (!in_array($group, $valid_groups)) {
        die('Invalid group!');
    }

    // Update user group
    $stmt = $conn->prepare("UPDATE users SET user_group = ? WHERE id = ?");
    $stmt->execute([$group, $user_id]);

    header('Location: manage_users.php'); // Redirect back to the user management page
    exit;
}
?>
