<?php
include '../db.php';  // Include the database connection

// Fetch header settings
$query = "SELECT * FROM header_settings WHERE id = 1";
$result = mysqli_query($conn, $query);  // Use $conn (as procedural resource)
if (!$result) {
    die('Query failed: ' . mysqli_error($conn));  // Handle query failure
}
$settings = mysqli_fetch_assoc($result);

// Fetch menu items
$query = "SELECT * FROM header_menu";
$menu_result = mysqli_query($conn, $query);  // Same for other queries
if (!$menu_result) {
    die('Query failed: ' . mysqli_error($conn));  // Handle query failure
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $currency = $_POST['currency'];

    // Update header settings
    $update_query = "UPDATE header_settings SET email = '$email', phone = '$phone', currency = '$currency' WHERE id = 1";
    if (!mysqli_query($conn, $update_query)) {
        die('Error updating settings: ' . mysqli_error($conn));
    }

    // Update menu items visibility
    foreach ($_POST['menu_visibility'] as $menu_id => $visibility) {
        $visibility_query = "UPDATE header_menu SET is_visible = $visibility WHERE id = $menu_id";
        if (!mysqli_query($conn, $visibility_query)) {
            die('Error updating menu item visibility: ' . mysqli_error($conn));
        }
    }

    header('Location: admin.php');
    exit;
}
?>

<!-- HTML to manage header settings -->
<form method="POST">
    <h3>Header Settings</h3>
    <label>Email:</label>
    <input type="text" name="email" value="<?php echo $settings['email']; ?>" required>
    
    <label>Phone:</label>
    <input type="text" name="phone" value="<?php echo $settings['phone']; ?>" required>
    
    <label>Currency:</label>
    <input type="text" name="currency" value="<?php echo $settings['currency']; ?>" required>
    
    <h3>Menu Visibility</h3>
    <?php while ($menu = mysqli_fetch_assoc($menu_result)): ?>
        <label><?php echo $menu['name']; ?>:</label>
        <select name="menu_visibility[<?php echo $menu['id']; ?>]">
            <option value="1" <?php echo $menu['is_visible'] ? 'selected' : ''; ?>>Visible</option>
            <option value="0" <?php echo !$menu['is_visible'] ? 'selected' : ''; ?>>Hidden</option>
        </select><br>
    <?php endwhile; ?>
    
    <button type="submit">Save Changes</button>
</form>
