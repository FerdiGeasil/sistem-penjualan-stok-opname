<?php
require_once 'koneksi.php';
requireAdmin();

$success_msg = '';
$error_msg   = '';
$added_item  = '';
$added_qty   = 0;

/* ── Proses simpan ── */
if (isset($_POST['simpan'])) {
    $id_barang = (int) $_POST['id_barang'];
    $jumlah    = (int) $_POST['jumlah'];

    if ($id_barang <= 0 || $jumlah <= 0) {
        $error_msg = 'Pilih barang dan masukkan jumlah yang valid.';
    } else {
        /* Ambil nama barang untuk pesan sukses */
        mysqli_begin_transaction($conn);
        $row = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT nama_barang, stok FROM barang WHERE id = $id_barang FOR UPDATE")
        );

      if ($row) {
    try {

        if (!mysqli_query($conn,
            "UPDATE barang SET stok = stok + $jumlah WHERE id = $id_barang"
        )) {
            throw new Exception(mysqli_error($conn));
        }

        $id_user = (int)($_SESSION['user_id'] ?? ($_SESSION['id_user'] ?? 0));

        if (!mysqli_query($conn,
            "INSERT INTO restock_log (id_barang, jumlah, tanggal, id_user)
            VALUES ($id_barang, $jumlah, NOW(), $id_user)"
        )) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_commit($conn);

        $added_item  = htmlspecialchars($row['nama_barang']);
        $added_qty   = $jumlah;
        $stok_baru   = $row['stok'] + $jumlah;

        $success_msg = "Stok <strong>$added_item</strong> berhasil ditambah
                        <strong>$jumlah unit</strong>. Stok sekarang:
                        <strong>$stok_baru unit</strong>.";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error_msg = "Restock gagal: " . $e->getMessage();
    }
} else {
    mysqli_rollback($conn);
    $error_msg = 'Barang tidak ditemukan.';
}
    }
}

/* ── Ambil semua barang (untuk dropdown & tabel) ── */
$barang_list = mysqli_query($conn, "SELECT * FROM barang ORDER BY nama_barang ASC");
$barang_rows = [];
while ($b = mysqli_fetch_assoc($barang_list)) {
    $barang_rows[] = $b;
}

