<?php
require_once 'koneksi.php';
requireAdmin();

$error = '';
$msg   = $_GET['msg'] ?? '';

/* ── Tambah ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' 
&& ($_POST['aksi'] ?? '') === 'tambah') {
    $nama      = escStr($conn, $_POST['nama'] ?? '');
    $deskripsi = escStr($conn, $_POST['deskripsi'] ?? '');

    if (!$nama) {
        $error = "Nama kategori wajib diisi.";
    } else {
        $cek = mysqli_query($conn, "SELECT id FROM kategori WHERE nama='$nama' LIMIT 1");

        if (mysqli_num_rows($cek) > 0) {
            $error = "Kategori sudah ada.";
        } else {
            mysqli_query($conn, "
                INSERT INTO kategori (nama, deskripsi)
                VALUES ('$nama', '$deskripsi')
            ");

            header("Location: kategori.php?msg=tambah");
            exit;
        }
    }
}

/* ── Edit ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST'
&& ($_POST['aksi'] ?? '') === 'edit') {
    $id        = intVal($_POST['id'] ?? 0);
    $nama      = escStr($conn, $_POST['nama'] ?? '');
    $deskripsi = escStr($conn, $_POST['deskripsi'] ?? '');

    if ($id <= 0 || !$nama) {
        $error = "Data kategori tidak valid.";
    } else {
        $cek = mysqli_query($conn, "
            SELECT id FROM kategori
            WHERE nama='$nama'
            AND id != $id
            LIMIT 1
        ");

        if (mysqli_num_rows($cek) > 0) {
            $error = "Nama kategori sudah digunakan.";
        } else {
            mysqli_query($conn, "
                UPDATE kategori
                SET nama='$nama',
                    deskripsi='$deskripsi'
                WHERE id=$id
            ");

            header("Location: kategori.php?msg=edit");
            exit;
        }
    }
}

/* ── Hapus (POST) ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus'])) {
    $id = intVal($_POST['id'] ?? 0);
    if ($id > 0) {
        /* Jangan hapus kalau masih dipakai barang */
        $cek = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) c FROM barang WHERE id_kategori=$id")
        );
        if ((int)$cek['c'] > 0) {
            header("Location: kategori.php?msg=hapus_gagal"); exit;
        }
        mysqli_query($conn, "DELETE FROM kategori WHERE id=$id");
        header("Location: kategori.php?msg=hapus"); exit;
    }
}

/* ── Data edit (untuk modal) ── */
$editData = null;
if (isset($_GET['edit'])) {
    $eid = intVal($_GET['edit']);
    $editData = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM kategori WHERE id=$eid LIMIT 1")
    );
}

