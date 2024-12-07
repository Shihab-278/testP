<?php
// Array of allowed domains
$allowedDomains = [
    "gsmxtool.com",
    "gsmrent.com",
    "gsmiger",
    "anotherdomain.com"
];

// Output domains as plain text, one per line
header('Content-Type: text/plain');
foreach ($allowedDomains as $domain) {
    echo $domain . "\n";
}
