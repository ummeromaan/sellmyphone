<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    exit();
}

if (!empty($_FILES['upload']['name'])) {
    $ext = strtolower(pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($ext, $allowed)) {
        echo json_encode(['error' => ['message' => 'Invalid file type.']]);
        exit();
    }

    $new_name = 'blog_content_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
    $dest = '../assets/images/' . $new_name;

    if (move_uploaded_file($_FILES['upload']['tmp_name'], $dest)) {
        // Store path relative to site root (matches how frontend blog-single.php reads images)
        echo json_encode(['url' => 'assets/images/' . $new_name]);
    } else {
        echo json_encode(['error' => ['message' => 'Upload failed.']]);
    }
    exit();
}

echo json_encode(['error' => ['message' => 'No file received.']]);