<?php
session_start();
include '../db.php'; // Ensure db connection is established


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Admin Dashboard</h1>

        <div class="text-center">
            <a href="manage_orders.php" class="btn btn-primary">Manage Orders</a>
            <a href="manage_services.php" class="btn btn-secondary">Manage Services</a>
        </div>

        <p><a href="logout.php" class="btn btn-danger mt-3">Logout</a></p>
    </div>
</body>
</html>
