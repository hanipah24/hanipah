<div class="modal fade" id="modalDetail<?php echo $row['PenjualanID']; ?>" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="modalDetailLabel">
          Detail Transaksi #<?php echo $row['PenjualanID']; ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <p><strong>Tanggal:</strong> 
          <?php echo date('d M Y H:i', strtotime($row['TanggalPenjualan'])); ?>
        </p>
        <p><strong>Petugas:</strong> 
          <?php echo htmlspecialchars($row['NamaPetugas']); ?>
        </p>
        <p><strong>Pelanggan:</strong> 
          <?php echo htmlspecialchars($row['NamaPelanggan']); ?>
        </p>

        <hr>
        <h6>Item Penjualan:</h6>

        <div class="table-responsive">
          <table class="table table-bordered table-sm">
            <thead>
              <tr>
                <th>Produk</th>
                <th>Harga Satuan</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $detail_query = "
                SELECT d.JumlahProduk, d.Subtotal, p.NamaProduk, p.Harga 
                FROM detailpenjualan d
                JOIN produk p ON d.ProdukID = p.ProdukID
                WHERE d.PenjualanID = {$row['PenjualanID']}
              ";

              $detail_result = $koneksi->query($detail_query);

              if ($detail_result && $detail_result->num_rows > 0):
                while ($detail = $detail_result->fetch_assoc()):
              ?>
                <tr>
                  <td><?php echo htmlspecialchars($detail['NamaProduk']); ?></td>
                  <td>Rp <?php echo number_format($detail['Harga'], 0, ',', '.'); ?></td>
                  <td><?php echo $detail['JumlahProduk']; ?></td>
                  <td>Rp <?php echo number_format($detail['Subtotal'], 0, ',', '.'); ?></td>
                </tr>
              <?php
                endwhile;
              else:
              ?>
                <tr>
                  <td colspan="4" class="text-center">Tidak ada detail produk.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <h5 class="text-end mt-3">
          Total Transaksi: 
          <span class="text-danger">
            Rp <?php echo number_format($row['TotalHarga'], 0, ',', '.'); ?>
          </span>
        </h5>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>

    </div>
  </div>
</div>

<?php 