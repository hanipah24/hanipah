<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: /index.php");
    exit;
}

$user_role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Kasir Sederhana | <?php echo strtoupper($user_role); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <link href="../assets/css/style.css" rel="stylesheet">

    <style>
        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            background-color: #5c554aff;
            padding-top: 56px; 
        }

        .content {
            margin-left: 250px;
            padding: 20px;
        }

        .sidebar .nav-link {
            color: #adb5bd;
        }

        .sidebar .nav-link:hover {
            color: #d4bf9aff;
            background-color: #495057;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <div class="sidebar text-white p-3">
            <h5 class="text-center mb-4 text-warning">KASIR APP</h5>

            <div class="alert alert-info text-center py-1">
                <small>Login sebagai: <?php echo $user_role; ?></small>
            </div>

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">
                        <i class="fas fa-home me-2"></i> Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="transaksi.php">
                        <i class="fas fa-cash-register me-2"></i> Penjualan / Transaksi
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="produk.php">
                        <i class="fas fa-box-open me-2"></i> Pendataan & Stok Barang
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="pelanggan.php">
                        <i class="fas fa-users me-2"></i> Data Pelanggan
                    </a>
                </li>

                <?php if ($user_role == 'Administrator'): ?>
                    <hr class="text-white">

                    <li class="nav-item">
                        <a class="nav-link" href="laporan.php?tipe=lengkap">
                            <i class="fas fa-chart-line me-2"></i> Laporan Lengkap
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="registrasi.php">
                            <i class="fas fa-user-plus me-2"></i> Registrasi Pengguna
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="laporan.php?tipe=terbatas">
                            <i class="fas fa-file-invoice me-2"></i> Laporan Transaksi Pribadi
                        </a>
                    </li>
                <?php endif; ?>

                <hr class="text-white">

                <li class="nav-item">
                    <a class="nav-link" href="../actions/logout_process.php">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>

        <div class="content flex-grow-1">