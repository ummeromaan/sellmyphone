<?php
session_start();

// Block direct access - must be logged in to view this page
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'includes/db.php';
/**@var mysqli $conn */

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['import_prices'])) {
    header("Location: phones.php");
    exit();
}

if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['msg'] = "<div class='alert alert-danger'>CSV file upload failed. Please try again.</div>";
    header("Location: phones.php");
    exit();
}

$tmp_path = $_FILES['csv_file']['tmp_name'];

// ---------- Detect the delimiter (some regional Excel settings use semicolon instead of comma) ----------
$first_line = file_get_contents($tmp_path, false, null, 0, 2000);
$first_line = preg_replace('/^\xEF\xBB\xBF/', '', $first_line); // Strip BOM if present

$comma_count     = substr_count($first_line, ',');
$semicolon_count = substr_count($first_line, ';');
$delimiter       = $semicolon_count > $comma_count ? ';' : ',';

$handle = fopen($tmp_path, 'r');

if (!$handle) {
    $_SESSION['msg'] = "<div class='alert alert-danger'>Could not read the uploaded file.</div>";
    header("Location: phones.php");
    exit();
}

// If the file starts with a BOM, skip the first 3 bytes
$bom_check = fread($handle, 3);
if ($bom_check !== "\xEF\xBB\xBF") {
    rewind($handle);
}

// ---------- Read the header row and map column names to their positions (case-insensitive, supports alternate naming) ----------
$header_row = fgetcsv($handle, 0, $delimiter);

if (!$header_row) {
    $_SESSION['msg'] = "<div class='alert alert-danger'>CSV file is empty or unreadable.</div>";
    header("Location: phones.php");
    exit();
}

// Possible header names for each column type (matched case-insensitively)
$column_aliases = [
    'brand'     => ['brand'],
    'model'     => ['model', 'phone model', 'phone_model'],
    'storage'   => ['storage'],
    'flawless'  => ['flawless', 'excellent'],
    'good'      => ['good'],
    'fair'      => ['fair', 'fair/cracked', 'fair cracked'],
    'charger'   => ['charger'],
    'earphones' => ['earphones', 'earphone'],
    'box'       => ['box'],
    'bill'      => ['bill', 'valid bill'],
];

$col_index = []; // e.g. ['model' => 0, 'storage' => 1, ...]

foreach ($header_row as $idx => $col_name) {
    $col_name_clean = strtolower(trim($col_name));
    foreach ($column_aliases as $key => $aliases) {
        if (in_array($col_name_clean, $aliases, true)) {
            $col_index[$key] = $idx;
            break;
        }
    }
}

// Brand, Model, Storage, Flawless, Good, Fair are required columns (accessories are optional)
$required_cols = ['brand', 'model', 'storage', 'flawless', 'good', 'fair'];
$missing_cols  = array_diff($required_cols, array_keys($col_index));

if (!empty($missing_cols)) {
    $_SESSION['msg'] = "<div class='alert alert-danger'>CSV is missing required column(s): " . implode(', ', $missing_cols) . ". Check the header row spelling.</div>";
    fclose($handle);
    header("Location: phones.php");
    exit();
}

// Small helper to pull a column's value from a row (empty string if the column isn't present in this CSV)
$get = function ($row, $key) use ($col_index) {
    return isset($col_index[$key]) && isset($row[$col_index[$key]]) ? trim($row[$col_index[$key]]) : '';
};

$updated_count  = 0;
$inserted_count = 0;
$skipped_count  = 0;

