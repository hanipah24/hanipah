<div class="modal fade" id="modalTambahPelanggan" tabindex="-1" aria-labelledby="modalTambahPelangganLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="../actions/pelanggan_process.php" method="POST">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalTambahPelangganLabel">Tambah Pelanggan Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="action" value="tambah">

          <div class="mb-3">
            <label for="nama_pelanggan_tambah" class="form-label">Nama Pelanggan</label>
            <input type="text" class="form-control" id="nama_pelanggan_tambah" name="nama_pelanggan" required>
          </div>

          <div class="mb-3">
            <label for="alamat_tambah" class="form-label">Alamat</label>
            <textarea class="form-control" id="alamat_tambah" name="alamat" rows="3" required></textarea>
          </div>

          <div class="mb-3">
            <label for="telepon_tambah" class="form-label">Nomor Telepon</label>
            <input type="text" class="form-control" id="telepon_tambah" name="telepon" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Pelanggan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalEditPelanggan" tabindex="-1" aria-labelledby="modalEditPelangganLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="../actions/pelanggan_process.php" method="POST">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title" id="modalEditPelangganLabel">Edit Data Pelanggan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="action" value="edit">
          <input type="hidden" name="pelanggan_id" id="edit_pelanggan_id">

          <div class="mb-3">
            <label for="edit_nama_pelanggan" class="form-label">Nama Pelanggan</label>
            <input type="text" class="form-control" id="edit_nama_pelanggan" name="nama_pelanggan" required>
          </div>

          <div class="mb-3">
            <label for="edit_alamat" class="form-label">Alamat</label>
            <textarea class="form-control" id="edit_alamat" name="alamat" rows="3" required></textarea>
          </div>

          <div class="mb-3">
            <label for="edit_telepon" class="form-label">Nomor Telepon</label>
            <input type="text" class="form-control" id="edit_telepon" name="telepon" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning">Update Pelanggan</button>
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
      const alamat = this.getAttribute('data-alamat');
      const telepon = this.getAttribute('data-telepon');

      document.getElementById('edit_pelanggan_id').value = id;
      document.getElementById('edit_nama_pelanggan').value = nama;
      document.getElementById('edit_alamat').value = alamat;
      document.getElementById('edit_telepon').value = telepon;
    });
  });
});
</script>