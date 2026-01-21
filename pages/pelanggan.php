<?php
require_once '../includes/header.php';
require_once '../includes/koneksi.php';

$is_admin = ($_SESSION['role'] == 'Administrator');

$pesan_status = '';
if (isset($_SESSION['status_pelanggan'])) {
    $pesan_status = $_SESSION['status_pelanggan'];
    unset($_SESSION['status_pelanggan']);
}

$query = "SELECT * FROM pelanggan ORDER BY NamaPelanggan ASC";
$result = $koneksi->query($query);
?>

<div class="container-fluid py-4">
    <h1 class="mb-4">
        <i class="fas fa-users me-2"></i>Manajemen Data Pelanggan
    </h1>

    <?php 
    if (!empty($pesan_status)) {
        $alert_class = (strpos($pesan_status, 'Sukses') !== false) ? 'alert-success' : 'alert-danger';
        echo '
        <div class="alert ' . $alert_class . ' alert-dismissible fade show" role="alert">
            ' . $pesan_status . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }
    ?>

    <?php if ($is_admin): ?>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambahPelanggan">
            <i class="fas fa-user-plus me-1"></i> Tambah Pelanggan Baru
        </button>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-header bg-light">Daftar Pelanggan</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Pelanggan</th>
                            <th>Alamat</th>
                            <th>Nomor Telepon</th>
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
                            <td><?php echo htmlspecialchars($row['NamaPelanggan']); ?></td>
                            <td><?php echo htmlspecialchars($row['Alamat']); ?></td>
                            <td><?php echo htmlspecialchars($row['NomorTelepon']); ?></td>
                            <?php if ($is_admin): ?>
                                <td>
                                    <button 
                                        class="btn btn-sm btn-warning edit-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditPelanggan"
                                        data-id="<?php echo $row['PelangganID']; ?>"
                                        data-nama="<?php echo htmlspecialchars($row['NamaPelanggan']); ?>"
                                        data-alamat="<?php echo htmlspecialchars($row['Alamat']); ?>"
                                        data-telepon="<?php echo htmlspecialchars($row['NomorTelepon']); ?>"
                                    >
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <a href="../actions/pelanggan_process.php?action=delete&id=<?php echo $row['PelangganID']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini? Pastikan tidak ada transaksi yang terkait!');">
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
                            <td colspan="<?php echo $is_admin ? '5' : '4'; ?>" class="text-center">
                                Tidak ada data pelanggan.
                            </td>
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
    include 'modals/modal_pelanggan.php';
}

$koneksi->close();
require_once '../includes/footer.php';
?>