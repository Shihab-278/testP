<?php
session_start();
include '../db.php';

// Set default timezone to Asia/Dhaka (fallback)
date_default_timezone_set('Asia/Dhaka');

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

// Directory to store uploaded images
$image_dir = '../uploads/';



// Ensure the directory exists
if (!file_exists($image_dir)) {
    mkdir($image_dir, 0777, true);
}

// Add a new pricing box
if (isset($_POST['add_box'])) {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $time = $_POST['time'];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = basename($_FILES['image']['name']);
        $image_path = $image_dir . $image_name;

        // Validate and move the uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            $image_url = 'uploads/' . $image_name; // Save relative path for the database
        } else {
            $image_url = '';
            $error_message = "Failed to upload the image.";
        }
    } else {
        $image_url = '';
        $error_message = "No image uploaded or file upload error.";
    }

    // Insert into database
    if (!empty($image_url)) {
        $stmt = $conn->prepare("INSERT INTO pricing_boxes (title, price, time, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $price, $time, $image_url]);
    }
}

// Delete a pricing box
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Fetch the image path to delete it from the server
    $stmt = $conn->prepare("SELECT image FROM pricing_boxes WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetchColumn();

    // Delete the image file
    if ($image && file_exists("../" . $image)) {
        unlink("../" . $image);
    }

    // Delete the record from the database
    $stmt = $conn->prepare("DELETE FROM pricing_boxes WHERE id = ?");
    $stmt->execute([$id]);
}

// Fetch all pricing boxes
$stmt = $conn->query("SELECT * FROM pricing_boxes");
$boxes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php
include 'header.php'; // Admin-side header
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Pricing Boxes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Admin Header -->
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item active">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pricing_boxes.php">Pricing Boxes</a>
                    </li>
                </ul>
            </div>
            <!-- Displaying username in the navbar -->
            <span class="navbar-text ml-auto">
                Welcome, <?php echo htmlspecialchars($username); ?>!
            </span>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        <h1 class="mb-4">Manage Pricing Boxes</h1>

        <!-- Add Pricing Box Form -->
        <form method="POST" enctype="multipart/form-data" class="mb-5">
            <div class="mb-3">
                <label for="title" class="form-label">Tool Name</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price ($)</label>
                <input type="number" class="form-control" id="price" name="price" required>
            </div>
            <div class="mb-3">
                <label for="time" class="form-label">Time</label>
                <input type="text" class="form-control" id="time" name="time" required>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Upload Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
            </div>
            <button type="submit" name="add_box" class="btn btn-primary">Add Pricing Box</button>
        </form>

        <!-- Display Existing Pricing Boxes -->
        <h2>Existing Pricing Boxes</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tool Name</th>
                    <th>Price</th>
                    <th>Time</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($boxes as $box): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($box['title']); ?></td>
                        <td><?php echo htmlspecialchars($box['price']); ?> Taka</td>
                        <td><?php echo htmlspecialchars($box['time']); ?></td>
                        <td>
                            <?php if ($box['image']): ?>
                                <img src="../<?php echo htmlspecialchars($box['image']); ?>" alt="Image" style="width: 50px;">
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="?delete=<?php echo $box['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Include the footer from the admin folder
include '../admin/footer.php';  // Adjust the path if necessary
?>