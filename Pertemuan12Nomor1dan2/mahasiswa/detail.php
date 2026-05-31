<?php
include_once(__DIR__ . "/../config.php");
requireLogin();

if (!isset($_GET['id'])) { header('Location: ../index.php'); exit(); }
$id     = (int)$_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM mahasiswa WHERE id=$id");
if (mysqli_num_rows($result) == 0) { header('Location: ../index.php'); exit(); }
$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Mahasiswa - <?= htmlspecialchars($row['nama']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .navbar { background: linear-gradient(135deg, #1a237e, #0d47a1) !important; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); }
        .profile-photo { width: 160px; height: 160px; object-fit: cover; border-radius: 50%; border: 4px solid #e8eaf6; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
        .profile-placeholder { width: 160px; height: 160px; border-radius: 50%; background: linear-gradient(135deg, #e8eaf6, #c5cae9); display: flex; align-items: center; justify-content: center; color: #7986cb; font-size: 5rem; border: 4px solid #e8eaf6; }
        .info-label { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.8px; color: #9e9e9e; font-weight: 600; }
        .info-value { font-size: 1rem; color: #212121; font-weight: 500; }
        .badge-jurusan { background-color: #e8eaf6; color: #3949ab; font-weight: 600; padding: 6px 14px; border-radius: 20px; font-size: 0.85rem; }
        .profile-header { background: linear-gradient(135deg, #1a237e, #0d47a1); border-radius: 12px 12px 0 0; color: white; padding: 2.5rem; text-align: center; }
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

<div class="container" style="max-width:680px">
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="../index.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
        <h5 class="mb-0 fw-bold">Detail Mahasiswa</h5>
    </div>

    <div class="card overflow-hidden">
        <div class="profile-header">
            <?php if ($row['foto']): ?>
                <img src="<?= photoUrl($row['foto']) ?>" class="profile-photo mb-3" alt="Foto">
            <?php else: ?>
                <div class="profile-placeholder mx-auto mb-3"><i class="bi bi-person-fill"></i></div>
            <?php endif; ?>
            <h4 class="mb-1 fw-bold"><?= htmlspecialchars($row['nama']) ?></h4>
            <p class="mb-2 opacity-75"><?= htmlspecialchars($row['nim']) ?></p>
            <span class="badge bg-white text-primary fw-semibold px-3 py-2 rounded-pill">
                <?= htmlspecialchars($row['jurusan']) ?>
            </span>
        </div>

        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="info-label"><i class="bi bi-123 me-1"></i>NIM</div>
                    <div class="info-value"><?= htmlspecialchars($row['nim']) ?></div>
                </div>
                <div class="col-md-6">
                    <div class="info-label"><i class="bi bi-person me-1"></i>Nama Lengkap</div>
                    <div class="info-value"><?= htmlspecialchars($row['nama']) ?></div>
                </div>
                <div class="col-md-6">
                    <div class="info-label"><i class="bi bi-book me-1"></i>Jurusan</div>
                    <div class="info-value">
                        <span class="badge-jurusan"><?= htmlspecialchars($row['jurusan']) ?></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-label"><i class="bi bi-envelope me-1"></i>Email</div>
                    <div class="info-value">
                        <a href="mailto:<?= htmlspecialchars($row['email']) ?>" class="text-decoration-none">
                            <?= htmlspecialchars($row['email']) ?>
                        </a>
                    </div>
                </div>
                <div class="col-12">
                    <div class="info-label"><i class="bi bi-geo-alt me-1"></i>Alamat</div>
                    <div class="info-value"><?= $row['alamat'] ? htmlspecialchars($row['alamat']) : '<span class="text-muted fst-italic">Belum diisi</span>' ?></div>
                </div>
                <div class="col-md-6">
                    <div class="info-label"><i class="bi bi-calendar me-1"></i>Tanggal Daftar</div>
                    <div class="info-value"><?= date('d F Y, H:i', strtotime($row['created_at'])) ?> WIB</div>
                </div>
                <div class="col-md-6">
                    <div class="info-label"><i class="bi bi-image me-1"></i>Foto</div>
                    <div class="info-value"><?= $row['foto'] ? '<span class="text-success"><i class="bi bi-check-circle me-1"></i>Ada</span>' : '<span class="text-muted"><i class="bi bi-x-circle me-1"></i>Belum ada</span>' ?></div>
                </div>
            </div>

            <hr class="my-4">
            <div class="d-flex gap-2">
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning text-white px-4">
                    <i class="bi bi-pencil me-2"></i>Edit Data
                </a>
                <a href="../index.php" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
