<?php
require_once 'koneksi.php';
requireAdmin();

$msg   = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $updates = $_POST['stok_fisik'] ?? [];
    $keterangan = escStr($conn, $_POST['keterangan'] ?? 'Stok Opname');

    mysqli_begin_transaction($conn);

    try {
        foreach ($updates as $id => $stok_fisik) {
            $id = intval($id);
            $stok_fisik = intval($stok_fisik);

            $getStok = mysqli_query($conn, "SELECT stok FROM barang WHERE id=$id FOR UPDATE");

            if (!$getStok) {
                throw new Exception(mysqli_error($conn));
            }

            $r = mysqli_fetch_assoc($getStok);

            if (!$r) {
                continue;
            }

            $stok_sistem = intval($r['stok']);
            $selisih = $stok_fisik - $stok_sistem;

            if ($selisih == 0) {
                continue;
            }

            if (!mysqli_query($conn, "UPDATE barang SET stok=$stok_fisik WHERE id=$id")) {
                throw new Exception(mysqli_error($conn));
            }

            if (!mysqli_query($conn, "
                INSERT INTO stok_opname_log
                (id_barang, stok_sistem, stok_fisik, selisih, keterangan, tanggal)
                VALUES
                ($id, $stok_sistem, $stok_fisik, $selisih, '$keterangan', NOW())
            ")) {
                throw new Exception(mysqli_error($conn));
            }
        }

        mysqli_commit($conn);
        $msg = "Stok opname berhasil disimpan!";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = "Stok opname gagal: " . $e->getMessage();
    }
}

$data = mysqli_query(
    $conn,
    "SELECT barang.*, kategori.nama AS kategori
     FROM barang
     LEFT JOIN kategori ON barang.id_kategori = kategori.id
     ORDER BY barang.nama_barang ASC"
);

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Stok Opname  PT Berkah Jaya Awing</title>
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

        <h2>Stok Opname</h2>
        <p>Inventarisasi fisik & penyesuaian stok · <?= date('d F Y') ?></p>
      </div>
      
<div class="topbar-actions">

    <a href="riwayat_opname.php" class="tb-btn outline-gold">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"
                    stroke="currentColor"
                    stroke-width="1.8"/>

            <polyline points="12 6 12 12 16 14"
                      stroke="currentColor"
                      stroke-width="1.8"/>
        </svg>

        Riwayat Opname
    </a>

</div>


    </div>

    <div class="content">

      <?php if ($msg): ?>
      <div class="alert alert-success auto-close"><span><?= $msg ?></span></div>
      <?php endif; ?>

          <?php if ($error): ?>
        <div class="alert alert-danger auto-close">
          <span><?= $error ?></span>
        </div>
        <?php endif; ?>
        
      <div class="card">
        <div class="card-header">
          <span class="card-title">Input Stok Fisik</span>
          <span class="card-link text-muted">Isi kolom "Stok Fisik" sesuai hasil hitung fisik</span>
        </div>
        <form method="POST">
          <div class="table-scroll">
            <table>
              <thead>
                <tr>
                  <th>No</th>
                  <th>Barcode</th>
                  <th>Nama Barang</th>
                  <th>Kategori</th>
                  <th>Stok Sistem</th>
                  <th>Stok Fisik</th>
                  <th>Selisih</th>
                </tr>
              </thead>
              <tbody id="opname-body">
                <?php $no=1; while($r=mysqli_fetch_assoc($data)): ?>
                <tr>
                  <td class="text-muted"><?= $no++ ?></td>
                  <td><span class="barcode-tag"><?= htmlspecialchars($r['barcode']) ?></span></td>
                  <td class="fw-600"><?= htmlspecialchars($r['nama_barang']) ?></td>
                  <td><?= htmlspecialchars($r['kategori'] ?? '-') ?></td>
                  <td class="stok-sistem" data-stok="<?= $r['stok'] ?>"><?= $r['stok'] ?></td>
                  <td>
                    <input type="number"
                                name="stok_fisik[<?= $r['id'] ?>]"
                                class="form-input stok-fisik input-opname"
                                min="0"
                                value="<?= $r['stok'] ?>"
                                oninput="hitungSelisih(this)">
                  </td>
                  <td class="selisih fw-600 selisih-default">0</td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>

          <div class="opname-actions">
            <input type="text"
                        name="keterangan"
                        class="form-input input-keterangan"
                        placeholder="Keterangan (opsional)">
            <button type="submit" name="simpan" class="tb-btn primary">
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
                <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"
                      stroke="currentColor" stroke-width="1.8" fill="none"/>
              </svg>
              Simpan Opname
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>
<script>
function hitungSelisih(input) {
  const tr = input.closest('tr');
  const sistem = parseInt(tr.querySelector('.stok-sistem').dataset.stok) || 0;
  const fisik = parseInt(input.value) || 0;
  const selisih = fisik - sistem;
  const cell = tr.querySelector('.selisih');

  cell.textContent = (selisih > 0 ? '+' : '') + selisih;

  cell.classList.remove(
    'selisih-default',
    'selisih-plus',
    'selisih-minus'
  );

  if (selisih > 0) {
    cell.classList.add('selisih-plus');
  } else if (selisih < 0) {
    cell.classList.add('selisih-minus');
  } else {
    cell.classList.add('selisih-default');
  }
}
document.querySelectorAll('.alert.auto-close').forEach(el=>{
  setTimeout(()=>{el.style.opacity='0';setTimeout(()=>el.remove(),400);},3500);
});
</script>
<script src="js/app.js?v=20"></script>
</body>
</html>