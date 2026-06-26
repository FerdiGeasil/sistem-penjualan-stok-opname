document.addEventListener('DOMContentLoaded', function () {
  const btnOpen = document.getElementById('btnOpenKategori');
  const modal = document.getElementById('modalBackdrop');
  const form = document.getElementById('kForm');

  const kId = document.getElementById('kId');
  const kAksi = document.getElementById('kAksi');
  const kNama = document.getElementById('kNama');
  const kDesc = document.getElementById('kDesc');
  const modalTitle = document.getElementById('modalTitle');
  const submitBtn = document.getElementById('kSubmitBtn');

  let formKategoriDelete = null;

  if (btnOpen && modal) {
    btnOpen.addEventListener('click', function () {
      openTambahKategoriModal();
    });
  }

  window.openTambahKategoriModal = function () {
    if (!modal || !form) return;

    form.reset();
    kId.value = '';
    kAksi.value = 'tambah';
    modalTitle.textContent = 'Tambah Kategori';
    submitBtn.textContent = 'Simpan';

    modal.classList.add('open');
  };

  window.openEditModal = function (id, nama, deskripsi) {
    if (!modal || !form) return;

    kId.value = id;
    kAksi.value = 'edit';
    kNama.value = nama;
    kDesc.value = deskripsi || '';
    modalTitle.textContent = 'Edit Kategori';
    submitBtn.textContent = 'Update';

    modal.classList.add('open');
  };

  window.closeKategoriModal = function () {
    if (!modal) return;
    modal.classList.remove('open');
  };

  document.querySelectorAll('.form-hapus-kategori').forEach(function (hapusForm) {
    hapusForm.addEventListener('submit', function (e) {
      e.preventDefault();

      formKategoriDelete = hapusForm;

      const modalHapus = document.getElementById('modalHapusKategori');
      if (modalHapus) {
        modalHapus.classList.add('open');
      }
    });
  });

  window.closeDeleteKategoriModal = function () {
    const modalHapus = document.getElementById('modalHapusKategori');

    if (modalHapus) {
      modalHapus.classList.remove('open');
    }

    formKategoriDelete = null;
  };

  const btnConfirmDelete = document.getElementById('btnConfirmDeleteKategori');

  if (btnConfirmDelete) {
    btnConfirmDelete.addEventListener('click', function () {
      if (formKategoriDelete) {
        formKategoriDelete.submit();
      }
    });
  }
});