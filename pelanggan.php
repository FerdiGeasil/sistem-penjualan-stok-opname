<?php
require_once 'koneksi.php';
requireAdmin();

$error = '';
$msg   = $_GET['msg'] ?? '';

/* ── Tambah ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $nama    = escStr($conn, $_POST['nama']    ?? '');
    $telepon = escStr($conn, $_POST['telepon'] ?? '');
    $email   = escStr($conn, $_POST['email']   ?? '');
    $alamat  = escStr($conn, $_POST['alamat']  ?? '');

    if (!$nama) {
    $error = "Nama pelanggan wajib diisi.";

} elseif ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Format email tidak valid.";

} else {

    $cek = mysqli_query(
        $conn,
        "SELECT id FROM pelanggan WHERE nama='$nama' LIMIT 1"
    );

    if (mysqli_num_rows($cek) > 0) {
        $error = "Pelanggan sudah terdaftar.";

    } else {
        mysqli_query($conn,
            "INSERT INTO pelanggan (nama, telepon, email, alamat)
             VALUES ('$nama', '$telepon', '$email', '$alamat')"
        );

        header("Location: pelanggan.php?msg=tambah");
        exit;
    }
}
}

/* ── Edit ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id      = intVal($_POST['id']      ?? 0);
    $nama    = escStr($conn, $_POST['nama']    ?? '');
    $telepon = escStr($conn, $_POST['telepon'] ?? '');
    $email   = escStr($conn, $_POST['email']   ?? '');
    $alamat  = escStr($conn, $_POST['alamat']  ?? '');

if ($id <= 0 || !$nama) {
    $error = "Data pelanggan tidak valid.";

} elseif ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Format email tidak valid.";

} else {

    $cek = mysqli_query(
        $conn,
        "SELECT id FROM pelanggan
         WHERE nama='$nama'
         AND id != $id
         LIMIT 1"
    );

    if (mysqli_num_rows($cek) > 0) {
        $error = "Nama pelanggan sudah digunakan.";

    } else {
        mysqli_query($conn,
            "UPDATE pelanggan SET
             nama='$nama',
             telepon='$telepon',
             email='$email',
             alamat='$alamat'
             WHERE id=$id"
        );

        header("Location: pelanggan.php?msg=edit");
        exit;
    }
}
}

/* ── Hapus ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus'])) {
    $id = intVal($_POST['id'] ?? 0);
    if ($id > 0) {
        /* Cek apakah pelanggan punya riwayat transaksi */
        $cek = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) c FROM penjualan WHERE id_pelanggan=$id")
        );
        if ((int)$cek['c'] > 0) {
            header("Location: pelanggan.php?msg=hapus_gagal"); exit;
        }
        mysqli_query($conn, "DELETE FROM pelanggan WHERE id=$id");
        header("Location: pelanggan.php?msg=hapus"); exit;
    }
}

/* ── Query list ── */
$search = escStr($conn, $_GET['q'] ?? '');
$where  = $search ? "WHERE nama LIKE '%$search%' OR telepon LIKE '%$search%'" : '';

