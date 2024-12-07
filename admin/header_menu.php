<?php
session_start();
include '../db.php';

// Handle logo upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['logo'])) {
    $logo = $_FILES['logo'];
    $logo_name = $logo['name'];
    $logo_tmp_name = $logo['tmp_name'];
    $logo_error = $logo['error'];

    if ($logo_error === 0) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($logo_name);
        if (move_uploaded_file($logo_tmp_name, $target_file)) {
            try {
                $sql = "UPDATE settings SET logo_url = :logo_url WHERE id = 1";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':logo_url', $target_file);
                $stmt->execute();
                echo "<p>Logo uploaded successfully!</p>";
            } catch (PDOException $e) {
                echo "<p>Error: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p>Sorry, there was an error uploading the logo.</p>";
        }
    } else {
        echo "<p>There was an error with the logo file.</p>";
    }
}

// Handle favicon upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['favicon'])) {
    $favicon = $_FILES['favicon'];
    $favicon_name = $favicon['name'];
    $favicon_tmp_name = $favicon['tmp_name'];
    $favicon_error = $favicon['error'];

    if ($favicon_error === 0) {
        $target_file = "../uploads/" . basename($favicon_name);
        if (move_uploaded_file($favicon_tmp_name, $target_file)) {
            try {
                $sql = "UPDATE settings SET favicon_url = :favicon_url WHERE id = 1";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':favicon_url', $target_file);
                $stmt->execute();
                echo "<p>Favicon uploaded successfully!</p>";
            } catch (PDOException $e) {
                echo "<p>Error: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p>Sorry, there was an error uploading the favicon.</p>";
        }
    } else {
        echo "<p>There was an error with the favicon file.</p>";
    }
}

// Handle extra menu item submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['menu_name']) && isset($_POST['menu_link'])) {
    $menu_name = $_POST['menu_name'];
    $menu_link = $_POST['menu_link'];

    try {
        $sql = "INSERT INTO extra_menu (menu_name, menu_link) VALUES (:menu_name, :menu_link)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':menu_name', $menu_name);
        $stmt->bindParam(':menu_link', $menu_link);
        $stmt->execute();
        echo "<p>Menu item added successfully!</p>";
    } catch (PDOException $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
}

// Handle reseller service submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['service_name']) && isset($_POST['service_link'])) {
    $service_name = $_POST['service_name'];
    $service_link = $_POST['service_link'];

    try {
        $sql = "INSERT INTO reseller_services (service_name, service_link) VALUES (:service_name, :service_link)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':service_name', $service_name);
        $stmt->bindParam(':service_link', $service_link);
        $stmt->execute();
        echo "<p>Reseller service added successfully!</p>";
    } catch (PDOException $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
}

