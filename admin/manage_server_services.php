<?php
session_start();
include '../db.php';

// Ensure DB connection is established

// Ensure only admin can access this page
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$response = "";

// Fetch existing services from the database
$stmt = $conn->query("SELECT * FROM server_services");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle service deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_service'])) {
    $service_id = $_POST['service_id'];

    try {
        // Delete service from server_services table
        $stmt = $conn->prepare("DELETE FROM server_services WHERE id = ?");
        $stmt->execute([$service_id]);

        // Log the deletion in server_service_updates table
        $update_type = "service deleted";
        $update_description = "Deleted service ID: $service_id";
        $update_stmt = $conn->prepare("INSERT INTO server_service_updates (service_id, update_type, update_description) VALUES (?, ?, ?)");
        $update_stmt->execute([$service_id, $update_type, $update_description]);

        $response = "Service deleted successfully!";
    } catch (PDOException $e) {
        $response = "Error: " . $e->getMessage();
    }
}

// Handle service update (edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_service'])) {
    $service_id = $_POST['service_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $group_id = trim($_POST['group_id']);
    $requirements = isset($_POST['requirements']) ? $_POST['requirements'] : [];
    $requirements_json = !empty($requirements) ? json_encode($requirements) : NULL;

    if (!empty($name) && !empty($description) && !empty($price) && !empty($group_id)) {
        try {
            // Update the service in server_services table
            $stmt = $conn->prepare("UPDATE server_services SET name = ?, description = ?, price = ?, requirements = ?, group_id = ? WHERE id = ?");
            $stmt->execute([$name, $description, $price, $requirements_json, $group_id, $service_id]);

            // Log the update in server_service_updates table
            $update_type = "service updated";
            $update_description = "Updated service ID: $service_id - $name";
            $update_stmt = $conn->prepare("INSERT INTO server_service_updates (service_id, update_type, update_description) VALUES (?, ?, ?)");
            $update_stmt->execute([$service_id, $update_type, $update_description]);

            $response = "Service updated successfully!";
        } catch (PDOException $e) {
            $response = "Error: " . $e->getMessage();
        }
    } else {
        $response = "All fields are required!";
    }
}
?>

<?php include 'header.php'; // Admin header ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Server Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Manage Server Services</h1>

        <?php if ($response): ?>
            <div class="alert alert-info"><?php echo $response; ?></div>
        <?php endif; ?>

        <!-- Existing Services Table -->
        <h2>Existing Services</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Service Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Requirements</th>
                    <th>Service Group</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($services) > 0): ?>
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($service['name']); ?></td>
                            <td><?php echo htmlspecialchars($service['description']); ?></td>
                            <td><?php echo number_format($service['price'], 2); ?></td>
                            <td>
                                <?php
                                    $requirements = json_decode($service['requirements'], true);
                                    echo $requirements ? implode(', ', $requirements) : 'None';
                                ?>
                            </td>
                            <td>
                                <?php
                                    // Fetch group name based on group_id
                                    $group_stmt = $conn->prepare("SELECT name FROM server_service_group WHERE id = ?");
                                    $group_stmt->execute([$service['group_id']]);
                                    $group = $group_stmt->fetch(PDO::FETCH_ASSOC);
                                    echo $group ? htmlspecialchars($group['name']) : 'None';
                                ?>
                            </td>
                            <td>
                                <!-- Edit Service Button -->
                                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $service['id']; ?>">Edit</button>
                                
                                <!-- Delete Service Button -->
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                    <button type="submit" name="delete_service" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this service?');">Delete</button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Service Modal -->
                        <div class="modal fade" id="editModal<?php echo $service['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Edit Service</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST">
                                            <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">

                                            <div class="mb-3">
                                                <label for="name" class="form-label">Service Name</label>
                                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($service['name']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($service['description']); ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="price" class="form-label">Price</label>
                                                <input type="number" name="price" class="form-control" value="<?php echo $service['price']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="group_id" class="form-label">Service Group</label>
                                                <select name="group_id" class="form-select" required>
                                                    <option value="">Select Service Group</option>
                                                    <?php
                                                    $group_stmt = $conn->query("SELECT * FROM server_service_group");
                                                    $groups = $group_stmt->fetchAll(PDO::FETCH_ASSOC);
                                                    foreach ($groups as $group) {
                                                        echo '<option value="' . $group['id'] . '" ' . ($group['id'] == $service['group_id'] ? 'selected' : '') . '>' . htmlspecialchars($group['name']) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <h4>Service Requirements</h4>
                                            <div class="mb-3">
                                                <label for="requirements" class="form-label">Select Required Information</label><br>
                                                <input type="checkbox" name="requirements[]" value="IMEI" <?php echo in_array("IMEI", $requirements) ? 'checked' : ''; ?>> IMEI<br>
                                                <input type="checkbox" name="requirements[]" value="Serial" <?php echo in_array("Serial", $requirements) ? 'checked' : ''; ?>> Serial Number<br>
                                                <input type="checkbox" name="requirements[]" value="Username" <?php echo in_array("Username", $requirements) ? 'checked' : ''; ?>> Username<br>
                                                <input type="checkbox" name="requirements[]" value="Gmail" <?php echo in_array("Gmail", $requirements) ? 'checked' : ''; ?>> Gmail<br>
                                                <input type="checkbox" name="requirements[]" value="ImgLink" <?php echo in_array("ImgLink", $requirements) ? 'checked' : ''; ?>> Img Link<br>
                                            </div>

                                            <button type="submit" name="edit_service" class="btn btn-primary">Update Service</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No services available</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'footer.php'; // Admin footer ?>
