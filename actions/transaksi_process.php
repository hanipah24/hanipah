<?php
session_start();

if (
    !isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true ||
    !isset($_SESSION['cart']) || empty($_SESSION['cart'])
) {
    header("Location: ../pages/transaksi.php");
    exit;
}

require_once '../includes/koneksi.php';

$koneksi->autocommit(FALSE); 

$status = '';
$transaksi_berhasil = true;

try {
    if ($_POST['action'] == 'finalize_transaction') {
        $total_harga = (float)$_POST['total_harga'];
        $jumlah_bayar = (float)$_POST['jumlah_bayar'];
        $pelanggan_id = (int)$_POST['pelanggan_id'];
        $user_id = (int)$_SESSION['userid'];
        $tanggal_penjualan = date("Y-m-d");

        if ($jumlah_bayar < $total_harga) {
            throw new Exception("Jumlah bayar tidak mencukupi total harga.");
        }
        $pelanggan_id_final = ($pelanggan_id == 0) ? NULL : $pelanggan_id;

        $sql_penjualan = "INSERT INTO penjualan (TanggalPenjualan, TotalHarga, PelangganID, UserID) VALUES (?, ?, ?, ?)";
        $stmt_penjualan = $koneksi->prepare($sql_penjualan);
        $stmt_penjualan->bind_param("sdis", $tanggal_penjualan, $total_harga, $pelanggan_id_final, $user_id);

        if (!$stmt_penjualan->execute()) {
            throw new Exception("Gagal menyimpan data penjualan utama. Error: " . $stmt_penjualan->error);
        }

        $penjualan_id = $koneksi->insert_id; 
        $stmt_penjualan->close();

        foreach ($_SESSION['cart'] as $item) {
            $produk_id = $item['id'];
            $jumlah = $item['jumlah'];
            $subtotal = $item['harga'] * $jumlah;

            $sql_detail = "INSERT INTO detailpenjualan (PenjualanID, ProdukID, JumlahProduk, Subtotal) VALUES (?, ?, ?, ?)";
            $stmt_detail = $koneksi->prepare($sql_detail);
            $stmt_detail->bind_param("iiid", $penjualan_id, $produk_id, $jumlah, $subtotal);

            if (!$stmt_detail->execute()) {
                throw new Exception("Gagal menyimpan detail produk '{$item['nama']}'.");
            }

            $stmt_detail->close();


            $sql_update_stok = "UPDATE produk SET Stok = Stok - ? WHERE ProdukID = ? AND Stok >= ?";
            $stmt_stok = $koneksi->prepare($sql_update_stok);
            $stmt_stok->bind_param("iii", $jumlah, $produk_id, $jumlah);

            if (!$stmt_stok->execute() || $koneksi->affected_rows === 0) {
                throw new Exception("Stok produk '{$item['nama']}' tidak cukup atau gagal diupdate.");
            }

            $stmt_stok->close();
        }

        $koneksi->commit();
        $status = "Sukses: Transaksi berhasil diproses! Kembalian: Rp " .
                  number_format($jumlah_bayar - $total_harga, 0, ',', '.');

        $_SESSION['cart'] = [];

    } else {
        $status = "Error: Aksi tidak valid.";
        $transaksi_berhasil = false;
    }

} catch (Exception $e) {
    $koneksi->rollback();
    $status = "Error: Transaksi Gagal! " . $e->getMessage();
    $transaksi_berhasil = false;
} finally {
    $koneksi->autocommit(TRUE);
    $koneksi->close();

    $_SESSION['transaksi_status'] = $status;
    header("Location: ../pages/transaksi.php");
    exit;
}
?>