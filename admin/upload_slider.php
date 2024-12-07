<?php
session_start();
include '../db.php'; // Include your database connection

// Ensure only admin can access this page
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php'); // Redirect non-admin users to login page
    exit;
}

// Retrieve user ID from session
$user_id = $_SESSION['user_id'];

// Get username from the database
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user ? $user['username'] : '';

// Image upload functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $upload_dir = '../uploads/'; // Upload directory
    $file_name = $_FILES['image']['name'];
    $file_tmp = $_FILES['image']['tmp_name'];
    $file_size = $_FILES['image']['size'];
    $file_type = pathinfo($file_name, PATHINFO_EXTENSION);

    // Set file size limit (optional)
    if ($file_size > 5000000) { // 5MB max file size
        echo "File size is too large.";
    } elseif (!in_array($file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
        echo "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
    } else {
        // Upload the image to the server
        $target_file = $upload_dir . basename($file_name);
        if (move_uploaded_file($file_tmp, $target_file)) {
            // Insert image details into the database
            $stmt = $conn->prepare("INSERT INTO images (filename) VALUES (:filename)");
            $stmt->execute(['filename' => $file_name]);
            // Redirect to the same page to avoid re-uploading on refresh
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error uploading image.";
        }
    }
}

// Delete image functionality
if (isset($_GET['delete_image'])) {
    $image_name = $_GET['delete_image'];
    $image_path = '../uploads/' . $image_name;

    // First, delete the image from the server
    if (file_exists($image_path)) {
        unlink($image_path);  // Delete the image file from the server

        // Now, delete the image from the database (mark as deleted)
        $stmt = $conn->prepare("UPDATE images SET status = 'deleted' WHERE filename = :filename");
        $stmt->execute(['filename' => $image_name]);

        echo "Image deleted successfully from server and marked as deleted in the database.";
    } else {
        echo "Image not found.";
    }
}
?>

<!-- Include header -->
<?php include 'header.php'; ?>

<!-- Page Content Starts Here -->
<div class="container">
    <h1 class="page-title">Admin Panel - Image Upload</h1>

    <div class="welcome-message">
        <p>Welcome, <?= htmlspecialchars($username); ?>! You are logged in as an Admin.</p>
    </div>

    <!-- Image Upload Form -->
    <div class="upload-form">
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="image" id="image" required>
            <button type="submit">Upload</button>
        </form>
    </div>

    <!-- List of uploaded images -->
    <h2>Uploaded Images</h2>
    <ul class="image-list">
        <?php
        // Fetch all active images from the database
        $stmt = $conn->query("SELECT * FROM images WHERE status = 'active'");
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($images as $image) {
            echo "<li>";
            echo "<img src='../uploads/{$image['filename']}' alt='{$image['filename']}' />";
            echo " <a href='?delete_image={$image['filename']}' class='delete-link' onclick='return confirm(\"Are you sure you want to delete this image?\")'>Delete</a>";
            echo "</li>";
        }
        ?>
    </ul>
</div>

<!-- Include footer -->
<?php include 'footer.php'; ?>

<!-- Add the CSS for a compact UI -->
<style>
    /* Simple and compact styling for the admin panel */
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f7f7f7;
        color: #333;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    .container {
        width: 90%;
        max-width: 800px;
        margin: 30px auto;
        padding: 20px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        flex: 1;
    }
    h1.page-title {
        font-size: 1.6rem;
        text-align: center;
        color: #555;
    }
    .welcome-message {
        text-align: center;
        font-size: 1rem;
        color: #777;
        margin-bottom: 20px;
    }
    .upload-form {
        display: flex;
        justify-content: center;
        gap: 10px;
    }
    .upload-form input[type="file"] {
        padding: 8px;
        font-size: 1rem;
        border-radius: 4px;
        border: 1px solid #ddd;
    }
    .upload-form button {
        padding: 8px 15px;
        background-color: #28a745;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 4px;
        font-size: 1rem;
    }
    .upload-form button:hover {
        background-color: #218838;
    }
    .image-list {
        list-style: none;
        padding: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
    }
    .image-list li {
        width: 100px;
        height: 100px;
        text-align: center;
    }
    .image-list img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 4px;
    }
    .delete-link {
        display: block;
        margin-top: 5px;
        font-size: 0.9rem;
        color: red;
        text-decoration: none;
    }
    .delete-link:hover {
        text-decoration: underline;
    }

    <!-- Footer Styling -->
<style>
    /* Footer Styling */
    footer {
        background-color: #333;
        color: #fff;
        text-align: center;
        padding: 15px;
        font-size: 0.9rem;
        margin-top: auto; /* Push the footer to the bottom */
    }
    footer a {
        color: #007bff;
        text-decoration: none;
    }
    footer a:hover {
        text-decoration: underline;
    }
</style>
</style>
