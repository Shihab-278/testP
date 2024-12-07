<?php
session_start();
include '../db.php'; // Ensure db connection is established

// Ensure only admin can access this page
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Retrieve user ID from session
$user_id = $_SESSION['user_id'];

// Get username from the database
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user ? $user['username'] : '';

$response = "";

// Handle form submission to add a new service group
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_group'])) {
    $group_name = trim($_POST['group_name']);
    $group_description = trim($_POST['group_description']);

    if (!empty($group_name)) {
        try {
            $stmt = $conn->prepare("INSERT INTO service_groups (name, description) VALUES (?, ?)");
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
    // Collect form data
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $group_id = $_POST['group_id'];  // Get the selected group ID

    // Handle Required Fields
    $required_fields = isset($_POST['required_fields']) ? implode(',', $_POST['required_fields']) : '';

    // Handle Delivery Time
    $delivery_time_type = $_POST['delivery_time_type']; // Instant, Minute, Hour, Week, Month
    $delivery_time_value = $_POST['delivery_time_value']; // The number input for the time

    // Validate the delivery time value
    if (!is_numeric($delivery_time_value) || $delivery_time_value <= 0) {
        $response = "Invalid Delivery Time value!";
    }

    // Handle Image Upload
    $image_url = NULL;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];
        $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array(strtolower($image_ext), $allowed_ext) && $image_size < 5000000) {
            $image_url = 'uploads/' . uniqid() . '.' . $image_ext;
            move_uploaded_file($image_tmp, $image_url);
        } else {
            $response = "Invalid image or file too large.";
        }
    }

    if (empty($response)) {
        try {
            // Insert the new service into the services table with delivery time
            $stmt = $conn->prepare("INSERT INTO services (name, description, price, image_url, required_fields, group_id, delivery_time, delivery_time_type) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $image_url, $required_fields, $group_id, $delivery_time_value, $delivery_time_type]);

            // Create an announcement for the new service
            $announcement_title = "New Service Added: $name";
            $announcement_description = "A new service has been added: $name. Price: $price. Description: $description.";
            $announcement_time = date('Y-m-d H:i:s'); // Current timestamp

            // Insert the announcement into the announcements table
            $announcement_stmt = $conn->prepare("INSERT INTO announcements (title, description, created_at) 
                                                 VALUES (?, ?, ?)");
            $announcement_stmt->execute([$announcement_title, $announcement_description, $announcement_time]);

            $response = "Service added and announcement created successfully!";
        } catch (PDOException $e) {
            $response = "Error: " . $e->getMessage();
        }
    }
}

// Fetch existing service groups
$stmt = $conn->query("SELECT * FROM service_groups");
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; // Admin header ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Add New Service</h1>

        <?php if ($response): ?>
        <div class="alert alert-info"><?php echo $response; ?></div>
        <?php endif; ?>

        <!-- Create Service Group Section -->
        <h3>Create New Service Group</h3>
        <form method="POST">
            <div class="mb-3">
                <label for="group_name" class="form-label">Group Name</label>
                <input type="text" name="group_name" class="form-control" id="group_name" required>
            </div>
            <div class="mb-3">
                <label for="group_description" class="form-label">Group Description</label>
                <textarea name="group_description" class="form-control" id="group_description" rows="4"></textarea>
            </div>
            <button type="submit" name="add_group" class="btn btn-success">Create Group</button>
        </form>

        <hr>

        <!-- Add New Service Form -->
        <h3>Add New Service</h3>
        <form method="POST" enctype="multipart/form-data">
            <!-- Service Name -->
            <div class="mb-3">
                <label for="name" class="form-label">Service Name</label>
                <input type="text" name="name" class="form-control" id="name" required>
            </div>

            <!-- Service Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" class="form-control" id="description" rows="4" required></textarea>
            </div>

            <!-- Service Price -->
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" name="price" class="form-control" id="price" required>
            </div>

            <!-- Service Group -->
            <div class="mb-3">
                <label for="group_id" class="form-label">Select Service Group</label>
                <select name="group_id" class="form-control" id="group_id" required>
                    <option value="">Select Group</option>
                    <?php foreach ($groups as $group): ?>
                        <option value="<?php echo $group['id']; ?>"><?php echo htmlspecialchars($group['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Select Required Information -->
            <div class="mb-3">
                <label for="requirements" class="form-label">Select Required Information</label><br>
                <input type="checkbox" name="required_fields[]" value="IMEI"> IMEI<br>
                <input type="checkbox" name="required_fields[]" value="Serial"> Serial Number<br>
                <input type="checkbox" name="required_fields[]" value="Username"> Username<br>
                <input type="checkbox" name="required_fields[]" value="Gmail"> Gmail<br>
            </div>

            <!-- Delivery Time -->
            <div class="mb-3">
                <label for="delivery_time" class="form-label">Delivery Time</label><br>
                <select name="delivery_time_type" class="form-control" required>
                    <option value="instant">Instant</option>
                    <option value="minute">Minute</option>
                    <option value="hour">Hour</option>
                    <option value="week">Week</option>
                    <option value="month">Month</option>
                </select>
                <input type="number" name="delivery_time_value" class="form-control mt-2" placeholder="Number" required>
            </div>

            <!-- Image Upload -->
            <div class="mb-3">
                <label for="image" class="form-label">Upload Image (Optional)</label>
                <input type="file" name="image" class="form-control" id="image">
                <small class="form-text text-muted">Supported formats: JPG, PNG, GIF. Max size: 5MB</small>
            </div>

            <button type="submit" name="add_service" class="btn btn-primary">Add Service</button>
        </form>

        <p><a href="manage_services.php" class="btn btn-secondary mt-3">Back to Manage Services</a></p>
    </div>
</body>
</html>

<?php include 'footer.php'; // Admin footer ?>
