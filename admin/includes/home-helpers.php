<?php
/**
 * Admin-side helpers for editing the Home Page dynamic content
 * (the home_* tables). Include admin/includes/db.php BEFORE this file.
 *
 * These do the actual INSERT/UPDATE/DELETE queries used by
 * admin/home-content.php. Kept separate from includes/home-data.php
 * (which only READS data for the public site) so the public pages
 * never pull in any admin/write logic.
 */

if (!function_exists('home_admin_upload')) {
    // Uploads one file (if present) into assets/images and returns the
    // new filename, or null if no file was sent / upload failed.
    function home_admin_upload($file_key) {
        if (!isset($_FILES[$file_key]) || $_FILES[$file_key]['error'] != 0) {
            return null;
        }
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
        $ext     = strtolower(pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            return null;
        }
        $name = time() . '-' . rand(100000000, 999999999) . '.' . $ext;
        move_uploaded_file($_FILES[$file_key]['tmp_name'], __DIR__ . '/../../assets/images/' . $name);
        return $name;
    }
}

if (!function_exists('home_save_single')) {
    /**
     * Updates a "single row" (id = 1) content table, e.g. home_hero.
     * $text_fields   = list of column names to pull straight from $_POST
     * $image_fields  = [ column_name => $_FILES key ] - only overwritten if a new file was uploaded
     */
    function home_save_single($conn, $table, $text_fields, $image_fields = []) {
        $sets = [];
        foreach ($text_fields as $f) {
            $v = mysqli_real_escape_string($conn, trim($_POST[$f] ?? ''));
            $sets[] = "`$f`='$v'";
        }
        foreach ($image_fields as $col => $file_key) {
            $name = home_admin_upload($file_key);
            if ($name) {
                $sets[] = "`$col`='" . mysqli_real_escape_string($conn, $name) . "'";
            }
        }
        if (empty($sets)) return;

        // make sure the id=1 row exists first
        mysqli_query($conn, "INSERT INTO `$table` (id) VALUES (1) ON DUPLICATE KEY UPDATE id=id");
        $result = mysqli_query($conn, "UPDATE `$table` SET " . implode(', ', $sets) . " WHERE id=1");

        if (!$result) {
            $_SESSION['msg'] = "<div class='alert alert-danger mb-0'>DB Error (save - $table): " . mysqli_error($conn) . "</div>";
        }
    }
}

if (!function_exists('home_row_add')) {
    function home_row_add($conn, $table, $text_fields, $image_fields = []) {
        $cols = [];
        $vals = [];
        foreach ($text_fields as $f) {
            $cols[] = "`$f`";
            $vals[] = "'" . mysqli_real_escape_string($conn, trim($_POST[$f] ?? '')) . "'";
        }
        foreach ($image_fields as $col => $file_key) {
            $name = home_admin_upload($file_key);
            $cols[] = "`$col`";
            $vals[] = $name ? "'" . mysqli_real_escape_string($conn, $name) . "'" : "NULL";
        }
        if (empty($cols)) return;
        $result = mysqli_query($conn, "INSERT INTO `$table` (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")");

        if (!$result) {
            $_SESSION['msg'] = "<div class='alert alert-danger mb-0'>DB Error (add - $table): " . mysqli_error($conn) . "</div>";
        }
    }
}

if (!function_exists('home_row_update')) {
    function home_row_update($conn, $table, $id, $text_fields, $image_fields = []) {
        $sets = [];
        foreach ($text_fields as $f) {
            $sets[] = "`$f`='" . mysqli_real_escape_string($conn, trim($_POST[$f] ?? '')) . "'";
        }
        foreach ($image_fields as $col => $file_key) {
            $name = home_admin_upload($file_key);
            if ($name) {
                $sets[] = "`$col`='" . mysqli_real_escape_string($conn, $name) . "'";
            }
        }
        if (empty($sets)) return;
        $id = intval($id);
        $result = mysqli_query($conn, "UPDATE `$table` SET " . implode(', ', $sets) . " WHERE id=$id");

        if (!$result) {
            $_SESSION['msg'] = "<div class='alert alert-danger mb-0'>DB Error (update - $table): " . mysqli_error($conn) . "</div>";
        }
    }
}

if (!function_exists('home_row_delete')) {
    function home_row_delete($conn, $table, $id) {
        $id = intval($id);
        $result = mysqli_query($conn, "DELETE FROM `$table` WHERE id=$id");

        if (!$result) {
            $_SESSION['msg'] = "<div class='alert alert-danger mb-0'>DB Error (delete - $table): " . mysqli_error($conn) . "</div>";
        }
    }
}

if (!function_exists('home_admin_img')) {
    // Same as the public home_img() but for previewing inside /admin (one folder up)
    function home_admin_img($name) {
        return $name ? '../assets/images/' . $name : '';
    }
}