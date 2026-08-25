<?php
session_start();

// Block direct access - must be logged in to view this page
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'includes/db.php';
/**@var mysqli $conn */

// This file is a backend processor only - the actual Add Phone form is
// a popup modal on phones.php (one form for every brand). If someone
// opens this file directly (GET), just send them back there.
if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['add_phone'])) {
    header("Location: phones.php");
    exit();
}

$brand       = mysqli_real_escape_string($conn, trim($_POST['brand']));
$phone_model = mysqli_real_escape_string($conn, trim($_POST['phone_model']));

// ----------1 IMAGE UPLAODED FOR ALL STORAGE ROWS----------
$image_name = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, $allowed)) {
        $image_name = time() . '-' . rand(100000000, 999999999) . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], '../assets/images/' . $image_name);
    }
}
$image_sql = $image_name ? "'$image_name'" : "NULL";

// ---------- 4 fixed storage options,----------
$storage_options = [
    '128GB' => '128',
    '256GB' => '256',
    '512GB' => '512',
    '1TB'   => '1tb',
];

$inserted_count = 0;

foreach ($storage_options as $storage_label => $suffix) {

    $flawless_raw = trim($_POST["flawless_$suffix"] ?? '');
    $good_raw     = trim($_POST["good_$suffix"] ?? '');
    $fair_raw     = trim($_POST["fair_$suffix"] ?? '');

   
    if ($flawless_raw === '' && $good_raw === '' && $fair_raw === '') {
        continue;
    }

    $flawless_price = floatval($flawless_raw);
    $good_price     = floatval($good_raw);
    $fair_price     = floatval($fair_raw);

    $sql = "INSERT INTO phones (brand, phone_model, storage, flawless_price, good_price, fair_price, image)
            VALUES ('$brand', '$phone_model', '$storage_label', '$flawless_price', '$good_price', '$fair_price', $image_sql)";

    if (mysqli_query($conn, $sql)) {
        $inserted_count++;
    }
}

// --------------------
$charger_price   = floatval($_POST['charger_price'] ?? 0);
$earphones_price = floatval($_POST['earphones_price'] ?? 0);
$box_price       = floatval($_POST['box_price'] ?? 0);
$bill_price      = floatval($_POST['bill_price'] ?? 0);

mysqli_query($conn, "INSERT INTO model_accessories (brand, phone_model, charger_price, earphones_price, box_price, bill_price)
    VALUES ('$brand', '$phone_model', '$charger_price', '$earphones_price', '$box_price', '$bill_price')
    ON DUPLICATE KEY UPDATE
    charger_price='$charger_price', earphones_price='$earphones_price', box_price='$box_price', bill_price='$bill_price'");

if ($inserted_count > 0) {
    $_SESSION['msg'] = "<div class='alert alert-success w-50 mb-0'>$brand Phone Added</div>";
} else {
    $_SESSION['msg'] = "<div class='alert alert-danger'>Atleast fill one storage.</div>";
}

header("Location: phones.php");
exit();