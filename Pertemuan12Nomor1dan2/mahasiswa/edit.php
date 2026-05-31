<?php
include_once(__DIR__ . "/../config.php");
requireLogin();

if (!isset($_GET['id'])) { header('Location: ../index.php'); exit(); }
$id = (int)$_GET['id'];

$result = mysqli_query($conn, "SELECT * FROM mahasiswa WHERE id=$id");
if (mysqli_num_rows($result) == 0) { header('Location: ../index.php'); exit(); }
$row         = mysqli_fetch_assoc($result);
$current_foto = $row['foto'];

$errors  = [];
$success = '';

if (isset($_POST['update'])) {
    $nim     = mysqli_real_escape_string($conn, trim($_POST['nim']));
    $nama    = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $jurusan = mysqli_real_escape_string($conn, trim($_POST['jurusan']));
    $email   = mysqli_real_escape_string($conn, trim($_POST['email']));
    $alamat  = mysqli_real_escape_string($conn, trim($_POST['alamat']));
    $foto_filename = $current_foto;

    if (empty($nim)) $errors[] = 'NIM tidak boleh kosong.';
    elseif (!preg_match('/^[0-9]{8,12}$/', $nim)) $errors[] = 'NIM harus berupa angka dengan panjang 8-12 digit.';

    if (empty($nama))    $errors[] = 'Nama tidak boleh kosong.';
    if (empty($jurusan)) $errors[] = 'Jurusan tidak boleh kosong.';
    if (empty($email))   $errors[] = 'Email tidak boleh kosong.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';

    if (empty($errors)) {
        $chk = mysqli_query($conn, "SELECT id FROM mahasiswa WHERE nim='$nim' AND id != $id");
        if (mysqli_num_rows($chk) > 0) $errors[] = 'NIM sudah digunakan mahasiswa lain.';
    }

    if (!empty($_FILES['foto']['name'])) {
        $upload = uploadFile($_FILES['foto']);
        if ($upload['success']) {
            if ($current_foto) deleteFile($current_foto);
            $foto_filename = $upload['filename'];
        } else {
            $errors[] = $upload['message'];
        }
    }

    if (isset($_POST['hapus_foto']) && $_POST['hapus_foto'] == '1') {
        if ($current_foto) deleteFile($current_foto);
        $foto_filename = null;
    }

    if (empty($errors)) {
        $foto_sql = $foto_filename ? "'$foto_filename'" : 'NULL';
        $sql = "UPDATE mahasiswa SET nim='$nim', nama='$nama', jurusan='$jurusan',
                email='$email', alamat='$alamat', foto=$foto_sql WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['message'] = "Data mahasiswa $nama berhasil diperbarui!";
            header('Location: ../index.php');
            exit();
        } else {
            $errors[] = 'Error database: ' . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .navbar { background: linear-gradient(135deg, #1a237e, #0d47a1) !important; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); }
        .card-header { background: linear-gradient(135deg, #e65100, #f57c00); border-radius: 12px 12px 0 0 !important; color: white; }
        .btn-warning-custom { background: linear-gradient(135deg, #e65100, #f57c00); border: none; color: white; }
        .btn-warning-custom:hover { background: linear-gradient(135deg, #bf360c, #e65100); color: white; }
        .form-control:focus, .form-select:focus { border-color: #e65100; box-shadow: 0 0 0 0.25rem rgba(230,81,0,0.15); }
        .required { color: #dc3545; }
        .photo-thumb { width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 3px solid #e0e0e0; }
        .photo-placeholder { width: 100px; height: 100px; border-radius: 50%; background: #fbe9e7; display: flex; align-items: center; justify-content: center; color: #ff8a65; font-size: 2.5rem; }
        #newPhotoPreview { width:100px; height:100px; object-fit:cover; border-radius:50%; border:3px solid #4caf50; display:none; }
    </style>
</head>
<body>
<nav class="navbar navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="../index.php">
            <i class="bi bi-mortarboard-fill me-2"></i>Sistem Mahasiswa
        </a>
        <span class="text-white-50 small"><i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['full_name']) ?></span>
    </div>
</nav>

<div class="container" style="max-width:720px">
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="../index.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
        <h5 class="mb-0 fw-bold">Edit Data Mahasiswa</h5>
    </div>

    <div class="card">
        <div class="card-header py-3">
            <h6 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Form Edit Data</h6>
        </div>
        <div class="card-body p-4">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="text-center mb-4">
                    <?php if ($current_foto): ?>
                        <img src="<?= photoUrl($current_foto) ?>" class="photo-thumb mx-auto d-block mb-2" alt="Foto saat ini" id="currentPhoto">
                        <p class="text-muted small mb-2">Foto saat ini</p>
                        <div class="form-check d-inline-flex align-items-center gap-2">
                            <input type="checkbox" name="hapus_foto" value="1" id="hapusFoto" class="form-check-input">
                            <label for="hapusFoto" class="form-check-label text-danger small">Hapus foto ini</label>
                        </div>
                    <?php else: ?>
                        <div class="photo-placeholder mx-auto mb-2"><i class="bi bi-person"></i></div>
                        <p class="text-muted small">Belum ada foto</p>
                    <?php endif; ?>
                    <img id="newPhotoPreview" class="mx-auto d-block mb-1" src="" alt="Preview baru">
                    <div class="mt-2">
                        <label class="form-label fw-semibold d-block">Ganti Foto <span class="text-muted small">(opsional)</span></label>
                        <input type="file" name="foto" id="fotoInput" accept="image/*" class="form-control form-control-sm" style="max-width:300px;margin:0 auto">
                        <small class="text-muted">Format: JPG, PNG, GIF | Maks 5MB</small>
                    </div>
                </div>
                <hr>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">NIM <span class="required">*</span></label>
                        <input type="text" name="nim" class="form-control"
                            value="<?= htmlspecialchars($_POST['nim'] ?? $row['nim']) ?>" required>
                        <small class="text-muted">8-12 digit angka</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="nama" class="form-control"
                            value="<?= htmlspecialchars($_POST['nama'] ?? $row['nama']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jurusan <span class="required">*</span></label>
                        <select name="jurusan" class="form-select" required>
                            <option value="">-- Pilih Jurusan --</option>
                            <?php
                            $jurusans = ['Teknik Informatika','Sistem Informasi','Teknik Elektro','Teknik Komputer','Manajemen Informatika'];
                            $selectedJurusan = $_POST['jurusan'] ?? $row['jurusan'];
                            foreach ($jurusans as $j):
                                $sel = ($selectedJurusan == $j) ? 'selected' : '';
                            ?>
                            <option value="<?= $j ?>" <?= $sel ?>><?= $j ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control"
                            value="<?= htmlspecialchars($_POST['email'] ?? $row['email']) ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($_POST['alamat'] ?? $row['alamat']) ?></textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" name="update" class="btn btn-warning-custom px-4">
                        <i class="bi bi-save me-2"></i>Perbarui Data
                    </button>
                    <a href="../index.php" class="btn btn-outline-secondary px-4">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('fotoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (ev) => {
            const preview = document.getElementById('newPhotoPreview');
            preview.src = ev.target.result;
            preview.style.display = 'block';
            const cur = document.getElementById('currentPhoto');
            if (cur) cur.style.opacity = '0.4';
        };
        reader.readAsDataURL(file);
    }
});
</script>
</body>
</html>
