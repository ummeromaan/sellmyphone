<?php
/**
 * Home Page dynamic content helpers.
 * Include admin/includes/db.php BEFORE this file so $conn exists.
 */

if (!function_exists('home_single')) {
    function home_single($conn, $table) {
        $result = mysqli_query($conn, "SELECT * FROM `$table` WHERE id = 1 LIMIT 1");
        return ($result && mysqli_num_rows($result) > 0) ? mysqli_fetch_assoc($result) : [];
    }
}

if (!function_exists('home_rows')) {
    function home_rows($conn, $table, $order = "sort_order ASC, id ASC") {
        $rows = [];
        $result = mysqli_query($conn, "SELECT * FROM `$table` ORDER BY $order");
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
        }
        return $rows;
    }
}

if (!function_exists('home_img')) {
    // Builds an assets/images/... path, falling back to the original image if the DB value is empty
    function home_img($name, $fallback = '') {
        $name = $name !== '' && $name !== null ? $name : $fallback;
        return $name ? 'assets/images/' . $name : '';
    }
}
