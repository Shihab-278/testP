<?php
// Include database connection
include '../db.php';

// Check if service_id is passed
if (isset($_GET['service_id'])) {
    $service_id = $_GET['service_id'];

    // Prepare and execute query to fetch the required fields for the selected service
    $stmt = $conn->prepare("SELECT required_fields FROM services WHERE id = ?");
    $stmt->execute([$service_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($service) {
        $required_fields = explode(',', $service['required_fields']); // Convert the CSV to array
        echo json_encode([
            'success' => true,
            'required_fields' => $required_fields
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Service not found'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Service ID not provided'
    ]);
}
?>
