<?php
require_once 'koneksi.php';
requireAdmin();

$error = '';
$msg   = $_GET['msg'] ?? '';

/* ── Tambah ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $nama    = escStr($conn, $_POST['nama'] ?? '');
    $telepon = escStr($conn, $_POST['telepon'] ?? '');
    $email   = escStr($conn, $_POST['email'] ?? '');
    $alamat  = escStr($conn, $_POST['alamat'] ?? '');

    if (!$nama) {
        $error = "Nama supplier wajib diisi.";

    } elseif ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";

    } else {
        $cek = mysqli_query(
            $conn,
            "SELECT id FROM supplier WHERE nama='$nama' LIMIT 1"
        );

        if (mysqli_num_rows($cek) > 0) {
            $error = "Supplier sudah terdaftar.";

        } else {
            if (!mysqli_query(
                $conn,
                "INSERT INTO supplier (nama, telepon, email, alamat)
                 VALUES ('$nama', '$telepon', '$email', '$alamat')"
            )) {
                $error = "Gagal menambahkan supplier: " . mysqli_error($conn);

            } else {
                header("Location: supplier.php?msg=tambah");
                exit;
            }
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
    $error = "Data supplier tidak valid.";
} elseif ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Format email tidak valid.";
} else {

$cek = mysqli_query(
    $conn,
    "SELECT id FROM supplier
     WHERE nama='$nama'
     AND id != $id
     LIMIT 1"
);

if (mysqli_num_rows($cek) > 0) {
    $error = "Nama supplier sudah digunakan.";
} else {

        if (!mysqli_query(
    $conn,
    "UPDATE supplier SET
     nama='$nama',
     telepon='$telepon',
     email='$email',
     alamat='$alamat'
     WHERE id=$id"
)) {
    $error = "Gagal memperbarui supplier: " . mysqli_error($conn);
} else {
    header("Location: supplier.php?msg=edit");
    exit;
}
}

}
}
/* ── Hapus (POST) ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus'])) {
    $id = intVal($_POST['id'] ?? 0);
    if ($id > 0) {
        /* Jangan hapus kalau masih dipakai pembelian */
        $cek = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) c FROM pembelian WHERE id_supplier=$id")
        );
        if ((int)$cek['c'] > 0) {
            header("Location: supplier.php?msg=hapus_gagal"); exit;
        }
        mysqli_query($conn, "DELETE FROM supplier WHERE id=$id");
        header("Location: supplier.php?msg=hapus"); exit;
    }
}

