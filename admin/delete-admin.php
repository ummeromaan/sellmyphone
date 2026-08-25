<?php
session_start();

// Block direct access - must be logged in to view this page
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'includes/db.php';
/**@var mysqli $conn */

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

   
    $check = mysqli_query($conn, "SELECT Role FROM admin WHERE id='$id' LIMIT 1");
    $row   = mysqli_fetch_assoc($check);

    // Never allow the Super admin account to be deleted
    if ($row && $row['Role'] === 'Super admin') {
        $_SESSION['msg'] = "<div class='alert alert-danger'>Super admin cannot be deleted.</div>";
        header("Location: add-admin.php");
        exit();
    }

    mysqli_query($conn, "DELETE FROM admin WHERE id='$id'");
    $_SESSION['msg'] = "<div class='alert alert-success w-100 mb-0'>Admin Deleted successfully.</div>";
}

header("Location: add-admin.php");
exit();?>