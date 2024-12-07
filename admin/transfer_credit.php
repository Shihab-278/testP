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

// Initialize Telegram variables
$telegramToken = '';
$chatId = '';

// Fetch current Telegram settings from the database
$stmt = $conn->query("SELECT * FROM telegram_settings LIMIT 1");
$settings = $stmt->fetch();

if ($settings) {
    $telegramToken = $settings['telegram_token'];
    $chatId = $settings['chat_id'];
}

// Handle form submission to update Telegram settings
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_telegram'])) {
    $telegramToken = $_POST['telegram_token'];
    $chatId = $_POST['chat_id'];

    // Update Telegram settings in the database
    $stmt = $conn->prepare("UPDATE telegram_settings SET telegram_token = ?, chat_id = ? WHERE id = 1");
    $stmt->execute([$telegramToken, $chatId]);

    // Set success message in session
    $_SESSION['success_message'] = "Telegram settings updated successfully.";

    // Redirect to avoid duplicate form submission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$success = false;
$users = [];

// Handle credit transfer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transfer_credit'])) {
    $receiver_username = $_POST['receiver_username'];
    $amount = $_POST['amount'];

    // Get receiver ID from username
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$receiver_username]);
    $receiver = $stmt->fetch();
    $receiver_id = $receiver ? $receiver['id'] : null;

    if ($receiver_id) {
        // Update receiver's balance
        $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$amount, $receiver_id]);

        // Log the transfer
        $stmt = $conn->prepare("INSERT INTO credit_transfers (sender_id, receiver_id, amount) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $receiver_id, $amount]);

        // Send Telegram notification
        sendTelegramNotification($receiver_username, $amount);

        // Set success message in session
        $_SESSION['success_message'] = "Credit transfer to $receiver_username of $amount was successful.";

        // Redirect to avoid duplicate form submission
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $_SESSION['error_message'] = 'Receiver not found.';
    }
}

// Fetch all users for dropdown
try {
    $users_stmt = $conn->query("SELECT username FROM users WHERE role='user'");
    $users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Database error: ' . $e->getMessage();
}

include 'header.php'; // AdminLTE header, sidebar, and navbar

// Telegram notification function
function sendTelegramNotification($receiver_username, $amount) {
    global $telegramToken, $chatId;
    
    $message = "Credit Transfer Notification:\n";
    $message .= "Receiver: $receiver_username\n";
    $message .= "Amount Transferred: $amount";

    $apiURL = "https://api.telegram.org/bot$telegramToken/sendMessage?chat_id=$chatId&text=" . urlencode($message);
    file_get_contents($apiURL);
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4><i class="fa fa-caret-right fw-r5"></i> Transfer Credit</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Transfer Credit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <!-- Display success and error messages -->
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
                            <h3 class="card-title">Transfer Credit Form</h3>
                        </div>
                        <!-- form start -->
                        <form method="POST">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="receiverUsername">Receiver Username</label>
                                    <select name="receiver_username" class="form-control select2" id="receiverUsername" required>
                                        <option value="">Search and Select User</option>
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?php echo htmlspecialchars($user['username']); ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="amount">Amount</label>
                                    <input type="number" name="amount" class="form-control" id="amount" placeholder="Enter Amount" required>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" name="transfer_credit" class="btn btn-primary w-100">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'footer.php'; // AdminLTE footer ?>

<!-- Select2 JS and Initialization -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#receiverUsername').select2({
            placeholder: "Search and Select a Username",
            allowClear: true,
            width: '100%', // Makes the dropdown take full width
            minimumInputLength: 1, // Start searching after 1 character
            templateResult: formatState // Custom result format
        });

        // Custom result format to display the search result
        function formatState(state) {
            if (!state.id) { return state.text; }
            var $state = $('<span>' + state.text + '</span>');
            return $state;
        }
    });
</script>

<!-- Custom Styles for Search -->
<style>
    /* Adjust the width and padding of the select input */
    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 5px 12px;
    }

    /* Style the search input inside the dropdown */
    .select2-container--default .select2-search--dropdown .select2-search__field {
        padding: 5px;
        font-size: 14px;
    }

    /* Style the dropdown container */
    .select2-dropdown {
        font-size: 14px;
        max-width: 100%;
    }

    /* Focus effect */
    .select2-selection--single:focus {
        border-color: #5c9ded;
        box-shadow: 0 0 5px rgba(92, 157, 237, 0.5);
    }
</style>
