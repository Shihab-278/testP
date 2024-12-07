<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Check</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        #message {
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            display: none;
            font-weight: bold;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
    <script>
        function checkForUpdate() {
            // Display a loading message while checking
            document.getElementById('message').style.display = 'none';  // Hide previous message
            document.getElementById('message').innerHTML = 'আপডেট চেক করা হচ্ছে...';
            document.getElementById('message').style.display = 'block';

            // Fetch the response from the PHP script
            fetch('check_update.php')
                .then(response => response.json())
                .then(data => {
                    // Show the result message based on the response
                    if (data.updateAvailable) {
                        document.getElementById('message').innerHTML = 'নতুন আপডেট উপলব্ধ!';
                        document.getElementById('message').className = 'success';  // Add success class for green message
                    } else {
                        document.getElementById('message').innerHTML = 'কোনো আপডেট নেই।';
                        document.getElementById('message').className = 'error';  // Add error class for red message
                    }
                    document.getElementById('message').style.display = 'block';  // Show the message
                })
                .catch(error => {
                    // Handle errors and show error message
                    console.error('Error:', error);
                    document.getElementById('message').innerHTML = 'একটি ত্রুটি ঘটেছে।';
                    document.getElementById('message').className = 'error';  // Red error message
                    document.getElementById('message').style.display = 'block';  // Show the message
                });
        }
    </script>
</head>
<body>
    <h1>আপডেট চেক করুন</h1>
    <button onclick="checkForUpdate()">চেক আপডেট</button>

    <!-- Message area to show results -->
    <div id="message"></div>
</body>
</html>
