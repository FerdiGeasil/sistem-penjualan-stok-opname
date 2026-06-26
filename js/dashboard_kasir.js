let cart = [];
let sessionTrx = [];
let trxCounter = 1;
let activeCategory = 'Semua';

/* ── CLOCK ── */
function updateClock() {
  const clock = document.getElementById('clockDisplay');
  if (!clock) return;

  const now = new Date();
  clock.textContent =
    now.toLocaleDateString('id-ID', {
      weekday:'long',
      day:'numeric',
      month:'long',
      year:'numeric'
    }) +
    ' · ' + now.toLocaleTimeString('id-ID');
}

setInterval(updateClock, 1000);
updateClock();

/* ── SECTION TOGGLE ── */
function showSection(name) {
  document.getElementById('section-dashboard').style.display =
    name === 'dashboard' ? 'block' : 'none';

  document.getElementById('section-pos').style.display =
    name === 'pos' ? 'block' : 'none';

  document.getElementById('pageTitle').textContent =
    name === 'dashboard' ? 'Dashboard Kasir' : 'Point of Sale (POS)';

  document.getElementById('pageSubtitle').innerHTML =
    name === 'dashboard'
      ? 'Selamat datang · <span id="clockDisplay"></span>'
      : 'Sistem kasir cepat dan intuitif';

  if (name === 'dashboard') {
    updateClock();
  }

  document.querySelectorAll('.nav-item').forEach(function(item){
    item.classList.remove('active');
  });

  if(name === 'dashboard'){
    document.querySelector('.nav-dashboard').classList.add('active');
  }

  if(name === 'pos'){
    document.querySelector('.nav-pos').classList.add('active');
  }
}

/* ── PRODUCT GRID ── */
function fmtRp(n) { return 'Rp ' + parseInt(n).toLocaleString('id-ID'); }

function buildCategories() {
  const cats = ['Semua', ...new Set(PRODUCTS.map(p=>p.kategori))];
  const wrap = document.getElementById('catFilter');
  wrap.innerHTML = '';
  cats.forEach(function(c) {
    const btn = document.createElement('button');
    btn.className = 'cat-chip' + (c==='Semua' ? ' active' : '');
    btn.textContent = c;
    btn.onclick = function() {
      activeCategory = c;
      document.querySelectorAll('.cat-chip').forEach(b=>b.classList.remove('active'));
      btn.classList.add('active');
      buildProductGrid();
    };
    wrap.appendChild(btn);
  });
}

function buildProductGrid(filter='') {
  const grid = document.getElementById('productGrid');
  grid.innerHTML = '';
  const filtered = PRODUCTS.filter(function(p) {
    const matchCat = activeCategory==='Semua' || p.kategori===activeCategory;
    const matchQ   = p.nama.toLowerCase().includes(filter.toLowerCase()) || p.barcode.includes(filter);
    return matchCat && matchQ;
  });
  if (filtered.length === 0) {
    grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;color:var(--text-muted);padding:24px">Produk tidak ditemukan</div>';
    return;
  }
  filtered.forEach(function(p) {
    const card = document.createElement('div');
    card.className = 'product-card' + (p.stok <= p.min_stok ? ' low-stock' : '');
    card.innerHTML =
  '<div class="prod-img-wrap">' +
    '<img src="' + (p.gambar || 'img/produk/default.jpg') + '" class="prod-img">' +
  '</div>' +
  '<div class="prod-name">' + p.nama + '</div>' +
  '<div class="prod-price">' + fmtRp(p.harga) + '</div>' +
  '<div class="prod-stock">Stok: ' + p.stok + ' unit</div>' +
  '<div class="prod-category">' + p.kategori + '</div>';
    card.onclick = function() { addToCart(p.id); };
    grid.appendChild(card);
  });
}

function scanBarcode() {
  const q = document.getElementById('barcodeInput').value.trim();
  buildProductGrid(q);
}

document.addEventListener('DOMContentLoaded', function() {
  buildCategories();
  buildProductGrid();
  buildQuickCash();

  document.getElementById('barcodeInput').addEventListener('keyup', function(e) {
    if (e.key==='Enter') scanBarcode();
    else buildProductGrid(this.value);
  });
});

/* ── CART ── */
function addToCart(productId) {
  const p = PRODUCTS.find(x=>x.id===productId);
  if (!p) return;
  if (p.stok <= 0) { showAlert('Stok habis!', 'danger'); return; }

  const existing = cart.find(x=>x.id===productId);
  if (existing) {
    if (existing.qty >= p.stok) { showAlert('Stok tidak mencukupi!', 'danger'); return; }
    existing.qty++;
  } else {
    cart.push({ id:p.id, nama:p.nama, harga:p.harga, qty:1, stok:p.stok });
  }
  renderCart();
}

