<?php
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and sanitize it
    $name = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['name']) : '';
    $url = isset($_POST['url']) ? mysqli_real_escape_string($conn, $_POST['url']) : '';
    $parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== 'NULL' ? intval($_POST['parent_id']) : NULL;
    $position = isset($_POST['position']) ? intval($_POST['position']) : 0;

    // Debugging output to see form values
    var_dump($name, $url, $parent_id, $position);

    // Validate input
    if (empty($name) || empty($url) || $position <= 0) {
        echo "All fields are required and position must be greater than zero.";
    } else {
        // Insert into database (existing code)
        $sql = "INSERT INTO navbar_menu (name, url, parent_id, position) 
                VALUES ('$name', '$url', " . ($parent_id ? $parent_id : "NULL") . ", $position)";
        
        if ($conn->query($sql) === TRUE) {
            echo "New menu item added successfully!";
            header("Location: admin_panel.php"); // Redirect after successful insert
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

?>

<!-- Back to the form if no submission happened -->
<form action="admin_process_menu.php" method="POST">
    <label for="name">Menu Name:</label>
    <input type="text" id="name" name="name" required>
    
    <label for="url">URL:</label>
    <input type="text" id="url" name="url" required>
    
    <label for="parent_id">Parent Menu:</label>
    <select id="parent_id" name="parent_id">
        <option value="NULL">None (Top Level)</option>
        <?php
        $result = $conn->query("SELECT id, name FROM navbar_menu WHERE parent_id IS NULL");
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
        }
        ?>
    </select>

    <label for="position">Position:</label>
    <input type="number" id="position" name="position" required>

    <button type="submit">Save</button>
</form>
