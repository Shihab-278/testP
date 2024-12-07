<?php
session_start();
include '../db.php';



// Get the service ID from the URL
if (isset($_GET['id'])) {
    $service_id = $_GET['id'];

    // Fetch the service details
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$service_id]);
    $service = $stmt->fetch();

    if (!$service) {
        header("Location: services.php");
        exit();
    }
}

// Handle form submission to update the service
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);

    if (empty($name) || empty($description) || empty($price)) {
        $response = "All fields are required!";
    } else {
        $stmt = $conn->prepare("UPDATE services SET name = ?, description = ?, price = ? WHERE id = ?");
        if ($stmt->execute([$name, $description, $price, $service_id])) {
            $response = "Service updated successfully!";
        } else {
            $response = "Failed to update service.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Edit Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Edit Service</h1>
        
        <?php if (isset($response)) { echo "<div class='alert alert-info'>$response</div>"; } ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Service Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($service['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" required><?php echo htmlspecialchars($service['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" name="price" id="price" class="form-control" value="<?php echo htmlspecialchars($service['price']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Service</button>
        </form>
    </div>
</body>
</html>