function changeQty(id, delta) {
  const item = cart.find(x=>x.id===id);
  if (!item) return;
  item.qty += delta;
  if (item.qty <= 0) cart = cart.filter(x=>x.id!==id);
  if (item.qty > item.stok) item.qty = item.stok;
  renderCart();
}

function removeItem(id) {
  cart = cart.filter(x=>x.id!==id);
  renderCart();
}

function clearCart() {
  cart = [];
  renderCart();
  document.getElementById('cashInput').value = '';
  document.getElementById('barcodeInput').focus();
  document.getElementById('kembalianRow').style.display = 'none';
  document.getElementById('barcodeInput').focus();
}

function renderCart() {
  const wrap = document.getElementById('cartItems');
  const total = cart.reduce(function(s,i){ return s + i.harga*i.qty; }, 0);
  const count = cart.reduce(function(s,i){ return s + i.qty; }, 0);

  document.getElementById('cartCount').textContent = cart.length;
  document.getElementById('subtotalVal').textContent = fmtRp(total);
  document.getElementById('totalVal').textContent    = fmtRp(total);
  document.getElementById('payBtn').disabled = cart.length === 0;
  buildQuickCash(total);
  hitungKembalian();

  if (cart.length === 0) {
    wrap.innerHTML = '<div class="cart-empty"><div><svg width="40" height="40" fill="none" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1.5" stroke="#ccc" stroke-width="1.5"/><circle cx="20" cy="21" r="1.5" stroke="#ccc" stroke-width="1.5"/><path d="M1 1h4l2.68 13.39a2 2 0 001.98 1.61h9.72a2 2 0 001.98-1.61L23 6H6" stroke="#ccc" stroke-width="1.5" fill="none"/></svg></div>Keranjang masih kosong</div>';
    return;
  }

  wrap.innerHTML = cart.map(function(item) {
    return '<div class="cart-item">' +
      '<div class="cart-item-info">' +
        '<div class="cart-item-name">' + item.nama + '</div>' +
        '<div class="cart-item-price">' + fmtRp(item.harga) + ' / unit</div>' +
      '</div>' +
      '<div class="cart-qty">' +
        '<button class="qty-btn" onclick="changeQty(' + item.id + ', -1)">−</button>' +
        '<span class="qty-val">' + item.qty + '</span>' +
        '<button class="qty-btn" onclick="changeQty(' + item.id + ', 1)">+</button>' +
      '</div>' +
      '<div class="cart-item-total">' + fmtRp(item.harga * item.qty) + '</div>' +
      '<div class="cart-remove" onclick="removeItem(' + item.id + ')"><svg width="14" height="14" fill="none" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2"/><line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2"/></svg></div>' +
    '</div>';
  }).join('');
}

/* ── QUICK CASH ── */
function buildQuickCash(total) {

  const total2 = total || 0;

  let options = [];

  if(total2 <= 50000){
    options = [50000, 100000, 200000];
  }
  else if(total2 <= 100000){
    options = [100000, 200000, 500000];
  }
  else if(total2 <= 200000){
    options = [200000, 500000, 1000000];
  }
  else{
    options = [
      total2,
      Math.ceil(total2 / 50000) * 50000,
      Math.ceil(total2 / 100000) * 100000
    ];
  }

  options = [...new Set(options)];

  const qcEl = document.getElementById('quickCash');

  qcEl.innerHTML = options.map(function(v){
    return `
      <button class="quick-cash-btn"
      onclick="setQuickCash(${v})">
        ${fmtRp(v)}
      </button>
    `;
  }).join('');
}

function setQuickCash(v) {
  document.getElementById('cashInput').value = v;
  hitungKembalian();
}

function hitungKembalian() {
  const total = cart.reduce(function(s,i){ return s + i.harga*i.qty; }, 0);
  const tunai = parseInt(document.getElementById('cashInput').value) || 0;
  const kembalian = tunai - total;
  const row = document.getElementById('kembalianRow');
  const btn = document.getElementById('payBtn');

  if (tunai > 0) {
    row.style.display = 'flex';
    document.getElementById('kembalianVal').textContent = fmtRp(Math.max(0, kembalian));
    row.style.background = kembalian >= 0 ? 'var(--green-light)' : 'var(--red-light)';
    row.querySelector('.lbl').style.color = kembalian >= 0 ? 'var(--green)' : 'var(--red)';
    row.querySelector('.val').style.color = kembalian >= 0 ? 'var(--green)' : 'var(--red)';
    btn.disabled = cart.length === 0 || kembalian < 0;
  } else {
    row.style.display = 'none';
    btn.disabled = cart.length === 0;
  }
}

