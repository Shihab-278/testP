<!-- <nav class="navbar-expand-lg navbar-xl p-lg-0">
    <button class="navbar-toggler" type="button" data-toggle="modal" data-target="#navbarToggler1" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fal fa-bars"></i>
    </button>
    <a class="navbar-brand mr-auto" href="#"> <img src="images/gallery/logo@4x (1).png"> </a>


    <div class="d-none d-lg-block">
        <ul class="navbar-nav navbar-mastermenu">

            <li class="nav-item  menu-item-1010">
                <a class="nav-link dropdown-item " href="./main">
                    Client Area
                </a>
            </li>
            <li class="nav-item dropdown menu-item-1011">
                <a class="nav-link dropdown-item dropdown-toggle" href="./resellerplaceorder/imei">
                    Place an order
                    <i class="fal fa-chevron-down"></i> </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="./resellerplaceorder/imei">IMEI Service</a>
                    <a class="dropdown-item" href="./resellerplaceorder/server">Server Services</a>
                    <a class="dropdown-item" href="./resellerplaceorder/remote">Remote Service</a>
                    <a class="dropdown-item" href="./resellerplaceorder/file">File services</a>

                </div>
            </li>
            <li class="nav-item dropdown menu-item-1012">
                <a class="nav-link dropdown-item dropdown-toggle" href="./orders/imei">
                    Order History
                    <i class="fal fa-chevron-down"></i> </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="./orders/imei/status/all">IMEI Orders</a>
                    <a class="dropdown-item" href="./orders/action/file/status/all">File Orders</a>
                    <a class="dropdown-item" href="./orders/action/newserver/status/all">Server Orders</a>
                    <a class="dropdown-item" href="./orders/action/remote/status/all">Remote Orders</a>
                    <a class="dropdown-item" href="./ordershopping">Retail Orders</a>
                    <a class="dropdown-item" href="./orders/action/advance">Advanced History</a>

                </div>
            </li>
            <li class="nav-item  menu-item-1117">
                <a class="nav-link dropdown-item " href="https://t.me/shunlockernews">
                    Connect Telegram
                </a>
            </li>





            <li class="nav-item dropdown d-block d-lg-none">
                <a class="nav-link d-block d-lg-none dropdown-toggle" href="#"> Products <i class="fal fa-chevron-down"></i></a>

            </li>



            <li class="nav-item dropdown profile">

                <div class=" d-none d-lg-block">
                    <div>
                        <a class="nav-link dropdown-toggle" href="./settings/profile">
                            <div class="d-flex justify-content-between align-items-center">

                                <div class="lg-icon avatar-icon d-lg-none d-xl-block m-0">
                                    <span class="avatar-img"> <img src="https://secure.gravatar.com/avatar/82cf4cb6cbfa64478df53a6e7b8f5146?s=25&amp;d=identicon"> </span>
                                </div>

                                <div>

                                    Shakil Ahammed
                                    <span>(VIP)</span>
                                    <i class="fal fa-chevron-down"></i>
                                    <div class="user-balance text-success"> <small>$ 14962.23 <span id="ccredit" style="display: none;" class="d-none">14962.23</span> </small> </div>
                                </div>


                            </div>
                        </a>
                    </div>
                </div>

                <a class="nav-link d-block d-lg-none dropdown-toggle" href="#"> My Account <i class="fal fa-chevron-down"></i></a>

                <div class="dropdown-menu  dropdown-menu-right  my-account">

                    <a class="dropdown-item" href="./settings/profile">My Profile</a>

                    <a href="./addfunds" class="dropdown-item"> +Add Funds </a>


                    <a class="dropdown-item" href="./settings/api">API Access</a>
                    <a class="dropdown-item" href="./settings/invoice">My Invoice</a>
                    <a class="dropdown-item" href="./settings/mail">My Mail</a>
                    <a class="dropdown-item" href="./settings/statement">My Statement</a>
                    <a class="dropdown-item" href="./settings/transfer">Credit Transfer</a>

                    <a class="dropdown-item" href="./settings/mailnotification">Email Preference</a>

                    <a class="dropdown-item" href="./settings/ticket">My Tickets</a>


                    <a class="dropdown-item" href="./settings/myservice">Service Status</a>

                    <a class="dropdown-item" href="./settings/subscription">Subscription</a>
                    <a class="dropdown-item" href="./settings/apps">Install App</a>



                    <h6 class="dropdown-divider"></h6>


                    <a class="dropdown-item" data-toggle="modal" data-target="#modal-ajax" data-whatever="Login Log" data-size="modal-md" href="./lastlogin">
                        <span>Last Login</span>
                        <br>
                        <small class="text-muted">
                            12/07/24, 07:41 PM <br>
                            IP : 103.141.71.239
                        </small>
                    </a>

                    <a class="dropdown-item text-danger" href="./logout">Logout</a>



                </div>
            </li>




        </ul>
    </div>

    <ul class="navbar-nav">
        <li class="nav-item cartminiview">

        </li>
    </ul>

