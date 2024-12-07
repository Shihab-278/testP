<?php

// Update check function
function checkUpdate($url) {
    // Full URL to the update.zip file
    $fileUrl = $url . 'update.zip';
    
    // Initialize cURL session
    $ch = curl_init();
    
    // Set cURL options to check if update.zip exists
    curl_setopt($ch, CURLOPT_URL, $fileUrl);
    curl_setopt($ch, CURLOPT_NOBODY, true);  // Don't download content, just check headers
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
    curl_setopt($ch, CURLOPT_HEADER, true); // Include the header in the output

    // Execute the cURL request for update.zip
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Check if the update.zip file is accessible (HTTP 200 OK)
    if ($httpCode == 200) {
        return true;  // Update available
    }

    // Close cURL session
    curl_close($ch);

    return false;  // No update available
}

// Function to download the update file (update.zip)
function downloadUpdate($url, $destination) {
    // Initialize cURL session for downloading the update
    $ch = curl_init();
    
    // Set cURL options to download the update.zip
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Return the response as a string
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // Follow redirects if any
    
    // Get the update content
    $updateContent = curl_exec($ch);
    
    // Check if the download was successful
    if ($updateContent === false) {
        curl_close($ch);
        return false;
    }
    
    // Save the content to a file (update.zip)
    file_put_contents($destination, $updateContent);
    
    curl_close($ch);
    
    return true;  // Successfully downloaded
}

// Function to unzip the update
function unzipUpdate($zipFile, $extractTo) {
    // Open the ZIP file
    $zip = new ZipArchive;
    
    if ($zip->open($zipFile) === TRUE) {
        // Extract the files to the specified directory
        $zip->extractTo($extractTo);
        $zip->close();
        
        return true;  // Successfully extracted
    }
    
    return false;  // Error extracting
}

// Admin Panel actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // URL to check (make sure to add trailing slash)
    $url = 'https://shunlocker.com/xtool/';  // The base URL to check for updates
    $updateFile = 'update.zip';  // The update file name
    $destination = 'path/to/your/server/update.zip';  // Destination on the server to save the downloaded update

    // Step 1: Check if an update is available
    if (checkUpdate($url)) {
        echo "Update Available!<br>";

        // Step 2: Download the update
        if (downloadUpdate($url . $updateFile, $destination)) {
            echo "Update downloaded successfully!<br>";

            // Step 3: Unzip the downloaded update
            $extractTo = 'path/to/your/server';  // Path where the update should be extracted
            if (unzipUpdate($destination, $extractTo)) {
                echo "Update installed successfully!";
            } else {
                echo "Error extracting update!";
            }
        } else {
            echo "Error downloading update!";
        }
    } else {
        echo "No update available. You're using the latest version!";
    }
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Update</title>
    <style>
        .btn {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <h2>Admin Panel - Update</h2>

    <form method="POST">
        <button type="submit" class="btn">Check for Update</button>
    </form>

</body>
</html>
