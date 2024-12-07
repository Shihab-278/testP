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
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user ? $user['username'] : '';

// Update URL
$updateUrl = 'https://shunlocker.com/xtoolupdate/yourfile.zip'; // The location of the update file

// Function to check if file exists at the given URL
function checkForUpdate($url) {
    // Fetch the headers of the file URL
    $headers = @get_headers($url);
    
    // Check if the response contains a 200 OK status (i.e., the file exists)
    if ($headers && strpos($headers[0], '200') !== false) {
        return true; // File exists
    }
    return false; // File does not exist
}

// Check if update is available by checking the URL
$isUpdateAvailable = checkForUpdate($updateUrl);

include 'header.php'; // AdminLTE header, sidebar, and navbar
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4><i class="fa fa-sync-alt"></i> Check for Updates</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Check Update</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Check for Updates</h3>
                        </div>
                        <div class="card-body text-center">
                            <?php if ($isUpdateAvailable): ?>
                                <p class="text-success">Update Available!</p>
                                <a href="update_handler.php?file=<?php echo urlencode($updateUrl); ?>" class="btn btn-success btn-lg">
                                    <i class="fas fa-download"></i> Download and Apply Update
                                </a>
                            <?php else: ?>
                                <p class="text-danger">No Update Available</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'footer.php'; // AdminLTE footer ?>
