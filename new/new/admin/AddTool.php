<?php
session_start();
include '../db.php';

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

// Handle form submission to add a new tool
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tool_name = $_POST['tool_name'];
    $tool_username = $_POST['tool_username'];
    $tool_password = $_POST['tool_password'];
    $tool_cost = $_POST['tool_cost'];
    $tool_category = $_POST['tool_category'];

    // Insert the new tool into the tools table
    $stmt = $conn->prepare("INSERT INTO tools (tool_name, tool_username, tool_password, tool_cost, category) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$tool_name, $tool_username, $tool_password, $tool_cost, $tool_category]);

    $message = 'Tool added successfully!';
}

// Fetch all tools from the database
$stmt = $conn->query("SELECT * FROM tools");
$tools = $stmt->fetchAll();

// Fetch distinct categories from the categories table
$category_stmt = $conn->query("SELECT name FROM categories");
if ($category_stmt === false) {
    die('Error fetching categories: ' . print_r($conn->errorInfo(), true));
}
$categories = $category_stmt->fetchAll();

include 'header.php'; // Admin header
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h4><i class="fa fa-caret-right fw-r5"></i> Add Tools</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Add Tool</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Add New Tool</h3>
                        </div>
                        <!-- Form to add tool -->
                        <form method="POST">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="toolName">Tool Name</label>
                                    <input type="text" name="tool_name" class="form-control" id="toolName" required>
                                </div>
                                <div class="form-group">
                                    <label for="toolUsername">Tool Username</label>
                                    <input type="text" name="tool_username" class="form-control" id="toolUsername" required>
                                </div>
                                <div class="form-group">
                                    <label for="toolPassword">Tool Password</label>
                                    <input type="password" name="tool_password" class="form-control" id="toolPassword" required>
                                </div>
                                <div class="form-group">
                                    <label for="toolCost">Tool Cost</label>
                                    <input type="number" name="tool_cost" class="form-control" id="toolCost" required>
                                </div>
                                <div class="form-group">
                                    <label for="toolCategory">Tool Category</label>
                                    <select name="tool_category" class="form-control" id="toolCategory" required>
                                        <option value="" disabled selected>Select a category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo htmlspecialchars($category['name']); ?>">
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Add Tool</button>
                            </div>
                        </form>

                        <?php if (isset($message)): ?>
                            <div class="alert alert-success mt-3">
                                <?php echo htmlspecialchars($message); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Display the list of added tools -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">List of Added Tools</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tool Name</th>
                                        <th>Tool Username</th>
                                        <th>Tool Password</th>
                                        <th>Tool Cost</th>
                                        <th>Category</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tools as $tool): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($tool['tool_name']); ?></td>
                                            <td><?php echo htmlspecialchars($tool['tool_username']); ?></td>
                                            <td><?php echo htmlspecialchars($tool['tool_password']); ?></td>
                                            <td><?php echo htmlspecialchars($tool['tool_cost']); ?> Taka</td>
                                            <td><?php echo htmlspecialchars($tool['category']); ?></td>
                                            <td>
                                                <a href="edit_tool.php?id=<?php echo $tool['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                                <a href="delete_tool.php?id=<?php echo $tool['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this tool?');">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php include 'footer.php'; // Admin footer ?>
