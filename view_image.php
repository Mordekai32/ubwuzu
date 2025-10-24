<?php
session_start();

// Check admin
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    http_response_code(403);
    exit("Access denied");
}

$image = $_GET['file'] ?? '';
$path = 'private_uploads/' . basename($image);

if (file_exists($path)) {
    $info = getimagesize($path);
    header("Content-Type: " . $info['mime']);
    readfile($path);
    exit;
} else {
    http_response_code(404);
    exit("Image not found");
}
?>
