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

// Handle category addition and editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_category'])) {
    $category_name = $_POST['category_name'];
    $category_id = $_POST['category_id'];

    if (!empty($category_id)) {
        // Update existing category
        $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->execute([$category_name, $category_id]);
        $message = 'Category updated successfully!';
    } else {
        // Insert the new category into the categories table
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$category_name]);
        $message = 'Category added successfully!';
    }
}

// Handle category deletion
if (isset($_GET['delete'])) {
    $category_id = $_GET['delete'];

    try {
        // Delete the category from the categories table
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$category_id]);

        // If delete successful, set success message
        if ($stmt->rowCount() > 0) {
            $message = 'Category deleted successfully!';
        } else {
            $message = 'Failed to delete the category. Please try again.';
        }
    } catch (Exception $e) {
        // Catch potential exceptions (like foreign key constraints)
        $message = 'Error: Could not delete category. ' . $e->getMessage();
    }
}

// Fetch all categories from the database
$stmt = $conn->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();

include 'header.php'; // Admin header
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4><i class="fa fa-caret-right fw-r5"></i> Manage Categories</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Manage Categories</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Add/Edit Category Form -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title" id="form-title">Add New Category</h3>
                        </div>
                        <form method="POST" id="categoryForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="categoryName">Category Name</label>
                                    <input type="text" name="category_name" class="form-control" id="categoryName" required>
                                    <input type="hidden" name="category_id" id="categoryId">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" name="save_category" class="btn btn-primary" id="submitButton">Save Category</button>
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

            <!-- Display the list of categories -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">List of Categories</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Category Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-info editCategory" data-id="<?php echo $category['id']; ?>" data-name="<?php echo htmlspecialchars($category['name']); ?>">Edit</button>
                                                <a href="categories.php?delete=<?php echo $category['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
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

<script>
    // Handle Edit button click and populate the form
    document.querySelectorAll('.editCategory').forEach(function(button) {
        button.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-id');
            const categoryName = this.getAttribute('data-name');

            // Populate the form with the category data
            document.getElementById('categoryName').value = categoryName;
            document.getElementById('categoryId').value = categoryId;

            // Change form title and button text
            document.getElementById('form-title').innerText = 'Edit Category';
            document.getElementById('submitButton').innerText = 'Update Category';
        });
    });
</script>

<?php include 'footer.php'; // Admin footer 
?>