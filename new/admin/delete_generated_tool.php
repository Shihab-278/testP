<?php
session_start();
include '../db.php';

date_default_timezone_set('Asia/Kolkata');

// Ensure only admin can access this page
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Retrieve tool generation ID from GET request
$generation_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validate the generation ID
if ($generation_id <= 0) {
    header('Location: ViewTools.php?error=1');
    exit;
}

// Delete the tool from the user_tools table
$stmt = $conn->prepare("DELETE FROM user_tools WHERE id = ?");
$stmt->execute([$generation_id]);

// Redirect back to the view page with a success message
header('Location: ViewTools.php?success=1');
exit;
