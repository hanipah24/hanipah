<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: pages/dashboard.php");
    exit;
}
    
require_once 'includes/koneksi.php'; 
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Aplikasi Kasir</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
        rel="stylesheet">
        <style>
            body {
            background-color: #e6ded3ff; /* Warna latar belakang ringan */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            }
            .login-container {
            width: 100%;
            max-width: 400px;
            padding: 15px;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">APLIKASI KASIR</h4>
                    <small>Silahkan masukkan Username dan Password Anda</small>
                </div>
                <div class="card-body">
                <?php
                if (isset($_SESSION['login_error'])) {
                    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['login_error'] . '</div>';
                    unset($_SESSION['login_error']); 
                }
                ?>
                <form action="actions/login_process.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">LOGIN</button>
                </form>
                </div>
                <div class="card-footer text-center text-muted">
                    &copy; 2025 Kasir App
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>