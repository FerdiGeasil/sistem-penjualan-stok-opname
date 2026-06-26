<?php
require_once 'koneksi.php';
requireAdmin();

$error   = '';
$msg     = $_GET['msg'] ?? '';

/* ── Simpan Pembelian Baru ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $id_supplier = intVal($_POST['id_supplier'] ?? 0);
    $id_barang   = intVal($_POST['id_barang'] ?? 0);
    $jumlah      = intVal($_POST['jumlah'] ?? 0);
    $harga_beli  = intVal($_POST['harga_beli'] ?? 0);
    $keterangan  = escStr($conn, $_POST['keterangan'] ?? '');
    $jatuh_tempo = escStr($conn, $_POST['jatuh_tempo'] ?? '');

    /* 1. Validasi input angka */
    if ($id_supplier <= 0 || $id_barang <= 0 || $jumlah <= 0 || $harga_beli <= 0) {
        $error = "Semua field bertanda * wajib diisi dengan benar.";

    } else {

        /* 2. Validasi supplier */
        $cekSupplier = mysqli_query(
            $conn,
            "SELECT id FROM supplier WHERE id=$id_supplier LIMIT 1"
        );

        if (mysqli_num_rows($cekSupplier) === 0) {
            $error = "Supplier tidak ditemukan.";
        }

        /* 3. Validasi barang */
        $cekBarangExist = mysqli_query(
            $conn,
            "SELECT id FROM barang WHERE id=$id_barang LIMIT 1"
        );

        if (mysqli_num_rows($cekBarangExist) === 0) {
            $error = "Barang tidak ditemukan.";
        }

        /* 4. Jalankan transaksi jika semua valid */
        if (!$error) {
            $total = $jumlah * $harga_beli;

            mysqli_begin_transaction($conn);

            try {
                /* Insert pembelian */
                if (!mysqli_query(
                    $conn,
                    "INSERT INTO pembelian
                     (id_supplier, id_barang, jumlah, harga_beli, total, keterangan, jatuh_tempo, tanggal, status)
                     VALUES (
                        $id_supplier,
                        $id_barang,
                        $jumlah,
                        $harga_beli,
                        $total,
                        '$keterangan',
                        " . ($jatuh_tempo ? "'$jatuh_tempo'" : "NULL") . ",
                        NOW(),
                        'lunas'
                     )"
                )) {
                    throw new Exception(mysqli_error($conn));
                }

                /* Lock barang */
                $cekBarang = mysqli_query(
                    $conn,
                    "SELECT stok FROM barang WHERE id=$id_barang FOR UPDATE"
                );

                if (!$cekBarang || mysqli_num_rows($cekBarang) === 0) {
                    throw new Exception("Barang tidak ditemukan.");
                }

                /* Update stok */
                if (!mysqli_query(
                    $conn,
                    "UPDATE barang
                     SET stok = stok + $jumlah,
                         harga_beli = $harga_beli
                     WHERE id = $id_barang"
                )) {
                    throw new Exception(mysqli_error($conn));
                }

                /* Restock log */
                if (!mysqli_query(
                    $conn,
                    "INSERT INTO restock_log (id_barang, jumlah, tanggal)
                     VALUES ($id_barang, $jumlah, NOW())"
                )) {
                    throw new Exception(mysqli_error($conn));
                }

                mysqli_commit($conn);

                header("Location: pembelian.php?msg=tambah");
                exit;

            } catch (Exception $e) {
                mysqli_rollback($conn);
                $error = "Pembelian gagal disimpan: " . $e->getMessage();
            }
        }
    }
}

/* ── Filter & Query List ── */
$dari   = escStr($conn, $_GET['dari']   ?? date('Y-m-01'));
$sampai = escStr($conn, $_GET['sampai'] ?? date('Y-m-d'));
$search = escStr($conn, $_GET['q']      ?? '');

$where = "WHERE DATE(pb.tanggal) BETWEEN '$dari' AND '$sampai'";
if ($search) $where .= " AND (s.nama LIKE '%$search%' OR b.nama_barang LIKE '%$search%')";

$data = mysqli_query($conn, "
    SELECT
        pb.*,
        s.nama AS nama_supplier,
        b.nama_barang,
        b.barcode
    FROM pembelian pb
    JOIN supplier s  ON s.id  = pb.id_supplier
    JOIN barang   b  ON b.id  = pb.id_barang
    $where
    ORDER BY pb.id DESC
");

$totalPembelian = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) c FROM pembelian pb
     JOIN supplier s ON s.id = pb.id_supplier
     JOIN barang b ON b.id = pb.id_barang
     $where"
))['c'];

$totalNilai = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COALESCE(SUM(pb.total),0) t FROM pembelian pb
     JOIN supplier s ON s.id = pb.id_supplier
     JOIN barang b ON b.id = pb.id_barang
     $where"
))['t'];