/* ── Query list ── */
$search = escStr($conn, $_GET['q'] ?? '');
$where  = $search ? "WHERE nama LIKE '%$search%'" : '';
$data   = mysqli_query($conn, "SELECT * FROM kategori $where ORDER BY nama ASC");
$total  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM kategori $where"))['c'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Kategori PT BJA</title>
  <link rel="icon" type="image/png" sizes="32x32" href="img/logo.png?v=2">
  <link rel="icon" type="image/png" sizes="192x192" href="img/logo.png?v=2">
  <link rel="shortcut icon" href="img/logo.png?v=2">
  <link rel="stylesheet" href="css/style.css?v=20"/>
  <link rel="stylesheet" href="css/kategori.css"/>
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
        <h2>Kategori Barang</h2>
        <p>Kelola kategori produk</p>
      </div>
      <div class="topbar-actions">
        <button class="tb-btn primary" id="btnOpenKategori">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
            <line x1="12" y1="5" x2="12" y2="19" stroke="currentColor" stroke-width="2"/>
            <line x1="5" y1="12" x2="19" y2="12" stroke="currentColor" stroke-width="2"/>
          </svg>
          Tambah Kategori
        </button>
      </div>
    </div>

    <div class="content">

      <?php if ($msg === 'tambah'): ?>
      <div class="alert alert-success auto-close"><span>Kategori berhasil ditambahkan!</span></div>
      <?php elseif ($msg === 'edit'): ?>
      <div class="alert alert-success auto-close"><span>Kategori berhasil diperbarui!</span></div>

      <?php elseif ($msg === 'hapus'): ?>
      <div class="alert alert-success auto-close"><span>Kategori berhasil dihapus.</span></div>

      <?php elseif ($msg === 'hapus_gagal'): ?>
      <div class="alert alert-warning auto-close">
        <span>Tidak bisa dihapus — kategori masih digunakan oleh barang.</span>
      </div>
      <?php endif; ?>

      <?php if ($error): ?>
      <div class="alert alert-danger"><span><?= htmlspecialchars($error) ?></span></div>
      <?php endif; ?>

      <!-- Toolbar -->
      <form method="GET" class="toolbar">
        <div class="search-box">
          <svg class="search-icon" width="14" height="14" fill="none" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="1.8"/>
            <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.8"/>
          </svg>
          <input type="text" name="q" placeholder="Cari kategori…"
                 value="<?= htmlspecialchars($search) ?>">
        </div>
        <button type="submit" class="tb-btn btn-cari-kategori">
          Cari
        </button>
        
        <?php if ($search): ?>
        <a href="kategori.php" class="tb-btn">Reset</a>
        <?php endif; ?>
      </form>

      <!-- Table -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">Daftar Kategori</span>
          <span class="text-muted kategori-count"><?= $total ?> kategori</span>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th class="col-no">No</th>
                <th>Nama Kategori</th>
                <th>Deskripsi</th>
                <th class="col-aksi">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $no = 1;
              $ada = false;
              while ($row = mysqli_fetch_assoc($data)):
                $ada = true;
              ?>
              <tr>
                <td class="text-muted"><?= $no++ ?></td>
                <td class="fw-600"><?= htmlspecialchars($row['nama']) ?></td>
                <td class="text-muted"><?= htmlspecialchars($row['deskripsi'] ?? '-') ?></td>
                <td>
                  <div class="action-group">
                    <button type="button"
                    class="act-btn"
                    title="Edit"
                    onclick='openEditModal(
                    <?= $row["id"] ?>,
                    <?= json_encode($row["nama"]) ?>,
                    <?= json_encode($row["deskripsi"] ?? "") ?>
                    )'>
                      <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"
                              stroke="currentColor" stroke-width="1.8"/>
                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"
                              stroke="currentColor" stroke-width="1.8"/>
                      </svg>
                    </button>
                    <form method="POST" class="form-hapus-kategori">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <input type="hidden" name="hapus" value="1">

                        <button type="submit" class="act-btn act-del" title="Hapus">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
                          <polyline points="3 6 5 6 21 6" stroke="currentColor" stroke-width="1.8"/>
                          <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6M10 11v6M14 11v6"
                                stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              <?php endwhile; ?>
              <?php if (!$ada): ?>
              <tr><td colspan="4" class="empty-kategori">
                Belum ada kategori
              </td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Modal Tambah/Edit -->
<div id="modalBackdrop" class="modal-backdrop">
  <div class="kategori-modal-box">
    <div class="kategori-modal-header">
      <span class="kategori-modal-title" id="modalTitle">Tambah Kategori</span>
      <button type="button" onclick="closeKategoriModal()" class="kategori-modal-close">✕</button>
    </div>
    <form method="POST" id="kForm">
  <input type="hidden" name="id" id="kId">
  <input type="hidden" name="aksi" id="kAksi" value="tambah">
      <div class="kategori-modal-body">
        <div class="form-group">
          <label class="form-label">Nama Kategori <span class="required">*</span></label>
          <input class="form-input" type="text" name="nama" id="kNama"
                 placeholder="Pet Care" required>
        </div>
        <div class="form-group">
          <label class="form-label">Deskripsi</label>
          <input class="form-input" type="text" name="deskripsi" id="kDesc"
                 placeholder="Produk perawatan hewan peliharaan">
        </div>
      </div>
      <div class="kategori-modal-footer">
        <button type="button" class="tb-btn" onclick="closeKategoriModal()">Batal</button>
        <button type="submit" class="tb-btn primary" id="kSubmitBtn">Simpan</button>
      </div>
    </form>
  </div>
</div>

<div id="modalHapusKategori" class="modal-delete-backdrop">
  <div class="modal-delete-box">
    <div class="modal-delete-icon">!</div>

    <h3>Hapus Kategori?</h3>
    <p>Data kategori akan dihapus permanen. Pastikan kategori tidak sedang digunakan oleh data barang.</p>

    <div class="modal-delete-actions">
      <button type="button" class="tb-btn" onclick="closeDeleteKategoriModal()">Batal</button>
      <button type="button" class="tb-btn danger" id="btnConfirmDeleteKategori">
        Ya, Hapus
      </button>
    </div>
  </div>
</div>

<script src="js/app.js?v=20"></script>
<script src="js/kategori.js"></script>
</body>
</html>