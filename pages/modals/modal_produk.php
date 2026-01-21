<div class="modal fade" id="modalTambahProduk" tabindex="-1" aria-labelledby="modalTambahProdukLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="../actions/produk_process.php" method="POST">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalTambahProdukLabel">Tambah Produk Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="action" value="tambah">

          <div class="mb-3">
            <label for="nama_produk_tambah" class="form-label">Nama Produk</label>
            <input type="text" class="form-control" id="nama_produk_tambah" name="nama_produk" required>
          </div>

          <div class="mb-3">
            <label for="harga_tambah" class="form-label">Harga Jual (Rp)</label>
            <input type="number" class="form-control" id="harga_tambah" name="harga" required min="100">
          </div>

          <div class="mb-3">
            <label for="stok_tambah" class="form-label">Stok Awal</label>
            <input type="number" class="form-control" id="stok_tambah" name="stok" required min="0">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Produk</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalEditProduk" tabindex="-1" aria-labelledby="modalEditProdukLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="../actions/produk_process.php" method="POST">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title" id="modalEditProdukLabel">Edit Produk</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="action" value="edit">
          <input type="hidden" name="produk_id" id="edit_produk_id">

          <div class="mb-3">
            <label for="nama_produk_edit" class="form-label">Nama Produk</label>
            <input type="text" class="form-control" id="edit_nama_produk" name="nama_produk" required>
          </div>

          <div class="mb-3">
            <label for="harga_edit" class="form-label">Harga Jual (Rp)</label>
            <input type="number" class="form-control" id="edit_harga" name="harga" required min="100">
          </div>

          <div class="mb-3">
            <label for="stok_edit" class="form-label">Stok</label>
            <input type="number" class="form-control" id="edit_stok" name="stok" required min="0">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning">Update Produk</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.edit-btn');
    
    editButtons.forEach(button => {
      button.addEventListener('click', function () {
        const id = this.getAttribute('data-id');
        const nama = this.getAttribute('data-nama');
        const harga = this.getAttribute('data-harga');
        const stok = this.getAttribute('data-stok');

        document.getElementById('edit_produk_id').value = id;
        document.getElementById('edit_nama_produk').value = nama;
        document.getElementById('edit_harga').value = harga;
        document.getElementById('edit_stok').value = stok;
      });
    });
  });
</script>