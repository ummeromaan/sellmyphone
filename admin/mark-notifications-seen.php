<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error']);
    exit();
}

$_SESSION['last_notif_seen'] = date('Y-m-d H:i:s');

header('Content-Type: application/json');
echo json_encode(['status' => 'ok']);