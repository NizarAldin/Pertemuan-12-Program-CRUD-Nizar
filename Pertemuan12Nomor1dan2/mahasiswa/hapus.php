<?php
include_once(__DIR__ . "/../config.php");
requireLogin();

if (isset($_GET['id'])) {
    $id     = (int)$_GET['id'];
    $result = mysqli_query($conn, "SELECT foto, nama FROM mahasiswa WHERE id=$id");

    if (mysqli_num_rows($result) > 0) {
        $row  = mysqli_fetch_assoc($result);
        $foto = $row['foto'];
        $nama = $row['nama'];

        if (mysqli_query($conn, "DELETE FROM mahasiswa WHERE id=$id")) {
            if ($foto) deleteFile($foto);
            $_SESSION['message'] = "Data mahasiswa $nama berhasil dihapus.";
        } else {
            $_SESSION['error'] = 'Gagal menghapus data: ' . mysqli_error($conn);
        }
    }
}

header('Location: ../index.php');
exit();
?>
