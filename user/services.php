<?php
session_start();
include '../db.php'; // Ensure DB connection is established

// Fetch available services along with their group names and delivery time details
$stmt = $conn->query("SELECT services.*, service_groups.name AS group_name 
                      FROM services 
                      JOIN service_groups ON services.group_id = service_groups.id");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group services by group_name
$grouped_services = [];
foreach ($services as $service) {
    $grouped_services[$service['group_name']][] = $service;
}
?>

<?php include '../header.php'; ?> <!-- Include header.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - User Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1200px;
        }
        h1 {
            font-size: 2.5rem;
            color: #007bff;
        }
        h3 {
            font-size: 1.8rem;
            color: #343a40;
        }
        .service-info {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 5px;
        }
        .price, .delivery-time {
            font-weight: bold;
            color: #28a745;
            font-size: 1rem;
        }
        .service-name {
            font-weight: bold;
            color: #007bff;
        }
        .list-group-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
            padding: 10px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .list-group-item:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .search-bar {
            margin-bottom: 20px;
        }
        .header-row {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 15px;
        }
        .header-row div {
            width: 30%;
            text-align: center;
        }
        .service-details {
            display: flex;
            justify-content: space-between;
        }
        .service-details div {
            width: 30%;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center mb-4">Available Services</h1>

        <!-- Search Section -->
        <div class="mb-4">
            <input type="text" id="searchService" class="form-control search-bar" placeholder="Search for services...">
        </div>
        
        <?php foreach ($grouped_services as $group_name => $group_services): ?>
        <div class="mb-5">
            <h3><?php echo htmlspecialchars($group_name); ?></h3>
            
            <!-- Header Row with column labels -->
            <div class="header-row">
                <div>Service Name</div>
                <div>Delivery Time</div>
                <div>Price</div>
            </div>

            <div id="servicesList">
                <?php foreach ($group_services as $service): ?>
                <div class="list-group-item">
                    <div class="service-details">
                        <div class="service-name">
                            <?php echo htmlspecialchars($service['name']); ?>
                        </div>
                        <div class="service-info">
                            <span class="delivery-time">
                                <?php
                                    $time = $service['delivery_time'];
                                    $unit = htmlspecialchars($service['delivery_unit']);
                                    switch ($unit) {
                                        case 'hours':
                                            echo "$time hour" . ($time > 1 ? 's' : ''); // Handle pluralization for hours
                                            break;
                                        case 'weeks':
                                            echo "$time week" . ($time > 1 ? 's' : ''); // Handle pluralization for weeks
                                            break;
                                        default:
                                            echo "$time minute" . ($time > 1 ? 's' : ''); // Handle pluralization for minutes
                                            break;
                                    }
                                ?>
                            </span>
                        </div>
                        <div class="price">
                            $<?php echo number_format($service['price'], 2); ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search Functionality
        document.getElementById('searchService').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const servicesList = document.getElementById('servicesList');
            const items = servicesList.getElementsByClassName('list-group-item');
            
            Array.from(items).forEach(item => {
                const serviceName = item.getElementsByClassName('service-name')[0].textContent.toLowerCase();
                if (serviceName.includes(query)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>

<?php include '../footer.php'; ?> <!-- Include footer.php -->
