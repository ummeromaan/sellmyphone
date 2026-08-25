<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error']);
    exit();
}

require_once 'includes/db.php';
require_once 'includes/notifications.php';
/**@var mysqli $conn */

$count = get_notification_count($conn);
$items = get_notification_items($conn, 8);

$out = [];
foreach ($items as $it) {
    $out[] = [
        'icon'     => $it['icon'],
        'title'    => $it['title'],
        'subtitle' => $it['subtitle'],
        'time'     => notif_time_ago($it['time']),
        'link'     => $it['link'],
        'is_new'   => $it['is_new'],
    ];
}

header('Content-Type: application/json');
echo json_encode(['status' => 'ok', 'count' => $count, 'items' => $out]);