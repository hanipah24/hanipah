<?php

require_once '../includes/header.php';
require_once '../includes/koneksi.php';

if ($_SESSION['role'] !== 'Administrator') {
    header("Location: dashboard.php");
    exit;
}

$pesan_status = '';
if (isset($_SESSION['status_registrasi'])) {
    $pesan_status = $_SESSION['status_registrasi'];
    unset($_SESSION['status_registrasi']);
}
?>

<div class="container-fluid py-4">
    <h1 class="mb-4"><i class="fas fa-user-plus me-2"></i>Registrasi Pengguna Baru</h1>

    <?php 
    if (!empty($pesan_status)) {
        $alert_class = (strpos($pesan_status, 'Sukses') !== false) ? 'alert-success' : 'alert-danger';
        echo '<div class="alert ' . $alert_class . ' alert-dismissible fade show" role="alert">';
        echo $pesan_status;
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
    ?>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">Formulir Tambah Pengguna</div>
                <div class="card-body">
                    <form action="../actions/registrasi_process.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                            <div class="form-text">Gunakan username yang unik dan mudah diingat.</div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="form-text">Minimal 6 karakter.</div>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Peran (Role)</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="" disabled selected>Pilih Peran</option>
                                <option value="Petugas">Petugas (Kasir)</option>
                                <option value="Administrator">Administrator</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Daftarkan Pengguna
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <?php 
            $user_query = "SELECT UserID, Username, Role FROM pengguna ORDER BY Role DESC, Username ASC";
            $user_result = $koneksi->query($user_query);
            ?>
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">Daftar Pengguna Aktif</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Peran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($u = $user_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($u['Username']); ?></td>
                                        <td>
                                            <span class="badge <?php echo ($u['Role'] == 'Administrator' ? 'bg-danger' : 'bg-success'); ?>">
                                                <?php echo $u['Role']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$koneksi->close();
require_once '../includes/footer.php';
?>