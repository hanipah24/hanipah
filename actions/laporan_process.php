<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php");
    exit;
}

require_once '../includes/koneksi.php';

if ($_SESSION['role'] !== 'Administrator') {
    $_SESSION['status_laporan'] = "Error: Hanya Administrator yang berhak menghapus transaksi.";
    header("Location: ../pages/laporan.php");
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $penjualan_id = (int)$_GET['id'];

    $koneksi->autocommit(FALSE);
    $status = '';

    try {
        $detail_query = "SELECT ProdukID, JumlahProduk FROM detailpenjualan WHERE PenjualanID = ?";
        $stmt_detail = $koneksi->prepare($detail_query);
        $stmt_detail->bind_param("i", $penjualan_id);
        $stmt_detail->execute();
        $result_detail = $stmt_detail->get_result();
        $stmt_detail->close();

        while ($row = $result_detail->fetch_assoc()) {
            $sql_update_stok = "UPDATE produk SET Stok = Stok + ? WHERE ProdukID = ?";
            $stmt_stok = $koneksi->prepare($sql_update_stok);
            $stmt_stok->bind_param("ii", $row['JumlahProduk'], $row['ProdukID']);

            if (!$stmt_stok->execute()) {
                throw new Exception("Gagal mengembalikan stok produk ID {$row['ProdukID']}.");
            }

            $stmt_stok->close();
        }

        $sql_delete_detail = "DELETE FROM detailpenjualan WHERE PenjualanID = ?";
        $stmt_del_detail = $koneksi->prepare($sql_delete_detail);
        $stmt_del_detail->bind_param("i", $penjualan_id);

        if (!$stmt_del_detail->execute()) {
            throw new Exception("Gagal menghapus detail penjualan.");
        }

        $stmt_del_detail->close();

        $sql_delete_penjualan = "DELETE FROM penjualan WHERE PenjualanID = ?";
        $stmt_del_penjualan = $koneksi->prepare($sql_delete_penjualan);
        $stmt_del_penjualan->bind_param("i", $penjualan_id);

        if (!$stmt_del_penjualan->execute()) {
            throw new Exception("Gagal menghapus data penjualan utama.");
        }

        $stmt_del_penjualan->close();

        $koneksi->commit();
        $status = "Sukses: Transaksi #{$penjualan_id} berhasil dihapus dan stok telah dikembalikan.";

    } catch (Exception $e) {
        $koneksi->rollback();
        $status = "Error: Transaksi gagal dihapus! " . $e->getMessage();
    }

    $koneksi->autocommit(TRUE);
    $_SESSION['status_laporan'] = $status;
}

$koneksi->close();
header("Location: ../pages/laporan.php");
exit;
?>