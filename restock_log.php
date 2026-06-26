<?php
require_once 'koneksi.php';
requireLogin();

$qLog = mysqli_query($conn, "
  SELECT 
    rl.id,
    rl.tanggal,
    rl.id_barang,
    rl.jumlah,
    rl.id_supplier,
    rl.no_faktur,
    rl.keterangan,
    rl.id_user,
    b.nama_barang,
    b.barcode,
    s.nama AS nama_supplier,
    u.nama AS nama_user
  FROM restock_log rl
  JOIN barang b ON b.id = rl.id_barang
  LEFT JOIN supplier s ON s.id = rl.id_supplier
  LEFT JOIN users u ON u.id = rl.id_user
  ORDER BY rl.id DESC
");

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Riwayat Restock PT BJA</title>
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
                  stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <line x1="3" y1="12" x2="21" y2="12"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <line x1="3" y1="18" x2="21" y2="18"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </button>

        <h2>Riwayat Restock</h2>
        <p>Catatan penambahan stok barang · <?= date('d F Y') ?></p>
      </div>

      <div class="topbar-actions">
        <a href="restock.php" class="tb-btn">← Kembali</a>
        <a href="barang.php" class="tb-btn primary">Data Barang</a>
      </div>
    </div>

    <div class="content">

      <div class="card">
        <div class="card-header">
          <span class="card-title">Riwayat Restock Barang</span>
          <span class="badge-count">
            <?= mysqli_num_rows($qLog) ?> data
          </span>
        </div>

        <div class="table-wrap tabel-responsive">
          <table>
            <thead>
              <tr>
              <th>No</th>
              <th>Tanggal</th>
              <th>Barang</th>
              <th>Barcode</th>
              <th>Jumlah</th>
              <th>Supplier</th>
              <th>No Faktur</th>
              <th>User</th>
              <th>Keterangan</th>
            </tr>
            </thead>
            <tbody>
              <?php if (mysqli_num_rows($qLog) > 0): ?>
                <?php $no = 1; while ($r = mysqli_fetch_assoc($qLog)): ?>
                  <tr>
                  <td><?= $no++ ?></td>
                  <td><?= date('d M Y H:i', strtotime($r['tanggal'])) ?></td>
                  <td class="fw-600"><?= htmlspecialchars($r['nama_barang']) ?></td>
                  <td>
                    <span class="barcode-tag">
                      <?= htmlspecialchars($r['barcode']) ?>
                    </span>
                  </td>
                  <td class="text-gold fw-600">+<?= (int)$r['jumlah'] ?></td>
                  <td><?= htmlspecialchars($r['nama_supplier'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($r['no_faktur'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($r['nama_user'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($r['keterangan'] ?? '-') ?></td>
                </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="9" class="empty-state">
                    Belum ada riwayat restock.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </div>

    </div>
  </div>
</div>

<script src="js/app.js?v=20"></script>
</body>
</html>