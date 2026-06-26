<?php
require_once 'koneksi.php';
requireKasir();

$qStok = mysqli_query($conn,"
SELECT nama_barang, stok, min_stok
FROM barang
WHERE stok <= min_stok
ORDER BY stok ASC
LIMIT 5
");

$qBarang = mysqli_query($conn,"
SELECT 
    barang.id,
    barang.barcode,
    barang.nama_barang,
    barang.harga_jual,
    barang.stok,
    barang.min_stok,
    barang.gambar,
    kategori.nama AS kategori
FROM barang
LEFT JOIN kategori 
ON barang.id_kategori = kategori.id
ORDER BY barang.nama_barang ASC
");

$qStat = mysqli_query($conn, "
SELECT
  COUNT(*) AS total_transaksi,
  COALESCE(SUM(total),0) AS total_pendapatan
FROM penjualan
WHERE DATE(tanggal) = CURDATE()
");

$stat = mysqli_fetch_assoc($qStat);

$qItem = mysqli_query($conn, "
SELECT COALESCE(SUM(dp.jumlah),0) AS total_item
FROM detail_penjualan dp
JOIN penjualan p ON dp.id_penjualan = p.id
WHERE DATE(p.tanggal) = CURDATE()
");

$item = mysqli_fetch_assoc($qItem);

$products = [];

while($row = mysqli_fetch_assoc($qBarang)){
  $products[] = [
    'id' => (int)$row['id'],
    'barcode' => $row['barcode'],
    'nama' => $row['nama_barang'],
    'harga' => (int)$row['harga_jual'],
    'stok' => (int)$row['stok'],
    'min_stok' => (int)$row['min_stok'],
    'kategori' => $row['kategori'],
    'gambar' => !empty($row['gambar'])
        ? 'img/produk/'.$row['gambar']
        : 'img/produk/default.jpg'
];
}

$qJumlahStok = mysqli_query($conn,"
SELECT COUNT(*) as total
FROM barang
WHERE stok <= min_stok
");
$jumlahStok = mysqli_fetch_assoc($qJumlahStok);

$qTransaksiTerakhir = mysqli_query($conn, "
SELECT 
  p.id,
  p.tanggal,
  p.total,
  p.status,
  COALESCE(SUM(dp.jumlah), 0) AS jumlah_item,
  GROUP_CONCAT(b.nama_barang SEPARATOR ', ') AS produk
FROM penjualan p
LEFT JOIN detail_penjualan dp ON dp.id_penjualan = p.id
LEFT JOIN barang b ON dp.id_barang = b.id
WHERE DATE(p.tanggal) = CURDATE()
GROUP BY p.id
ORDER BY p.id DESC
LIMIT 5
");
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="css/style.css?v=20"/>
  <link rel="stylesheet" href="css/dashboard_kasir.css">
  <title>Dashboard Kasir PT Berkah Jaya Awing</title>
  <link rel="icon" type="image/png" sizes="32x32" href="img/logo.png?v=2">
  <link rel="icon" type="image/png" sizes="192x192" href="img/logo.png?v=2">
  <link rel="shortcut icon" href="img/logo.png?v=2">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">

</head>
<body>
<div class="app">

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<?php include 'sidebar.php'; ?>


  <!-- MAIN -->
  <div class="main">
    <!-- TOPBAR -->
    <div class="topbar">
      <div class="topbar-left">

        <button class="menu-toggle" id="menuToggle" aria-label="Menu">
    <svg width="20" height="20" fill="none" viewBox="0 0 24 24">
      <line x1="3" y1="6" x2="21" y2="6"
            stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      <line x1="3" y1="12" x2="21" y2="12"
            stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      <line x1="3" y1="18" x2="21" y2="18"
            stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    </svg>
  </button>

        <h2 id="pageTitle">Dashboard Kasir</h2>
        <p id="pageSubtitle">Selamat datang · <span id="clockDisplay"></span></p>
      </div>
    </div>

    <!-- CONTENT -->
    <div class="content">

      <!-- ===== DASHBOARD SECTION ===== -->
      <div id="section-dashboard">
        <div class="kasir-badge">
          <div class="kasir-badge-dot"></div>
          Sesi Aktif — Kasir
        </div>

        <!-- STATS -->
        <div class="stats-grid">
          <div class="stat-card gold">
            <div class="stat-icon icon-gold">
              <svg width="20" height="20" fill="none" viewBox="0 0 24 24">
                <circle cx="9" cy="21" r="1.5" fill="#C9A84C"/>
                <circle cx="20" cy="21" r="1.5" fill="#C9A84C"/>
                <path d="M1 1h4l2.68 13.39a2 2 0 001.98 1.61h9.72a2 2 0 001.98-1.61L23 6H6" stroke="#C9A84C" stroke-width="1.8" fill="none"/>
              </svg>
            </div>
            <div class="stat-label">Transaksi Hari Ini</div>
            <div class="stat-val" id="statTrx">
  <?= $stat['total_transaksi']; ?>
</div>
            <div class="stat-sub">transaksi selesai</div>
          </div>

          <div class="stat-card green">
            <div class="stat-icon icon-green">
              <svg width="20" height="20" fill="none" viewBox="0 0 24 24">
                <line x1="12" y1="1" x2="12" y2="23" stroke="#15803D" stroke-width="1.8"/>
                <path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" stroke="#15803D" stroke-width="1.8" fill="none"/>
              </svg>
            </div>
            <div class="stat-label">Pendapatan Hari Ini</div>
            <div class="stat-val income" id="statPendapatan">
            Rp <?= number_format($stat['total_pendapatan'], 0, ',', '.'); ?>
          </div>
            <div class="stat-sub">total penjualan</div>
          </div>

          <div class="stat-card blue">
            <div class="stat-icon icon-blue">
              <svg width="20" height="20" fill="none" viewBox="0 0 24 24">
                <path d="M20 7L12 3 4 7v10l8 4 8-4V7z" stroke="#1D4ED8" stroke-width="1.8" fill="none"/>
              </svg>
            </div>
            <div class="stat-label">Item Terjual</div>
            <div class="stat-val" id="statItem">
            <?= $item['total_item']; ?>
          </div>
            <div class="stat-sub">unit produk</div>
          </div>

          <div class="stat-card orange">
            <div class="stat-icon icon-orange">
              <svg width="20" height="20" fill="none" viewBox="0 0 24 24">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" stroke="#C2410C" stroke-width="1.8" fill="none"/>
                <line x1="12" y1="9" x2="12" y2="13" stroke="#C2410C" stroke-width="1.8"/>
                <line x1="12" y1="17" x2="12.01" y2="17" stroke="#C2410C" stroke-width="2"/>
              </svg>
            </div>
            <div class="stat-label">Stok Menipis</div>
            <div class="stat-val" style="color:var(--orange)">
            <?= $jumlahStok['total']; ?>
          </div>
            <div class="stat-sub">produk perlu restock</div>
          </div>
        </div>

        <!-- BOTTOM ROW -->
        <div class="row-bottom">
          <!-- Transaksi Terakhir -->
          <div class="card">
            <div class="card-header">
              <span class="card-title">Transaksi Terakhir Hari Ini</span>
            </div>
            <div class="card-body" style="padding:0">
              <div class="table-wrap">
                <table>
                  <thead>
                    <tr>
                      <th>#</th><th>Produk</th><th>Jml</th><th>Total</th><th>Status</th>
                    </tr>
                  </thead>
                  <tbody id="trxTableBody">

<?php if (mysqli_num_rows($qTransaksiTerakhir) > 0): ?>

  <?php while($t = mysqli_fetch_assoc($qTransaksiTerakhir)): ?>
    <tr>
      <td class="trx-id">TRX<?= $t['id']; ?></td>
      <td><?= htmlspecialchars($t['produk'] ?? '-'); ?></td>
      <td><?= $t['jumlah_item']; ?></td>
      <td class="fw-600">
        Rp <?= number_format($t['total'], 0, ',', '.'); ?>
      </td>
      <td>
        <span class="badge badge-success">
          <?= htmlspecialchars($t['status']); ?>
        </span>
      </td>
    </tr>
  <?php endwhile; ?>

<?php else: ?>

  <tr>
    <td colspan="5" style="text-align:center;color:var(--text-muted);padding:24px;">
      Belum ada transaksi hari ini
    </td>
  </tr>

<?php endif; ?>

</tbody>
                </table>
              </div>
            </div>
          </div>

<!-- Stok Menipis -->
<div class="card">
  <div class="card-header">
    <span class="card-title">Stok Menipis</span>
    <span class="card-link">Perlu Restock</span>
  </div>

  <div class="card-body">

    <?php while($s = mysqli_fetch_assoc($qStok)): ?>
      <div class="stock-item">

        <div class="stock-img">
          <svg width="18" height="18" fill="none" viewBox="0 0 24 24">
            <path d="M20 7L12 3 4 7v10l8 4 8-4V7z"
                  stroke="#9e9e9e"
                  stroke-width="1.5"
                  fill="none"/>
          </svg>
        </div>

        <div class="stock-info">
          <div class="stock-name">
            <?= htmlspecialchars($s['nama_barang']) ?>
          </div>

          <div class="stock-sku <?= $s['stok'] <= 2 ? 'stock-kritis' : 'stock-menipis' ?>">
            <?= $s['stok'] <= 2 ? 'Stok kritis' : 'Stok hampir habis' ?>
            — <?= $s['stok'] ?> unit
          </div>
        </div>

      </div>
    <?php endwhile; ?>

  </div>
</div>
</div> <!-- row-bottom -->

</div> <!-- section-dashboard -->

<!-- ===== POS SECTION ===== -->
<div id="section-pos" style="display:none">
        <div id="posAlert" style="display:none"></div>

        <div class="pos-wrapper">
          <!-- KIRI: Produk -->
          <div>
            <div class="card" style="margin-bottom:0">
              <div class="card-header">
                <span class="card-title">Pilih Produk</span>
                <span style="font-size:11.5px;color:var(--text-muted)">Klik produk untuk tambah ke keranjang</span>
              </div>
              <div class="card-body">
                <div class="scan-row">
                  <div class="scan-input-wrap">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" style="color:var(--text-muted)">
                      <rect x="3" y="3" width="4" height="2" rx=".5" fill="currentColor"/>
                      <rect x="8" y="3" width="2" height="2" rx=".5" fill="currentColor"/>
                      <rect x="11" y="3" width="4" height="2" rx=".5" fill="currentColor"/>
                      <rect x="3" y="6" width="2" height="6" rx=".5" fill="currentColor"/>
                      <rect x="6" y="6" width="3" height="6" rx=".5" fill="currentColor"/>
                      <rect x="10" y="6" width="2" height="6" rx=".5" fill="currentColor"/>
                      <rect x="13" y="6" width="2" height="6" rx=".5" fill="currentColor"/>
                      <rect x="3" y="13" width="4" height="2" rx=".5" fill="currentColor"/>
                      <rect x="8" y="13" width="2" height="2" rx=".5" fill="currentColor"/>
                      <rect x="11" y="13" width="4" height="2" rx=".5" fill="currentColor"/>
                    </svg>
                    <input class="scan-input" id="barcodeInput" placeholder="Cari nama produk..." type="text"/>
                  </div>
                  <button class="scan-btn" onclick="scanBarcode()">Cari</button>
                </div>

                <div class="cat-filter" id="catFilter"></div>

                <div class="product-grid" id="productGrid"></div>
              </div>
            </div>
          </div>

          <!-- KANAN: Checkout -->
          <div class="checkout-panel">
            <div class="checkout-header">
              <div class="checkout-title">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
                  <circle cx="9" cy="21" r="1.5" fill="currentColor"/>
                  <circle cx="20" cy="21" r="1.5" fill="currentColor"/>
                  <path d="M1 1h4l2.68 13.39a2 2 0 001.98 1.61h9.72a2 2 0 001.98-1.61L23 6H6" stroke="currentColor" stroke-width="1.8" fill="none"/>
                </svg>
                Keranjang Belanja
                <div class="cart-count" id="cartCount">0</div>
              </div>
            </div>

            <div class="cart-items" id="cartItems">
              <div class="cart-empty">
                <div><svg width="40" height="40" fill="none" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1.5" stroke="#ccc" stroke-width="1.5"/><circle cx="20" cy="21" r="1.5" stroke="#ccc" stroke-width="1.5"/><path d="M1 1h4l2.68 13.39a2 2 0 001.98 1.61h9.72a2 2 0 001.98-1.61L23 6H6" stroke="#ccc" stroke-width="1.5" fill="none"/></svg></div>
                Keranjang masih kosong
              </div>
            </div>

            <div class="checkout-summary">
              <div class="summary-row">
                <span class="lbl">Subtotal</span>
                <span class="val" id="subtotalVal">Rp 0</span>
              </div>
              <div class="summary-row">
                <span class="lbl">Diskon</span>
                <span class="val">Rp 0</span>
              </div>
              <div class="summary-row total">
                <span class="lbl">Total</span>
                <span class="val" id="totalVal">Rp 0</span>
              </div>

              <div class="cash-input-wrap">
                <div class="cash-label">Uang Tunai (Rp)</div>
                <input class="cash-input" id="cashInput" type="number" placeholder="0" min="0" oninput="hitungKembalian()"/>
              </div>

              <div class="quick-cash" id="quickCash"></div>

              <div class="kembalian-row" id="kembalianRow" style="display:none">
                <span class="lbl">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
                  <rect x="3" y="6" width="18" height="12" rx="2"
                        stroke="currentColor" stroke-width="1.8"/>
                  <circle cx="12" cy="12" r="2.5"
                          stroke="currentColor" stroke-width="1.8"/>
                </svg>
                Kembalian
              </span>
                <span class="val" id="kembalianVal">Rp 0</span>
              </div>

              <button class="pay-btn" id="payBtn" onclick="prosesTransaksi()" disabled>
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
                  <polyline points="20 6 9 17 4 12" stroke="currentColor" stroke-width="2"/>
                </svg>
                Proses Pembayaran
              </button>

              <button class="pay-btn" style="margin-top:8px;background:var(--red-light);color:var(--red);box-shadow:none;font-weight:600" onclick="clearCart()">
                Kosongkan Keranjang
              </button>
            </div>
          </div>
        </div>
      </div>

    </div><!-- /content -->
  </div><!-- /main -->
</div><!-- /app -->

<!-- MODAL STRUK -->
<div class="modal-backdrop" id="modalStruk">
  <div class="modal">
    <div class="struk-header">
      <div class="struk-logo">PT Berkah Jaya Awing</div>
      <div class="struk-sub">Distributor Parfum & Pet Care</div>
      <div class="struk-sub" style="margin-top:4px">JL. Mahid Pamulang, Pamulang Barat</div>
    </div>
    <hr class="struk-divider">
    <div id="strukItems"></div>
    <hr class="struk-divider">
    <div class="struk-row">
      <span class="key">Total</span>
      <span class="val struk-total" id="strukTotal"></span>
    </div>
    <div class="struk-row">
      <span class="key">Tunai</span>
      <span class="val" id="strukTunai"></span>
    </div>
    <div class="struk-row">
      <span class="key">Kembalian</span>
      <span class="val struk-kembalian" id="strukKembalian"></span>
    </div>
    <div class="struk-footer">
      <div id="strukWaktu"></div>
      <div style="margin-top:6px;font-weight:600;color:var(--gold)">Terima kasih atas kunjungan Anda!</div>
    </div>

    <div style="
font-size:10px;
color:#999;
margin-top:6px">
Dicetak melalui Sistem Informasi Penjualan & Stok
</div>
    <div class="modal-actions">
      <button class="btn-close-modal" onclick="closeStruk()">Tutup</button>
      <button class="btn-print" type="button" onclick="printStruk()">
      <svg width="15" height="15" fill="none" viewBox="0 0 24 24">
        <path d="M6 9V3h12v6" stroke="currentColor" stroke-width="1.8"/>
        <rect x="6" y="14" width="12" height="7" stroke="currentColor" stroke-width="1.8"/>
        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"
              stroke="currentColor" stroke-width="1.8"/>
      </svg>
      Cetak Struk
    </button>
    </div>
  </div>
</div>

<script>
const PRODUCTS = <?= json_encode($products); ?>;
</script>

<script src="js/dashboard_kasir.js"></script>

</body>
</html>