/* ── PROSES TRANSAKSI ── */
function prosesTransaksi() {
  const total  = cart.reduce(function(s,i){ return s + i.harga*i.qty; }, 0);
  const tunai  = parseInt(document.getElementById('cashInput').value) || 0;
  const kembalian = tunai - total;

  if (cart.length === 0) {
    showAlert('Keranjang masih kosong!', 'danger');
    return;
  }

  if (tunai < total) {
    showAlert('Uang tunai kurang!', 'danger');
    return;
  }

  fetch('proses_pos.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      cart: cart,
      tunai: tunai
    })
  })
  .then(response => response.json())
  .then(result => {
    if (result.status === 'success') {
      const trxId = 'TRX' + result.id_penjualan;
      const now = new Date();
      const items = [...cart];

      sessionTrx.unshift({
        id: trxId,
        produk: items.map(i=>i.nama).join(', '),
        jml: items.reduce((s,i)=>s+i.qty,0),
        total: result.total,
        status: 'lunas',
        waktu: now.toLocaleTimeString('id-ID')
      });


      renderTrxTable();
      showStruk(trxId, items, result.total, tunai, result.kembalian, now);

cart = [];
renderCart();

document.getElementById('cashInput').value = '';
document.getElementById('kembalianRow').style.display = 'none';

showAlert('Transaksi berhasil disimpan ke database!', 'success');


    } else {
      showAlert(result.message, 'danger');
    }
  })
  .catch(error => {
    showAlert('Gagal menghubungkan ke proses_pos.php', 'danger');
    console.error(error);
  });
}

function renderTrxTable() {
  const body = document.getElementById('trxTableBody');
  if (!sessionTrx.length) {
    body.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:24px;">Belum ada transaksi hari ini</td></tr>';
    return;
  }
  body.innerHTML = sessionTrx.slice(0,8).map(function(t,i) {
    return '<tr>' +
      '<td class="trx-id">' + t.id + '</td>' +
      '<td>' + t.produk.substring(0,30) + (t.produk.length>30?'...':'') + '</td>' +
      '<td>' + t.jml + '</td>' +
      '<td class="fw-600">' + fmtRp(t.total) + '</td>' +
      '<td><span class="badge badge-success">lunas</span></td>' +
    '</tr>';
  }).join('');
}

/* ── STRUK ── */
function showStruk(id, items, total, tunai, kembalian, waktu) {
  document.getElementById('strukItems').innerHTML = items.map(function(i) {
    return '<div class="struk-row"><span class="key">' + i.nama + ' x' + i.qty + '</span><span class="val">' + fmtRp(i.harga * i.qty) + '</span></div>';
  }).join('');
  document.getElementById('strukTotal').textContent     = fmtRp(total);
  document.getElementById('strukTunai').textContent     = fmtRp(tunai);
  document.getElementById('strukKembalian').textContent = fmtRp(kembalian);
  const tgl = waktu.toLocaleDateString('id-ID');
  const jam = waktu.toLocaleTimeString('id-ID', {
    hour:'2-digit',
    minute:'2-digit'
  });

document.getElementById('strukWaktu').innerHTML =
'No Transaksi : '+id+
'<br>Tanggal : '+tgl+
'<br>Jam : '+jam;
  document.getElementById('modalStruk').classList.add('open');
}

function closeStruk(){
document.getElementById('modalStruk').classList.remove('open');
buildProductGrid();
}

/* ── ALERT ── */
function showAlert(msg, type) {
  const el = document.getElementById('posAlert');
  el.className = 'alert alert-' + type;
  el.textContent = msg;
  el.style.display = 'flex';
  setTimeout(function() { el.style.display = 'none'; }, 3000);
}

const menuToggle = document.getElementById('menuToggle');
const sidebar = document.getElementById('sidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');

if (menuToggle && sidebar && sidebarOverlay) {
  menuToggle.addEventListener('click', function () {
    sidebar.classList.add('open');
    sidebarOverlay.classList.add('open');
  });

  sidebarOverlay.addEventListener('click', function () {
    sidebar.classList.remove('open');
    sidebarOverlay.classList.remove('open');
  });
}

document.addEventListener('keydown', function(e){

if(e.key==="F2"){
e.preventDefault();
document.getElementById('barcodeInput').focus();
}

if(e.key==="Escape"){
document.getElementById('barcodeInput').value='';
buildProductGrid();
}

if(e.key==="F9"){
e.preventDefault();

if(!document.getElementById('payBtn').disabled){
prosesTransaksi();
}
}

});

function printStruk(){
  const now = new Date();

  document.title =
    'Struk_TRX_' +
    now.getFullYear() + '-' +
    String(now.getMonth()+1).padStart(2,'0') + '-' +
    String(now.getDate()).padStart(2,'0') + '_' +
    String(now.getHours()).padStart(2,'0') + '-' +
    String(now.getMinutes()).padStart(2,'0');

  window.print();
}

function openLogoutModal() {
  const modal = document.getElementById('logoutModal');
  if (modal) modal.classList.add('show');
}

function closeLogoutModal() {
  const modal = document.getElementById('logoutModal');
  if (modal) modal.classList.remove('show');
}