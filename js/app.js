document.addEventListener('DOMContentLoaded', function () {

  /* ============================================================
     MOBILE SIDEBAR TOGGLE
     ============================================================ */
  const menuToggle  = document.getElementById('menuToggle');
  const sidebar     = document.getElementById('sidebar');
  const overlay     = document.getElementById('sidebarOverlay');

  function openSidebar() {
    if (!sidebar) return;
    sidebar.classList.add('open');
    if (overlay) overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeSidebar() {
    if (!sidebar) return;
    sidebar.classList.remove('open');
    if (overlay) overlay.classList.remove('open');
    document.body.style.overflow = '';
  }

  if (menuToggle) menuToggle.addEventListener('click', openSidebar);
  if (overlay)    overlay.addEventListener('click', closeSidebar);

  document.querySelectorAll('.nav-item').forEach(function (item) {
    item.addEventListener('click', function () {
      if (window.innerWidth <= 768) closeSidebar();
    });
  });

  /* ============================================================
     AUTO-CLOSE ALERTS (4 detik)
     ============================================================ */
  document.querySelectorAll('.alert.auto-close').forEach(function (el) {
    setTimeout(function () {
      el.style.opacity = '0';
      setTimeout(function () { if (el.parentNode) el.remove(); }, 400);
    }, 4000);
  });

  /* ============================================================
     BAR CHART DASHBOARD
     ============================================================ */
  const barsEl = document.getElementById('bars');
  if (barsEl) {
    const days = window.chartDays || ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
    const vals = window.chartVals || [0,0,0,0,0,0,0];
    const max  = Math.max(...vals) || 1;

    vals.forEach(function (v, i) {
      const isToday = (i === days.length - 1);
      const wrap = document.createElement('div');
      wrap.className = 'bar-wrap';
      const h = Math.max(Math.round((v / max) * 100), 4);
      wrap.innerHTML =
      '<div class="bar" style="height:' + h + 'px;' +
      'background:' + (isToday ? 'var(--gold)' : 'rgba(212,175,55,.25)') + ';min-height:4px"' +
      ' title="Rp ' + v.toLocaleString('id-ID') + '"></div>' +
      '<div class="bar-label" style="color:' + (isToday ? 'var(--gold)' : 'var(--text-muted)') + ';' +
      'font-weight:' + (isToday ? '700' : '400') + '">' + days[i] + '</div>';
      barsEl.appendChild(wrap);
    });
  }

  /* ============================================================
     RESTOCK — PREVIEW & HITUNG STOK SETELAH RESTOCK
     ============================================================ */
  const restockSelect = document.getElementById('id_barang_restock');
  const restockForm   = document.getElementById('restockForm');

  function previewBarangRestock(select) {
    const panel = document.getElementById('restockPreview');
    if (!panel) return;

    if (!select || !select.value) {
      panel.classList.add('hidden');
      panel.style.display = 'none';
      return;
    }

    const opt  = select.options[select.selectedIndex];
    const stok = parseInt(opt.dataset.stok) || 0;

    const elNama = document.getElementById('rprev-nama');
    const elStok = document.getElementById('rprev-stok');
    if (elNama) elNama.textContent = opt.dataset.nama || opt.text;
    if (elStok) elStok.textContent = stok + ' unit';

    panel.classList.remove('hidden');
    panel.style.display = 'block';
    hitungAfterRestock();
  }

  function hitungAfterRestock() {
    const select = document.getElementById('id_barang_restock');
    const panel  = document.getElementById('restockPreview');
    if (!select || !select.value || !panel || panel.style.display === 'none') return;

    const stok   = parseInt(select.options[select.selectedIndex]?.dataset.stok) || 0;
    const jumlah = parseInt(document.getElementById('jumlah_restock')?.value) || 0;
    const after  = stok + jumlah;

    const el = document.getElementById('rprev-after');
    if (!el) return;
    el.textContent = after + ' unit';
    el.style.color = after > 5 ? 'var(--green)' : (after === 0 ? 'var(--red)' : 'var(--orange)');
  }

  if (restockSelect) {
    restockSelect.addEventListener('change', function () { previewBarangRestock(this); });
  }
  const jumlahRestock = document.getElementById('jumlah_restock');
  if (jumlahRestock) {
    jumlahRestock.addEventListener('input', hitungAfterRestock);
  }

  /* Validasi submit restock */
  if (restockForm) {
    restockForm.addEventListener('submit', function (e) {
      const sel = document.getElementById('id_barang_restock');
      const qty = parseInt(document.getElementById('jumlah_restock')?.value) || 0;

      if (!sel || !sel.value) {
        e.preventDefault(); alert('Pilih barang terlebih dahulu.'); return;
      }
      if (qty <= 0) {
        e.preventDefault(); alert('Jumlah tambah stok harus lebih dari 0.'); return;
      }
    });
  }

  /* ============================================================
     STOK OPNAME — HITUNG SELISIH LIVE
     ============================================================ */
  document.querySelectorAll('.stok-fisik').forEach(function (input) {
    input.addEventListener('input', function () { hitungSelisih(this); });
  });

  /* ============================================================
     BARCODE GENERATOR
     ============================================================ */
  const barcodeInput = document.getElementById('barcode');
  const barcodeBtn   = document.getElementById('genBarcodeBtn');
  if (barcodeBtn && barcodeInput) {
    barcodeBtn.addEventListener('click', function () {
      barcodeInput.value = 'BC' + Math.floor(Math.random() * 9000000 + 1000000);
    });
  }
  
  const gambarProduk = document.getElementById('gambarProduk');
const fileNameProduk = document.getElementById('fileNameProduk');

if (gambarProduk && fileNameProduk) {
  gambarProduk.addEventListener('change', function () {
    fileNameProduk.textContent =
      this.files.length > 0
        ? this.files[0].name
        : 'JPG, PNG, WEBP';
  });
}

}); /* END DOMContentLoaded */

/* ============================================================
   GLOBAL HELPERS
   ============================================================ */

function fmtRp(n) {
  return 'Rp ' + (parseInt(n) || 0).toLocaleString('id-ID');
}

function hitungSelisih(input) {
  const tr     = input.closest('tr');
  if (!tr) return;
  const sistem  = parseInt(tr.querySelector('.stok-sistem')?.dataset.stok) || 0;
  const fisik   = parseInt(input.value) || 0;
  const selisih = fisik - sistem;
  const cell    = tr.querySelector('.selisih');
  if (!cell) return;
  cell.textContent = (selisih > 0 ? '+' : '') + selisih;
  cell.style.color = selisih > 0 ? 'var(--green)' : selisih < 0 ? 'var(--red)' : 'var(--text-muted)';
}

function resetPreviewRestock(){
  const panel = document.getElementById('restockPreview');
  const after = document.getElementById('rprev-after');

  if(panel){
    panel.classList.add('hidden');
    panel.style.display = 'none';
  }

  if(after){
    after.textContent = '—';
    after.style.color = '';
  }
}

/* ============================================================
   MODAL HELPERS (dipakai inline di PHP)
   ============================================================ */

function modalOpen(id) {
  const el = document.getElementById(id);
  if (el) el.classList.add('open');
}

function modalClose(id) {
  const el = document.getElementById(id);
  if (el) el.classList.remove('open');
}

/* Tutup modal kalau klik backdrop */
document.addEventListener('click', function (e) {
  if (e.target.classList.contains('modal-backdrop')) {
    e.target.classList.remove('open');
  }
});

/* Tutup modal dengan Escape */
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal-backdrop.open').forEach(function (el) {
      el.classList.remove('open');
    });
  }
});

