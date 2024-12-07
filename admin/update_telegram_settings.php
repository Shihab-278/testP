<?php
session_start();
include '../db.php';

// Set default timezone to Asia/Dhaka (fallback)
date_default_timezone_set('Asia/Dhaka');

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

$telegramToken = '';
$chatId = '';

// Get current Telegram settings from the database
$stmt = $conn->query("SELECT * FROM telegram_settings WHERE id = 1");
$settings = $stmt->fetch();

if ($settings) {
    $telegramToken = $settings['telegram_token'];
    $chatId = $settings['chat_id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newTelegramToken = $_POST['telegram_token'];
    $newChatId = $_POST['chat_id'];

    // Check if the new values are different from the current ones
    if ($newTelegramToken !== $telegramToken || $newChatId !== $chatId) {
        try {
            // Prepare the statement
            $stmt = $conn->prepare("UPDATE telegram_settings SET telegram_token = ?, chat_id = ? WHERE id = 1");
            
            if (!$stmt) {
                throw new Exception("Failed to prepare the statement: " . $conn->error);
            }

            // Execute the query
            $stmt->execute([$newTelegramToken, $newChatId]);

            // Check the number of rows affected
            if ($stmt->rowCount() > 0) {
                $_SESSION['success_message'] = "Telegram settings updated successfully.";
            } else {
                $_SESSION['error_message'] = "No changes were made. Please ensure the values are different.";
            }
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Error updating Telegram settings: " . $e->getMessage();
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = "No changes were made to the Telegram settings.";
    }

    // Redirect to avoid duplicate form submission
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

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
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
