<?php
require_once 'koneksi.php';
requireAdmin();

$data = mysqli_query($conn, "
    SELECT 
        l.*,
        b.nama_barang,
        b.barcode
    FROM stok_opname_log l
    LEFT JOIN barang b ON l.id_barang = b.id
    ORDER BY l.tanggal DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Opname</title>
  <link rel="icon" type="image/png" sizes="32x32" href="img/logo.png?v=2">
  <link rel="icon" type="image/png" sizes="192x192" href="img/logo.png?v=2">
  <link rel="shortcut icon" href="img/logo.png?v=2">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="app">
  <div class="sidebar-overlay" id="sidebarOverlay"></div>
  <?php include 'sidebar.php'; ?>

  <div class="main">
    <div class="topbar">
      <div class="topbar-left">
        <button class="menu-toggle" id="menuToggle" aria-label="Menu">
          ☰
        </button>
        <h2>Riwayat Opname</h2>
        <p>Histori penyesuaian stok fisik</p>
      </div>

      <div class="topbar-actions">
        <a href="stock_opname.php" class="tb-btn outline-gold">Kembali</a>
      </div>
    </div>

    <div class="content">
      <div class="card">
        <div class="card-header">
          <span class="card-title">Riwayat Stok Opname</span>
        </div>

        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Barcode</th>
                <th>Nama Barang</th>
                <th>Stok Sistem</th>
                <th>Stok Fisik</th>
                <th>Selisih</th>
                <th>Keterangan</th>
              </tr>
            </thead>

            <tbody>
            <?php while($row = mysqli_fetch_assoc($data)): ?>
              <tr>
                <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])) ?></td>
                <td><span class="barcode-tag"><?= htmlspecialchars($row['barcode']) ?></span></td>
                <td class="fw-600"><?= htmlspecialchars($row['nama_barang']) ?></td>
                <td><?= $row['stok_sistem'] ?></td>
                <td><?= $row['stok_fisik'] ?></td>
                <td class="<?= $row['selisih'] < 0 ? 'text-danger' : ($row['selisih'] > 0 ? 'text-gold' : 'text-muted') ?>">
                  <?= $row['selisih'] > 0 ? '+' : '' ?><?= $row['selisih'] ?>
                </td>
                <td><?= htmlspecialchars($row['keterangan']) ?></td>
              </tr>
            <?php endwhile; ?>
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