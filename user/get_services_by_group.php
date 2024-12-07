<?php
include '../db.php';

// Get service_id from the request
$service_id = $_GET['service_id'] ?? null;

if ($service_id) {
    // Fetch the service details including price and required fields
    $stmt = $conn->prepare("SELECT price, required_fields, name FROM services WHERE id = ?");
    $stmt->execute([$service_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($service) {
        // Split required fields into an array
        $required_fields = $service['required_fields'] ? explode(',', $service['required_fields']) : [];

        // Return the service details with the required fields
        echo json_encode([
            'name' => $service['name'],
            'price' => $service['price'],
            'required_fields' => $required_fields
        ]);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
