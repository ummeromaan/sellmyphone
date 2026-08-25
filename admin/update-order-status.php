<?php
session_start();

// Block direct access - must be logged in to view this page
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'includes/db.php';
/**@var mysqli $conn */

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id     = mysqli_real_escape_string($conn, $_POST['id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id='$id'");
    $_SESSION['msg'] = "<div class='alert alert-success w-100 mb-0'>Status Updated.</div>";
}

header("Location: orders.php");
exit();