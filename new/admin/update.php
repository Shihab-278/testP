<?php
session_start();
include '../db.php';

// Ensure only admin can access this page
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

include 'header.php'; // AdminLTE header, sidebar, and navbar
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4><i class="fa fa-sync-alt fw-r5"></i> Check Update</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Check Update</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Check for Updates</h3>
                        </div>
                        <!-- form start -->
                        <form id="updateForm">
                            <div class="card-body text-center">
                                <p>Click the button below to check for system updates.</p>
                                <button type="button" id="updateButton" class="btn btn-warning btn-lg">
                                    <i class="fas fa-sync-alt"></i> Check Update
                                </button>

                                <!-- Loading Bar -->
                                <div id="loadingBarContainer" class="progress mt-4 d-none">
                                    <div id="loadingBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%;"></div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Display Update Status -->
                    <div id="updateStatus" class="card mt-3 d-none">
                        <div class="card-body text-center">
                            <h5 class="text-info">
                                <span id="statusMessage"></span>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
    document.getElementById('updateButton').addEventListener('click', function () {
        const updateButton = document.getElementById('updateButton');
        const loadingBarContainer = document.getElementById('loadingBarContainer');
        const loadingBar = document.getElementById('loadingBar');
        const updateStatus = document.getElementById('updateStatus');
        const statusMessage = document.getElementById('statusMessage');

        // Reset states
        updateButton.disabled = true;
        loadingBarContainer.classList.remove('d-none');
        updateStatus.classList.add('d-none');
        loadingBar.style.width = '0%';
        statusMessage.textContent = ''; // Clear the previous message

        // Animate the loading bar
        let width = 0;
        const interval = setInterval(() => {
            width += 10; // Increase width by 10%
            loadingBar.style.width = width + '%';

            // Once loading bar is complete
            if (width >= 100) {
                clearInterval(interval); // Stop the animation
                loadingBarContainer.classList.add('d-none'); // Hide the loading bar
                updateStatus.classList.remove('d-none'); // Show the status message
                statusMessage.innerHTML = "Your system is up to date.<br><strong>Thanks for using Our System!</strong>";
                updateButton.disabled = false; // Re-enable the button
            }
        }, 100); // Update the width every 100ms (total duration: 1 second)
    });
</script>

<?php include 'footer.php'; // AdminLTE footer ?>
