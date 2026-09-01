<?php

session_start();
require_once 'includes/db.php';
/** @var mysqli $conn */

$name     = mysqli_real_escape_string($conn, $_POST['name']);
$password = $_POST['password'];

$sql = "SELECT * FROM admin WHERE Name='$name' LIMIT 1";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $admin = mysqli_fetch_assoc($result);

    if (password_verify($password, $admin['password'])) {
        $_SESSION['admin_id']   = $admin['id'];
        $_SESSION['admin_name'] = $admin['Name'];
        $_SESSION['last_notif_seen'] = date('Y-m-d H:i:s', strtotime('-1 day'));

        header("Location: dashboard.php");
        exit();
    }
}

header("Location: login.php?error=1");
exit();