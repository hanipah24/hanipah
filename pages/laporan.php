<?php
require_once '../includes/header.php';
require_once '../includes/koneksi.php';

$is_admin = ($_SESSION['role'] == 'Administrator');
$user_id = isset($_SESSION['userid']) ? (int)$_SESSION['userid'] : 0;

$where_condition = "";
$report_title = "Laporan Penjualan Lengkap";

if (!$is_admin) {
    $where_condition = " WHERE p.UserID = {$user_id}";
    $report_title = "Laporan Transaksi Pribadi (Anda)";
}

$query_transaksi = "
    SELECT 
        p.PenjualanID,
        p.TanggalPenjualan,
        p.TotalHarga,
        u.Username AS NamaPetugas,
        COALESCE(c.NamaPelanggan, 'Umum') AS NamaPelanggan
    FROM penjualan p
    JOIN pengguna u ON p.UserID = u.UserID
    LEFT JOIN pelanggan c ON p.PelangganID = c.PelangganID
    {$where_condition}
    ORDER BY p.TanggalPenjualan DESC, p.PenjualanID DESC
";
$result_transaksi = $koneksi->query($query_transaksi);

$query_rekap = "
    SELECT 
        SUM(p.TotalHarga) AS TotalPendapatan, 
        COUNT(p.PenjualanID) AS JumlahTransaksi
    FROM penjualan p
    {$where_condition}
";
$result_rekap = $koneksi->query($query_rekap);
$rekap_data = $result_rekap->fetch_assoc();

$modal_html_buffer = '';
?>

<div class="container-fluid py-4">
    <h1 class="mb-4">
        <i class="fas fa-chart-line me-2"></i>
        <?php echo $report_title; ?>
    </h1>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-dark">
                <div class="card-body">
                    <h5 class="card-title">Total Pendapatan</h5>
                    <p class="card-text fs-2">
                        Rp <?php echo number_format($rekap_data['TotalPendapatan'] ?? 0, 0, ',', '.'); ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-white bg-secondary">
                <div class="card-body">
                    <h5 class="card-title">Jumlah Transaksi</h5>
                    <p class="card-text fs-2">
                        <?php echo number_format($rekap_data['JumlahTransaksi'] ?? 0, 0, ',', '.'); ?> Transaksi
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-light">Detail Transaksi</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Tanggal</th>
                            <th>Petugas Kasir</th>
                            <th>Pelanggan</th>
                            <th>Total Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_transaksi && $result_transaksi->num_rows > 0): ?>
                            <?php while($row = $result_transaksi->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $row['PenjualanID']; ?></td>
                                    <td><?php echo date('d M Y', strtotime($row['TanggalPenjualan'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['NamaPetugas']); ?></td>
                                    <td><?php echo htmlspecialchars($row['NamaPelanggan']); ?></td>
                                    <td class="fw-bold">Rp <?php echo number_format($row['TotalHarga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <button 
                                            class="btn btn-sm btn-info text-white" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalDetail<?php echo $row['PenjualanID']; ?>">
                                            <i class="fas fa-eye"></i> Detail
                                        </button>

                                        <?php if ($is_admin): ?>
                                            <a href="../actions/laporan_process.php?action=delete&id=<?php echo $row['PenjualanID']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('PERINGATAN! Hapus transaksi akan MENGEMBALIKAN STOK. Lanjutkan?');">
                                               <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <?php
                                ob_start();
                                include 'modals/modal_detail_transaksi.php';
                                $modal_html_buffer .= ob_get_clean();
                                ?>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data transaksi yang tercatat.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php echo $modal_html_buffer; ?>

<?php
$koneksi->close();

require_once '../includes/footer.php';
?>