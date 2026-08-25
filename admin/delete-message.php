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

    mysqli_query($conn, "DELETE FROM contact_messages WHERE id='$id'");
    $_SESSION['msg'] = "<div class='alert alert-success w-100 mb-0'>Message deleted successfully.</div>";
}

header("Location: messages.php");
exit();