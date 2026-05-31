<?php
include_once(__DIR__ . "/config.php");
requireLogin();

$limit  = 5;
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where  = '';
if (!empty($search)) {
    $where = "WHERE nim LIKE '%$search%' OR nama LIKE '%$search%'
              OR jurusan LIKE '%$search%' OR email LIKE '%$search%'";
}

$count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM mahasiswa $where");
$total_data   = mysqli_fetch_assoc($count_result)['total'];
$total_pages  = ceil($total_data / $limit);

$query  = "SELECT * FROM mahasiswa $where ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .navbar-brand { font-weight: 700; letter-spacing: 0.5px; }
        .navbar { background: linear-gradient(135deg, #1a237e, #0d47a1) !important; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); }
        .table thead th { background: linear-gradient(135deg, #1a237e, #0d47a1); color: white; border: none; font-weight: 600; font-size: 0.875rem; }
        .table tbody tr:hover { background-color: #f8f9ff; }
        .photo-thumb { width: 45px; height: 45px; object-fit: cover; border-radius: 50%; border: 2px solid #e0e0e0; }
        .no-photo { width: 45px; height: 45px; border-radius: 50%; background: #e8eaf6; display: flex; align-items: center; justify-content: center; color: #9fa8da; font-size: 1.2rem; }
        .badge-jurusan { background-color: #e8eaf6; color: #3949ab; font-weight: 500; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; }
        .btn-sm { font-size: 0.78rem; }
        .page-link { color: #1a237e; }
        .page-item.active .page-link { background-color: #1a237e; border-color: #1a237e; }
        .stat-card { background: white; border-radius: 12px; padding: 1.25rem; box-shadow: 0 2px 10px rgba(0,0,0,0.06); }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
    </style>
</head>
<body>
<nav class="navbar navbar-dark navbar-expand-lg mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-mortarboard-fill me-2"></i>Sistem Mahasiswa
        </a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="text-white-50 small"><i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['full_name']) ?></span>
            <a href="auth/logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid px-4">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#e8eaf6;color:#3949ab"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="text-muted small">Total Mahasiswa</div>
                    <div class="fw-bold fs-5"><?= $total_data ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32"><i class="bi bi-book-fill"></i></div>
                <div>
                    <div class="text-muted small">Halaman</div>
                    <div class="fw-bold fs-5"><?= $page ?> / <?= max(1, $total_pages) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-4">
                <h5 class="mb-0 fw-bold"><i class="bi bi-table me-2 text-primary"></i>Daftar Data Mahasiswa</h5>
                <div class="d-flex gap-2">
                    <form method="GET" class="d-flex gap-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari NIM, nama, jurusan..." value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-outline-primary" type="submit">Cari</button>
                            <?php if ($search): ?>
                                <a href="index.php" class="btn btn-outline-secondary">Reset</a>
                            <?php endif; ?>
                        </div>
                    </form>
                    <a href="mahasiswa/tambah.php" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Data
                    </a>
                </div>
            </div>

            <?php if (!empty($search)): ?>
                <div class="alert alert-info py-2 small mb-3">
                    <i class="bi bi-info-circle me-1"></i>Menampilkan hasil pencarian untuk: <strong><?= htmlspecialchars($search) ?></strong>
                    (<?= $total_data ?> data ditemukan)
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px">#</th>
                            <th style="width:60px">Foto</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Jurusan</th>
                            <th>Email</th>
                            <th style="width:160px" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = $offset + 1;
                        while ($row = mysqli_fetch_assoc($result)):
                        ?>
                        <tr>
                            <td class="text-muted small"><?= $no++ ?></td>
                            <td>
                                <?php if ($row['foto']): ?>
                                    <img src="<?= photoUrl($row['foto']) ?>" class="photo-thumb" alt="Foto">
                                <?php else: ?>
                                    <div class="no-photo"><i class="bi bi-person"></i></div>
                                <?php endif; ?>
                            </td>
                            <td><code class="text-primary"><?= htmlspecialchars($row['nim']) ?></code></td>
                            <td class="fw-semibold"><?= htmlspecialchars($row['nama']) ?></td>
                            <td><span class="badge-jurusan"><?= htmlspecialchars($row['jurusan']) ?></span></td>
                            <td class="text-muted small"><?= htmlspecialchars($row['email']) ?></td>
                            <td class="text-center">
                                <a href="mahasiswa/detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="mahasiswa/edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="mahasiswa/hapus.php?id=<?= $row['id'] ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   title="Hapus"
                                   onclick="return confirm('Yakin ingin menghapus data <?= htmlspecialchars(addslashes($row['nama'])) ?>?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if ($total_data == 0): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox" style="font-size:2.5rem;display:block;margin-bottom:0.5rem"></i>
                                <?= $search ? 'Tidak ada data yang cocok dengan pencarian.' : 'Belum ada data mahasiswa.' ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination pagination-sm justify-content-center mb-0">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>

        </div>
    </div>
    <p class="text-center text-muted small mt-3">Menampilkan <?= min($limit, $total_data - $offset) ?> dari <?= $total_data ?> data</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
