<?php
session_start();
include '../db.php';

// Ensure only admin can access this page
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$telegramToken = '';
$chatId = '';

// Check if Telegram settings row exists
$stmt = $conn->query("SELECT * FROM telegram_settings WHERE id = 1");
$settings = $stmt->fetch();

if ($settings) {
    // Existing settings, load them
    $telegramToken = $settings['telegram_token'];
    $chatId = $settings['chat_id'];
} else {
    // If no settings exist, initialize empty values
    $telegramToken = '';
    $chatId = '';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telegramToken = $_POST['telegram_token'];
    $chatId = $_POST['chat_id'];

    // Update or Insert settings in the database
    if ($settings) {
        // Update existing record
        $stmt = $conn->prepare("UPDATE telegram_settings SET telegram_token = ?, chat_id = ? WHERE id = 1");
        $stmt->execute([$telegramToken, $chatId]);
    } else {
        // Insert new settings if no row exists
        $stmt = $conn->prepare("INSERT INTO telegram_settings (telegram_token, chat_id) VALUES (?, ?)");
        $stmt->execute([$telegramToken, $chatId]);
    }

    $_SESSION['success_message'] = "Telegram settings updated successfully.";
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

include 'header.php'; // AdminLTE header, sidebar, and navbar
?>



<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4><i class="fa fa-caret-right fw-r5"></i> Update Telegram Settings</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Update Telegram Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Telegram Settings</h3>
                        </div>
                        <form method="POST">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="telegramToken">Telegram Bot Token</label>
                                    <input type="text" name="telegram_token" class="form-control" id="telegramToken" value="<?php echo htmlspecialchars($telegramToken); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="chatId">Telegram Chat ID</label>
                                    <input type="text" name="chat_id" class="form-control" id="chatId" value="<?php echo htmlspecialchars($chatId); ?>" required>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update Settings</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<?php include 'footer.php'; ?>
