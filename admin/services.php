<?php
session_start();
include '../db.php'; // Ensure the path is correct


// Fetch services from the database for admin management
$stmt = $conn->query("SELECT * FROM services");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle service deletion
if (isset($_GET['delete'])) {
    $service_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
    if ($stmt->execute([$service_id])) {
        $response = "Service deleted successfully!";
    } else {
        $response = "Failed to delete service.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Manage Services</h1>
        
        <?php if (isset($response)) { echo "<div class='alert alert-info'>$response</div>"; } ?>
        
        <a href="add_service.php" class="btn btn-success mb-3">Add New Service</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $service): ?>
                <tr>
                    <td><?php echo $service['id']; ?></td>
                    <td><?php echo htmlspecialchars($service['name']); ?></td>
                    <td><?php echo htmlspecialchars($service['description']); ?></td>
                    <td>$<?php echo number_format($service['price'], 2); ?></td>
                    <td>
                        <a href="edit_service.php?id=<?php echo $service['id']; ?>" class="btn btn-warning">Edit</a>
                        <a href="?delete=<?php echo $service['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this service?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