/* ── Dropdown Supplier & Barang ── */
$supplierList = mysqli_query($conn, "SELECT * FROM supplier ORDER BY nama ASC");
$barangList   = mysqli_query($conn, "SELECT * FROM barang ORDER BY nama_barang ASC");
$sRows = [];
while ($s = mysqli_fetch_assoc($supplierList)) $sRows[] = $s;
$bRows = [];
while ($b = mysqli_fetch_assoc($barangList))   $bRows[] = $b;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pembelian PT Berkah Jaya Awing</title>
  <link rel="icon" type="image/png" sizes="32x32" href="img/logo.png?v=2">
  <link rel="icon" type="image/png" sizes="192x192" href="img/logo.png?v=2">
  <link rel="shortcut icon" href="img/logo.png?v=2">
  <link rel="stylesheet" href="css/style.css"/>
  <link rel="stylesheet" href="css/pembelian.css"/>
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

        <h2>Manajemen Pembelian</h2>
        <p>Pencatatan pembelian barang dari supplier · <?= date('d F Y') ?></p>
      </div>
      <div class="topbar-actions">
        <button class="tb-btn primary" onclick="openFormModal()">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
            <line x1="12" y1="5" x2="12" y2="19" stroke="currentColor" stroke-width="2"/>
            <line x1="5" y1="12" x2="19" y2="12" stroke="currentColor" stroke-width="2"/>
          </svg>
          Catat Pembelian Baru
        </button>
        <a href="supplier.php" class="tb-btn">
          <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"
                  stroke="currentColor" stroke-width="1.8" fill="none"/>
          </svg>
          Data Supplier
        </a>
      </div>
    </div>

    <div class="content">

      <?php if ($msg === 'tambah'): ?>
      <div class="alert alert-success auto-close">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24">
          <path d="M22 11.08V12a10 10 0 11-5.93-9.14" stroke="currentColor" stroke-width="1.8" fill="none"/>
          <polyline points="22 4 12 14.01 9 11.01" stroke="currentColor" stroke-width="1.8" fill="none"/>
        </svg>
        <span>Pembelian berhasil dicatat! Stok barang telah diperbarui.</span>
      </div>
      <?php endif; ?>

      <?php if ($error): ?>
      <div class="alert alert-danger"><span><?= htmlspecialchars($error) ?></span></div>
      <?php endif; ?>

      <!-- Stats -->
      <div class="stats-grid pembelian-stats">
        <div class="stat-card">
          <div class="stat-icon icon-blue">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24">
              <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"
                    stroke="#1565C0" stroke-width="1.8" fill="none"/>
              <line x1="3" y1="6" x2="21" y2="6" stroke="#1565C0" stroke-width="1.8"/>
            </svg>
          </div>
          <div class="stat-label">Total Pembelian</div>
          <div class="stat-val"><?= number_format($totalPembelian) ?></div>
          <div class="stat-sub"><?= $dari ?> s/d <?= $sampai ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon icon-gold">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24">
              <line x1="12" y1="1" x2="12" y2="23" stroke="#D4AF37" stroke-width="1.8"/>
              <path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"
                    stroke="#D4AF37" stroke-width="1.8" fill="none"/>
            </svg>
          </div>
          <div class="stat-label">Total Nilai Pembelian</div>
          <div class="stat-val pembelian-nilai">Rp <?= number_format($totalNilai) ?></div>
          <div class="stat-sub">periode terpilih</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon icon-green">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24">
              <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"
                    stroke="#2e7d32" stroke-width="1.8" fill="none"/>
            </svg>
          </div>
          <div class="stat-label">Total Supplier Aktif</div>
          <div class="stat-val"><?= count($sRows) ?></div>
          <div class="stat-sub">supplier terdaftar</div>
        </div>
      </div>

      <!-- Filter -->
     <form method="GET" class="toolbar pembelian-toolbar">
        <div class="search-box">
          <svg class="search-icon" width="14" height="14" fill="none" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="1.8"/>
            <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.8"/>
          </svg>
          <input type="text" name="q" placeholder="Cari supplier / barang…"
                 value="<?= htmlspecialchars($search) ?>">
        </div>
        <label class="pembelian-label">Dari</label>
        <input type="date" name="dari" class="form-input pembelian-date"
               value="<?= $dari ?>">
        <label class="pembelian-label">Sampai</label>
        <input type="date" name="sampai" class="form-input pembelian-date"
               value="<?= $sampai ?>">
       <div class="filter-actions">

<button class="btn-filter">

<svg width="15" height="15" fill="none" viewBox="0 0 24 24">
<path d="M4 6H20M7 12H17M10 18H14"
stroke="currentColor"
stroke-width="2"
stroke-linecap="round"/>
</svg>

Filter

</button>

<a href="pembelian.php" class="btn-reset">

<svg width="15" height="15" fill="none" viewBox="0 0 24 24">
<path d="M3 12a9 9 0 109-9"
stroke="currentColor"
stroke-width="2"
stroke-linecap="round"/>

<polyline points="3 4 3 12 11 12"
stroke="currentColor"
stroke-width="2"
stroke-linecap="round"
stroke-linejoin="round"/>
</svg>

