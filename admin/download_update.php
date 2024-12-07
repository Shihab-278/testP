<?php
// The URL of the update.zip file
$update_url = 'https://shunlocker.com/xtool/update.zip';

// Force the browser to download the zip file
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="update.zip"');
header('Content-Length: ' . filesize($update_url));

// Read the file and send it to the browser
readfile($update_url);
exit;