// Handle delete actions for menu items and services
if (isset($_GET['delete_menu_item'])) {
    $menu_id = $_GET['delete_menu_item'];
    try {
        $sql = "DELETE FROM extra_menu WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $menu_id);
        $stmt->execute();
        echo "<p>Menu item deleted successfully!</p>";
    } catch (PDOException $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
}

if (isset($_GET['delete_service_item'])) {
    $service_id = $_GET['delete_service_item'];
    try {
        $sql = "DELETE FROM reseller_services WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $service_id);
        $stmt->execute();
        echo "<p>Reseller service deleted successfully!</p>";
    } catch (PDOException $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
}

// Fetch current logo, favicon, email, and phone number
$sql = "SELECT logo_url, favicon_url, contact_email, contact_phone FROM settings WHERE id = 1 LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$current_logo = $row['logo_url'];
$current_favicon = $row['favicon_url'];
$current_email = $row['contact_email'];
$current_phone = $row['contact_phone'];

// Fetch extra menu items
$sql = "SELECT id, menu_name, menu_link FROM extra_menu";
$stmt = $conn->prepare($sql);
$stmt->execute();
$extra_menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch reseller services
$sql = "SELECT id, service_name, service_link FROM reseller_services";
$stmt = $conn->prepare($sql);
$stmt->execute();
$reseller_services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Update Settings</title>
    <link rel="icon" href="<?php echo $current_favicon; ?>" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f8f8f8;
        }

        .form-container, .menu-item-list, .service-item-list {
            width: 50%;
            margin: 20px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-container h2, .menu-item-list h3, .service-item-list h3 {
            text-align: center;
        }

        .form-container input, .form-container button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-container button {
            background-color: #333;
            color: white;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #555;
        }

        .menu-item-list table, .service-item-list table {
            width: 100%;
            border-collapse: collapse;
        }

        .menu-item-list th, .menu-item-list td, .service-item-list th, .service-item-list td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }

        .menu-item-list th, .service-item-list th {
            background-color: #333;
            color: white;
        }

        .menu-item-list a, .service-item-list a {
            text-decoration: none;
            color: #007bff;
        }

        .menu-item-list a:hover, .service-item-list a:hover {
            text-decoration: underline;
        }

        .favicon-preview img {
            max-width: 32px;
            height: auto;
            display: block;
            margin: 20px auto;
        }

        .logo-preview img {
            max-width: 200px;
            display: block;
            margin-top: 20px;
            border-radius: 8px;
        }

        /* Delete Button Styling */
        .delete-btn {
            background-color: #ff4d4d;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 12px;
            display: inline-block;
        }

        .delete-btn:hover {
            background-color: #ff1a1a;
        }

        .delete-btn:focus {
            outline: none;
        }

        /* Ensure buttons are aligned properly in tables */
        td a {
            margin-right: 10px;
        }
    </style>
</head>
<body>

    <!-- Logo Upload Section -->
    <div class="form-container">
        <h2>Upload Logo</h2>
        <form action="header_menu.php" method="post" enctype="multipart/form-data">
            <input type="file" name="logo" accept="image/*" required>
            <button type="submit">Upload Logo</button>
        </form>
        <div class="logo-preview">
            <h3>Current Logo:</h3>
            <img src="<?php echo $current_logo; ?>" alt="Current Logo">
        </div>
    </div>

    <!-- Favicon Upload Section -->
    <div class="form-container">
        <h2>Upload Favicon</h2>
        <form action="header_menu.php" method="post" enctype="multipart/form-data">
            <input type="file" name="favicon" accept="image/x-icon" required>
            <button type="submit">Upload Favicon</button>
        </form>
        <div class="favicon-preview">
            <h3>Current Favicon:</h3>
            <img src="<?php echo $current_favicon; ?>" alt="Current Favicon">
        </div>
    </div>

    <!-- Add Extra Menu Section -->
    <div class="form-container">
        <h2>Add Extra Menu Item</h2>
        <form action="header_menu.php" method="post">
            <label for="menu_name">Menu Name</label>
            <input type="text" name="menu_name" id="menu_name" required>

            <label for="menu_link">Menu Link</label>
            <input type="text" name="menu_link" id="menu_link" required>

            <button type="submit">Add Menu Item</button>
        </form>
    </div>

    <!-- Display Existing Menu Items -->
    <div class="menu-item-list">
        <h3>Existing Menu Items</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Menu Name</th>
                <th>Menu Link</th>
                <th>Action</th>
            </tr>
            <?php foreach ($extra_menu_items as $item): ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo $item['menu_name']; ?></td>
                <td><a href="<?php echo $item['menu_link']; ?>"><?php echo $item['menu_link']; ?></a></td>
                <td><a href="?delete_menu_item=<?php echo $item['id']; ?>" class="delete-btn">Delete</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Add Reseller Service Section -->
    <div class="form-container">
        <h2>Add Reseller Service</h2>
        <form action="header_menu.php" method="post">
            <label for="service_name">Service Name</label>
            <input type="text" name="service_name" id="service_name" required>

            <label for="service_link">Service Link</label>
            <input type="text" name="service_link" id="service_link" required>

            <button type="submit">Add Service</button>
        </form>
    </div>

    <!-- Display Existing Reseller Services -->
    <div class="service-item-list">
        <h3>Existing Reseller Services</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Service Name</th>
                <th>Service Link</th>
                <th>Action</th>
            </tr>
            <?php foreach ($reseller_services as $service): ?>
            <tr>
                <td><?php echo $service['id']; ?></td>
                <td><?php echo $service['service_name']; ?></td>
                <td><a href="<?php echo $service['service_link']; ?>"><?php echo $service['service_link']; ?></a></td>
                <td><a href="?delete_service_item=<?php echo $service['id']; ?>" class="delete-btn">Delete</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

</body>
</html>