Reset
</a>

</div>
      </form>

      <!-- Table -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">Riwayat Pembelian</span>
          <span class="text-muted pembelian-count"><?= $totalPembelian ?> transaksi</span>
        </div>
        <div class="table-scroll">
          <table>
            <thead>
              <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th>Barang</th>
                <th>Barcode</th>
                <th>Jumlah</th>
                <th>Harga Beli</th>
                <th>Total</th>
                <th>Keterangan</th>
                <th>Jatuh Tempo</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $no  = 1;
              $ada = false;
              while ($r = mysqli_fetch_assoc($data)):
                $ada = true;
                $statusCls = $r['status'] === 'lunas' ? 'badge-success' : 'badge-warning';
              ?>
              <tr>
                <td class="text-muted"><?= $no++ ?></td>
                <td><?= date('d/m/Y', strtotime($r['tanggal'])) ?></td>
                <td class="fw-600"><?= htmlspecialchars($r['nama_supplier']) ?></td>
                <td><?= htmlspecialchars($r['nama_barang']) ?></td>
                <td><span class="barcode-tag"><?= htmlspecialchars($r['barcode']) ?></span></td>
                <td class="fw-600"><?= number_format($r['jumlah']) ?></td>
                <td>Rp <?= number_format($r['harga_beli']) ?></td>
                <td class="fw-600">Rp <?= number_format($r['total']) ?></td>
                <td class="text-muted"><?= htmlspecialchars($r['keterangan'] ?: '-') ?></td>
                <td class="text-muted">
                  <?= $r['jatuh_tempo'] ? date('d/m/Y', strtotime($r['jatuh_tempo'])) : '-' ?>
                </td>
                <td><span class="badge <?= $statusCls ?>"><?= ucfirst($r['status']) ?></span></td>
              </tr>
              <?php endwhile; ?>
              <?php if (!$ada): ?>
              <tr><td colspan="11" class="empty-pembelian">
                Tidak ada data pembelian pada periode ini
              </td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Modal Catat Pembelian -->
<div id="modalBackdrop" class="modal-backdrop pembelian-modal-backdrop">
  <div class="pembelian-modal-box">
    <div class="pembelian-modal-header">
      <span class="pembelian-modal-title">Catat Pembelian Baru</span>
      <button type="button" class="pembelian-modal-close" onclick="closePembelianModal()">✕</button>
    </div>
    <form method="POST" id="bForm">
      <div class="pembelian-modal-body">

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Supplier <span class="required">*</span></label>
            <select class="form-input" name="id_supplier" required>
              <option value="">— Pilih Supplier —</option>
              <?php foreach ($sRows as $s): ?>
              <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Barang <span class="required">*</span></label>
            <select class="form-input" name="id_barang" id="selectBarang" required
                    onchange="previewHargaBeli(this)">
              <option value="">— Pilih Barang —</option>
              <?php foreach ($bRows as $b): ?>
              <option value="<?= $b['id'] ?>"
                      data-harga="<?= $b['harga_beli'] ?>"
                      data-stok="<?= $b['stok'] ?>"
                      data-nama="<?= htmlspecialchars($b['nama_barang']) ?>">
                <?= htmlspecialchars($b['nama_barang']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Preview barang -->
        <div id="barangInfo" class="barang-info">
          <span>Stok saat ini: <strong id="infoStok">—</strong></span>
          <span>Harga beli lama: <strong id="infoHarga">—</strong></span>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Jumlah Beli <span class="required">*</span></label>
            <input class="form-input" type="number" name="jumlah" id="inputJumlah"
                   min="1" placeholder="0" required oninput="hitungTotalBeli()">
          </div>
          <div class="form-group">
            <label class="form-label">Harga Beli / Unit (Rp) <span class="required">*</span></label>
            <input class="form-input" type="number" name="harga_beli" id="inputHargaBeli"
                   min="1" placeholder="0" required oninput="hitungTotalBeli()">
          </div>
        </div>

        <!-- Preview total -->
        <div id="totalInfo" class="total-info">
          Total: Rp <span id="previewTotal">0</span>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Keterangan</label>
            <input class="form-input" type="text" name="keterangan"
                   placeholder="Pembelian rutin / keterangan lain">
          </div>
          <div class="form-group">
            <label class="form-label">Jatuh Tempo Pembayaran</label>
            <input class="form-input" type="date" name="jatuh_tempo">
          </div>
        </div>

      </div>
      <div class="pembelian-modal-footer">
        <button type="button" class="tb-btn" onclick="closePembelianModal()">Batal</button>
        <button type="submit" name="simpan" class="tb-btn primary">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"
                  stroke="currentColor" stroke-width="1.8" fill="none"/>
          </svg>
          Simpan & Update Stok
        </button>
      </div>
    </form>
  </div>
</div>

<script src="js/app.js?v=20"></script>
<script src="js/pembelian.js"></script>
</body>
</html>