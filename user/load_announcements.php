<?php
include '../db.php';

if (isset($_GET['start'])) {
    $start = intval($_GET['start']);

    // Fetch next set of announcements (6 at a time)
    $announcements_stmt = $conn->prepare("SELECT title, description, created_at FROM announcements ORDER BY created_at DESC LIMIT 6 OFFSET ?");
    $announcements_stmt->execute([$start]);
    $announcements = $announcements_stmt->fetchAll();

    // Prepare response
    $response = [
        'announcements' => [],
    ];

    // Format announcement data
    foreach ($announcements as $announcement) {
        $response['announcements'][] = [
            'title' => $announcement['title'],
            'description' => $announcement['description'],
            'date' => date('F j, Y', strtotime($announcement['created_at'])),
        ];
    }

    echo json_encode($response);
}
?>
