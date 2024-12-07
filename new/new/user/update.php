<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GSMXTOOL - Rent Tool Update</title>
    <meta name="description" content="GSMXTOOL Tool Rental">
    <meta name="author" content="GSMXTOOL">

    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        /* General Body Styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            padding-top: 80px; /* Space for fixed navbar */
        }

        /* Card Styling */
        .card {
            background-color: #f9f9f9; /* Light background */
            border-radius: 15px; /* Rounded corners */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Soft shadow effect */
            transition: all 0.3s ease; /* Smooth transition */
            max-width: 500px;
            margin: 20px auto;
        }

        .card:hover {
            transform: translateY(-5px); /* Hover effect */
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2); /* Enhanced shadow on hover */
        }

        .card-body {
            padding: 30px;
        }

        /* Title Styling */
        .card-body h2 {
            color: #28a745; /* Green color for success */
            margin-bottom: 20px;
        }

        .card-body .p-b-md {
            color: #7f8c8d; /* Muted text for description */
            font-size: 1.1rem;
        }

        /* Button Styling */
        .btn-warning {
            font-size: 16px; /* Larger text */
            padding: 12px 24px; /* Increased padding */
            font-weight: bold; /* Bold text */
            text-transform: uppercase; /* Uppercase text */
            border-radius: 5px; /* Rounded corners */
            width: 100%; /* Full width button */
            background-color: #f39c12; /* Default color */
            border: none; /* Remove border */
        }

        .btn-warning:hover {
            background-color: #e67e22; /* Darker shade of orange on hover */
        }

        /* Icon Styling */
        .fas {
            font-size: 20px; /* Icon size */
            margin-right: 8px; /* Space between icon and text */
        }

        /* Padding and Margin Adjustments */
        .p-b-md {
            margin-bottom: 20px; /* Space below the text */
        }

    </style>
</head>
<body>

    <!-- Card Section -->
    <div class="card text-center shadow-lg mb-4">
        <div class="card-body">
            <!-- Title Section -->
            <h2>
                <i class="fas fa-check-circle"></i> Cheers! Rent Tool is up to date.
            </h2>

            <!-- Description Section -->
            <p class="p-b-md">
                It looks like there are no new updates available at the moment.
            </p>

            <!-- Recheck Button -->
            <a id="click_load" href="https://rent.shunlocker.com/" style="margin-top: 20px; display: inline-block;" previewlistener="true">
                <button class="btn btn-warning btn-lg">
                    <i class="fas fa-sync-alt"></i> Recheck
                </button>
            </a>
        </div>
    </div>

    <!-- Bootstrap JS (Optional, if needed for dropdowns, modals, etc.) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
