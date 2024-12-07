<?php
ob_start();  // Start output buffering
session_start();
include '../db.php';

// Ensure only admin can access this page
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Check if tool IDs are passed
if (isset($_POST['tool_ids']) && is_array($_POST['tool_ids'])) {
    $tool_ids = $_POST['tool_ids'];

    // Prepare the SQL query to delete the selected tools
    $placeholders = rtrim(str_repeat('?,', count($tool_ids)), ',');
    $stmt = $conn->prepare("DELETE FROM user_tools WHERE id IN ($placeholders)");
    $stmt->execute($tool_ids);

    // Redirect with success message
    header('Location: view_generated_tools.php?success=1');
    exit;
} else {
    // Redirect with error if no tools are selected
    header('Location: view_generated_tools.php?error=1');
    exit;
}
?>