while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {

    // Skip blank lines
    if (count(array_filter($row, fn($v) => trim((string) $v) !== '')) === 0) {
        continue;
    }

    // Brand: must come from the CSV's Brand column, and must be Apple or Samsung
    $brand = $get($row, 'brand');
    if (strcasecmp($brand, 'Apple') === 0) {
        $brand = 'Apple';
    } elseif (strcasecmp($brand, 'Samsung') === 0) {
        $brand = 'Samsung';
    } else {
        $skipped_count++;
        continue;
    }

    $model   = $get($row, 'model');
    // Normalize storage: "128 GB" -> "128GB" (strip spaces to match the DB format)
    $storage = str_replace(' ', '', $get($row, 'storage'));

    $flawless  = $get($row, 'flawless');
    $good      = $get($row, 'good');
    $fair      = $get($row, 'fair');
    $charger   = $get($row, 'charger');
    $earphones = $get($row, 'earphones');
    $box       = $get($row, 'box');
    $bill      = $get($row, 'bill');

    if ($model === '' || $storage === '') {
        $skipped_count++;
        continue;
    }

    $brand_esc   = mysqli_real_escape_string($conn, $brand);
    $model_esc   = mysqli_real_escape_string($conn, $model);
    $storage_esc = mysqli_real_escape_string($conn, $storage);

    // ---------- phones table: UPDATE if the variant already exists, otherwise INSERT ----------
    $check = mysqli_query($conn, "SELECT id FROM phones WHERE brand='$brand_esc' AND phone_model='$model_esc' AND storage='$storage_esc' LIMIT 1");

    if ($check && mysqli_num_rows($check) > 0) {
        $existing = mysqli_fetch_assoc($check);

        $set_parts = [];
        if ($flawless !== '') $set_parts[] = "flawless_price='" . floatval($flawless) . "'";
        if ($good !== '')     $set_parts[] = "good_price='" . floatval($good) . "'";
        if ($fair !== '')     $set_parts[] = "fair_price='" . floatval($fair) . "'";

        if (!empty($set_parts)) {
            $set_sql = implode(', ', $set_parts);
            mysqli_query($conn, "UPDATE phones SET $set_sql WHERE id='" . $existing['id'] . "'");
            $updated_count++;
        }
    } else {
        // New variant - all three price fields are required to insert
        if ($flawless === '' || $good === '' || $fair === '') {
            $skipped_count++;
            continue;
        }
        mysqli_query($conn, "INSERT INTO phones (brand, phone_model, storage, flawless_price, good_price, fair_price)
            VALUES ('$brand_esc', '$model_esc', '$storage_esc', '" . floatval($flawless) . "', '" . floatval($good) . "', '" . floatval($fair) . "')");
        $inserted_count++;
    }

    // ---------- model_accessories table: update/insert if accessory columns were provided ----------
    if ($charger !== '' || $earphones !== '' || $box !== '' || $bill !== '') {
        $acc_check = mysqli_query($conn, "SELECT id FROM model_accessories WHERE brand='$brand_esc' AND phone_model='$model_esc' LIMIT 1");

        if ($acc_check && mysqli_num_rows($acc_check) > 0) {
            $acc_existing = mysqli_fetch_assoc($acc_check);
            $acc_set = [];
            if ($charger !== '')   $acc_set[] = "charger_price='" . floatval($charger) . "'";
            if ($earphones !== '') $acc_set[] = "earphones_price='" . floatval($earphones) . "'";
            if ($box !== '')       $acc_set[] = "box_price='" . floatval($box) . "'";
            if ($bill !== '')      $acc_set[] = "bill_price='" . floatval($bill) . "'";

            if (!empty($acc_set)) {
                $acc_set_sql = implode(', ', $acc_set);
                mysqli_query($conn, "UPDATE model_accessories SET $acc_set_sql WHERE id='" . $acc_existing['id'] . "'");
            }
        } else {
            mysqli_query($conn, "INSERT INTO model_accessories (brand, phone_model, charger_price, earphones_price, box_price, bill_price)
                VALUES ('$brand_esc', '$model_esc', '" . floatval($charger ?: 0) . "', '" . floatval($earphones ?: 0) . "', '" . floatval($box ?: 0) . "', '" . floatval($bill ?: 0) . "')");
        }
    }
}

fclose($handle);

$_SESSION['msg'] = "<div class='alert alert-success w-100 mb-0'>
    Import complete &mdash; $updated_count updated, $inserted_count added"
    . ($skipped_count > 0 ? ", $skipped_count skipped (missing model/storage or invalid brand)" : "")
    . ".</div>";

header("Location: phones.php");
exit();