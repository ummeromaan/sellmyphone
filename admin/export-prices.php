<?php
session_start();

// Block direct access - must be logged in to view this page
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'includes/db.php';
/**@var mysqli $conn */

// ---------- Fetch all phones, ordered by model then storage ----------
$result = mysqli_query($conn, "SELECT brand, phone_model, storage, flawless_price, good_price, fair_price FROM phones ORDER BY phone_model ASC, storage ASC");

// ---------- Fetch all accessories once, keyed by brand|model, to avoid a query per row ----------
$acc_map = [];
$acc_result = mysqli_query($conn, "SELECT brand, phone_model, charger_price, earphones_price, box_price, bill_price FROM model_accessories");
if ($acc_result) {
    while ($a = mysqli_fetch_assoc($acc_result)) {
        $acc_map[$a['brand'] . '|' . $a['phone_model']] = $a;
    }
}

// ---------- Send CSV headers ----------
$filename = "phones-export-" . date('Y-m-d') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$out = fopen('php://output', 'w');

// UTF-8 BOM so Excel opens special characters correctly
fwrite($out, "\xEF\xBB\xBF");

// Header row - matches the column names the Import Prices feature expects
fputcsv($out, ['Brand', 'Model', 'Storage', 'Flawless', 'Good', 'Fair', 'Charger', 'Earphones', 'Box', 'Bill']);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $key = $row['brand'] . '|' . $row['phone_model'];
        $acc = $acc_map[$key] ?? null;

        fputcsv($out, [
            $row['brand'],
            $row['phone_model'],
            $row['storage'],
            $row['flawless_price'],
            $row['good_price'],
            $row['fair_price'],
            $acc['charger_price']   ?? '',
            $acc['earphones_price'] ?? '',
            $acc['box_price']       ?? '',
            $acc['bill_price']      ?? '',
        ]);
    }
}

fclose($out);
exit();