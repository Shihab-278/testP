<?php
session_start();
include '../db.php'; // Make sure this path is correct to your db.php file

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

// Include the admin header
include 'header.php'; // Make sure this path is correct

// Connect to the database
$conn = new mysqli('localhost', 'domhoste_test', 'domhoste_test', 'domhoste_test');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all payments with status 'pending', 'approved', or 'rejected' along with the username and created_at
$sql = "SELECT mp.id, mp.user_id, mp.amount, mp.transaction_id, mp.payment_method, mp.status, mp.payment_type, mp.created_at, u.username
        FROM manual_payments mp
        JOIN users u ON mp.user_id = u.id"; // Assuming users table has 'id' and 'username' columns

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments List</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            flex: 1; /* Makes the container take available space */
        }
        h2 {
            text-align: center;
            color: #333;
            font-size: 24px;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 14px;
        }
        table th {
            background-color: #007bff;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:hover {
            background-color: #f1f1f1;
        }
        .actions a {
            text-decoration: none;
            color: white;
            padding: 6px 12px;
            border-radius: 5px;
            margin: 0 5px;
            font-weight: bold;
            font-size: 14px;
        }
        .approve {
            background-color: #28a745;
        }
        .reject {
            background-color: #dc3545;
        }
        .actions a:hover {
            opacity: 0.8;
        }
        .no-payments {
            text-align: center;
            color: #666;
            font-size: 16px;
        }
        footer {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 12px;
            margin-top: auto;  /* Pushes footer to the bottom */
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                width: 95%;
                margin: 20px auto;
            }
            table th, table td {
                font-size: 12px;
                padding: 8px;
            }
            h2 {
                font-size: 20px;
            }
            .actions a {
                font-size: 12px;
                padding: 4px 8px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Payments List</h2>

    <?php
    if ($result->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Amount</th>
                    <th>Transaction ID</th>
                    <th>Payment Method</th>
                    <th>Payment Type</th> <!-- Add Payment Type column -->
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>";

        // Display each payment with the username and created_at
        while($row = $result->fetch_assoc()) {
            // Format the created_at date (optional)
            $created_at = date('Y-m-d H:i:s', strtotime($row['created_at']));
            
            echo "<tr>
                    <td>" . $row['id'] . "</td>
                    <td>" . $row['username'] . "</td>
                    <td>" . $row['amount'] . "</td>
                    <td>" . $row['transaction_id'] . "</td>
                    <td>" . $row['payment_method'] . "</td>
                    <td>" . ucfirst($row['payment_type']) . "</td> <!-- Display payment type -->
                    <td>" . $row['status'] . "</td>
                    <td>" . $created_at . "</td>
                    <td class='actions'>
                        <a href='approve_payment.php?id=" . $row['id'] . "' class='approve'>Approve</a>
                        <a href='reject_payment.php?id=" . $row['id'] . "' class='reject'>Reject</a>
                    </td>
                </tr>";
        }

        echo "</table>";
    } else {
        echo "<p class='no-payments'>No payments found.</p>";
    }

    // Close the connection
    $conn->close();
    ?>

</div>

<!-- Include the admin footer -->
<?php include '../admin/footer.php'; // Adjust the path if necessary ?>

</body>
</html>
