<?php
session_start();
include '../db.php'; // Ensure DB connection is established

// Ensure only admin can access this page
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$response = "";

// Fetch existing service groups from the database
$group_stmt = $conn->query("SELECT * FROM server_service_group");
$groups = $group_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission to add a new service group
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_group'])) {
    $group_name = trim($_POST['group_name']);
    $group_description = trim($_POST['group_description']);

    if (!empty($group_name)) {
        try {
            // Insert the new service group into server_service_group table
            $stmt = $conn->prepare("INSERT INTO server_service_group (name, description) VALUES (?, ?)");
            $stmt->execute([$group_name, $group_description]);

            $response = "Service group added successfully!";
        } catch (PDOException $e) {
            $response = "Error: " . $e->getMessage();
        }
    } else {
        $response = "Group name is required!";
    }
}

// Handle form submission to add a new service
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $group_id = trim($_POST['group_id']); // Get the selected service group

    // Collect requirements from checkboxes
    $requirements = isset($_POST['requirements']) ? $_POST['requirements'] : [];
    $requirements_json = !empty($requirements) ? json_encode($requirements) : NULL;

    if (!empty($name) && !empty($description) && !empty($price) && !empty($group_id)) {
        try {
            // Insert the new service into server_services table
            $stmt = $conn->prepare("INSERT INTO server_services (name, description, price, requirements, group_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $requirements_json, $group_id]);

            // Get the newly inserted service's ID
            $service_id = $conn->lastInsertId();

            // Create an announcement for the new service
            $announcement_title = "New Service Added: $name";
            $announcement_description = "A new service has been added: $name. Price: $price. Description: $description.";
            $announcement_time = date('Y-m-d H:i:s'); // Current timestamp

            // Insert the announcement into announcements table
            $announcement_stmt = $conn->prepare("INSERT INTO announcements (title, description, created_at) VALUES (?, ?, ?)");
            $announcement_stmt->execute([$announcement_title, $announcement_description, $announcement_time]);

            $response = "Service added and announcement created successfully!";
        } catch (PDOException $e) {
            $response = "Error: " . $e->getMessage();
        }
    } else {
        $response = "All fields are required!";
    }
}

// Fetch existing services from the database
$stmt = $conn->query("SELECT * FROM server_services");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include 'header.php'; // Admin header ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Server Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Add New Server Service</h1>

        <?php if ($response): ?>
        <div class="alert alert-info"><?php echo $response; ?></div>
        <?php endif; ?>

        <!-- Add New Server Service Form -->
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Service Name</label>
                <input type="text" name="name" class="form-control" id="name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" class="form-control" id="description" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" name="price" class="form-control" id="price" required>
            </div>

            <!-- Select Service Group -->
            <div class="mb-3">
                <label for="group_id" class="form-label">Service Group</label>
                <select name="group_id" class="form-select" id="group_id" required>
                    <option value="">Select Service Group</option>
                    <?php foreach ($groups as $group): ?>
                        <option value="<?php echo $group['id']; ?>"><?php echo htmlspecialchars($group['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Select Requirements -->
            <h4>Service Requirements</h4>
            <div class="mb-3">
                <label for="requirements" class="form-label">Select Required Information</label><br>
                <input type="checkbox" name="requirements[]" value="IMEI"> IMEI<br>
                <input type="checkbox" name="requirements[]" value="Serial"> Serial Number<br>
                <input type="checkbox" name="requirements[]" value="Username"> Username<br>
                <input type="checkbox" name="requirements[]" value="Gmail"> Gmail<br>
                <input type="checkbox" name="requirements[]" value="ImgLink"> Img Link<br>
            </div>

            <button type="submit" name="add_service" class="btn btn-primary">Add Service</button>
        </form>

        <hr>

        <!-- Add New Service Group Form -->
        <h2 class="text-center mt-5">Add New Service Group</h2>

        <form method="POST">
            <div class="mb-3">
                <label for="group_name" class="form-label">Group Name</label>
                <input type="text" name="group_name" class="form-control" id="group_name" required>
            </div>
            <div class="mb-3">
                <label for="group_description" class="form-label">Group Description (Optional)</label>
                <textarea name="group_description" class="form-control" id="group_description" rows="4"></textarea>
            </div>

            <button type="submit" name="add_group" class="btn btn-success">Add Service Group</button>
        </form>

        <p><a href="manage_services.php" class="btn btn-secondary mt-3">Back to Manage Services</a></p>

        <h2 class="mt-5">Existing Services</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Service Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Requirements</th>
                    <th>Service Group</th>
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
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No services available</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</body>
</html>

<?php include 'footer.php'; // Admin footer ?>
