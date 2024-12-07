<?php
session_start();
include '../db.php';

// Ensure only admin can access this page
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Check if tool ID is provided in the URL
if (isset($_GET['id'])) {
    $tool_id = $_GET['id'];

    // Fetch tool details from the database
    $stmt = $conn->prepare("SELECT * FROM tools WHERE id = ?");
    $stmt->execute([$tool_id]);
    $tool = $stmt->fetch();

    if (!$tool) {
        echo "Tool not found!";
        exit;
    }

    // Fetch all categories for the dropdown
    $category_stmt = $conn->query("SELECT name FROM categories");
    $categories = $category_stmt->fetchAll();

    // Handle form submission to update the tool
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tool_name = htmlspecialchars(trim($_POST['tool_name']));
        $tool_username = htmlspecialchars(trim($_POST['tool_username']));
        $tool_password = htmlspecialchars(trim($_POST['tool_password']));
        $tool_cost = htmlspecialchars(trim($_POST['tool_cost']));
        $tool_category = htmlspecialchars(trim($_POST['tool_category']));

        // Ensure all fields are filled
        if (empty($tool_name) || empty($tool_username) || empty($tool_password) || empty($tool_cost) || empty($tool_category)) {
            $message = "<div class='alert alert-danger'>All fields are required!</div>";
        } else {
            // Update the tool details in the database
            $stmt = $conn->prepare("UPDATE tools SET tool_name = ?, tool_username = ?, tool_password = ?, tool_cost = ?, category = ? WHERE id = ?");
            $stmt->execute([$tool_name, $tool_username, $tool_password, $tool_cost, $tool_category, $tool_id]);

            $message = "<div class='alert alert-success'>Tool updated successfully!</div>";
        }
    }
} else {
    echo "No tool ID provided!";
    exit;
}

include 'header.php'; // Admin header
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Edit Tool</h1>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Edit Tool</h3>
                        </div>
                        <!-- Form to edit tool -->
                        <form method="POST">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="toolName">Tool Name</label>
                                    <input type="text" name="tool_name" class="form-control" id="toolName" value="<?php echo htmlspecialchars($tool['tool_name']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="toolUsername">Tool Username</label>
                                    <input type="text" name="tool_username" class="form-control" id="toolUsername" value="<?php echo htmlspecialchars($tool['tool_username']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="toolPassword">Tool Password</label>
                                    <input type="password" name="tool_password" class="form-control" id="toolPassword" value="<?php echo htmlspecialchars($tool['tool_password']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="toolCost">Tool Cost</label>
                                    <input type="number" name="tool_cost" class="form-control" id="toolCost" value="<?php echo htmlspecialchars($tool['tool_cost']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="toolCategory">Tool Category</label>
                                    <select name="tool_category" class="form-control" id="toolCategory" required>
                                        <option value="" disabled>Select a category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo htmlspecialchars($category['name']); ?>" <?php echo ($tool['category'] == $category['name']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update Tool</button>
                            </div>
                        </form>

                        <!-- Display message after update -->
                        <?php if (isset($message)): ?>
                            <div class="mt-3"><?php echo $message; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'footer.php'; // Admin footer ?>