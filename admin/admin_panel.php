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

// Handle Add Box form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_box'])) {
    $title = $_POST['title'];
    $text = $_POST['text'];
    $image = '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    // Insert new box into the database
    $sql = "INSERT INTO boxes (title, image, text) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$title, $image, $text]);

    // Redirect to the same page to refresh the dashboard
    header("Location: admin_panel.php");
    exit;
}

// Handle Edit Box form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_box'])) {
    $box_id = $_POST['box_id'];
    $title = $_POST['title'];
    $text = $_POST['text'];
    $image = $_POST['existing_image']; // Keep the existing image by default

    // If a new image is uploaded, replace the old one
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    // Update the box in the database
    $sql = "UPDATE boxes SET title = ?, text = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$title, $text, $image, $box_id]);

    // Redirect to the same page to refresh the dashboard
    header("Location: admin_panel.php");
    exit;
}

// Handle Delete Box
if (isset($_GET['delete_box_id'])) {
    $box_id = $_GET['delete_box_id'];

    // Delete the box from the database
    $sql = "DELETE FROM boxes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$box_id]);

    // Redirect to the same page to refresh the dashboard
    header("Location: admin_panel.php");
    exit;
}

// Fetch all boxes for the dashboard
$sql = "SELECT * FROM boxes ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$boxes = $stmt->fetchAll();
?>
<?php include 'header.php'; // Include your header ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Boxes</title>
    <style>
        /* Add your custom styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f8fa;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .table th {
            background-color: #007bff;
            color: white;
        }
        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            margin-right: 5px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .form-group {
            margin-bottom: 20px;
        }
        input[type="text"], textarea, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Admin Panel - Manage Boxes</h2>

    <!-- Add New Box Form -->
    <h3>Add New Box</h3>
    <form action="admin_panel.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="add_box" value="1">
        <div class="form-group">
            <label for="title">Box Title:</label>
            <input type="text" name="title" required>
        </div>
        <div class="form-group">
            <label for="text">Box Text:</label>
            <textarea name="text" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <label for="image">Box Image:</label>
            <input type="file" name="image" required>
        </div>
        <button type="submit" class="btn">Add Box</button>
    </form>

    <!-- View All Boxes -->
    <h3>All Boxes</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Image</th>
                <th>Text</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($boxes as $box): ?>
                <tr>
                    <td><?php echo htmlspecialchars($box['title']); ?></td>
                    <td><img src="<?php echo htmlspecialchars($box['image']); ?>" alt="Box Image" style="width: 100px;"></td>
                    <td><?php echo htmlspecialchars(substr($box['text'], 0, 50)) . '...'; ?></td>
                    <td>
                        <!-- Edit Button -->
                        <a href="admin_panel.php?edit_box_id=<?php echo $box['id']; ?>" class="btn">Edit</a>
                        <!-- Delete Button -->
                        <a href="admin_panel.php?delete_box_id=<?php echo $box['id']; ?>" class="btn" onclick="return confirm('Are you sure you want to delete this box?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Edit Box Form (if the user clicked the "Edit" button) -->
    <?php if (isset($_GET['edit_box_id'])): ?>
        <?php
        $box_id = $_GET['edit_box_id'];
        $sql = "SELECT * FROM boxes WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$box_id]);
        $box = $stmt->fetch();
        ?>
        <h3>Edit Box</h3>
        <form action="admin_panel.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="edit_box" value="1">
            <input type="hidden" name="box_id" value="<?php echo $box['id']; ?>">
            <input type="hidden" name="existing_image" value="<?php echo $box['image']; ?>">

            <div class="form-group">
                <label for="title">Box Title:</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($box['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="text">Box Text:</label>
                <textarea name="text" rows="4" required><?php echo htmlspecialchars($box['text']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="image">Box Image (Optional):</label>
                <input type="file" name="image">
            </div>
            <button type="submit" class="btn">Update Box</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>

<?php
// Close database connection
$conn = null;
?>
