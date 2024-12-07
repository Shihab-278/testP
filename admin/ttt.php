<?php
session_start();
include '../db.php';

// Check if the user is logged in and has admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Handle form submission to update Telegram link
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telegram_link = $_POST['telegram_link'];

    // Update the Telegram channel link in the database
    $stmt = $conn->prepare("UPDATE settings SET telegram_channel_link = ? WHERE id = 1");
    $stmt->execute([$telegram_link]);
    echo "Telegram link updated successfully!";
}

?>

<!-- Admin Panel Form -->
<form action="" method="POST">
    <div class="form-group">
        <label for="telegram_link">Telegram Channel Link:</label>
        <input type="url" name="telegram_link" id="telegram_link" class="form-control" value="<?php echo htmlspecialchars($telegram_link ?? ''); ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Update Telegram Link</button>
</form>
