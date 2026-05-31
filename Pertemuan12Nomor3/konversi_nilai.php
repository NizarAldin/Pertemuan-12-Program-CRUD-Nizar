<?php
function bersihkan($input): string {
    return htmlspecialchars(stripslashes(trim($input)));
}

$nilai  = null;
$grade  = null;
$deskripsi = null;
$warna  = null;
$error  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw   = $_POST['nilai'] ?? '';
    $nilai = bersihkan($raw);

    if ($nilai === '' || !is_numeric($nilai)) {
        $error = 'Masukkan nilai angka yang valid.';
    } else {
        $nilai = (int)$nilai;
        if ($nilai < 0 || $nilai > 100) {
            $error = 'Nilai harus berada di antara 0 dan 100.';
        } else {
            if ($nilai >= 85) {
                $grade     = 'A';
                $deskripsi = 'Sangat Baik - Lulus dengan predikat istimewa.';
                $warna     = '#1b5e20';
                $bg        = '#e8f5e9';
                $border    = '#4caf50';
            } elseif ($nilai >= 75) {
                $grade     = 'B';
                $deskripsi = 'Baik - Lulus dengan hasil yang memuaskan.';
                $warna     = '#1565c0';
                $bg        = '#e3f2fd';
                $border    = '#42a5f5';
            } elseif ($nilai >= 65) {
                $grade     = 'C';
                $deskripsi = 'Cukup - Lulus namun perlu peningkatan.';
                $warna     = '#e65100';
                $bg        = '#fff3e0';
                $border    = '#ffa726';
            } elseif ($nilai >= 55) {
                $grade     = 'D';
                $deskripsi = 'Kurang - Tidak lulus, perlu mengulang.';
                $warna     = '#b71c1c';
                $bg        = '#fce4ec';
                $border    = '#ef5350';
            } else {
                $grade     = 'E';
                $deskripsi = 'Sangat Kurang - Tidak lulus, wajib mengulang.';
                $warna     = '#4a148c';
                $bg        = '#f3e5f5';
                $border    = '#ab47bc';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konversi Nilai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1a237e 0%, #283593 100%); min-height: 100vh; display: flex; align-items: center; }
        .card { border: none; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.25); }
        .card-header { background: linear-gradient(135deg, #1a237e, #0d47a1); color: white; border-radius: 16px 16px 0 0 !important; padding: 1.75rem; }
        .btn-primary { background: linear-gradient(135deg, #1a237e, #0d47a1); border: none; }
        .grade-box { border-radius: 12px; padding: 1.5rem; border-left: 5px solid; transition: all 0.3s; }
        .grade-letter { font-size: 4rem; font-weight: 900; line-height: 1; }
        .form-range::-webkit-slider-thumb { background: #1a237e; }
        .tabel-grade th { background: #e8eaf6; }
        .tabel-grade td, .tabel-grade th { padding: 0.5rem 0.8rem; font-size:0.875rem; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card">
                <div class="card-header text-center">
                    <i class="bi bi-award-fill" style="font-size:2.5rem"></i>
                    <h4 class="mb-0 mt-2 fw-bold">Konversi Nilai</h4>
                    <p class="mb-0 opacity-75 small">Masukkan nilai angka untuk melihat grade huruf</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nilai Angka (0 - 100)</label>
                            <input type="number" name="nilai" class="form-control form-control-lg text-center fw-bold"
                                   placeholder="Contoh: 78" min="0" max="100"
                                   value="<?= $nilai !== null && !$error ? htmlspecialchars($nilai) : '' ?>"
                                   required>
                        </div>
                        <?php if ($error): ?>
                            <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-triangle me-1"></i><?= $error ?></div>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary w-100 py-2 text-white fw-semibold">
                            <i class="bi bi-calculator me-2"></i>Konversi Nilai
                        </button>
                    </form>

                    <?php if ($grade !== null): ?>
                    <div class="grade-box mt-4" style="background:<?= $bg ?>;border-color:<?= $border ?>;color:<?= $warna ?>">
                        <div class="d-flex align-items-center gap-3">
                            <div class="grade-letter"><?= $grade ?></div>
                            <div>
                                <div class="fw-bold fs-5">Nilai: <?= $nilai ?></div>
                                <div class="fw-semibold"><?= $deskripsi ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="mt-4">
                        <p class="fw-semibold small text-muted mb-2"><i class="bi bi-table me-1"></i>Tabel Referensi Grade</p>
                        <table class="table table-bordered tabel-grade mb-0 rounded overflow-hidden">
                            <thead>
                                <tr><th>Nilai</th><th>Grade</th><th>Keterangan</th></tr>
                            </thead>
                            <tbody>
                                <tr style="color:#1b5e20;background:#f1f8e9"><td>85 - 100</td><td class="fw-bold">A</td><td>Sangat Baik</td></tr>
                                <tr style="color:#1565c0;background:#e8f4fd"><td>75 - 84</td><td class="fw-bold">B</td><td>Baik</td></tr>
                                <tr style="color:#e65100;background:#fff8e1"><td>65 - 74</td><td class="fw-bold">C</td><td>Cukup</td></tr>
                                <tr style="color:#b71c1c;background:#fdf0f0"><td>55 - 64</td><td class="fw-bold">D</td><td>Kurang</td></tr>
                                <tr style="color:#4a148c;background:#f8f0ff"><td>0 - 54</td><td class="fw-bold">E</td><td>Sangat Kurang</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
