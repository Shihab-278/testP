<?php
session_start();
include '../db.php'; // Ensure db connection is established

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "You must be an admin to access this page.";
    exit();
}

// Handle credit addition
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $credit_amount = $_POST['credit_amount'] ?? 0;

    // Validate input
    if ($user_id && $credit_amount > 0) {
        // Fetch the user's current credit balance
        $stmt = $conn->prepare("SELECT credit FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Update the user's credit balance
            $new_credit = $user['credit'] + $credit_amount;

            // Update the user's credit
            $stmt = $conn->prepare("UPDATE users SET credit = ? WHERE id = ?");
            if ($stmt->execute([$new_credit, $user_id])) {
                $response = "Successfully added $credit_amount credits to the user's account. New balance: $new_credit credits.";
            } else {
                $response = "There was an error updating the user's credit. Please try again later.";
            }
        } else {
            $response = "User not found.";
        }
    } else {
        $response = "Invalid user ID or credit amount.";
    }
}

// Fetch all users for admin to select
$stmt = $conn->query("SELECT id, username FROM users");
$users = $stmt->fetchAll();
?>

<?php include 'header.php'; // Include header ?>

<div class="container my-5">
    <h1 class="text-center">Admin - Add Credits</h1>

    <?php if (isset($response)) { echo "<div class='alert alert-info'>$response</div>"; } ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="user_id" class="form-label">Select User</label>
            <select name="user_id" class="form-control" id="user_id" required>
                <option value="">Select User</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="credit_amount" class="form-label">Credit Amount</label>
            <input type="number" name="credit_amount" class="form-control" id="credit_amount" min="1" required>
        </div>

        <button type="submit" class="btn btn-primary">Add Credits</button>
    </form>

    <p><a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a></p>
</div>

<?php include 'footer.php'; // Include footer ?>

</body>
</html>
