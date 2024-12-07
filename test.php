<?php
include 'db.php'; // Include the database connection

// Get the service ID from the URL
$service_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($service_id) {
    // Fetch the service details from the database
    $stmt = $conn->prepare("SELECT * FROM server_services WHERE id = ?");
    $stmt->execute([$service_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$service) {
        echo "Service not found.";
        exit;
    }
} else {
    echo "Invalid service ID.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($service['name']); ?> Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center"><?php echo htmlspecialchars($service['name']); ?></h1>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($service['name']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($service['description']); ?></p>
                <p class="card-text"><strong>Price: </strong><?php echo number_format($service['price'], 2); ?> USD</p>
                
                <?php 
                    // Display requirements (if any)
                    $requirements = json_decode($service['requirements'], true);
                    if ($requirements) {
                        echo '<strong>Requirements:</strong><ul>';
                        foreach ($requirements as $requirement) {
                            echo "<li>" . htmlspecialchars($requirement) . "</li>";
                        }
                        echo '</ul>';
                    }
                ?>

                <a href="index.php" class="btn btn-secondary">Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>
