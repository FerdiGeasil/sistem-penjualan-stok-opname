<?php
require_once 'koneksi.php';
requireLogin();

$error   = '';
$success = '';
$uang_bayar = intVal($_POST['uang_bayar'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {

    $cart_json = $_POST['cart_data'] ?? '';
    $cart = json_decode($cart_json, true);

    if (!is_array($cart) || count($cart) === 0) {
        $error = "Keranjang masih kosong.";
    } else {

        mysqli_begin_transaction($conn);

        try {
            $items = [];
            $total = 0;

            foreach ($cart as $item) {
                $id_barang = intval($item['id'] ?? 0);
                $jumlah    = intval($item['qty'] ?? 0);

                if ($id_barang <= 0 || $jumlah <= 0) {
                    throw new Exception("Data keranjang tidak valid.");
                }

                $brg = mysqli_fetch_assoc(
                    mysqli_query($conn, "SELECT * FROM barang WHERE id=$id_barang LIMIT 1 FOR UPDATE")
                );

                if (!$brg) {
                    throw new Exception("Barang tidak ditemukan.");
                }

                if ($jumlah > (int)$brg['stok']) {
                    throw new Exception("Stok {$brg['nama_barang']} tidak cukup. Stok tersisa: {$brg['stok']} unit.");
                }

                $harga = (int)$brg['harga_jual'];
                $subtotal = $harga * $jumlah;
                $total += $subtotal;

                $items[] = [
                    'id_barang' => $id_barang,
                    'jumlah'    => $jumlah,
                    'harga'     => $harga,
                    'nama'      => $brg['nama_barang']
                ];
            }

            if ($uang_bayar < $total) {
                throw new Exception("Uang bayar kurang.");
            }

            if (!mysqli_query($conn,
                "INSERT INTO penjualan (tanggal, total, status)
                 VALUES (NOW(), $total, 'selesai')"
            )) {
                throw new Exception(mysqli_error($conn));
            }

            $id_penjualan = mysqli_insert_id($conn);

            foreach ($items as $it) {
                $id_barang = $it['id_barang'];
                $jumlah    = $it['jumlah'];
                $harga     = $it['harga'];

                if (!mysqli_query($conn,
                    "INSERT INTO detail_penjualan (id_penjualan, id_barang, jumlah, harga)
                     VALUES ($id_penjualan, $id_barang, $jumlah, $harga)"
                )) {
                    throw new Exception(mysqli_error($conn));
                }

                if (!mysqli_query($conn,
                    "UPDATE barang 
                     SET stok = stok - $jumlah 
                     WHERE id = $id_barang"
                )) {
                    throw new Exception(mysqli_error($conn));
                }
            }

            mysqli_commit($conn);

            $success = "Transaksi berhasil! Total: <strong>Rp " . number_format($total, 0, ',', '.') . "</strong>";
            $_POST = [];

        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "Transaksi gagal: " . $e->getMessage();
        }
    }
}

$barang = mysqli_query($conn, "
    SELECT 
        barang.id,
        barang.barcode,
        barang.nama_barang,
        barang.harga_jual,
        barang.stok,
        barang.min_stok,
        kategori.nama AS kategori
    FROM barang
    LEFT JOIN kategori ON barang.id_kategori = kategori.id
    WHERE barang.stok > 0
    ORDER BY barang.nama_barang ASC
");

$rows = [];
$products = [];

while ($r = mysqli_fetch_assoc($barang)) {
    $rows[] = $r;

    $products[] = [
        'id'       => (int)$r['id'],
        'barcode'  => $r['barcode'],
        'nama'     => $r['nama_barang'],
        'harga'    => (int)$r['harga_jual'],
        'stok'     => (int)$r['stok'],
        'min_stok' => (int)$r['min_stok'],
        'kategori' => $r['kategori'] ?: 'Lainnya'
    ];
}

$histori = mysqli_query($conn, "
    SELECT p.id, p.tanggal, p.total, b.nama_barang, dp.jumlah, dp.harga
    FROM penjualan p
    JOIN detail_penjualan dp ON dp.id_penjualan = p.id
    JOIN barang b ON b.id = dp.id_barang
    WHERE DATE(p.tanggal) = CURDATE()
    ORDER BY p.id DESC LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Transaksi / POS PT BJA</title>
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


<h2>Transaksi Manual</h2>
<p>Input penjualan admin · <?= date('d F Y') ?></p>


      </div>
    </div>

    <div class="content">

      <?php if ($success): ?>
      <div class="alert alert-success auto-close">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24">
          <path d="M22 11.08V12a10 10 0 11-5.93-9.14" stroke="currentColor" stroke-width="1.8" fill="none"/>
          <polyline points="22 4 12 14.01 9 11.01" stroke="currentColor" stroke-width="1.8" fill="none"/>
        </svg>
        <span><?= $success ?></span>
      </div>
      <?php endif; ?>

      <?php if ($error): ?>
      <div class="alert alert-danger auto-close">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8" fill="none"/>
          <line x1="12" y1="8"  x2="12"    y2="12" stroke="currentColor" stroke-width="1.8"/>
          <line x1="12" y1="16" x2="12.01" y2="16" stroke="currentColor" stroke-width="2"/>
        </svg>
        <span><?= htmlspecialchars($error) ?></span>
      </div>
      <?php endif; ?>

<div class="grid-pos">

  <!-- KIRI: PRODUK -->
  <div class="pos-left">
    <div class="card">
      <div class="card-header">
        <span class="card-title">Pilih Produk</span>
        <span style="font-size:11.5px;color:var(--text-muted)">
          Klik produk untuk pilih
        </span>
      </div>

      <div class="card-body">
        <input type="text"
               id="searchBarang"
               class="form-input"
               placeholder="Cari nama produk...">

        <div class="product-grid">
          <?php foreach ($rows as $b): ?>
          <div class="product-card"
               data-id="<?= $b['id'] ?>"
               onclick="pilihBarang(<?= $b['id'] ?>)">

            <div class="product-info">
              <h4><?= htmlspecialchars($b['nama_barang']) ?></h4>
              <small>Stok: <?= $b['stok'] ?> unit</small>
            </div>

            <div class="product-price">
              Rp <?= number_format($b['harga_jual'], 0, ',', '.') ?>
            </div>

          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- KANAN: FORM BAYAR + HISTORI -->
  <div class="pos-right">

    <div class="card">
      <div class="card-header">
        <span class="card-title">Ringkasan Transaksi</span>
      </div>

      <div class="card-body">
        <form method="POST" id="posForm">

        <input type="hidden" name="cart_data" id="cart_data">

<div id="emptyCart" class="empty-state">
  Keranjang masih kosong
</div>

<div id="cartItems" class="cart-items"></div>

<div class="barang-preview">
  <div class="preview-row">
    <span class="preview-label">Subtotal</span>
    <span class="preview-val" id="subtotal">Rp 0</span>
  </div>

  <div class="preview-row">
    <span class="preview-label">Kembalian</span>
    <span class="preview-val" id="kembalian">Rp 0</span>
  </div>
</div>

<div class="form-group">
  <label class="form-label">Uang Bayar</label>
  <input class="form-input"
         type="number"
         name="uang_bayar"
         value="0"
         id="uang_bayar"
         min="0"
         oninput="hitungKembalianPOS()">
</div>

<div class="form-actions">
  <button type="button"
          class="tb-btn"
          onclick="resetFormPOS()">
    Reset
  </button>

  <button type="submit"
          name="simpan"
          class="tb-btn primary">
    Simpan Transaksi
  </button>
</div>

        </form>
      </div>
    </div>

  </div>

      <!-- HISTORI HARI INI -->
    <div class="card history-card pos-history-wide">
      <div class="card-header">
        <span class="card-title">Transaksi Hari Ini</span>
        <a href="laporan.php" class="card-link">Semua </a>
      </div>

      <div class="table-wrap history-table">
        <table>
          <thead>
            <tr>
              <th>Produk</th>
              <th>Jml</th>
              <th>Harga</th>
              <th>Total</th>
              <th>Waktu</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $ada = false;
            while ($h = mysqli_fetch_assoc($histori)):
              $ada = true;
            ?>
            <tr>
              <td><?= htmlspecialchars($h['nama_barang']) ?></td>
              <td><?= $h['jumlah'] ?></td>
              <td>Rp <?= number_format($h['harga'], 0, ',', '.') ?></td>
              <td class="fw-600">Rp <?= number_format($h['total'], 0, ',', '.') ?></td>
              <td class="text-muted"><?= date('H:i', strtotime($h['tanggal'])) ?></td>
            </tr>
            <?php endwhile; ?>

            <?php if (!$ada): ?>
            <tr>
              <td colspan="5" class="empty-state">
                Belum ada transaksi hari ini
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

<script>
const PRODUCTS = <?= json_encode($products, JSON_UNESCAPED_UNICODE); ?>;
</script>

<script src="js/app.js?v=20"></script>
<script src="js/transaksi.js"></script>
</body>
</html>