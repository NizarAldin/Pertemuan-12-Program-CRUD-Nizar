<?php
include_once(__DIR__ . "/../config.php");
if (isLoggedIn()) { header('Location: ../index.php'); exit(); }

$errors  = [];
$success = '';

if (isset($_POST['register'])) {
    $username  = mysqli_real_escape_string($conn, $_POST['username']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $password  = $_POST['password'];
    $confirm   = $_POST['confirm_password'];

    if (empty($username))  $errors[] = 'Username tidak boleh kosong.';
    if (empty($email))     $errors[] = 'Email tidak boleh kosong.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
    if (empty($full_name)) $errors[] = 'Nama lengkap tidak boleh kosong.';
    if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';
    if ($password !== $confirm)  $errors[] = 'Konfirmasi password tidak cocok.';

    if (empty($errors)) {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' OR email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $errors[] = 'Username atau email sudah terdaftar.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql    = "INSERT INTO users (username, email, full_name, password) VALUES ('$username','$email','$full_name','$hashed')";
            if (mysqli_query($conn, $sql))
                $success = 'Registrasi berhasil! Silakan login.';
            else
                $errors[] = 'Error: ' . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1a237e 0%, #0d47a1 100%); min-height: 100vh; display: flex; align-items: center; padding: 2rem 0; }
        .card { border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); border: none; }
        .card-header { background: linear-gradient(135deg, #1a237e, #0d47a1); border-radius: 16px 16px 0 0; padding: 2rem; text-align: center; color: white; }
        .btn-primary { background: linear-gradient(135deg, #1a237e, #0d47a1); border: none; }
        .btn-primary:hover { background: linear-gradient(135deg, #0d47a1, #1565c0); }
        .form-control:focus { border-color: #1a237e; box-shadow: 0 0 0 0.25rem rgba(26,35,126,0.2); }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-person-plus-fill" style="font-size:2.5rem"></i>
                    <h4 class="mb-0 mt-2 fw-bold">Buat Akun Baru</h4>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2"></i><?= $success ?>
                            <a href="login.php" class="ms-auto btn btn-sm btn-success">Login</a>
                        </div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                <input type="text" name="full_name" class="form-control" placeholder="Nama lengkap Anda"
                                    value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-at"></i></span>
                                <input type="text" name="username" class="form-control" placeholder="Pilih username"
                                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="email@example.com"
                                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Konfirmasi Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password" required>
                            </div>
                        </div>
                        <button type="submit" name="register" class="btn btn-primary w-100 py-2 text-white fw-semibold">
                            <i class="bi bi-person-check me-2"></i>Daftar Sekarang
                        </button>
                    </form>
                    <hr class="my-3">
                    <p class="text-center text-muted small mb-0">
                        Sudah punya akun? <a href="login.php" class="text-decoration-none fw-semibold">Login di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
