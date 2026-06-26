<?php
require_once 'koneksi.php';
requireAdmin();

$tab    = $_GET['tab']    ?? 'penjualan';
$dari   = $_GET['dari']   ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');
$dari   = escStr($conn, $dari);
$sampai = escStr($conn, $sampai);

/* ── Data penjualan ── */
// SESUDAH
$qJual = mysqli_query($conn,"
    SELECT p.id, p.tanggal, b.nama_barang, b.barcode, dp.jumlah, dp.harga,
           (dp.jumlah * dp.harga) AS subtotal
    FROM penjualan p
    JOIN detail_penjualan dp ON dp.id_penjualan=p.id
    JOIN barang b ON b.id=dp.id_barang
    WHERE DATE(p.tanggal) BETWEEN '$dari' AND '$sampai'
    ORDER BY p.id DESC
");

$totalPendapatan = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COALESCE(SUM(total),0) t FROM penjualan
    WHERE DATE(tanggal) BETWEEN '$dari' AND '$sampai'
"))['t'];

$totalTrx = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) c FROM penjualan
    WHERE DATE(tanggal) BETWEEN '$dari' AND '$sampai'
"))['c'];

/* ── Data stok ── */
$qStok = mysqli_query($conn,"
SELECT 
    barang.*,
    kategori.nama AS kategori
FROM barang
LEFT JOIN kategori
ON barang.id_kategori = kategori.id
ORDER BY barang.stok ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Laporan PT Berkah Jaya Awing</title>
  <link rel="icon" type="image/png" sizes="32x32" href="img/logo.png?v=2">
  <link rel="icon" type="image/png" sizes="192x192" href="img/logo.png?v=2">
  <link rel="shortcut icon" href="img/logo.png?v=2">
  <link rel="stylesheet" href="css/style.css?v=20"/>
  <link rel="stylesheet" href="css/laporan.css"/>
</head>
<body>
<div class="app">

<div class="sidebar-overlay" id="sidebarOverlay"></div>

  <?php include 'sidebar.php'; ?>

  <div class="main">
    <div class="topbar">
      <div class="topbar-left">

      <button class="menu-toggle" id="menuToggle" aria-label="Menu">
  <svg width="20" height="20" fill="none" viewBox="0 0 24 24">
    <line x1="3" y1="6" x2="21" y2="6"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"/>

    <line x1="3" y1="12" x2="21" y2="12"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"/>

    <line x1="3" y1="18" x2="21" y2="18"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"/>
  </svg>
</button>

        <h2>Laporan</h2>
        <p>Laporan penjualan & stok barang</p>
      </div>
      <div class="topbar-actions">

      <?php if($tab === 'penjualan'): ?>

<a href="export_penjualan.php?dari=<?= $dari ?>&sampai=<?= $sampai ?>"
   class="tb-btn btn-export-excel">

  <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"
          stroke="currentColor"
          stroke-width="1.8"
          fill="none"/>
    <polyline points="14 2 14 8 20 8"
              stroke="currentColor"
              stroke-width="1.8"
              fill="none"/>
    <path d="M8 13l2 3 4-6"
          stroke="currentColor"
          stroke-width="1.8"
          fill="none"/>
  </svg>

  Export Penjualan
</a>

<?php else: ?>

<a href="export_stok.php"
   class="tb-btn btn-export-excel">

  <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"
          stroke="currentColor"
          stroke-width="1.8"
          fill="none"/>
    <polyline points="14 2 14 8 20 8"
              stroke="currentColor"
              stroke-width="1.8"
              fill="none"/>
    <path d="M8 13l2 3 4-6"
          stroke="currentColor"
          stroke-width="1.8"
          fill="none"/>
  </svg>

  Export Stok
</a>

<?php endif; ?>

        <button type="button"
        id="btnPrintLaporan"
        class="tb-btn btn-export-pdf">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
            <polyline points="6 9 6 2 18 2 18 9" stroke="currentColor" stroke-width="1.8" fill="none"/>
            <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"
                  stroke="currentColor" stroke-width="1.8" fill="none"/>
            <rect x="6" y="14" width="12" height="8" stroke="currentColor" stroke-width="1.8" fill="none"/>
          </svg>
          Cetak / Export PDF
        </button>
      </div>
    </div>

    <div class="content">

      <!-- Tabs -->
      <div class="tabs-wrap">
        <a href="?tab=penjualan&dari=<?= $dari ?>&sampai=<?= $sampai ?>"
           class="tab <?= $tab==='penjualan'?'active':'' ?>">Laporan Penjualan</a>
        <a href="?tab=stok&dari=<?= $dari ?>&sampai=<?= $sampai ?>"
           class="tab <?= $tab==='stok'?'active':'' ?>">Laporan Stok</a>
      </div>

      <?php if ($tab === 'penjualan'): ?>


<form method="GET" class="toolbar laporan-filter">
  <input type="hidden" name="tab" value="penjualan">

  <label class="laporan-label">Dari</label>
  <input type="date" name="dari" class="form-input laporan-date"
         value="<?= $dari ?>">

  <label class="laporan-label">Sampai</label>
  <input type="date" name="sampai" class="form-input laporan-date"
         value="<?= $sampai ?>">
        <button type="submit" class="tb-btn">Filter</button>

<a href="laporan.php" class="btn btn-secondary">Reset Filter</a>
      </form>

<div class="print-title">

    <h1>LAPORAN PENJUALAN</h1>

    <h2>PT BERKAH JAYA AWING</h2>

    <p>
        Periode:
        <?= date('d/m/Y', strtotime($dari)) ?>
        -
        <?= date('d/m/Y', strtotime($sampai)) ?>
    </p>

    <div class="print-summary">

        <span>
          Total Transaksi :
      <?= number_format($totalTrx) ?>
        </span>

        &nbsp;&nbsp;&nbsp;

        <span>
            Total Pendapatan :
            Rp<?= number_format($totalPendapatan,0,',','.') ?>
        </span>

    </div>

    <hr class="print-line">

</div>


      <!-- Summary -->
      <div class="stats-row laporan-stats">
        <div class="stat-card">
          <div class="stat-label">Total Transaksi</div>
          <div class="stat-val"><?= number_format($totalTrx) ?></div>
          <div class="stat-sub"><?= $dari ?> s/d <?= $sampai ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Total Pendapatan</div>
          <div class="stat-val laporan-total">Rp <?= number_format($totalPendapatan) ?></div>
          <div class="stat-sub">periode terpilih</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Riwayat Penjualan</span>
        </div>
        <div class="table-scroll">
          <table>
            <thead>
              <tr>
                <th>No</th><th>Tanggal</th><th>Barcode</th><th>Produk</th>
                <th>Jml</th><th>Harga</th><th>Total</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $no=1;
              $ada=false;
              while($r=mysqli_fetch_assoc($qJual)):
                $ada=true;
              ?>
              <tr>
                <td class="text-muted"><?= $no++ ?></td>
                <td><?= date('d/m/Y H:i', strtotime($r['tanggal'])) ?></td>
                <td><span class="barcode-tag"><?= htmlspecialchars($r['barcode']) ?></span></td>
                <td><?= htmlspecialchars($r['nama_barang']) ?></td>
                <td><?= $r['jumlah'] ?></td>
                <td>Rp <?= number_format($r['harga']) ?></td>
                <td class="fw-600">Rp <?= number_format($r['subtotal']) ?></td>
              </tr>
              <?php endwhile; ?>
              <?php if (!$ada): ?>
              <tr><td colspan="7" class="empty-laporan">
                Tidak ada transaksi pada periode ini
              </td></tr>
              <?php endif; ?>
            </tbody>
            
             <?php if ($ada): ?>
            <tfoot>
              <tr class="laporan-grand-total">
                <td colspan="6" class="text-right fw-700">TOTAL KESELURUHAN</td>
                <td class="fw-700">Rp <?= number_format($totalPendapatan) ?></td>
              </tr>
            </tfoot>
            <?php endif; ?>
            
          </table>
        </div>
      </div>

      <?php else: /* tab stok */ ?>

      <div class="card">
        <div class="card-header">
          <span class="card-title">Laporan Stok Barang</span>
          <span class="text-muted laporan-date-info"><?= date('d F Y') ?></span>
        </div>
        <div class="table-scroll">
          <table>
            <thead>
              <tr>
                <th>No</th><th>Barcode</th><th>Nama Barang</th><th>Kategori</th>
                <th>Stok</th><th>Min Stok</th><th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $no=1;
              while($r=mysqli_fetch_assoc($qStok)):
                $s=(int)$r['stok']; $m=(int)$r['min_stok'];
                if($s===0){$cls='badge-danger';$lbl='Habis';}
                elseif($s<=$m){$cls='badge-warning';$lbl='Menipis';}
                else{$cls='badge-success';$lbl='Normal';}
              ?>
              <tr>
                <td class="text-muted"><?= $no++ ?></td>
                <td><span class="barcode-tag"><?= htmlspecialchars($r['barcode']) ?></span></td>
                <td class="fw-600"><?= htmlspecialchars($r['nama_barang']) ?></td>
                <td><?= htmlspecialchars($r['kategori']) ?></td>
                <?php
if ($s === 0) {
  $stokClass = 'stok-habis';
} elseif ($s <= $m) {
  $stokClass = 'stok-menipis';
} else {
  $stokClass = 'stok-normal';
}
?>

<td class="fw-600 <?= $stokClass ?>"><?= $s ?></td>
                <td class="text-muted"><?= $m ?></td>
                <td><span class="badge <?= $cls ?>"><?= $lbl ?></span></td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>

      <?php endif; ?>

    </div>
  </div>
</div>

<script src="js/app.js?v=20"></script>
<script src="js/laporan.js"></script>
</body>
</html>