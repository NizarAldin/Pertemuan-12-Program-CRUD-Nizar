<?php
include_once(__DIR__ . "/../config.php");
if (isLoggedIn()) { header('Location: ../index.php'); exit(); }

$error = '';
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query  = "SELECT * FROM users WHERE username='$username' OR email='$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            header('Location: ../index.php');
            exit();
        } else {
            $error = "Username atau password salah!";
        }
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1a237e 0%, #0d47a1 100%); min-height: 100vh; display: flex; align-items: center; }
        .login-card { border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); border: none; }
        .login-header { background: linear-gradient(135deg, #1a237e, #0d47a1); border-radius: 16px 16px 0 0; padding: 2rem; text-align: center; color: white; }
        .btn-login { background: linear-gradient(135deg, #1a237e, #0d47a1); border: none; padding: 12px; font-weight: 600; letter-spacing: 0.5px; }
        .btn-login:hover { background: linear-gradient(135deg, #0d47a1, #1565c0); }
        .form-control:focus { border-color: #1a237e; box-shadow: 0 0 0 0.25rem rgba(26,35,126,0.2); }
        .brand-icon { font-size: 3rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card login-card">
                <div class="login-header">
                    <div class="brand-icon"><i class="bi bi-mortarboard-fill"></i></div>
                    <h4 class="mb-1 fw-bold">Sistem Mahasiswa</h4>
                    <p class="mb-0 opacity-75 small">Masuk ke akun Anda</p>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Username / Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                            </div>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary btn-login w-100 text-white">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                        </button>
                    </form>
                    <hr class="my-3">
                    <p class="text-center text-muted small mb-0">
                        Belum punya akun? <a href="register.php" class="text-decoration-none fw-semibold">Daftar di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
