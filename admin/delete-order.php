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

    // Get the order's image filenames first so we can also remove them from disk before deleting the DB row
    $result = mysqli_query($conn, "SELECT image_1, image_2, image_3, image_4 FROM orders WHERE id='$id' LIMIT 1");
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $images = array_filter([$row['image_1'], $row['image_2'], $row['image_3'], $row['image_4']]);
        foreach ($images as $img) {
            $path = '../assets/images/' . $img;
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }

    mysqli_query($conn, "DELETE FROM orders WHERE id='$id'");
    $_SESSION['msg'] = "<div class='alert alert-success w-50 mb-0'>Order deleted successfully.</div>";
}

header("Location: orders.php");
exit();