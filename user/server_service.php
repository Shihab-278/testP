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

// Retrieve user ID and current balance and credit from session
$user_id = $_SESSION['user_id'];

// Get user information
$stmt = $conn->prepare("SELECT username, `group`, balance, credit FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$username = $user['username'] ?? 'Unknown User';
$group_name = $user['group'] ?? 'No Group';
$balance = $user['balance'] ?? 0;
$credit = $user['credit'] ?? 0;

$response = "";

// Handle form submission to place an order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    // Check if user has enough credit
    if ($credit <= 0) {
        $response = "You do not have enough credit to place an order.";
    } else {
        // Make sure service_ids is an array
        $service_ids = isset($_POST['service_ids']) ? $_POST['service_ids'] : [];
        $additional_info = trim($_POST['additional_info']);
        $user_id = $_SESSION['user_id']; // User's ID from session
        $requirements = isset($_POST['requirements']) ? json_encode($_POST['requirements']) : null;

        // Check if the selected services are valid
        $invalid_services = [];
        foreach ($service_ids as $service_id) {
            $stmt = $conn->prepare("SELECT * FROM server_services WHERE id = ?");
            $stmt->execute([$service_id]);
            $service = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$service) {
                $invalid_services[] = $service_id;
            }
        }

        if (!empty($invalid_services)) {
            $response = "Invalid services selected: " . json_encode($invalid_services);
        } else {
            // Start a transaction to ensure both credit deduction and order placement are handled atomically
            try {
                $conn->beginTransaction();

                // Deduct the credit for the order
                $total_cost = 0;
                foreach ($service_ids as $service_id) {
                    $stmt = $conn->prepare("SELECT price FROM server_services WHERE id = ?");
                    $stmt->execute([$service_id]);
                    $service = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($service) {
                        $total_cost += $service['price'];  // Add up the total cost of the selected services
                    }
                }

                if ($credit < $total_cost) {
                    $response = "You do not have enough credit to place this order.";
                    $conn->rollBack();
                } else {
                    // Deduct the total cost from the user's credit
                    $new_credit = $credit - $total_cost;
                    $updateStmt = $conn->prepare("UPDATE users SET credit = ? WHERE id = ?");
                    $updateStmt->execute([$new_credit, $user_id]);

                    // Insert the new order into server_order table for each valid service selected
                    foreach ($service_ids as $service_id) {
                        $stmt = $conn->prepare("INSERT INTO server_order (user_id, service_id, additional_info, requirements, submit_time) 
                                                VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)");
                        $stmt->execute([$user_id, $service_id, $additional_info, $requirements]);
                    }

                    // Commit the transaction if everything is successful
                    $conn->commit();
                    $response = "Your order has been placed successfully!";
                }
            } catch (PDOException $e) {
                // Rollback transaction in case of an error
                $conn->rollBack();
                $response = "Error: " . $e->getMessage();
            }
        }
    }
}

// Fetch available services for users to order
$stmt = $conn->query("SELECT * FROM server_services");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch available groups for filtering services
$group_stmt = $conn->query("SELECT * FROM server_service_group");
$groups = $group_stmt->fetchAll(PDO::FETCH_ASSOC);

// Group services by group ID for easy display
$grouped_services = [];
foreach ($services as $service) {
    $grouped_services[$service['group_id']][] = $service;
}
?>

