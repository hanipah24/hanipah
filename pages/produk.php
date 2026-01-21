<?php
require_once '../includes/header.php';

require_once '../includes/koneksi.php';

$is_admin = ($_SESSION['role'] == 'Administrator');

$pesan_status = '';
if (isset($_SESSION['status_produk'])) {
    $pesan_status = $_SESSION['status_produk'];
    unset($_SESSION['status_produk']);
}

$query = "SELECT * FROM produk ORDER BY NamaProduk ASC";
$result = $koneksi->query($query);
?>

<div class="container-fluid py-4">
    <h1 class="mb-4"><i class="fas fa-box-open me-2"></i>Pendataan & Stok Barang</h1>

    <?php 
    if (!empty($pesan_status)) {
        $alert_class = (strpos($pesan_status, 'Sukses') !== false) ? 'alert-success' : 'alert-danger';
        echo '<div class="alert ' . $alert_class . ' alert-dismissible fade show" role="alert">';
        echo $pesan_status;
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
    ?>

    <?php if ($is_admin):  ?>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambahProduk">
            <i class="fas fa-plus me-1"></i> Tambah Produk Baru
        </button>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-header bg-light">Daftar Produk yang Tersedia</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <?php if ($is_admin): ?>
                                <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if ($result->num_rows > 0): 
                            while ($row = $result->fetch_assoc()): 
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['NamaProduk']); ?></td>
                                <td><?php echo 'Rp ' . number_format($row['Harga'], 0, ',', '.'); ?></td>
                                <td>
                                    <span class="badge <?php echo ($row['Stok'] < 10) ? 'bg-warning text-dark' : 'bg-success'; ?>">
                                        <?php echo $row['Stok']; ?>
                                    </span>
                                </td>

                                <?php if ($is_admin): ?>
                                    <td>
                                        <button 
                                            class="btn btn-sm btn-warning edit-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditProduk"
                                            data-id="<?php echo $row['ProdukID']; ?>"
                                            data-nama="<?php echo htmlspecialchars($row['NamaProduk']); ?>"
                                            data-harga="<?php echo $row['Harga']; ?>"
                                            data-stok="<?php echo $row['Stok']; ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>

                                        <a href="../actions/produk_process.php?action=delete&id=<?php echo $row['ProdukID']; ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini? Aksi ini tidak dapat dibatalkan!');">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php 
                            endwhile;
                        else: 
                        ?>
                            <tr>
                                <td colspan="<?php echo $is_admin ? '5' : '4'; ?>" class="text-center">Tidak ada data produk.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
if ($is_admin) {
    include 'modals/modal_produk.php';
}

$koneksi->close();

require_once '../includes/footer.php';
?>