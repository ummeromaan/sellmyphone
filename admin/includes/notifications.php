<?php
 

if (!isset($_SESSION['last_notif_seen'])) {
    // Pehli dafa login pe sirf pichle 24 hours ka data "new" count ho
    $_SESSION['last_notif_seen'] = date('Y-m-d H:i:s', strtotime('-1 day'));
}

function get_notification_count($conn) {
    $last_seen = mysqli_real_escape_string($conn, $_SESSION['last_notif_seen']);

    $orders_cnt = 0;
    $res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM orders WHERE created_at > '$last_seen'");
    if ($res) $orders_cnt = (int) mysqli_fetch_assoc($res)['cnt'];

    $msg_cnt = 0;
    $res2 = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM contact_messages WHERE created_at > '$last_seen'");
    if ($res2) $msg_cnt = (int) mysqli_fetch_assoc($res2)['cnt'];

    return $orders_cnt + $msg_cnt;
}

/**
 * Recent activity list dropdown ke liye (orders + messages mix, latest pehle)
 */
function get_notification_items($conn, $limit = 8) {
    $last_seen = $_SESSION['last_notif_seen'];
    $items = [];

    $res = mysqli_query($conn, "SELECT id, customer_name, phone_model, created_at FROM orders ORDER BY created_at DESC LIMIT " . (int)$limit);
    while ($row = mysqli_fetch_assoc($res)) {
        $items[] = [
            'type'     => 'order',
            'icon'     => 'fa-cart-shopping',
            'title'    => 'New order from ' . $row['customer_name'],
            'subtitle' => $row['phone_model'],
            'time'     => $row['created_at'],
            'link'     => 'orders.php',
            'is_new'   => strtotime($row['created_at']) > strtotime($last_seen),
        ];
    }

    $res2 = mysqli_query($conn, "SELECT id, full_name, subject, created_at FROM contact_messages ORDER BY created_at DESC LIMIT " . (int)$limit);
    while ($row = mysqli_fetch_assoc($res2)) {
        $items[] = [
            'type'     => 'message',
            'icon'     => 'fa-envelope',
            'title'    => 'New message from ' . $row['full_name'],
            'subtitle' => $row['subject'] ?: 'No subject',
            'time'     => $row['created_at'],
            'link'     => 'messages.php',
            'is_new'   => strtotime($row['created_at']) > strtotime($last_seen),
        ];
    }

    usort($items, function ($a, $b) {
        return strtotime($b['time']) - strtotime($a['time']);
    });

    return array_slice($items, 0, $limit);
}

function notif_time_ago($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    return floor($diff / 86400) . 'd ago';
}