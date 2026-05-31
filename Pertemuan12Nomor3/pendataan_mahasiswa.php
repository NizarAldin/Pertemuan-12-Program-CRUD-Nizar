<?php
function bersihkan($input): string {
    return htmlspecialchars(stripslashes(trim($input)));
}

$errors  = [];
$sukses  = false;
$data    = [];
$predikat = null;
$predikat_warna = null;

$prodi_list = [
    'Teknik Informatika',
    'Sistem Informasi',
    'Teknik Elektro',
    'Teknik Komputer',
    'Manajemen Informatika',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = bersihkan($_POST['nama'] ?? '');
    $nim      = bersihkan($_POST['nim'] ?? '');
    $prodi    = bersihkan($_POST['prodi'] ?? '');
    $ipk      = bersihkan($_POST['ipk'] ?? '');
    $semester = bersihkan($_POST['semester'] ?? '');

    if (empty($nama))     $errors[] = 'Nama tidak boleh kosong.';
    elseif (strlen($nama) < 3) $errors[] = 'Nama minimal 3 karakter.';

    if (empty($nim))      $errors[] = 'NIM tidak boleh kosong.';
    elseif (!preg_match('/^[0-9]{8,12}$/', $nim)) $errors[] = 'NIM harus berupa 8-12 digit angka.';

    if (empty($prodi) || !in_array($prodi, $prodi_list)) $errors[] = 'Pilih program studi yang valid.';

    if ($ipk === '')      $errors[] = 'IPK tidak boleh kosong.';
    elseif (!is_numeric($ipk) || (float)$ipk < 0 || (float)$ipk > 4.00) $errors[] = 'IPK harus berupa angka antara 0.00 dan 4.00.';

    if (empty($semester)) $errors[] = 'Semester tidak boleh kosong.';
    elseif (!is_numeric($semester) || (int)$semester < 1 || (int)$semester > 14) $errors[] = 'Semester harus antara 1 dan 14.';

    if (empty($errors)) {
        $sukses = true;
        $ipk_val = (float)$ipk;
        $data = [
            'nama'     => $nama,
            'nim'      => $nim,
            'prodi'    => $prodi,
            'ipk'      => number_format($ipk_val, 2),
            'semester' => (int)$semester,
        ];

        if ($ipk_val >= 3.51) {
            $predikat       = 'Cumlaude (Dengan Pujian)';
            $predikat_warna = 'success';
            $predikat_icon  = 'trophy-fill';
        } elseif ($ipk_val >= 3.01) {
            $predikat       = 'Sangat Memuaskan';
            $predikat_warna = 'primary';
            $predikat_icon  = 'star-fill';
        } elseif ($ipk_val >= 2.76) {
            $predikat       = 'Memuaskan';
            $predikat_warna = 'info';
            $predikat_icon  = 'patch-check-fill';
        } elseif ($ipk_val >= 2.00) {
            $predikat       = 'Cukup';
            $predikat_warna = 'warning';
            $predikat_icon  = 'check-circle';
        } else {
            $predikat       = 'Belum Memenuhi Syarat Kelulusan';
            $predikat_warna = 'danger';
            $predikat_icon  = 'x-circle-fill';
        }
    } else {
        $data = [
            'nama'     => $nama,
            'nim'      => $nim,
            'prodi'    => $prodi,
            'ipk'      => $ipk,
            'semester' => $semester,
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendataan Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .navbar { background: linear-gradient(135deg, #1a237e, #0d47a1) !important; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); }
        .card-header-main { background: linear-gradient(135deg, #1a237e, #0d47a1); color: white; border-radius: 12px 12px 0 0 !important; padding: 1.5rem; }
        .btn-primary { background: linear-gradient(135deg, #1a237e, #0d47a1); border: none; }
        .result-card { border-radius: 12px; border: 2px solid; }
        .info-row { padding: 0.6rem 0; border-bottom: 1px solid #f0f0f0; }
        .info-row:last-child { border-bottom: none; }
        .required { color: #dc3545; }
    </style>
</head>
<body>
<nav class="navbar navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">
            <i class="bi bi-mortarboard-fill me-2"></i>Pendataan Mahasiswa
        </a>
    </div>
</nav>

<div class="container" style="max-width:800px">
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header-main">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-person-plus me-2"></i>Form Pendataan Mahasiswa</h5>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle me-1"></i>Terdapat kesalahan:</div>
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" novalidate>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="required">*</span></label>
                            <input type="text" name="nama" class="form-control"
                                   placeholder="Masukkan nama lengkap"
                                   value="<?= htmlspecialchars($data['nama'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">NIM <span class="required">*</span></label>
                            <input type="text" name="nim" class="form-control"
                                   placeholder="8-12 digit angka"
                                   value="<?= htmlspecialchars($data['nim'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Program Studi <span class="required">*</span></label>
                            <select name="prodi" class="form-select">
                                <option value="">-- Pilih Program Studi --</option>
                                <?php foreach ($prodi_list as $p): ?>
                                    <option value="<?= $p ?>" <?= (($data['prodi'] ?? '') == $p) ? 'selected' : '' ?>><?= $p ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">IPK <span class="required">*</span></label>
                                <input type="number" name="ipk" class="form-control"
                                       placeholder="0.00 - 4.00" step="0.01" min="0" max="4"
                                       value="<?= htmlspecialchars($data['ipk'] ?? '') ?>">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Semester <span class="required">*</span></label>
                                <select name="semester" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <?php for ($s = 1; $s <= 14; $s++): ?>
                                        <option value="<?= $s ?>" <?= (($data['semester'] ?? '') == $s) ? 'selected' : '' ?>>
                                            Semester <?= $s ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 text-white fw-semibold mt-4">
                            <i class="bi bi-send me-2"></i>Kirim Data
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <?php if ($sukses && !empty($data)): ?>
            <div class="card result-card border-<?= $predikat_warna ?>">
                <div class="card-header bg-<?= $predikat_warna ?> text-white border-0">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-check-circle me-2"></i>Data Berhasil Dikirim</h6>
                </div>
                <div class="card-body p-3">
                    <div class="info-row d-flex justify-content-between">
                        <span class="text-muted small">Nama</span>
                        <span class="fw-semibold"><?= htmlspecialchars($data['nama']) ?></span>
                    </div>
                    <div class="info-row d-flex justify-content-between">
                        <span class="text-muted small">NIM</span>
                        <code><?= htmlspecialchars($data['nim']) ?></code>
                    </div>
                    <div class="info-row d-flex justify-content-between">
                        <span class="text-muted small">Prodi</span>
                        <span class="fw-semibold text-end" style="max-width:55%;font-size:0.85rem"><?= htmlspecialchars($data['prodi']) ?></span>
                    </div>
                    <div class="info-row d-flex justify-content-between">
                        <span class="text-muted small">IPK</span>
                        <span class="fw-bold text-primary"><?= htmlspecialchars($data['ipk']) ?></span>
                    </div>
                    <div class="info-row d-flex justify-content-between">
                        <span class="text-muted small">Semester</span>
                        <span class="fw-semibold"><?= htmlspecialchars($data['semester']) ?></span>
                    </div>
                    <div class="mt-3 p-3 rounded-3 bg-<?= $predikat_warna ?> bg-opacity-10 border border-<?= $predikat_warna ?> text-center">
                        <i class="bi bi-<?= $predikat_icon ?> text-<?= $predikat_warna ?> mb-1" style="font-size:1.5rem;display:block"></i>
                        <div class="fw-bold text-<?= $predikat_warna ?>"><?= $predikat ?></div>
                        <small class="text-muted">Predikat Kelulusan</small>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="card h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center p-4" style="min-height:200px">
                    <i class="bi bi-clipboard-data text-muted" style="font-size:3rem"></i>
                    <p class="text-muted mt-2 mb-0">Data yang Anda isi akan ditampilkan di sini setelah dikirim.</p>
                </div>
            </div>
            <?php endif; ?>

            <div class="card mt-3">
                <div class="card-body p-3">
                    <p class="fw-semibold small text-muted mb-2"><i class="bi bi-info-circle me-1"></i>Predikat Berdasarkan IPK</p>
                    <table class="table table-sm mb-0" style="font-size:0.8rem">
                        <tr><td><span class="badge bg-success">Cumlaude</span></td><td>IPK &ge; 3.51</td></tr>
                        <tr><td><span class="badge bg-primary">Sangat Memuaskan</span></td><td>3.01 - 3.50</td></tr>
                        <tr><td><span class="badge bg-info">Memuaskan</span></td><td>2.76 - 3.00</td></tr>
                        <tr><td><span class="badge bg-warning text-dark">Cukup</span></td><td>2.00 - 2.75</td></tr>
                        <tr><td><span class="badge bg-danger">Belum Lulus</span></td><td>IPK &lt; 2.00</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
