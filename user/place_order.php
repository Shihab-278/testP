<?php
session_start();
include '../db.php';

// Set timezone to Asia/Dhaka (Bangladesh Standard Time)
date_default_timezone_set('Asia/Dhaka');

// Check if the user is logged in and has the correct role
if ($_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit;
}

// Retrieve user ID from session
$user_id = $_SESSION['user_id'];

// Get user information
$stmt = $conn->prepare("SELECT username, `group`, balance, credit FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user['username'] ?? 'Unknown User';
$group_name = $user['group'] ?? 'No Group';
$balance = $user['balance'] ?? 0;
$credit = $user['credit'] ?? 0;

// Get user information from the database
$stmt = $conn->prepare("SELECT username, `group` FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user ? $user['username'] : '';
$group_name = $user ? $user['group'] : '';

// Function to send Telegram notification
function sendTelegramNotification($message)
{
    global $conn;
    $stmt = $conn->query("SELECT * FROM telegram_settings LIMIT 1");
    $settings = $stmt->fetch();

    if ($settings) {
        $telegramToken = $settings['telegram_token'];
        $chatId = $settings['chat_id'];

        // Telegram API URL
        $url = "https://api.telegram.org/bot$telegramToken/sendMessage";

        // Prepare data to send
        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML' // You can use HTML formatting in the message
        ];

        // Use cURL to send the request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }
}

// Fetch service groups
$stmt = $conn->query("SELECT * FROM service_groups");
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch the service details based on service_id
if (isset($_GET['group_id'])) {
    $group_id = $_GET['group_id'];
    $stmt = $conn->prepare("SELECT id, name, price, description, delivery_time FROM services WHERE group_id = ?");
    $stmt->execute([$group_id]);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($services);
    exit;
}

if (isset($_GET['service_id'])) {
    $service_id = $_GET['service_id'];
    $stmt = $conn->prepare("SELECT required_fields, delivery_time FROM services WHERE id = ?");
    $stmt->execute([$service_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($service);
    exit;
}

// Handle order placement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = $_POST['service_id'] ?? null;

    if (!$service_id) {
        $response = "No service ID provided.";
    } else {
        // Fetch the price, required fields, and delivery time of the selected service
        $stmt = $conn->prepare("SELECT price, required_fields, name, delivery_time FROM services WHERE id = ?");
        $stmt->execute([$service_id]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($service) {
            $required_fields = explode(',', $service['required_fields']);
            $missing_fields = [];

            // Check if all required fields are provided
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    $missing_fields[] = ucfirst($field);
                }
            }

            if (!empty($missing_fields)) {
                $response = "Please fill in the following required fields: " . implode(', ', $missing_fields);
            } else {
                // Assuming you have a session with user information
                $user_id = $_SESSION['user_id'];
                $stmt = $conn->prepare("SELECT username, credit FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
                $current_credit = $user['credit'] ?? 0;
                $username = $user['username'] ?? '';

                // Calculate total price
                $total_price = $service['price'];

                if ($current_credit >= $total_price) {
                    // Deduct credit
                    $new_credit = $current_credit - $total_price;
                    $stmt = $conn->prepare("UPDATE users SET credit = ? WHERE id = ?");
                    $stmt->execute([$new_credit, $user_id]);

                    // Insert order into the orders table
                    $stmt = $conn->prepare("INSERT INTO orders (user_id, service_id, total_price, submit_time) VALUES (?, ?, ?, NOW())");
                    if ($stmt->execute([$user_id, $service_id, $total_price])) {
                        // Fetch the last inserted order ID
                        $order_id = $conn->lastInsertId();

                        // Insert required fields into the order_fields table
                        foreach ($required_fields as $field) {
                            if (!empty($_POST[$field])) {
                                $stmt = $conn->prepare("INSERT INTO order_fields (order_id, field_name, field_value) VALUES (?, ?, ?)");
                                $stmt->execute([$order_id, $field, $_POST[$field]]);
                            }
                        }

                        $response = "Order placed successfully! Your remaining credit is $" . number_format($new_credit, 2);

                        // Send Telegram Notification
                        $message = "✅ New Order Placed ✅\n\n";
                        $message .= "Order ID: " . $order_id . "\n";
                        $message .= "User: " . htmlspecialchars($username) . "\n";
                        $message .= "Service: " . htmlspecialchars($service['name']) . "\n";
                        $message .= "Total Price: $" . number_format($total_price, 2) . "\n";
                        $message .= "Remaining Credit: $" . number_format($new_credit, 2) . "\n";
                        $message .= "Delivery Time: " . htmlspecialchars($service['delivery_time']) . "\n";
                        $message .= "Please review the order.";

                        // Call the sendTelegramNotification function
                        sendTelegramNotification($message);
                    } else {
                        $response = "Error: Failed to insert the order into the database.";
                    }
                } else {
                    $response = "You do not have enough credit.";
                }
            }
        } else {
            $response = "Service not found.";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service UI</title>

    <style>
        .top-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #2563eb;
            color: white;
            padding: 10px 0;
            text-align: center;
            z-index: 1000;
        }

        #service-list {
            max-height: 300px;
            /* Limit the height of the card body */
            overflow-y: auto;
            /* Enable vertical scroll when content overflows */
        }

        .list-group-item {
            transition: background-color 0.3s ease;
        }

        .list-group-item:hover {
            background-color: #f1f1f1;
            /* Highlight effect on hover */
        }

        body {
            padding-top: 70px;
            /* background-color: #1a1a2e; */
            /* Dark blue background */
            /* color: white; */
            /* Text color */
        }

        .dropdown-menu {
            background-color: #162447;
            /* Slightly lighter dark blue */
            border: none;
        }

        .dropdown-toggle {
            background-color: #0f3460;
            /* Dropdown button color */
            color: white;
        }

        .dropdown-menu .list-group-item {
            background-color: white;
            color: black;
            border: 0px solid #0f3460;
            cursor: pointer;
        }

        .dropdown-menu .list-group-item:hover {
            background-color: gray;
            /* Highlight color */
            color: white;
        }

        .card {
            background-color: #0f3460;
            /* Card background */
            color: white;
            border: none;
        }

        .card-header {
            background-color: #1a1a2e;
            /* Header section */
        }

        .btn-primary {
            background-color: #e94560;
            /* Button color */
            border: none;
        }

        .btn-primary:hover {
            background-color: #d62d52;
            /* Darker red */
        }

        .alert-info {
            background-color: #1a1a2e;
            border: 1px solid #e94560;
            color: white;
        }

        #servicesCard {
            display: none;
        }

        .content2 {
            padding: 20px;
        }

        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #2563eb;
            color: white;
            text-align: center;
            padding: 10px 0;
        }

        .success-message {
            animation: fadeIn 1s ease-in-out forwards;
            background-color: #28a745;
            /* Green background for success */
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 1.2rem;
        }

        .success-message.hide {
            animation: fadeOut 1s ease-in-out forwards;
        }

        .bg-custom {

            background: #cacaca;

        }
    </style>
</head>

<body>

    <?php include 'header.php'; ?>

    <div class="container-fluid mt-3 px-5">
        <!-- Dropdown Menu -->
        <div class="container-fluid mt-3 px-5">
            <!-- Search Bar -->
            <div class="mb-3">
                <input
                    type="text"
                    class="form-control"
                    id="serviceSearch"
                    placeholder="Search Services"
                    onkeyup="filterServices()">
            </div>

            <!-- Dropdown Menu -->
            <div class="bg-custom rounded">
                <div class="row mt-2 p-4">
                    <div class="col-12">
                        <div class="dropdown">
                            <button
                                class="btn btn-secondary dropdown-toggle w-100 text-start"
                                type="button"
                                id="dropdownMenuButton"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Service Groups
                            </button>

                            <div class="dropdown-menu dropdown-menu-start bg-white text-dark p-3" style="width: 250px;" aria-labelledby="dropdownMenuButton">
                                <!-- Search Box -->
                                <input
                                    type="text"
                                    class="form-control mb-2"
                                    id="groupSearch"
                                    placeholder="Search Groups"
                                    onkeyup="searchGroup()">

                                <!-- Group List -->
                                <ul class="list-group" id="groupList">
                                    <?php foreach ($groups as $group): ?>
                                        <li class="list-group-item list-group-item-action" onclick="loadServices(<?php echo $group['id']; ?>)">
                                            <?php echo htmlspecialchars($group['name']); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Dropdown Menu -->
        </div>



        <!-- End Dropdown Menu -->

        <div class="container-fluid mt-3 px-5">

            <!-- Services Section -->
            <div class="row mt-4">
                <div class="col-6">
                    <div class="card shadow-lg">
                        <div class="card-header">
                            <h4 class="card-title text-dark">Available Services</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <h5 id="selected-group-name" class="text-dark" style="font-family: 'Lato', sans-serif; font-weight: bold;">Please select a Service group</h5>
                                    <div id="service-list" class="row">

                                        <!-- Services will appear here dynamically -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Services Section -->


                <!-- Main Section -->
                <div class="col-6">
                    <div class="content2 card shadow-lg">
                        <div class="card-body text-center text-dark">
                            <div class="order-section">
                                <h1 class="text-center">Place Order</h1>

                                <div class="alert alert-info">
                                    <strong>Your Current Credit: $<?php
                                                                    $stmt = $conn->prepare("SELECT credit FROM users WHERE id = ?");
                                                                    $stmt->execute([$_SESSION['user_id']]);
                                                                    $user = $stmt->fetch();
                                                                    echo number_format($user['credit'] ?? 0, 2);
                                                                    ?></strong>
                                </div>

                                <form method="POST" action="">
                                    <div class="mb-3">
                                        <label for="service_id" class="form-label">Select Service</label>
                                        <select name="service_id" class="form-control" id="service_id" required>
                                            <option value="">Select Service</option>
                                        </select>
                                    </div>

                                    <div class="mb-3" id="required_fields"></div>

                                    <button type="submit" class="mb-3 btn btn-primary">Place Order</button>
                                </form>

                                <?php if (isset($response)) {
                                    echo "<div class='alert alert-info'>$response</div>";
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <!--End Main Content Section -->



    <script>
        let allServices = [];
        // Load services based on selected group
        function loadServices(groupId) {
    const selectedGroupName = document.querySelector(`#groupList li[onclick*="${groupId}"]`).textContent;
    document.getElementById('selected-group-name').textContent = `Group: ${selectedGroupName}`;

    fetch('?group_id=' + groupId)
        .then(response => response.json())
        .then(services => {
            allServices = services; // Store all services globally
            displayServices(services); // Display services
        })
        .catch(error => console.error('Error loading services:', error));
}

// Function to display services
function displayServices(services) {
    const serviceList = document.getElementById('service-list');
    serviceList.innerHTML = '';

    if (services.length > 0) {
        services.forEach(service => {
            const serviceItem = document.createElement('div');
            serviceItem.classList.add('list-group-item', 'p-3', 'mb-2', 'border', 'rounded'); // Bootstrap styling
            serviceItem.style.cursor = 'pointer';

            serviceItem.onclick = () => selectService(service.id, service.name, service.price, service.delivery_time);

            serviceItem.innerHTML = `
                <h5 class="text-primary mb-1">${service.name}</h5>
                <p class="mb-1 text-muted">Price: $${service.price}</p>
                <p class="mb-1 text-muted">${service.description}</p>
                <p class="text-info"><strong>Delivery Time:</strong> ${service.delivery_time}</p>
            `;

            serviceList.appendChild(serviceItem);
        });
    } else {
        serviceList.innerHTML = '<p class="text-muted">No services available for this group.</p>';
    }
}


        // Select a service and fetch its required fields
        function selectService(serviceId, serviceName, price, deliveryTime) {
            // Update selected service in dropdown
            document.getElementById('service_id').innerHTML = `<option value="${serviceId}">${serviceName} - $${price}</option>`;

            // Update delivery time information (if needed in your UI)
            let fixedDeliveryTime = deliveryTime;
            document.getElementById('service_id').dataset.deliveryTime = fixedDeliveryTime;

            // Fetch required fields for the selected service
            fetch('?service_id=' + serviceId)
                .then(response => response.json())
                .then(service => {
                    var requiredFieldsContainer = document.getElementById('required_fields');
                    requiredFieldsContainer.innerHTML = '';

                    var requiredFields = service.required_fields ? service.required_fields.split(',') : [];
                    if (requiredFields.length > 0) {
                        requiredFields.forEach(function(field) {
                            var fieldDiv = document.createElement('div');
                            fieldDiv.className = 'mb-3';
                            fieldDiv.innerHTML = `
                        <label for="${field}" class="form-label">${field.charAt(0).toUpperCase() + field.slice(1)}</label>
                        <input type="text" class="form-control" name="${field}" id="${field}" required>
                    `;
                            requiredFieldsContainer.appendChild(fieldDiv);
                        });
                    }
                })
                .catch(error => console.error('Error fetching service details:', error));
        }

        // Search and filter service groups
        function searchGroup() {
            let filter = document.getElementById('groupSearch').value.toUpperCase();
            let list = document.getElementById('groupList');
            let items = list.getElementsByTagName('li');

            for (let i = 0; i < items.length; i++) {
                let item = items[i];
                let text = item.textContent || item.innerText;
                if (text.toUpperCase().indexOf(filter) > -1) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            }
        }

        function filterServices() {
            const query = document.getElementById('serviceSearch').value.toLowerCase();

            // Filter services that include the query in their name
            const filteredServices = allServices.filter(service =>
                service.name.toLowerCase().includes(query)
            );

            // Update the service list with the filtered results
            displayServices(filteredServices);
        }
    </script>
</body>

</html>