/* ── Statistik ringkas ── */
$total_barang  = count($barang_rows);
$stok_menipis  = 0;
$stok_habis    = 0;
$total_unit    = 0;
foreach ($barang_rows as $b) {
    $total_unit += (int)$b['stok'];
    if ((int)$b['stok'] === 0) $stok_habis++;
    elseif ((int)$b['stok'] <= 5) $stok_menipis++;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Restock Barang Sistem Inventori</title>
  <link rel="icon" type="image/png" sizes="32x32" href="img/logo.png?v=2">
  <link rel="icon" type="image/png" sizes="192x192" href="img/logo.png?v=2">
  <link rel="shortcut icon" href="img/logo.png?v=2">
  <link rel="stylesheet" href="css/style.css?v=20"/>
</head>
<body>

<div class="app">

<div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- ── SIDEBAR ── -->
<?php include 'sidebar.php'; ?>


  <!-- ── MAIN ── -->
  <div class="main">

    <!-- Topbar -->
    <header class="topbar">
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

        <h2>Restock Barang</h2>
        <p>Tambah stok barang ke inventori · <?= date('l, d F Y') ?></p>
      </div>
      <div class="topbar-actions">
        <a href="restock_log.php" class="tb-btn outline-gold">
          <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8" fill="none"/>
            <polyline points="12 6 12 12 16 14" stroke="currentColor" stroke-width="1.8" fill="none"/>
          </svg>
          Riwayat Restock
        </a>

        <a href="barang.php" class="tb-btn outline-gold">
          <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
            <path d="M20 7L12 3 4 7v10l8 4 8-4V7z" stroke="currentColor" stroke-width="1.8" fill="none"/>
          </svg>
          Data Barang
        </a>
      </div>
    </header>


    <!-- Content -->
    <div class="content">

      <!-- Alert sukses -->
      <?php if ($success_msg): ?>
      <div class="alert alert-success">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
          <path d="M22 11.08V12a10 10 0 11-5.93-9.14" stroke="#2e7d32" stroke-width="1.8" fill="none"/>
          <polyline points="22 4 12 14.01 9 11.01" stroke="#2e7d32" stroke-width="1.8" fill="none"/>
        </svg>
        <span><?= $success_msg ?></span>
        <button class="alert-close" onclick="this.parentElement.remove()">✕</button>
      </div>
      <?php endif; ?>

      <!-- Alert error -->
      <?php if ($error_msg): ?>
      <div class="alert alert-danger">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10" stroke="#c62828" stroke-width="1.8" fill="none"/>
          <line x1="12" y1="8" x2="12" y2="12" stroke="#c62828" stroke-width="1.8"/>
          <line x1="12" y1="16" x2="12.01" y2="16" stroke="#c62828" stroke-width="2"/>
        </svg>
        <span><?= htmlspecialchars($error_msg) ?></span>
        <button class="alert-close" onclick="this.parentElement.remove()">✕</button>
      </div>
      <?php endif; ?>

      <!-- Stats -->
      <div class="stats-row">
        <div class="stat-card">
          <div class="sc-icon bg-blue-soft">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
              <path d="M20 7L12 3 4 7v10l8 4 8-4V7z" stroke="#1565C0" stroke-width="1.8" fill="none"/>
            </svg>
          </div>
          <div class="sc-label">Total Barang</div>
          <div class="sc-val"><?= $total_barang ?></div>
          <div class="sc-sub">Item terdaftar</div>
        </div>
        <div class="stat-card">
          <div class="sc-icon bg-green-soft">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
              <polyline points="20 12 20 22 4 22 4 12" stroke="#2e7d32" stroke-width="1.8" fill="none"/>
              <rect x="2" y="7" width="20" height="5" stroke="#2e7d32" stroke-width="1.8" fill="none"/>
              <line x1="12" y1="22" x2="12" y2="7" stroke="#2e7d32" stroke-width="1.8"/>
            </svg>
          </div>
          <div class="sc-label">Total Unit Stok</div>
          <div class="sc-val"><?= number_format($total_unit) ?></div>
          <div class="sc-sub">Unit tersedia</div>
        </div>
        <div class="stat-card">
          <div class="sc-icon bg-yellow-soft">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
              <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"
                    stroke="#E65100" stroke-width="1.8" fill="none"/>
              <line x1="12" y1="9" x2="12" y2="13" stroke="#E65100" stroke-width="1.8"/>
            </svg>
          </div>
          <div class="sc-label">Stok Menipis</div>
          <div class="sc-val text-warning"><?= $stok_menipis ?></div>
          <div class="sc-sub">Stok ≤ 5 unit</div>
        </div>
        <div class="stat-card">
          <div class="sc-icon bg-red-soft">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="10" stroke="#c62828" stroke-width="1.8" fill="none"/>
              <line x1="12" y1="8"  x2="12"   y2="12"  stroke="#c62828" stroke-width="1.8"/>
              <line x1="12" y1="16" x2="12.01" y2="16" stroke="#c62828" stroke-width="2"/>
            </svg>
          </div>
          <div class="sc-label">Stok Habis</div>
          <div class="sc-val text-danger"><?= $stok_habis ?></div>
          <div class="sc-sub">Perlu segera restock</div>
        </div>
      </div>


      <!-- Grid: Form + Tabel -->
      <div class="grid-2">

        <!-- ── FORM RESTOCK ── -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">Form Restock</span>
            <span class="card-badge">Tambah Stok</span>
          </div>
          <div class="card-body">
            <form method="POST" action="restock.php" id="restockForm">

              <div class="form-group">
                <label class="form-label" for="id_barang_restock">
                  Pilih Barang
                  <span class="required">*</span>
                </label>
                <select class="form-select" name="id_barang" id="id_barang_restock"
                        required
                        onchange="previewBarangRestock(this)">
                  <option value="">— Pilih Barang —</option>
                  <?php foreach ($barang_rows as $b): ?>
                  <option value="<?= $b['id'] ?>"
                          data-stok="<?= (int)$b['stok'] ?>"
                          data-nama="<?= htmlspecialchars($b['nama_barang']) ?>">
                    <?= htmlspecialchars($b['nama_barang']) ?>
                    (Stok: <?= (int)$b['stok'] ?>)
                  </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Preview info barang terpilih -->
              <div class="barang-preview hidden" id="restockPreview">
                <div class="preview-row">
                  <span class="preview-label">Barang dipilih</span>
                  <span class="preview-val" id="rprev-nama">—</span>
                </div>
                <div class="preview-row">
                  <span class="preview-label">Stok saat ini</span>
                  <span class="preview-val" id="rprev-stok">—</span>
                </div>
                <div class="preview-row">
                  <span class="preview-label">Setelah restock</span>
                  <span class="preview-val preview-after" id="rprev-after">—</span>
                </div>
              </div>

              <div class="form-group">
                <label class="form-label" for="jumlah">
                    Jumlah Tambah Stok
                    <span class="required">*</span>
                </label>

                <input class="form-input"
                      type="number"
                      name="jumlah"
                      id="jumlah_restock"
                      min="1"
                      placeholder="0"
                      required
                      oninput="hitungAfterRestock()">

                <div class="form-hint">
                    Masukkan jumlah stok yang akan ditambahkan.
                </div>
            </div>

              <div class="form-actions">
                <button type="reset" class="btn" onclick="resetPreviewRestock()">Reset</button>
                <button type="submit" name="simpan" class="btn primary">
                  <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
                    <polyline points="1 4 1 10 7 10" stroke="currentColor" stroke-width="2" fill="none"/>
                    <path d="M3.51 15a9 9 0 102.13-9.36L1 10" stroke="currentColor" stroke-width="2" fill="none"/>
                  </svg>
                  Tambah Stok
                </button>
              </div>

            </form>
          </div>
        </div>


        <!-- ── TABEL STOK SAAT INI ── -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">Daftar Stok Barang</span>
            <span class="card-badge badge-count"><?= $total_barang ?> item</span>
          </div>
          <div class="card-body no-padding">

            <div class="table-search">
              <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8" stroke="#9e9e9e" stroke-width="1.8"/>
                <path d="M21 21l-4.35-4.35" stroke="#9e9e9e" stroke-width="1.8"/>
              </svg>
              <input type="text" id="tabelSearch" placeholder="Cari barang…"
                     oninput="filterTabelRestock()">
            </div>

            <div class="table-wrap">
              <table id="stokTable">
                <thead>
                  <tr>
                    <th>Nama Barang</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($barang_rows as $b):
                    $stok = (int)$b['stok'];
                    if ($stok === 0)     { $status_cls = 'badge-habis';   $status_lbl = 'Habis';   }
                    elseif ($stok <= 5) { $status_cls = 'badge-menipis'; $status_lbl = 'Menipis'; }
                    else                { $status_cls = 'badge-normal';  $status_lbl = 'Normal';  }
                  ?>
                  <tr>
                    <td class="td-nama"><?= htmlspecialchars($b['nama_barang']) ?></td>
                    <td class="text-center font-semibold
                    <?= $stok === 0 ? 'text-danger' : ($stok <= 5 ? 'text-warning' : 'text-normal') ?>">
                    <?= $stok ?>
                </td>
                    <td class="text-center">
                      <span class="badge <?= $status_cls ?>"><?= $status_lbl ?></span>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

          </div>
        </div>

      </div><!-- /grid-2 -->

    </div><!-- /content -->
  </div><!-- /main -->
</div><!-- /app -->

<script src="js/app.js?v=20"></script>
</body>
</html>