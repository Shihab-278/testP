<?php
session_start();
include '../db.php'; // Ensure the path is correct

// Ensure service ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Invalid service ID.');
}

$service_id = $_GET['id']; // Get service ID from URL parameter

// Fetch service details from the database using the service ID
$stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$service_id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the service exists in the database
if (!$service) {
    die('Service not found.');
}

// Handle form submission to update required fields
if (isset($_POST['update_service'])) {
    $required_fields = isset($_POST['required_fields']) ? implode(',', $_POST['required_fields']) : ''; // Handle case when no fields are selected

    // Update the service's required fields in the database
    $stmt = $conn->prepare("UPDATE services SET required_fields = ? WHERE id = ?");
    if ($stmt->execute([$required_fields, $service_id])) {
        $response = "Service requirements updated successfully!";
    } else {
        $response = "Failed to update service requirements.";
    }
}

// Prepare the list of available fields
$available_fields = ['imei', 'serial', 'username', 'gmail']; // Add more fields if needed

// Check if 'required_fields' is NULL and handle it
$required_fields = !empty($service['required_fields']) ? explode(',', $service['required_fields']) : []; // Default to empty array if NULL
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Service Requirements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Edit Service Requirements</h1>
        
        <?php if (isset($response)) { echo "<div class='alert alert-info'>$response</div>"; } ?>

        <form method="POST">
            <h5>Select required fields for this service:</h5>
            <div class="form-group">
                <?php foreach ($available_fields as $field): ?>
                    <div class="form-check">
                        <input type="checkbox" name="required_fields[]" value="<?php echo $field; ?>" 
                            class="form-check-input" 
                            <?php echo in_array($field, $required_fields) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="<?php echo $field; ?>"><?php echo ucfirst($field); ?></label>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" name="update_service" class="btn btn-primary mt-3">Update Service</button>
        </form>
        
        <p><a href="manage_services.php" class="btn btn-secondary mt-3">Back to Manage Services</a></p>
    </div>
</body>
</html>
