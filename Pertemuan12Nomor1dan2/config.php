<?php
session_start();

$host     = "localhost";
$username = "root";
$password = "";
$database = "praktikum_crud";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

define('ROOT_PATH', __DIR__);
define('UPLOAD_DIR', ROOT_PATH . '/uploads/mahasiswa/');
define('UPLOAD_URL', 'uploads/mahasiswa/');

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        $depth = substr_count($_SERVER['SCRIPT_NAME'], '/') - 1;
        $back  = str_repeat('../', max(0, $depth - 1));
        header("Location: " . $back . "auth/login.php");
        exit();
    }
}

function uploadFile($file) {
    $target_dir = UPLOAD_DIR;
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

    if ($file["size"] > 5000000)
        return ['success' => false, 'message' => 'File terlalu besar. Maks 5MB.'];

    $allowed = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowed))
        return ['success' => false, 'message' => 'Format tidak didukung. Gunakan JPG, PNG, atau GIF.'];

    $new_filename = uniqid() . "." . $imageFileType;
    $target_file  = $target_dir . $new_filename;

    if (move_uploaded_file($file["tmp_name"], $target_file))
        return ['success' => true, 'filename' => $new_filename];

    return ['success' => false, 'message' => 'Gagal mengupload file.'];
}

function deleteFile($filename) {
    $path = UPLOAD_DIR . $filename;
    if ($filename && file_exists($path))
        unlink($path);
}

function photoUrl($filename) {
    // Count how deep the calling script is inside the project
    $depth = substr_count($_SERVER['SCRIPT_NAME'], '/') - 1;
    $back  = str_repeat('../', max(0, $depth - 1));
    return $back . UPLOAD_URL . htmlspecialchars($filename);
}
