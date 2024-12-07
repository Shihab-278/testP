<?php
session_start();
include '../db.php';

// Ensure only admin can access this page
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if (isset($_GET['id'])) {
    $tool_id = $_GET['id'];

    // First, delete all references to this tool in user_tools
    $stmt = $conn->prepare("DELETE FROM user_tools WHERE tool_id = ?");
    $stmt->execute([$tool_id]);

    // Now delete the tool
    $stmt = $conn->prepare("DELETE FROM tools WHERE id = ?");
    $stmt->execute([$tool_id]);

    header('Location: AddTool.php'); // Redirect back to the tool list
    exit;
} else {
    echo "No tool ID provided!";
    exit;
}
