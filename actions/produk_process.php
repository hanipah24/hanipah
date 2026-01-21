<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php");
    exit;
}

require_once '../includes/koneksi.php';

if ($_SESSION['role'] !== 'Administrator') {
    $_SESSION['status_produk'] = "Error: Anda tidak memiliki hak akses untuk melakukan aksi ini.";
    header("Location: ../pages/produk.php");
    exit;
}

$action = $_REQUEST['action'] ?? '';
$status = ''; 

try {
    if ($action == 'tambah') {
        $nama_produk = trim($_POST['nama_produk']);
        $harga = $_POST['harga'];
        $stok = $_POST['stok'];

        $sql = "INSERT INTO produk (NamaProduk, Harga, Stok) VALUES (?, ?, ?)";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("sdi", $nama_produk, $harga, $stok); 

        if ($stmt->execute()) {
            $status = "Sukses: Produk '{$nama_produk}' berhasil ditambahkan!";
        } else {
            throw new Exception("Gagal menyimpan data ke database.");
        }

        $stmt->close();

    } elseif ($action == 'edit') {
        $id = $_POST['produk_id'];
        $nama_produk = trim($_POST['nama_produk']);
        $harga = $_POST['harga'];
        $stok = $_POST['stok'];

        $sql = "UPDATE produk SET NamaProduk = ?, Harga = ?, Stok = ? WHERE ProdukID = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("sdii", $nama_produk, $harga, $stok, $id);

        if ($stmt->execute()) {
            $status = "Sukses: Produk ID {$id} berhasil diupdate!";
        } else {
            throw new Exception("Gagal mengupdate data produk.");
        }

        $stmt->close();

    } elseif ($action == 'delete') {
        $id = $_GET['id'];

        $sql = "DELETE FROM produk WHERE ProdukID = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $status = "Sukses: Produk ID {$id} berhasil dihapus!";
        } else {
            throw new Exception("Gagal menghapus produk. Pastikan produk ini tidak terkait dengan data penjualan!");
        }

        $stmt->close();
    }
} catch (Exception $e) {

    $status = "Error: " . $e->getMessage();
}
$_SESSION['status_produk'] = $status;
$koneksi->close();
header("Location: ../pages/produk.php");
exit;
?>