</nav> -->











<!-- <div class="card  card-search mb-3 bottom-space">
    <div class="form-inline">

        <div class="form-group pr-3 d-none d-lg-block">
            <i class="fal fa-search"></i>
        </div>
        <div class="form-group w-25">
            <ul class="list-group" id="groupList">
                <?php foreach ($groups as $group): ?>
                    <li class="list-group-item" onclick="loadServices(<?php echo $group['id']; ?>)">
                        <?php echo htmlspecialchars($group['name']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="chosen-container chosen-container-single" title="" style="width: 314px;"><a class="chosen-single">
                    <input class="chosen-focus-input" type="text" autocomplete="off">
                    <span>All Group</span>
                    <div><b></b></div>
                </a>
                <div class="chosen-drop">
                    <div class="chosen-search">
                        <!-- <input class="chosen-search-input" type="text" autocomplete="off"> -->
<input type="text" class="form-control" id="groupSearch" placeholder="Search Groups" onkeyup="searchGroup()">
</div>
<div id="service-list" class="mt-4"></div>
</div>
</div>
</div>

<div class="form-group flex-grow-1  ml-lg-2 mb-0">
    <input type="text" class="form-control w-100" placeholder="Search Service..." id="searchservicebox2" autocomplete="off">
</div>



</div>
</div> -->













<!-- <div class="header-middle  position-relative">
    <div class="container ">
        <nav class="navbar-expand-lg navbar-xl p-lg-0">
            <button class="navbar-toggler" type="button" data-toggle="modal" data-target="#navbarToggler1" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fal fa-bars"></i>
            </button>
            <a class="navbar-brand mr-auto" href="#"> <img src="images/gallery/logo@4x (1).png"> </a>


            <div class="d-none d-lg-block">
                <ul class="navbar-nav navbar-mastermenu">

                    <li class="nav-item  menu-item-1010">
                        <a class="nav-link dropdown-item " href="./main">
                            Client Area
                        </a>
                    </li>
                    <li class="nav-item dropdown menu-item-1011">
                        <a class="nav-link dropdown-item dropdown-toggle" href="./resellerplaceorder/imei">
                            Place an order
                            <i class="fal fa-chevron-down"></i> </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="./resellerplaceorder/imei">IMEI Service</a>
                            <a class="dropdown-item" href="./resellerplaceorder/server">Server Services</a>
                            <a class="dropdown-item" href="./resellerplaceorder/remote">Remote Service</a>
                            <a class="dropdown-item" href="./resellerplaceorder/file">File services</a>

                        </div>
                    </li>
                    <li class="nav-item dropdown menu-item-1012">
                        <a class="nav-link dropdown-item dropdown-toggle" href="./orders/imei">
                            Order History
                            <i class="fal fa-chevron-down"></i> </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="./orders/imei/status/all">IMEI Orders</a>
                            <a class="dropdown-item" href="./orders/action/file/status/all">File Orders</a>
                            <a class="dropdown-item" href="./orders/action/newserver/status/all">Server Orders</a>
                            <a class="dropdown-item" href="./orders/action/remote/status/all">Remote Orders</a>
                            <a class="dropdown-item" href="./ordershopping">Retail Orders</a>
                            <a class="dropdown-item" href="./orders/action/advance">Advanced History</a>

                        </div>
                    </li>
                    <li class="nav-item  menu-item-1117">
                        <a class="nav-link dropdown-item " href="https://t.me/shunlockernews">
                            Connect Telegram
                        </a>
                    </li>





                    <li class="nav-item dropdown d-block d-lg-none">
                        <a class="nav-link d-block d-lg-none dropdown-toggle" href="#"> Products <i class="fal fa-chevron-down"></i></a>

                    </li>



                    <li class="nav-item dropdown profile">

                        <div class=" d-none d-lg-block">
                            <div>
                                <a class="nav-link dropdown-toggle" href="./settings/profile">
                                    <div class="d-flex justify-content-between align-items-center">

                                        <div class="lg-icon avatar-icon d-lg-none d-xl-block m-0">
                                            <span class="avatar-img"> <img src="https://secure.gravatar.com/avatar/82cf4cb6cbfa64478df53a6e7b8f5146?s=25&amp;d=identicon"> </span>
                                        </div>

                                        <div>

                                            Shakil Ahammed
                                            <span>(VIP)</span>
                                            <i class="fal fa-chevron-down"></i>
                                            <div class="user-balance text-success"> <small>$ 14962.23 <span id="ccredit" style="display: none;" class="d-none">14962.23</span> </small> </div>
                                        </div>


                                    </div>
                                </a>
                            </div>
                        </div>

                        <a class="nav-link d-block d-lg-none dropdown-toggle" href="#"> My Account <i class="fal fa-chevron-down"></i></a>

                        <div class="dropdown-menu  dropdown-menu-right  my-account">

                            <a class="dropdown-item" href="./settings/profile">My Profile</a>

                            <a href="./addfunds" class="dropdown-item"> +Add Funds </a>


                            <a class="dropdown-item" href="./settings/api">API Access</a>
                            <a class="dropdown-item" href="./settings/invoice">My Invoice</a>
                            <a class="dropdown-item" href="./settings/mail">My Mail</a>
                            <a class="dropdown-item" href="./settings/statement">My Statement</a>
                            <a class="dropdown-item" href="./settings/transfer">Credit Transfer</a>

                            <a class="dropdown-item" href="./settings/mailnotification">Email Preference</a>

                            <a class="dropdown-item" href="./settings/ticket">My Tickets</a>


                            <a class="dropdown-item" href="./settings/myservice">Service Status</a>

                            <a class="dropdown-item" href="./settings/subscription">Subscription</a>
                            <a class="dropdown-item" href="./settings/apps">Install App</a>



                            <h6 class="dropdown-divider"></h6>


                            <a class="dropdown-item" data-toggle="modal" data-target="#modal-ajax" data-whatever="Login Log" data-size="modal-md" href="./lastlogin">
                                <span>Last Login</span>
                                <br>
                                <small class="text-muted">
                                    12/07/24, 09:43 PM <br>
                                    IP : 103.141.71.239
                                </small>
                            </a>

                            <a class="dropdown-item text-danger" href="./logout">Logout</a>
                        </div>
                    </li>

                </ul>
            </div>

            <ul class="navbar-nav">
                <li class="nav-item cartminiview">

                </li>
            </ul>

        </nav>
    </div>
</div>
 -->


<!-- 
old  -->

<div class="sidebar">
    <h3>Service Groups</h3>

    <input type="text" class="form-control" id="groupSearch" placeholder="Search Groups" onkeyup="searchGroup()">

    <ul class="list-group" id="groupList">
        <?php foreach ($groups as $group): ?>
            <li class="list-group-item" onclick="loadServices(<?php echo $group['id']; ?>)">
                <?php echo htmlspecialchars($group['name']); ?>
            </li>
        <?php endforeach; ?>
    </ul>


    <div id="service-list" class="mt-4"></div> <!-- Services will appear here -->

</div>






















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
    <title>Select Service Group and Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- ==================== -->
    <base href="https://shunlocker.com/" />
    <link rel="stylesheet" href="templates/default/css/bootstrap.min.css" />
    <link rel="stylesheet" href="templates/default/css/chosen.min.css" />

    <script src="templates/default/js/jquery-3.2.1.min.js"></script>
    <script src="templates/default/js/popper.min.js"></script>
    <script src="templates/default/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="templates/default/js/theme.js?28d6304dd6d05d172bd2c2ad4fe98d0bebabf4de"></script>
    <script type="text/javascript" src="includes/js/custom.js?28d6304dd6d05d172bd2c2ad4fe98d0bebabf4de"></script>
    <script src="templates/default/js/chosen.jquery.min.js"></script>
    <script src="templates/default/js/Chart.bundle.min.js"></script>
    <script src="templates/default/js/bootstrap-datepicker.min.js"></script>
    <script src="templates/default/js/jquery.lightSlider.min.js" type="text/javascript"></script>
    <script src="templates/default/js/table-cell-selector.js" type="text/javascript"></script>
    <script src="templates/default/js/wow.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="includes/js/imei.js"></script>

    <script type="text/javascript" src="templates/default/js/jquery.steps.min.js"></script>

    <link type="text/css" rel="stylesheet" href="templates/default/css/lightSlider.css" />
    <link rel="stylesheet" href="templates/default/css/bootstrap-datepicker.min.css" />
    <link rel="stylesheet" href="templates/default/css/animate.min.css" />`
    <link href="templates/default/css/typekit-offline.css" rel="stylesheet" />


    <link rel="stylesheet" href="templates/default/css/all.css" />
    <link href="includes/icons/menu-icon.css" rel="stylesheet" type="text/css" />
    <link href="includes/icons/flags/flags.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="templates/default/css/theme.css?228d6304dd6d05d172bd2c2ad4fe98d0bebabf4de" />



    <link rel="stylesheet" href="templates/default/css/themes/dark.css?28d6304dd6d05d172bd2c2ad4fe98d0bebabf4de783" />
    <!-- ============================= -->
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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

        body {
            padding-top: 70px;
            /* Prevents content from overlapping with the fixed header */
        }

        .sidebar {
            position: fixed;
            top: 70px;
            left: 0;
            height: calc(100% - 70px);
            width: 250px;
            background-color: #f8f9fa;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
        }

        .sidebar h3 {
            text-align: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .sidebar .form-control {
            margin-bottom: 15px;
            border-radius: 20px;
            padding: 12px 15px;
        }

        .list-group-item {
            cursor: pointer;
            transition: background-color 0.3s ease;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .list-group-item:hover {
            background-color: #007bff;
            color: white;
            transform: scale(1.05);
        }

        .content {
            margin-left: 270px;
            padding: 20px;
        }

        .order-section {
            width: 60%;
            /* Make it smaller */
            margin: 20px auto;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .service-card {
            margin-bottom: 15px;
        }

        .service-card .card-body {
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .service-card .card-body:hover {
            transform: scale(1.05);
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
    </style>
</head>

<body class="svg-light default tpl-client_order_imei     page-resellerplaceorder  cart">

    <!-- Include the Header -->


    <!-- Sidebar for Service Groups -->

    <!-- <div class="container-main">
        <h3>Service Groups</h3>

        Dropdown for Service Groups
        <div class="dropdown dropdown-menu-lg-end mt-2">
            <button
                class="btn btn-secondary dropdown-toggle"
                type="button"
                id="groupDropdown"
                data-bs-toggle="dropdown"
                aria-expanded="false">
                Select a Group
            </button>
            <ul class="dropdown-menu p-3 dropdown-menu-lg" aria-labelledby="groupDropdown" id="groupList">
                Search input inside the dropdown
                <li class="mb-2">
                    <input type="text" class="form-control" id="groupSearch" placeholder="Search Groups" onkeyup="searchGroup()">
                </li>
                Group items
                < php foreach ($groups as $group): ?>
                    <li class="group-item">
                        <button class="dropdown-item" onclick="loadServices(< php echo $group['id']; ?>)">
                            < php echo htmlspecialchars($group['name']); ?>
                        </button>
                    </li>
                < php endforeach; ?>
            </ul>
        </div>

         Services will appear here -->
    <!-- <div id="service-list" class="mt-4"></div> -->
    <!-- </div> -->

    <!-- ========================================================= -->

    <div class="container">
        <div class="page-container">
            <div>
                <div class="card  card-search mb-3 bottom-space">
                    <div class="form-inline">
                        <div class="form-group pr-3 d-none d-lg-block">
                            <i class="fal fa-search"></i>
                        </div>
                        <div class="form-group w-25">
                            <select class="form-control" onChange="loadItems(this.value)" style="min-width: 300px">
                                <option value="">All Group</option>
                                <?php foreach ($groups as $group): ?>
                                    <option value="<?php echo $group['id']; ?>">
                                        <?php echo htmlspecialchars($group['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="custom-control custom-checkbox mr-3 mt-lg-0 ml-lg-auto" data-toggle="tooltip" data-title="Discounted services">
                            <input type="checkbox" name="discounted" onclick="showdiscounted(this);" class="custom-control-input" id="chk1">
                            <label class="custom-control-label" for="chk1"> Discounted </label>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="h-100 d-flex w-100 flex-column">
    <div class="row">
        <!-- Service List -->
      
        <!-- End Service List -->

        <!-- Main Section -->
        <div class="col-lg-8 col-right d-flex h-100 flex-column">
            <div class="card card-servicedetail">
                <h4 class="mb-4 bottom-space service-title">Place Order</h4>
                <div class="alert alert-info">
                    <strong>Your Current Credit: $
                        <?php
                        $stmt = $conn->prepare("SELECT credit FROM users WHERE id = ?");
                        $stmt->execute([$_SESSION['user_id']]);
                        $user = $stmt->fetch();
                        echo number_format($user['credit'] ?? 0, 2);
                        ?>
                    </strong>
                </div>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="service_id" class="form-label">Select Service</label>
                        <select name="service_id" class="form-control" id="service_id" required>
                            <option value="">Select Service</option>
                            <?php foreach ($services as $group): ?>
                                <?php foreach ($group['services'] as $service): ?>
                                    <option value="<?php echo htmlspecialchars($service['id']); ?>">
                                        <?php echo htmlspecialchars($service['name']); ?> - $<?php echo number_format($service['price'], 2); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3" id="required_fields"></div>

                    <button type="submit" class="btn btn-primary">Place Order</button>
                </form>

                <?php if (isset($response)) {
                    echo "<div class='alert alert-info'>$response</div>";
                } ?>
            </div>
        </div>
        <!-- End Main Section -->
    </div>
</div>


        <!-- ===================================================================== -->
        <!-- Main Content Area -->
        <!-- <div class="content">
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



                    <button type="submit" class="btn btn-primary">Place Order</button>
                </form>

                <?php if (isset($response)) {
                    echo "<div class='alert alert-info'>$response</div>";
                } ?>
            </div>
        </div> -->

        <script>
            // Load services based on selected group
            function loadServices(groupId) {
                fetch('?group_id=' + groupId)
                    .then(response => response.json())
                    .then(services => {
                        let serviceList = document.getElementById('service-list');
                        serviceList.innerHTML = '';
                        services.forEach(service => {
                            const serviceDiv = document.createElement('div');
                            serviceDiv.classList.add('card', 'mb-3', 'service-card');
                            serviceDiv.innerHTML = `

                            <div class="card-body" onclick="selectService(${service.id}, '${service.name}', ${service.price}, '${service.delivery_time}')">
                                <h5 class="card-title">${service.name}</h5>
                                <p class="card-text">Price: $${service.price}</p>
                                <p class="card-text text-muted">${service.description}</p>
                                <p class="card-text text-info"><strong>Delivery Time:</strong> ${service.delivery_time}</p>
                            </div>
                        `;
                            serviceList.appendChild(serviceDiv);
                        });
                    });
            }

            // Select a service and fetch its required fields
            function selectService(serviceId, serviceName, price, deliveryTime) {
                document.getElementById('service_id').innerHTML = `<option value="${serviceId}">${serviceName} - $${price}</option>`;

                // Fixed delivery time based on service selection
                let fixedDeliveryTime = deliveryTime; // Set delivery time from the service object
                document.getElementById('service_id').dataset.deliveryTime = fixedDeliveryTime;

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
                    });
            }

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
        </script>
</body>

</html>
<style scoped>
    html body .cn {
        position: fixed !important;
        z-index: 999999999 !important;
        text-align: right !important;
    }

    @media (max-width: 992px) {
        html body .cn {
            text-align: center !important;
            position: relative !important;
            top: 16px !important;
            z-index: 1 !important;
        }
    }
</style>
<div class="cn" style="font-size: 11px !important; padding: 2px 5px !important; display: block !important; color: rgb(117, 117, 117) !important;left:0 !important; bottom: 0px !important; right: 0px !important; text-transform: uppercase !important;opacity:0.5 !important;display:block !important;">Powered by <a href="http://www.dhru.com/?fromaid=shunlocker.com" style="display: normal;color:#777  !important" target="_blank">Dhru Fusion</a></div>