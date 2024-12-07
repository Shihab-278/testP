<?php
session_start();
include '../db.php';

// Check if the user is logged in as an admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Fetch available services for the admin to select from
$stmt = $conn->query("SELECT id, name, required_fields FROM services");
$services = $stmt->fetchAll();

// Handle updating required fields
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = $_POST['service_id'] ?? null;
    $required_fields = $_POST['required_fields'] ?? '';

    if ($service_id) {
        // Update the required_fields for the selected service
        $stmt = $conn->prepare("UPDATE services SET required_fields = ? WHERE id = ?");
        if ($stmt->execute([$required_fields, $service_id])) {
            $response = "Service updated successfully!";
        } else {
            $response = "Error updating service.";
        }
    } else {
        $response = "No service selected.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Service Requirements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Update Service Requirements</h1>

        <?php if (isset($response)) { echo "<div class='alert alert-info'>$response</div>"; } ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="service_id" class="form-label">Select Service</label>
                <select name="service_id" class="form-control" id="service_id" required>
                    <option value="">Select Service</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?php echo $service['id']; ?>" data-required-fields="<?php echo $service['required_fields']; ?>">
                            <?php echo htmlspecialchars($service['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="required_fields" class="form-label">Required Fields (Comma Separated)</label>
                <textarea name="required_fields" class="form-control" id="required_fields" rows="3" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update Service</button>
        </form>

        <p><a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a></p>
    </div>

    <script>
        // When a service is selected, populate the required fields
        document.getElementById('service_id').addEventListener('change', function() {
            const serviceId = this.value;
            const selectedOption = document.querySelector(`#service_id option[value="${serviceId}"]`);
            const requiredFields = selectedOption ? selectedOption.getAttribute('data-required-fields') : '';
            document.getElementById('required_fields').value = requiredFields;
        });
    </script>
</body>
</html>
