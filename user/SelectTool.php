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

$showModal = false;
$modalMessage = '';
$toolDetails = [];

// Fetch categories
$category_stmt = $conn->query("SELECT DISTINCT category FROM tools");
$categories = $category_stmt->fetchAll();

// Fetch tools already generated by the user
$tools_stmt = $conn->prepare("SELECT tools.id FROM tools JOIN user_tools ON tools.id = user_tools.tool_id WHERE user_tools.user_id = ?");
$tools_stmt->execute([$user_id]);
$generated_tool_ids = $tools_stmt->fetchAll(PDO::FETCH_COLUMN, 0);

include 'header.php'; 
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4><i class="fa fa-caret-right fw-r5"></i> Generate Tool</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                        <li class="breadcrumb-item active">Generate Tool</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <!-- Tool Selection -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Tool Selection</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($categories)): ?>
                                <div class="alert alert-danger" role="alert">No Available Tools</div>
                            <?php else: ?>
                                <form id="selectToolForm">
                                    <div class="form-group">
                                        <label for="categorySelect">Select Tool Category</label>
                                        <select class="form-control" id="categorySelect" name="category" required>
                                            <option value="" disabled selected>Tool Category</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo htmlspecialchars($category['category']); ?>">
                                                    <?php echo htmlspecialchars($category['category']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="toolSelect">Select Tools</label>
                                        <select class="form-control" id="toolSelect" name="tool_id" required>
                                            <option value="" disabled selected>Select Tools List</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block">Generate Tool</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <!-- Display Generated Tools -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3 class="card-title">My Generated Tools</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tool Name</th>
                                        <th>Tool Username</th>
                                        <th>Tool Password</th>
                                        <th>Date & Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($generated_tool_ids as $tool_id): ?>
                                        <?php
                                        // Fetch the details of the generated tools
                                        $tool_stmt = $conn->prepare("SELECT tool_name, tool_username, tool_password, generated_at FROM tools JOIN user_tools ON tools.id = user_tools.tool_id WHERE user_tools.tool_id = ? AND user_tools.user_id = ?");
                                        $tool_stmt->execute([$tool_id, $_SESSION['user_id']]);
                                        $tool = $tool_stmt->fetch();

                                        // Convert the UTC time to Asia/Dhaka
                                        $generated_at = new DateTime($tool['generated_at'], new DateTimeZone('UTC'));
                                        $generated_at->setTimezone(new DateTimeZone('Asia/Dhaka'));
                                        $formatted_time = $generated_at->format('h:i:s A, d M Y'); // Format time
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($tool['tool_name']); ?></td>
                                            <td><?php echo htmlspecialchars($tool['tool_username']); ?></td>
                                            <td><?php echo htmlspecialchars($tool['tool_password']); ?></td>
                                            <td><?php echo $formatted_time; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'footer.php'; ?>

<!-- Include necessary JS files -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#categorySelect').change(function() {
            var category = $(this).val();
            $.ajax({
                url: 'fetch_tools.php',
                type: 'POST',
                data: { category: category },
                success: function(data) {
                    $('#toolSelect').html(data);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Error: ' + textStatus + ' ' + errorThrown);
                }
            });
        });

        $('#selectToolForm').submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: 'process_tool_generation.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#toolModal .modal-title').text('Tool Generated Successfully!');
                        $('#toolModal .modal-body').html(
                            '<p>' + response.message + '</p>' +
                            '<p><strong>Tool Name:</strong> ' + response.tool.name + '</p>' +
                            '<p><strong>Username:</strong> ' + response.tool.username + '</p>' +
                            '<p><strong>Password:</strong> ' + response.tool.password + '</p>'
                        );

                        // Update "My Generated Tools" table dynamically
                        var generatedToolsTable = $('table tbody');
                        generatedToolsTable.empty(); 

                        response.generated_tools.forEach(function(tool) {
                            generatedToolsTable.append(
                                '<tr>' +
                                '<td>' + tool.tool_name + '</td>' +
                                '<td>' + tool.tool_username + '</td>' +
                                '<td>' + tool.tool_password + '</td>' +
                                '<td>' + tool.generated_at + '</td>' +
                                '</tr>'
                            );
                        });

                        $('#toolModal').modal('show');
                    } else {
                        $('#toolModal .modal-title').text('Error');
                        $('#toolModal .modal-body').text(response.message);
                        $('#toolModal').modal('show');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Error: ' + textStatus + ' ' + errorThrown);
                }
            });
        });
    });
</script>

<!-- Modal for success or error messages -->
<div class="modal fade" id="toolModal" tabindex="-1" aria-labelledby="toolModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="toolModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<style>
    /* General Layout Adjustments */
    .container-fluid {
        padding: 20px 70px;
    }

    /* Tool Card Adjustments */
    .card {
        border: 1px solid #ccc !important;
        margin-bottom: 20px;
    }

    /* Card Titles */
    .card-header h3.card-title {
        font-size: 1.25rem;
        font-weight: bold;
    }

    /* Select Dropdowns */
    .form-control {
        font-size: 1rem;
        padding: 0.75rem;
    }

    /* Tool Generation Button */
    .btn-block {
        padding: 12px;
        font-size: 1rem;
    }

    /* Table Adjustments */
    .table {
        width: 100%;
        font-size: 0.9rem;
    }

    .table th, .table td {
        text-align: center;
        padding: 10px;
    }

    /* Modal Adjustments */
    .modal-dialog {
        max-width: 600px;
        margin: 1.75rem auto;
    }

    .modal-header .modal-title {
        font-size: 1.2rem;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .card-body {
            padding: 1rem;
        }

        .form-control {
            font-size: 0.9rem;
        }

        .btn-block {
            font-size: 0.9rem;
        }

        .table th, .table td {
            padding: 8px;
        }
    }
</style>
