<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php");
    exit;
}

require_once '../includes/koneksi.php';

if ($_SESSION['role'] !== 'Administrator') {
    $_SESSION['status_pelanggan'] = "Error: Anda tidak memiliki hak akses untuk memodifikasi data pelanggan.";
    header("Location: ../pages/pelanggan.php");
    exit;
}

$action = $_REQUEST['action'] ?? '';
$status = '';

try {
    if ($action == 'tambah') {
        $nama_pelanggan = trim($_POST['nama_pelanggan']);
        $alamat = trim($_POST['alamat']);
        $telepon = trim($_POST['telepon']);

        $sql = "INSERT INTO pelanggan (NamaPelanggan, Alamat, NomorTelepon) VALUES (?, ?, ?)";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("sss", $nama_pelanggan, $alamat, $telepon);

        if ($stmt->execute()) {
            $status = "Sukses: Pelanggan '{$nama_pelanggan}' berhasil ditambahkan!";
        } else {
            throw new Exception("Gagal menyimpan data ke database.");
        }

        $stmt->close();

    } elseif ($action == 'edit') {
        $id = $_POST['pelanggan_id'];
        $nama_pelanggan = trim($_POST['nama_pelanggan']);
        $alamat = trim($_POST['alamat']);
        $telepon = trim($_POST['telepon']);

        $sql = "UPDATE pelanggan SET NamaPelanggan = ?, Alamat = ?, NomorTelepon = ? WHERE PelangganID = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("sssi", $nama_pelanggan, $alamat, $telepon, $id);

        if ($stmt->execute()) {
            $status = "Sukses: Data pelanggan ID {$id} berhasil diupdate!";
        } else {
            throw new Exception("Gagal mengupdate data pelanggan.");
        }

        $stmt->close();

    } elseif ($action == 'delete') {
        $id = $_GET['id'];

        $cek_transaksi_sql = "SELECT COUNT(*) FROM penjualan WHERE PelangganID = ?";
        $stmt_cek = $koneksi->prepare($cek_transaksi_sql);
        $stmt_cek->bind_param("i", $id);
        $stmt_cek->execute();
        $stmt_cek->bind_result($count);
        $stmt_cek->fetch();
        $stmt_cek->close();

        if ($count > 0) {
            throw new Exception("Pelanggan tidak dapat dihapus karena memiliki riwayat {$count} transaksi penjualan.");
        }

        // Jika tidak ada transaksi, hapus data pelanggan
        $sql = "DELETE FROM pelanggan WHERE PelangganID = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $status = "Sukses: Pelanggan ID {$id} berhasil dihapus!";
        } else {
            throw new Exception("Gagal menghapus pelanggan.");
        }

        $stmt->close();
    }
} catch (Exception $e) {
    // Tangani error umum
    $status = "Error: " . $e->getMessage();
}

// Simpan status ke sesi untuk ditampilkan di halaman pelanggan
$_SESSION['status_pelanggan'] = $status;

// Tutup koneksi dan arahkan kembali ke halaman pelanggan
$koneksi->close();
header("Location: ../pages/pelanggan.php");
exit;
?>