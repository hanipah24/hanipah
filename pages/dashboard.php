<?php
require_once '../includes/header.php';

require_once '../includes/koneksi.php';

if (isset($_SESSION['test_success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['test_success'] . '</div>';
    unset($_SESSION['test_success']);
}

if (!isset($_SESSION['userid']) || empty($_SESSION['userid']) || $_SESSION['userid'] == 0) {
    echo '<div class="alert alert-danger">ERROR: Sesi UserID hilang atau bernilai 0 di halaman Dashboard.</div>';
}

$total_transaksi = 0;
$total_produk = 0;
$total_pelanggan = 0;


if ($_SESSION['role'] == 'Administrator') {
    $query_transaksi = "SELECT COUNT(*) AS total FROM penjualan";
    $result_transaksi = $koneksi->query($query_transaksi);
    $data_transaksi = $result_transaksi->fetch_assoc();
    $total_transaksi = $data_transaksi['total'];

    $query_produk = "SELECT COUNT(*) AS total FROM produk";
    $result_produk = $koneksi->query($query_produk);
    $data_produk = $result_produk->fetch_assoc();
    $total_produk = $data_produk['total'];

    $query_pelanggan = "SELECT COUNT(*) AS total FROM pelanggan";
    $result_pelanggan = $koneksi->query($query_pelanggan);
    $data_pelanggan = $result_pelanggan->fetch_assoc();
    $total_pelanggan = $data_pelanggan['total'];

} else {
    $user_id = isset($_SESSION['userid']) ? (int)$_SESSION['userid'] : 0;
    $query_transaksi = "SELECT COUNT(*) AS total FROM penjualan WHERE UserID = $user_id";
    $result_transaksi = $koneksi->query($query_transaksi);
    $data_transaksi = $result_transaksi->fetch_assoc();
    $total_transaksi = $data_transaksi['total'];
}

$koneksi->close();
?>

<div class="container-fluid py-4">
    <h4>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h4>
    <p>Anda login sebagai <strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong>.</p>
    <hr>

    <h1 class="mb-4">Dashboard</h1>

    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Transaksi</h5>
                    <p class="card-text fs-2"><?php echo $total_transaksi; ?></p>
                </div>
            </div>
        </div>

        <?php if ($_SESSION['role'] == 'Administrator'): ?>
    
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Produk</h5>
                        <p class="card-text fs-2"><?php echo $total_produk; ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Pelanggan</h5>
                        <p class="card-text fs-2"><?php echo $total_pelanggan; ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>