/* ── Query list ── */
$search = escStr($conn, $_GET['q'] ?? '');
$where  = $search ? "WHERE nama LIKE '%$search%' OR telepon LIKE '%$search%'" : '';
$data   = mysqli_query($conn, "SELECT * FROM supplier $where ORDER BY nama ASC");
$total  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM supplier $where"))['c'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Supplier PT Berkah Jaya Awing</title>
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

        <h2>Data Supplier</h2>
        <p>Kelola data supplier / pemasok</p>
      </div>
      <div class="topbar-actions">
        <button type="button" class="tb-btn primary" onclick="openSupplierModal()">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
            <line x1="12" y1="5" x2="12" y2="19" stroke="currentColor" stroke-width="2"/>
            <line x1="5" y1="12" x2="19" y2="12" stroke="currentColor" stroke-width="2"/>
          </svg>
          Tambah Supplier
        </button>
      </div>
    </div>

    <div class="content">

      <?php if ($msg === 'tambah'): ?>
      <div class="alert alert-success auto-close"><span>Supplier berhasil ditambahkan!</span></div>
      <?php elseif ($msg === 'edit'): ?>
      <div class="alert alert-success auto-close"><span>Supplier berhasil diperbarui!</span></div>
      <?php elseif ($msg === 'hapus'): ?>
      <div class="alert alert-danger auto-close"><span>Supplier berhasil dihapus.</span></div>
      <?php elseif ($msg === 'hapus_gagal'): ?>
      <div class="alert alert-warning auto-close">
        <span>Tidak bisa dihapus — supplier masih memiliki riwayat pembelian.</span>
      </div>
      <?php endif; ?>

      <?php if ($error): ?>
      <div class="alert alert-danger"><span><?= htmlspecialchars($error) ?></span></div>
      <?php endif; ?>

      <!-- Toolbar -->
      <form method="GET" class="toolbar supplier-toolbar">
        <div class="search-box">
          <svg class="search-icon" width="14" height="14" fill="none" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="1.8"/>
            <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.8"/>
          </svg>
          <input type="text" name="q" placeholder="Cari nama / telepon…"
                 value="<?= htmlspecialchars($search) ?>">
        </div>
        <button type="submit" class="tb-btn btn-cari-supplier">
          Cari
        </button>
        <?php if ($search): ?>
        <a href="supplier.php" class="tb-btn">Reset</a>
        <?php endif; ?>
      </form>

      <!-- Table -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">Daftar Supplier</span>
          <span class="text-muted supplier-count"><?= $total ?> supplier</span>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th class="th-no">No</th>
                <th>Nama Supplier</th>
                <th>Telepon</th>
                <th>Email</th>
                <th>Alamat</th>
                <th class="th-action">Aksi</th>
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
                <td class="text-muted td-address">
                  <?= htmlspecialchars($row['alamat'] ?? '-') ?>
                </td>
                <td>
                  <div class="action-group">
                    <button class="act-btn" title="Edit"
                            onclick="openEditSupplierModal(
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
        onclick="openDeleteSupplierModal(<?= $row['id'] ?>)">
                    
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24">
                          <polyline points="3 6 5 6 21 6" stroke="currentColor" stroke-width="1.8"/>
                          <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6M10 11v6M14 11v6"
                                stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                      </button>


                  </div>
                </td>
              </tr>
              <?php endwhile; ?>
              <?php if (!$ada): ?>
              <tr>
              <td colspan="6" class="empty-state">
                Belum ada data supplier
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
  <div class="modal-box">
    <div class="modal-header">
      <span class="modal-title" id="modalTitle" id="modalTitle">Tambah Supplier</span>
      <button onclick="closeSupplierModal()" class="modal-close">✕</button>
    </div>
    <form method="POST" id="sForm">
      <input type="hidden" name="id" id="sId">
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Nama Supplier <span class="required">*</span></label>
          <input class="form-input" type="text" name="nama" id="sNama"
                 placeholder="PT Sumber Wangi" required>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Telepon</label>
            <input class="form-input" type="text" name="telepon" id="sTelp"
                   placeholder="0812-xxxx-xxxx">
          </div>
          <div class="form-group">
            <label class="form-label">Email</label>
            <input class="form-input" type="email" name="email" id="sEmail"
                   placeholder="supplier@email.com">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Alamat</label>
          <input class="form-input" type="text" name="alamat" id="sAlamat"
                 placeholder="Jl. Contoh No. 1, Jakarta">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="tb-btn" onclick="closeSupplierModal()">Batal</button>
        <button type="submit" class="tb-btn primary" id="sSubmitBtn">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
function openSupplierModal() {
  document.getElementById('modalTitle').textContent = 'Tambah Supplier';
  document.getElementById('sId').value    = '';
  document.getElementById('sNama').value  = '';
  document.getElementById('sTelp').value  = '';
  document.getElementById('sEmail').value = '';
  document.getElementById('sAlamat').value= '';
  document.getElementById('sSubmitBtn').name = 'tambah';
  document.getElementById('modalBackdrop').style.display = 'flex';
}
function openEditSupplierModal(id, nama, telp, email, alamat) {
  document.getElementById('modalTitle').textContent = 'Edit Supplier';
  document.getElementById('sId').value    = id;
  document.getElementById('sNama').value  = nama;
  document.getElementById('sTelp').value  = telp;
  document.getElementById('sEmail').value = email;
  document.getElementById('sAlamat').value= alamat;
  document.getElementById('sSubmitBtn').name = 'edit';
  document.getElementById('modalBackdrop').style.display = 'flex';
}
function closeSupplierModal() {
  document.getElementById('modalBackdrop').style.display = 'none';
}
document.getElementById('modalBackdrop').addEventListener('click', function(e) {
  if (e.target === this) closeSupplierModal();
});
document.querySelectorAll('.alert.auto-close').forEach(el => {
  setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 400); }, 3500);
});
</script>
<script src="js/app.js"></script>

<script>
function openDeleteSupplierModal(id){
    document.getElementById("deleteSupplierModal").style.display="flex";
    document.getElementById("deleteSupplierId").value=id;
}

function closeDeleteSupplierModal(){
    document.getElementById("deleteSupplierModal").style.display="none";
}
</script>

<div class="modal-delete-backdrop" id="deleteSupplierModal">

  <div class="modal-delete-box">

      <h3>Hapus Supplier</h3>
      <p>Supplier yang dihapus tidak dapat dikembalikan.</p>

      <div class="modal-delete-actions">

          <button type="button"
                  class="tb-btn"
                  onclick="closeDeleteSupplierModal()">
              Batal
          </button>

          <form method="POST" id="deleteSupplierForm">
              <input type="hidden"
                     name="id"
                     id="deleteSupplierId">

              <button type="submit"
                      name="hapus"
                      class="tb-btn danger">
                  Hapus
              </button>
          </form>

      </div>

  </div>

</div>
</body>
</html>