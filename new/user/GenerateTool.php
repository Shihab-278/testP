<?php
session_start();
include '../db.php';

// Set timezone to Asia/Kolkata (Indian Standard Time)
date_default_timezone_set('Asia/Kolkata');

if ($_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

$showModal = false;
$toolDetails = [];
$modalMessage = '';
$generatedAt = ''; // Store generated time

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tool_id'])) {
        $tool_id = $_POST['tool_id'];
        $user_id = $_SESSION['user_id'];

        // Get the tool details
        $stmt = $conn->prepare("SELECT * FROM tools WHERE id = ?");
        $stmt->execute([$tool_id]);
        $tool = $stmt->fetch();

        if (!$tool) {
            $modalMessage = 'Tool not found!';
            $showModal = true;
        } else {
            // Check if the user has already generated this tool
            $stmt = $conn->prepare("SELECT * FROM user_tools WHERE user_id = ? AND tool_id = ?");
            $stmt->execute([$user_id, $tool_id]);
            $alreadyGenerated = $stmt->fetch();

            if ($alreadyGenerated) {
                $modalMessage = 'You have already generated this tool!';
                $showModal = true;
            } else {
                // Get user info
                $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();

                // Check if the user has enough credits
                if ($user['balance'] < $tool['tool_cost']) {
                    $modalMessage = 'Not enough credit to generate this tool.';
                    $showModal = true;
                } else {
                    // Deduct the tool cost from the user's balance
                    $stmt = $conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
                    $stmt->execute([$tool['tool_cost'], $user_id]);

                    // Log the credit usage
                    $stmt = $conn->prepare("INSERT INTO credit_usage (user_id_fk, credits_used, usage_date) VALUES (?, ?, NOW())");
                    $stmt->execute([$user_id, $tool['tool_cost']]);

                    // Store the generated tool in the user_tools table
                    $stmt = $conn->prepare("INSERT INTO user_tools (user_id, tool_id, generated_at) VALUES (?, ?, NOW())");
                    $stmt->execute([$user_id, $tool_id]);

                    // Get the generated tool time in IST
                    $generatedAt = date('Y-m-d h:i A'); // Format the current time in 12-hour format with AM/PM

                    // Prepare tool details for modal
                    $toolDetails = [
                        'name' => htmlspecialchars($tool['tool_name']),
                        'username' => htmlspecialchars($tool['tool_username']),
                        'password' => htmlspecialchars($tool['tool_password']),
                        'generated_at' => $generatedAt, // Store time to display in the modal
                    ];
                    $modalMessage = 'Tool generated successfully!';
                    $showModal = true;
                }
            }
        }
    } else {
        $modalMessage = 'No tool selected!';
        $showModal = true;
    }
}

include 'header.php'; // User-side header
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Tool Generated</h1>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Bootstrap Modal -->
            <div class="modal fade" id="toolModal" tabindex="-1" role="dialog" aria-labelledby="toolModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="toolModalLabel">
                                <?php echo $modalMessage === 'Tool generated successfully!' ? 'Tool Generated Successfully!' : 'Error'; ?>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <?php if ($modalMessage === 'Tool generated successfully!'): ?>
                                <p><strong>Tool Name:</strong> <?php echo $toolDetails['name']; ?></p>
                                <p><strong>Username:</strong> <?php echo $toolDetails['username']; ?></p>
                                <p><strong>Password:</strong> <?php echo $toolDetails['password']; ?></p>
                                <p><strong>Generated At:</strong> <?php echo $toolDetails['generated_at']; ?> (IST)</p>
                            <?php else: ?>
                                <p><?php echo $modalMessage; ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'footer.php'; ?>

<!-- Show modal -->
<?php if ($showModal): ?>
<script>
    $(document).ready(function() {
        $('#toolModal').modal('show');
    });
</script>
<?php endif; ?>
