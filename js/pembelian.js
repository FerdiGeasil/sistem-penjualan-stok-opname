function openFormModal() {
  const modal = document.getElementById('modalBackdrop');
  if (modal) modal.classList.add('open');
}

function closePembelianModal() {
  const modal = document.getElementById('modalBackdrop');
  if (modal) modal.classList.remove('open');
}

function previewHargaBeli(sel) {
  const opt = sel.options[sel.selectedIndex];
  const harga = opt.dataset.harga || 0;
  const stok = opt.dataset.stok || 0;
  const infoBox = document.getElementById('barangInfo');

  if (sel.value) {
    document.getElementById('infoStok').textContent = stok + ' unit';
    document.getElementById('infoHarga').textContent =
      'Rp ' + parseInt(harga).toLocaleString('id-ID');
    document.getElementById('inputHargaBeli').value = harga;

    infoBox.classList.add('open');
  } else {
    infoBox.classList.remove('open');
  }

  hitungTotalBeli();
}

function hitungTotalBeli() {
  const jml = parseInt(document.getElementById('inputJumlah').value) || 0;
  const harga = parseInt(document.getElementById('inputHargaBeli').value) || 0;
  const total = jml * harga;
  const box = document.getElementById('totalInfo');

  if (jml > 0 && harga > 0) {
    document.getElementById('previewTotal').textContent =
      total.toLocaleString('id-ID');
    box.classList.add('open');
  } else {
    box.classList.remove('open');
  }
}

document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('modalBackdrop');

  if (modal) {
    modal.addEventListener('click', function (e) {
      if (e.target === modal) {
        closePembelianModal();
      }
    });
  }
});