<?php include 'header.php'; // Include the header ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .service-item {
            cursor: pointer;
            padding: 10px;
            border: 1px solid #ddd;
            margin: 5px 0;
            border-radius: 4px;
        }

        .service-item.selected {
            background-color: #007bff;
            color: white;
        }

        .service-item:hover {
            background-color: #f0f0f0;
        }

        #order-btn {
            display: none; /* Initially hidden */
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">Place an Order</h1>

        <!-- Show response message (e.g., insufficient credit, success, etc.) -->
        <?php if ($response): ?>
        <div class="alert alert-info"><?php echo $response; ?></div>
        <?php endif; ?>

        <!-- Order Form -->
        <form method="POST" id="order-form">
            <div class="row">
                <!-- Group and Service Selection Section -->
                <div class="col-md-6">
                    <!-- Service Group Section -->
                    <div class="mb-3">
                        <label for="group_id" class="form-label">Select Service Group</label>
                        <select name="group_id" class="form-select" id="group_id" required>
                            <option value="">--Select a Group--</option>
                            <?php foreach ($groups as $group): ?>
                                <option value="<?php echo $group['id']; ?>"><?php echo htmlspecialchars($group['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Service List (Will be populated dynamically) -->
                    <div id="service-list">
                        <!-- Dynamic service list will be shown here after selecting a group -->
                    </div>
                </div>

                <!-- Additional Info and Order Section -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="additional_info" class="form-label">Additional Information</label>
                        <textarea name="additional_info" class="form-control" id="additional_info" rows="4"></textarea>
                    </div>

                    <input type="hidden" name="service_ids[]" id="selected_services">

                    <div id="requirements-container"></div> <!-- Dynamic requirements container -->

                    <button type="submit" name="place_order" class="btn btn-primary" id="order-btn">Place Order</button>
                </div>
            </div>
        </form>

        <p><a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a></p>
    </div>

    <script>
        const groupSelect = document.getElementById('group_id'); // Group selection dropdown
        const serviceListContainer = document.getElementById('service-list'); // Container for the service list
        const selectedServicesInput = document.getElementById('selected_services'); // Hidden input to hold selected service IDs
        const orderBtn = document.getElementById('order-btn'); // The Order button
        const requirementsContainer = document.getElementById('requirements-container'); // Container for dynamic requirements

        let selectedServices = []; // Array to hold selected service IDs

        // Function to handle group selection and display corresponding services
        groupSelect.addEventListener('change', () => {
            const groupId = groupSelect.value;
            const servicesForGroup = <?php echo json_encode($grouped_services); ?>[groupId] || [];
            
            // Clear existing service list and requirements
            serviceListContainer.innerHTML = '';
            requirementsContainer.innerHTML = '';

            if (servicesForGroup.length > 0) {
                // Create clickable items for each service
                servicesForGroup.forEach(service => {
                    const serviceDiv = document.createElement('div');
                    serviceDiv.classList.add('service-item');
                    serviceDiv.setAttribute('data-service-id', service.id);
                    
                    // Display the service name and price correctly
                    serviceDiv.innerHTML = `${service.name} - $${service.price}`; // Assuming price is stored in service['price']
                    
                    // Add click event to toggle selection
                    serviceDiv.addEventListener('click', () => toggleServiceSelection(serviceDiv, service.id, service.requirements));

                    serviceListContainer.appendChild(serviceDiv);
                });
            } else {
                serviceListContainer.innerHTML = '<p>No services available for this group.</p>';
            }
        });

        // Function to toggle service selection
        function toggleServiceSelection(serviceDiv, serviceId, serviceRequirements) {
            if (serviceDiv.classList.contains('selected')) {
                // Deselect service
                serviceDiv.classList.remove('selected');
                selectedServices = selectedServices.filter(id => id !== serviceId);
            } else {
                // Select service
                serviceDiv.classList.add('selected');
                selectedServices.push(serviceId);
            }

            // Update the hidden input field with selected service IDs
            selectedServicesInput.value = selectedServices.join(",");  // Pass service IDs as a comma-separated string

            // Show or hide the order button based on the selection
            if (selectedServices.length > 0) {
                orderBtn.style.display = 'inline-block'; // Show the order button
            } else {
                orderBtn.style.display = 'none'; // Hide the order button
            }

            // Display service requirements if any
            displayRequirements(serviceRequirements);
        }

        // Function to display the requirements for a selected service
        function displayRequirements(serviceRequirements) {
            // Clear previous requirements
            requirementsContainer.innerHTML = '';

            if (serviceRequirements) {
                const requirements = JSON.parse(serviceRequirements);
                requirements.forEach(requirement => {
                    const inputField = document.createElement('div');
                    inputField.classList.add('mb-3');
                    inputField.innerHTML = `
                        <label for="${requirement}" class="form-label">${requirement}</label>
                        <input type="text" name="requirements[${requirement}]" id="${requirement}" class="form-control">
                    `;
                    requirementsContainer.appendChild(inputField);
                });
            }
        }
    </script>
</body>
</html>

<?php include 'footer.php'; ?>
