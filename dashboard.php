<?php
require_once 'koneksi.php';
requireLogin();

$totalProduk      = (int) mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM barang"))['c'];
$totalStok        = (int) mysqli_fetch_assoc(mysqli_query($conn,"SELECT COALESCE(SUM(stok),0) c FROM barang"))['c'];
$penjualanHariIni = (int) mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM penjualan WHERE DATE(tanggal)=CURDATE()"))['c'];
$pendapatan       = (int) mysqli_fetch_assoc(mysqli_query($conn,"SELECT COALESCE(SUM(total),0) c FROM penjualan"))['c'];
$jmlMenipis       = (int) mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM barang WHERE stok <= min_stok"))['c'];

$qMenipis   = mysqli_query($conn,"SELECT * FROM barang WHERE stok <= min_stok ORDER BY stok ASC LIMIT 5");
$qTransaksi = mysqli_query($conn,"
  SELECT 
    p.id,
    p.tanggal,
    p.total,
    p.status,
    GROUP_CONCAT(b.nama_barang SEPARATOR ', ') AS produk,
    SUM(dp.jumlah) AS total_item,
    COUNT(dp.id) AS jenis_produk
  FROM penjualan p
  JOIN detail_penjualan dp ON dp.id_penjualan = p.id
  JOIN barang b ON b.id = dp.id_barang
  GROUP BY p.id, p.tanggal, p.total, p.status
  ORDER BY p.id DESC
  LIMIT 3
");
$qKategori = mysqli_query($conn, "
    SELECT 
        k.nama,
        COUNT(b.id) as total
    FROM kategori k
    LEFT JOIN barang b ON b.id_kategori = k.id
    GROUP BY k.id
    ORDER BY total DESC
");

$kategoriData = [];
while($k = mysqli_fetch_assoc($qKategori)){
    $persen = $totalProduk > 0 ? round(($k['total'] / $totalProduk) * 100) : 0;
    $kategoriData[] = [
        'nama' => $k['nama'],
        'total' => $k['total'],
        'persen' => $persen
    ];
}

$chartDays = [];
$chartVals = [];

for ($i = 6; $i >= 0; $i--) {
    $tanggal = date('Y-m-d', strtotime("-$i days"));
    $hari    = date('D', strtotime($tanggal));

    $namaHari = [
        'Mon' => 'Sen',
        'Tue' => 'Sel',
        'Wed' => 'Rab',
        'Thu' => 'Kam',
        'Fri' => 'Jum',
        'Sat' => 'Sab',
        'Sun' => 'Min'
    ][$hari];

    $qChart = mysqli_query($conn, "
    SELECT COALESCE(SUM(total),0) AS total
    FROM penjualan
    WHERE DATE(tanggal) = '$tanggal'
");

    $rChart = mysqli_fetch_assoc($qChart);

    $chartDays[] = $namaHari;
    $chartVals[] = (int)$rChart['total'];
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard  PT Berkah Jaya Awing</title>
<link rel="icon" type="image/png" sizes="32x32" href="img/logo.png?v=2">
<link rel="icon" type="image/png" sizes="192x192" href="img/logo.png?v=2">
<link rel="shortcut icon" href="img/logo.png?v=2">
<link rel="stylesheet" href="css/style.css?v=20"/>
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

        <h2>Dashboard</h2>
        <p>Ringkasan sistem · <?= date('l, d F Y') ?></p>
      </div>
      <div class="topbar-actions">
        <a href="laporan.php" class="tb-btn primary">
          <svg width="1" height="14" fill="none" viewBox="0 0 24 24">
          </svg>
          Lihat Laporan
        </a>
      </div>
    </div>

    <div class="content">

      <!-- STATS -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon icon-blue">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24">
              <path d="M20 7L12 3 4 7v10l8 4 8-4V7z" stroke="#1565C0" stroke-width="1.8" fill="none"/>
            </svg>
          </div>
          <div class="stat-label">Total Produk</div>
          <div class="stat-val"><?= $totalProduk ?></div>
          <div class="stat-sub"><?= number_format($totalStok) ?> unit stok</div>
        </div>

        <div class="stat-card">
          <div class="stat-icon icon-green">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24">
              <circle cx="9" cy="21" r="1.5" fill="#2e7d32"/>
              <circle cx="20" cy="21" r="1.5" fill="#2e7d32"/>
              <path d="M1 1h4l2.68 13.39a2 2 0 001.98 1.61h9.72a2 2 0 001.98-1.61L23 6H6"
                    stroke="#2e7d32" stroke-width="1.8" fill="none"/>
            </svg>
          </div>
          <div class="stat-label">Penjualan Hari Ini</div>
          <div class="stat-val"><?= $penjualanHariIni ?></div>
          <div class="stat-sub">transaksi hari ini</div>
        </div>

        <div class="stat-card">
          <div class="stat-icon icon-gold">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24">
              <line x1="12" y1="1" x2="12" y2="23" stroke="#D4AF37" stroke-width="1.8"/>
              <path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"
                    stroke="#D4AF37" stroke-width="1.8" fill="none"/>
            </svg>
          </div>
          <div class="stat-label">Total Pendapatan</div>
          <div class="stat-val stat-income">Rp <?= number_format($pendapatan) ?></div>
          <div class="stat-sub">semua waktu</div>
        </div>

        <div class="stat-card">
          <div class="stat-icon icon-orange">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24">
              <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"
                    stroke="#e65100" stroke-width="1.8" fill="none"/>
              <line x1="12" y1="9"  x2="12"    y2="13" stroke="#e65100" stroke-width="1.8"/>
              <line x1="12" y1="17" x2="12.01" y2="17" stroke="#e65100" stroke-width="2"/>
            </svg>
          </div>
          <div class="stat-label">Stok Menipis</div>
          <div class="stat-val stock-warning"><?= $jmlMenipis ?></div>
          <div class="stat-sub">produk perlu restock</div>
        </div>
      </div>

      <!-- ROW 2: Chart + Donut -->
      <div class="row2">
        <div class="card">
          <div class="card-header">
            <span class="card-title">Pendapatan 7 Hari Terakhir</span>
          </div>
          <div class="card-body">
            <div class="chart-bars" id="bars"></div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <span class="card-title">Produk per Kategori</span>
          </div>
          <div class="card-body">
            <div class="donut-wrap">
              <svg width="110" height="110" viewBox="0 0 110 110">
                <circle class="donut-bg"     cx="55" cy="55" r="40"/>
                <circle class="donut-blue"   cx="55" cy="55" r="40" stroke-dasharray="100 152" stroke-dashoffset="25"/>
                <circle class="donut-green"  cx="55" cy="55" r="40" stroke-dasharray="62 190"  stroke-dashoffset="-77"/>
                <circle class="donut-purple" cx="55" cy="55" r="40" stroke-dasharray="42 210"  stroke-dashoffset="-141"/>
                <text x="55" y="51" text-anchor="middle" font-size="14" font-weight="600" fill="#111827"><?= $totalProduk ?></text>
                <text x="55" y="63" text-anchor="middle" font-size="9"  fill="#9e9e9e">Produk</text>
              </svg>
              
              <div class="donut-legend">
    <?php
    $dotClasses = ['dot-blue', 'dot-green', 'dot-purple'];

    foreach ($kategoriData as $i => $kat):
        $dot = $dotClasses[$i] ?? 'dot-blue';
    ?>
        <div class="legend-item">
            <div class="legend-dot <?= $dot ?>"></div>
            <?= htmlspecialchars($kat['nama']) ?>
            <span class="legend-val"><?= $kat['persen'] ?>%</span>
        </div>
    <?php endforeach; ?>
</div>
            </div>
          </div>
        </div>
      </div>

      <!-- ROW 3: Transaksi + Stok Menipis -->
      <div class="row3">
        <div class="card">
          <div class="card-header">
            <span class="card-title">Transaksi Terbaru</span>
            <a href="laporan.php" class="card-link">Lihat Semua</a>
          </div>
          <div class="card-body no-padding">
            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>ID</th><th>Produk</th><th>Jml Item</th><th>Total</th><th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($r = mysqli_fetch_assoc($qTransaksi)): ?>
                  <tr>
                    <td class="trx-id">TRX-<?= $r['id'] ?></td>
                    <td class="produk-ringkas">
                    <?= htmlspecialchars(mb_strimwidth($r['produk'], 0, 65, '...')) ?>
                    <?php if ((int)$r['jenis_produk'] > 1): ?>
                      <span class="item-count"><?= (int)$r['jenis_produk'] ?> jenis</span>
                    <?php endif; ?>
                  </td>
                    <td><?= $r['total_item'] ?></td>
                    <td class="fw-600">Rp <?= number_format($r['total']) ?></td>
                    <?php
                          $badgeClass = 'badge-success';

                          if($r['status'] == 'pending'){
                              $badgeClass = 'badge-warning';
                          }elseif($r['status'] == 'batal'){
                              $badgeClass = 'badge-danger';
                          }
                          ?>

                          <td>
                              <span class="badge <?= $badgeClass ?>">
                                  <?= htmlspecialchars($r['status']) ?>
                              </span>
                          </td>
                  </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-side">
          <div class="card">
            <div class="card-header">
              <span class="card-title">Stok Menipis</span>
              <a href="restock.php" class="card-link">Restock</a>
            </div>
            <div class="card-body">
              <?php if ($jmlMenipis === 0): ?>
                <p class="stock-aman">Semua stok aman ✓</p>
              <?php else: ?>
                <?php while ($r = mysqli_fetch_assoc($qMenipis)):
                  $pct = $r['min_stok'] > 0 ? min(100, round($r['stok']/$r['min_stok']*100)) : 0;
                  if ($r['stok'] <= $r['min_stok']/2) { $cls='stock-kritis'; $lbl='KRITIS'; $col='#ef5350'; }
                  elseif ($r['stok'] <= $r['min_stok']) { $cls='stock-menipis'; $lbl='MENIPIS'; $col='#FF9800'; }
                  else { $cls='stock-aman'; $lbl='AMAN'; $col='#4CAF50'; }
                ?>
                <div class="stock-item">
                  <div class="stock-img">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24">
                      <path d="M20 7L12 3 4 7v10l8 4 8-4V7z" stroke="#9e9e9e" stroke-width="1.5" fill="none"/>
                    </svg>
                  </div>
                  <div class="stock-info">
                    <div class="stock-name"><?= htmlspecialchars($r['nama_barang']) ?></div>
                    <div class="stock-sku <?= $cls ?>"><?= $lbl ?> — <?= $r['stok'] ?> unit</div>
                    <div class="progress-bar">
                      <div class="progress-fill" style="width:<?= $pct ?>%;background:<?= $col ?>"></div>
                    </div>
                  </div>
                </div>
                <?php endwhile; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
window.chartDays = <?= json_encode($chartDays) ?>;
window.chartVals = <?= json_encode($chartVals) ?>;
</script>

<script src="js/app.js?v=20"></script>
</body>
</html>