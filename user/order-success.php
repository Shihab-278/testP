<?php
session_start();

// Ensure only logged-in users can access the order success page
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Response message
$response = "Your order has been placed successfully!";
?>

<?php include 'header.php'; // Include the header ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .success-container {
            text-align: center;
            padding: 50px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .success-message {
            font-size: 24px;
            color: #28a745;
            margin-top: 20px;
        }
        .order-history-btn {
            margin-top: 30px;
            display: none;
        }
        .checkmark {
            width: 50px;
            height: 50px;
            border: 5px solid #28a745;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px auto;
            animation: checkmark-animation 2s ease-in-out forwards;
        }

        .checkmark::before {
            content: '';
            width: 15px;
            height: 30px;
            border: solid #28a745;
            border-width: 0 5px 5px 0;
            transform: rotate(45deg);
            animation: tick-animation 2s ease-in-out forwards;
        }

        @keyframes checkmark-animation {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.2);
                opacity: 0.8;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes tick-animation {
            0% {
                width: 0;
                height: 0;
            }
            100% {
                width: 15px;
                height: 30px;
            }
        }
    </style>
</head>
<body>

    <div class="success-container">
        <div class="checkmark"></div>
        <div class="success-message"><?php echo $response; ?></div>
        <button class="btn btn-success order-history-btn" id="order-history-btn" onclick="window.location.href='order-history.php';">View Order History</button>
    </div>

    <script>
        // Display the Order History button after the animation completes
        setTimeout(function() {
            document.getElementById('order-history-btn').style.display = 'inline-block';
        }, 2000); // Wait for animation duration before showing the button
    </script>

</body>
</html>

<?php include 'footer.php'; // Include the footer ?>
