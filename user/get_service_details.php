<?php
include '../db.php';

$service_id = $_GET['id'] ?? null;

if ($service_id) {
    $stmt = $conn->prepare("SELECT required_fields FROM services WHERE id = ?");
    $stmt->execute([$service_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($service) {
        echo json_encode($service);
    } else {
        echo json_encode([]);
    }
}
?>
