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

// Get username and balance from the database
$stmt = $conn->prepare("SELECT username, balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user ? $user['username'] : '';
$balance = $user ? number_format($user['balance'], 2) : '0.00'; // Format balance

// Handle search query
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

// Fetch all users
try {
    if ($search_query) {
        $users_stmt = $conn->prepare("SELECT id, username, balance, banned, `group` FROM users WHERE role='user' AND username LIKE ?");
        $users_stmt->execute(["%$search_query%"]);
    } else {
        $users_stmt = $conn->query("SELECT id, username, balance, banned, `group` FROM users WHERE role='user'");
    }
    $users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Database error: ' . $e->getMessage();
    $users = [];
}

// Handle user ban, unban, deletion, and group assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ban_user'])) {
        $user_id = $_POST['user_id'];
        try {
            $ban_stmt = $conn->prepare("UPDATE users SET banned = 1 WHERE id = ?");
            $ban_stmt->execute([$user_id]);
            header('Location: user_list.php');
            exit;
        } catch (PDOException $e) {
            echo 'Ban error: ' . $e->getMessage();
        }
    }

    if (isset($_POST['unban_user'])) {
        $user_id = $_POST['user_id'];
        try {
            $unban_stmt = $conn->prepare("UPDATE users SET banned = 0 WHERE id = ?");
            $unban_stmt->execute([$user_id]);
            header('Location: user_list.php');
            exit;
        } catch (PDOException $e) {
            echo 'Unban error: ' . $e->getMessage();
        }
    }

    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        try {
            $delete_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $delete_stmt->execute([$user_id]);
            header('Location: user_list.php');
            exit;
        } catch (PDOException $e) {
            echo 'Delete error: ' . $e->getMessage();
        }
    }

    if (isset($_POST['assign_group'])) {
        $user_id = $_POST['user_id'];
        $group = $_POST['group'];
        try {
            $assign_group_stmt = $conn->prepare("UPDATE users SET `group` = ? WHERE id = ?");
            $assign_group_stmt->execute([$group, $user_id]);
            header('Location: user_list.php');
            exit;
        } catch (PDOException $e) {
            echo 'Assign Group error: ' . $e->getMessage();
        }
    }
}

include 'header.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4><i class="fa fa-caret-right fw-r5"></i> Manage Users</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Manage Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">User List</h3>
                            <!-- Search Form -->
                            <form method="GET" class="form-inline float-right">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control" placeholder="Search by username" value="<?php echo htmlspecialchars($search_query); ?>">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <!-- Display User's Credit and Balance -->
                            <div class="user-info mb-4">
                                <span class="user-name">
                                    <?php echo "$username | Credit: $balance"; ?>
                                </span>
                            </div>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Balance</th>
                                        <th>Group</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($users)): ?>
                                        <tr>
                                            <td colspan="6">No users found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                <td><?php echo htmlspecialchars($user['balance']); ?></td>
                                                <td><?php echo htmlspecialchars($user['group']); ?></td>
                                                <td><?php echo $user['banned'] ? 'Banned' : 'Active'; ?></td>
                                                <td>
                                                    <?php if (!$user['banned']): ?>
                                                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#banModal<?php echo $user['id']; ?>">Ban</button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#unbanModal<?php echo $user['id']; ?>">Unban</button>
                                                    <?php endif; ?>
                                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal<?php echo $user['id']; ?>">Delete</button>
                                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#assignGroupModal<?php echo $user['id']; ?>">Assign Group</button>
                                                </td>
                                            </tr>
                                            <!-- Modals for Ban, Unban, Delete, Assign Group -->
                                            <!-- [MODALS CODE GOES HERE] -->
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php include 'footer.php'; ?>