/* Total transaksi per pelanggan */
$data  = mysqli_query($conn, "
    SELECT p.*, COUNT(pj.id) AS total_transaksi,
           COALESCE(SUM(pj.total),0) AS total_belanja
    FROM pelanggan p
    LEFT JOIN penjualan pj ON pj.id_pelanggan = p.id
    $where
    GROUP BY p.id
    ORDER BY p.nama ASC
");
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM pelanggan $where"))['c'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pelanggan PT Berkah Jaya Awing</title>
  <link rel="icon" type="image/png" sizes="32x32" href="img/logo.png?v=2">
  <link rel="icon" type="image/png" sizes="192x192" href="img/logo.png?v=2">
  <link rel="shortcut icon" href="img/logo.png?v=2">
  <link rel="stylesheet" href="css/style.css?v=20"/>
  <link rel="stylesheet" href="css/pelanggan.css"/>
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

        <h2>Data Pelanggan</h2>
        <p>Kelola data pelanggan / customer</p>
      </div>
      <div class="topbar-actions">
        <button type="button" class="tb-btn primary" onclick="openPelangganModal()">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
            <line x1="12" y1="5" x2="12" y2="19" stroke="currentColor" stroke-width="2"/>
            <line x1="5" y1="12" x2="19" y2="12" stroke="currentColor" stroke-width="2"/>
          </svg>
          Tambah Pelanggan
        </button>
      </div>
    </div>

    <div class="content">

      <?php if ($msg === 'tambah'): ?>
      <div class="alert alert-success auto-close"><span>Pelanggan berhasil ditambahkan!</span></div>
      <?php elseif ($msg === 'edit'): ?>
      <div class="alert alert-success auto-close"><span>Pelanggan berhasil diperbarui!</span></div>
      <?php elseif ($msg === 'hapus'): ?>
      <div class="alert alert-danger auto-close"><span>Pelanggan berhasil dihapus.</span></div>
      <?php elseif ($msg === 'hapus_gagal'): ?>
      <div class="alert alert-warning auto-close">
        <span>Tidak bisa dihapus — pelanggan masih memiliki riwayat transaksi.</span>
      </div>
      <?php endif; ?>

      <?php if ($error): ?>
      <div class="alert alert-danger"><span><?= htmlspecialchars($error) ?></span></div>
      <?php endif; ?>

      <!-- Toolbar -->
      <form method="GET" class="toolbar pelanggan-toolbar">
        <div class="search-box">
          <svg class="search-icon" width="14" height="14" fill="none" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="1.8"/>
            <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.8"/>
          </svg>
          <input type="text" name="q" placeholder="Cari nama / telepon…"
                 value="<?= htmlspecialchars($search) ?>">
        </div>
        <button type="submit" class="tb-btn btn-cari-pelanggan">Cari</button>
        <?php if ($search): ?>
        <a href="pelanggan.php" class="tb-btn">Reset</a>
        <?php endif; ?>
      </form>

      <!-- Table -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">Daftar Pelanggan</span>
          <span class="text-muted pelanggan-count"><?= $total ?> pelanggan</span>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th class="col-no">No</th>
                <th>Nama Pelanggan</th>
                <th>Telepon</th>
                <th>Email</th>
                <th>Alamat</th>
                <th class="text-center">Total Transaksi</th>
                <th>Total Belanja</th>
                <th class="col-aksi">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $no  = 1;
              $ada = false;
              while ($row = mysqli_fetch_assoc($data)):
                $ada = true;
              ?>
              <tr>
                <td class="text-muted"><?= $no++ ?></td>
                <td class="fw-600"><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars($row['telepon'] ?? '-') ?></td>
                <td class="text-muted"><?= htmlspecialchars($row['email'] ?? '-') ?></td>
                <td class="text-muted pelanggan-alamat">
                  <?= htmlspecialchars($row['alamat'] ?? '-') ?>
                </td>
                <td class="text-center">
                  <span class="badge badge-info"><?= $row['total_transaksi'] ?> trx</span>
                </td>
                <td class="fw-600">Rp <?= number_format($row['total_belanja']) ?></td>

                <td>
<div class="action-group">

<button type="button"
class="act-btn"
title="Edit"
onclick="openEditPelangganModal(
<?= $row['id'] ?>,
'<?= addslashes($row['nama']) ?>',
'<?= addslashes($row['telepon'] ?? '') ?>',
'<?= addslashes($row['email'] ?? '') ?>',
'<?= addslashes($row['alamat'] ?? '') ?>'
)">
<svg width="13" height="13" fill="none" viewBox="0 0 24 24">
<path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"
stroke="currentColor" stroke-width="1.8"/>
<path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"
stroke="currentColor" stroke-width="1.8"/>
</svg>
</button>

<button type="button"
class="act-btn act-del"
title="Hapus"
onclick="openDeletePelangganModal(<?= $row['id'] ?>)">
<svg width="13" height="13" fill="none" viewBox="0 0 24 24">
<polyline points="3 6 5 6 21 6"
stroke="currentColor" stroke-width="1.8"/>
<path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6M10 11v6M14 11v6"
stroke="currentColor" stroke-width="1.8"/>
</svg>
</button>

</div>
</td>

              </tr>
              <?php endwhile; ?>
              <?php if (!$ada): ?>
              <tr><td colspan="8" class="empty-pelanggan">
                Belum ada data pelanggan
              </td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>
<div id="modalBackdrop" class="modal-backdrop">
  <div class="modal">

    <div class="modal-header">
      <span class="modal-title" id="modalTitle">Tambah Pelanggan</span>
      <button type="button" class="modal-close" onclick="closePelangganModal()">×</button>
    </div>

    <form method="POST" id="pForm">
      <input type="hidden" id="pId" name="id">

      <div class="modal-body">

        <div class="form-group">
          <label class="form-label">Nama Pelanggan <span class="required">*</span></label>
          <input class="form-input" type="text" id="pNama" name="nama" placeholder="Budi Santoso" required>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Telepon</label>
            <input class="form-input" type="text" id="pTelp" name="telepon" placeholder="0812-xxxx-xxxx">
          </div>

          <div class="form-group">
            <label class="form-label">Email</label>
            <input class="form-input" type="email" id="pEmail" name="email" placeholder="pelanggan@email.com">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Alamat</label>
          <input class="form-input" type="text" id="pAlamat" name="alamat" placeholder="Jl. Contoh No.1, Jakarta">
        </div>

      </div>
<div class="modal-footer">

<button
type="button"
class="tb-btn"
onclick="closePelangganModal()">

Batal
</button>

<button
type="submit"
id="pSubmitBtn"
class="tb-btn primary"
name="tambah">

Simpan
</button>

</div>

</form>
</div>
</div>

<div class="modal-delete-backdrop" id="deletePelangganModal">
  <div class="modal-delete-box">
    <div class="modal-delete-icon">!</div>
    <h3>Hapus Pelanggan?</h3>
    <p>Data pelanggan akan dihapus jika belum memiliki transaksi.</p>

    <form method="POST" class="modal-delete-actions">
      <input type="hidden" name="id" id="deletePelangganId">

      <button type="button" class="tb-btn" onclick="closeDeletePelangganModal()">Batal</button>
      <button type="submit" name="hapus" class="tb-btn danger">Hapus</button>
    </form>
  </div>
</div>


<script src="js/app.js?v=20"></script>
<script src="js/pelanggan.js"></script>
</body>
</html>