function openModal(id){
    document.getElementById(id).classList.add('open');
}

function closeModal(id){
    document.getElementById(id).classList.remove('open');
}

document.querySelectorAll(".toggle-password").forEach(btn=>{

btn.addEventListener("click",()=>{

const input = btn.previousElementSibling;

if(input.type==="password"){

input.type="text";

btn.innerHTML=`
<svg width="18" height="18"
fill="none"
viewBox="0 0 24 24">

<path d="M2 2L22 22"
stroke="currentColor"
stroke-width="1.8"
stroke-linecap="round"/>

<path d="M10.58 10.58
A2 2 0 0013.42 13.42"
stroke="currentColor"
stroke-width="1.8"/>

<path d="M9.88 5.09
A10.94 10.94 0 0112 5
c7 0 11 7 11 7
a21.8 21.8 0 01-5.17 5.88"
stroke="currentColor"
stroke-width="1.8"/>

<path d="M6.61 6.61
A21.8 21.8 0 001 12
s4 7 11 7
a10.94 10.94 0 005.39-1.39"
stroke="currentColor"
stroke-width="1.8"/>

</svg>`;
}
else{

input.type="password";

btn.innerHTML=`
<svg width="18" height="18"
fill="none"
viewBox="0 0 24 24">

<path d="M1 12
s4-7 11-7
11 7
11 7
-4 7
-11 7
S1 12 1 12z"
stroke="currentColor"
stroke-width="1.8"/>

<circle cx="12"
cy="12"
r="3"
stroke="currentColor"
stroke-width="1.8"/>

</svg>`;
}

});

});


function openLogoutModal() {
  const modal = document.getElementById('logoutModal');
  if (modal) modal.classList.add('show');
}

function closeLogoutModal() {
  const modal = document.getElementById('logoutModal');
  if (modal) modal.classList.remove('show');
}