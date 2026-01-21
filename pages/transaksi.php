<?php
require_once '../includes/header.php';
require_once '../includes/koneksi.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$pesan_status = '';
if (isset($_SESSION['transaksi_status'])) {
    $pesan_status = $_SESSION['transaksi_status'];
    unset($_SESSION['transaksi_status']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_item') {
    $produk_id = (int)$_POST['produk_id'];
    $jumlah = (int)$_POST['jumlah'];

    $sql = "SELECT ProdukID, NamaProduk, Harga, Stok FROM produk WHERE ProdukID = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $produk_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $produk = $result->fetch_assoc();

    if ($produk) {
        $stok_saat_ini_di_cart = $_SESSION['cart'][$produk_id]['jumlah'] ?? 0;
        if ($stok_saat_ini_di_cart + $jumlah > $produk['Stok']) {
            $_SESSION['transaksi_status'] = "Error: Stok produk {$produk['NamaProduk']} tidak mencukupi.";
        } else {
            if (isset($_SESSION['cart'][$produk_id])) {
                $_SESSION['cart'][$produk_id]['jumlah'] += $jumlah;
            } else {
                $_SESSION['cart'][$produk_id] = [
                    'id' => $produk_id,
                    'nama' => $produk['NamaProduk'],
                    'harga' => $produk['Harga'],
                    'stok_tersedia' => $produk['Stok'],
                    'jumlah' => $jumlah
                ];
            }
            $_SESSION['transaksi_status'] = "Sukses: Produk {$produk['NamaProduk']} ditambahkan ke keranjang.";
        }
    } else {
        $_SESSION['transaksi_status'] = "Error: Produk tidak ditemukan.";
    }

    header("Location: transaksi.php");
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'remove_item' && isset($_GET['id'])) {
    $produk_id_hapus = (int)$_GET['id'];
    if (isset($_SESSION['cart'][$produk_id_hapus])) {
        unset($_SESSION['cart'][$produk_id_hapus]);
        $_SESSION['transaksi_status'] = "Sukses: Produk dihapus dari keranjang.";
    }
    header("Location: transaksi.php");
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'clear_cart') {
    $_SESSION['cart'] = [];
    $_SESSION['transaksi_status'] = "Sukses: Keranjang dibersihkan.";
    header("Location: transaksi.php");
    exit;
}

$subtotal_belanja = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal_belanja += $item['harga'] * $item['jumlah'];
}
?>

<div class="container-fluid py-4">
    <h1 class="mb-4"><i class="fas fa-cash-register me-2"></i>Transaksi Penjualan</h1>

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
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header bg-dark text-white">Pencarian Produk</div>
                <div class="card-body">
                    <form action="transaksi.php" method="POST" id="formAddItem">
                        <input type="hidden" name="action" value="add_item">
                        <div class="mb-3">
                            <label for="produk_id" class="form-label">Scan Barcode / ID Produk</label>
                            <input type="text" class="form-control" id="produk_input" name="produk_id"
                                   placeholder="Masukkan ID Produk..." required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" value="1" min="1" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Tambahkan ke Keranjang</button>
                    </form>
                    <hr>
                    <form action="transaksi.php" method="GET">
                        <label for="pelanggan_id" class="form-label">Pelanggan (Opsional)</label>
                        <select class="form-select" name="pelanggan_id" id="pelanggan_id">
                            <option value="">-- Umum --</option>
                            <?php
                            $pelanggan_query = "SELECT PelangganID, NamaPelanggan FROM pelanggan ORDER BY NamaPelanggan ASC";
                            $pelanggan_result = $koneksi->query($pelanggan_query);
                            while ($p = $pelanggan_result->fetch_assoc()):
                            ?>
                                <option value="<?php echo $p['PelangganID']; ?>"
                                    <?php echo (isset($_GET['pelanggan_id']) && $_GET['pelanggan_id'] == $p['PelangganID']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['NamaPelanggan']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-secondary mt-2 w-100">Pilih Pelanggan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow">
                <div class="card-header bg-success text-white">Keranjang Belanja</div>
                <div class="card-body">
                    <?php if (empty($_SESSION['cart'])): ?>
                        <div class="alert alert-warning text-center">
                            Keranjang masih kosong. Silakan tambahkan produk.
                        </div>
                    <?php else: ?>
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_SESSION['cart'] as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['nama']); ?></td>
                                        <td><?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                                        <td><?php echo $item['jumlah']; ?></td>
                                        <td><?php echo number_format($item['harga'] * $item['jumlah'], 0, ',', '.'); ?></td>
                                        <td>
                                            <a href="transaksi.php?action=remove_item&id=<?php echo $item['id']; ?>"
                                               class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-between align-items-center mt-3 p-3 bg-light rounded">
                            <h4 class="mb-0">TOTAL BELANJA:</h4>
                            <h4 class="mb-0 text-danger">Rp <?php echo number_format($subtotal_belanja, 0, ',', '.'); ?></h4>
                        </div>

                        <div class="mt-4">
                            <form action="../actions/transaksi_process.php" method="POST" onsubmit="return validatePayment()">
                                <input type="hidden" name="action" value="finalize_transaction">
                                <input type="hidden" name="total_harga" value="<?php echo $subtotal_belanja; ?>">
                                <input type="hidden" name="pelanggan_id" value="<?php echo $_GET['pelanggan_id'] ?? 0; ?>">

                                <div class="mb-3">
                                    <label for="jumlah_bayar" class="form-label fs-5">Jumlah Bayar (Uang Diterima)</label>
                                    <input type="number" class="form-control form-control-lg text-end" id="jumlah_bayar"
                                           name="jumlah_bayar" min="<?php echo $subtotal_belanja; ?>"
                                           required placeholder="Masukkan jumlah uang...">
                                </div>

                                <div class="alert alert-primary text-end fs-4" role="alert">
                                    Kembalian: <span id="kembalian_display">Rp 0</span>
                                </div>

                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-check-circle me-1"></i> FINALISASI & CETAK STRUK
                                </button>
                            </form>

                            <a href="transaksi.php?action=clear_cart"
                               class="btn btn-outline-danger w-100 mt-2"
                               onclick="return confirm('Yakin ingin membatalkan transaksi ini?')">
                               Batalkan Transaksi
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalHarga = parseFloat(document.querySelector('input[name="total_harga"]')?.value) || 0;
    const inputBayar = document.getElementById('jumlah_bayar');
    const kembalianDisplay = document.getElementById('kembalian_display');

    function hitungKembalian() {
        const jumlahBayar = parseFloat(inputBayar.value) || 0;
        const kembalian = jumlahBayar - totalHarga;
        kembalianDisplay.textContent = 'Rp ' + (kembalian > 0 ? kembalian.toLocaleString('id-ID') : '0');
        if (jumlahBayar < totalHarga) {
            inputBayar.setCustomValidity('Jumlah bayar tidak boleh kurang dari total harga!');
        } else {
            inputBayar.setCustomValidity('');
        }
    }

    if (inputBayar) inputBayar.addEventListener('input', hitungKembalian);
    document.getElementById('produk_input').focus();
});

function validatePayment() {
    const totalHarga = parseFloat(document.querySelector('input[name="total_harga"]').value) || 0;
    const jumlahBayar = parseFloat(document.getElementById('jumlah_bayar').value) || 0;
    if (jumlahBayar < totalHarga) {
        alert("Jumlah bayar harus mencukupi total belanja!");
        return false;
    }
    return true;
}
</script>

<?php
$koneksi->close();
require_once '../includes/footer.php';
?>