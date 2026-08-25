<?php

require_once 'admin/includes/db.php';
/**@var mysqli $conn */

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $customer_name   = mysqli_real_escape_string($conn, trim($_POST['customer_name'] ?? ''));
    $email           = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $phone_no        = mysqli_real_escape_string($conn, trim($_POST['phone_no'] ?? ''));
    $location        = mysqli_real_escape_string($conn, trim($_POST['location'] ?? ''));
    $address         = mysqli_real_escape_string($conn, trim($_POST['address'] ?? ''));
    $pickup_date     = mysqli_real_escape_string($conn, trim($_POST['pickup_date'] ?? ''));
    $time_slot       = mysqli_real_escape_string($conn, trim($_POST['time_slot'] ?? ''));
    $phone_model     = mysqli_real_escape_string($conn, trim($_POST['phone_model'] ?? ''));
    $storage         = mysqli_real_escape_string($conn, trim($_POST['storage'] ?? ''));
    $phone_condition = mysqli_real_escape_string($conn, trim($_POST['phone_condition'] ?? ''));
    $accessories     = mysqli_real_escape_string($conn, trim($_POST['accessories_selected'] ?? ''));
    $offered_price   = floatval($_POST['offered_price'] ?? 0);

    // ---------- Basic validation ----------
    if ($customer_name === '' || $phone_no === '' || $phone_model === '') {
        echo json_encode(['success' => false, 'message' => 'Important fields are empty']);
        exit();
    }

    // ---------- Images: atleast 1 image, max 4 ----------
    $image_slots = ['image_1' => null, 'image_2' => null, 'image_3' => null, 'image_4' => null];
    $keys = array_keys($image_slots);
    $uploaded_count = 0;

    if (isset($_FILES['images']) && is_array($_FILES['images']['tmp_name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        foreach ($_FILES['images']['tmp_name'] as $i => $tmp_name) {
            if ($uploaded_count >= 4) break;
            if ($_FILES['images']['error'][$i] != 0) continue;

            $ext = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) continue;

            $filename = time() . '-' . rand(100000000, 999999999) . '.' . $ext;

            // this file is in root folder so it saves here
            if (move_uploaded_file($tmp_name, 'assets/images/' . $filename)) {
                $image_slots[$keys[$uploaded_count]] = $filename;
                $uploaded_count++;
            }
        }
    }

    if ($uploaded_count === 0) {
        echo json_encode(['success' => false, 'message' => 'Atleast upload 1 image']);
        exit();
    }

    $img1 = $image_slots['image_1'] ? "'{$image_slots['image_1']}'" : "NULL";
    $img2 = $image_slots['image_2'] ? "'{$image_slots['image_2']}'" : "NULL";
    $img3 = $image_slots['image_3'] ? "'{$image_slots['image_3']}'" : "NULL";
    $img4 = $image_slots['image_4'] ? "'{$image_slots['image_4']}'" : "NULL";

    $sql = "INSERT INTO orders
            (customer_name, email, phone_no, location, address, pickup_date, time_slot,
             phone_model, storage, phone_condition, accessories_selected, offered_price,
             image_1, image_2, image_3, image_4, status)
            VALUES
            ('$customer_name', '$email', '$phone_no', '$location', '$address', '$pickup_date', '$time_slot',
             '$phone_model', '$storage', '$phone_condition', '$accessories', '$offered_price',
             $img1, $img2, $img3, $img4, 'Pending')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Order submitted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);