<?php

$currentPage = basename($_SERVER['PHP_SELF']);
$currentSection = $_GET['section'] ?? 'dashboard';
$role        = $_SESSION['role'] ?? 'kasir';
$nama        = $_SESSION['nama'] ?? 'User';

function sidebarLink(string $href, string $label, string $icon, string $current)
{
    $active = ($current === $href) ? 'active' : '';
    echo "<a href='$href' class='nav-item $active'>$icon $label</a>";
}
?>


<aside class="sidebar" id="sidebar">
  <div class="logo-area">
    <div class="logo-box">
      <div class="brand-logo">
        <img src="img/logo.png" alt="PT BJA" class="logo-img">
      </div>
      <div class="logo-text">
        PT Berkah Jaya Awing
        <span>Distributor Parfum & Pet Care</span>
      </div>
    </div>
  </div>

  <nav class="nav-section">
    <div class="nav-label">Menu Utama</div>
    <a href="<?= $role == 'admin' ? 'dashboard.php' : 'dashboard_kasir.php' ?>"
class="nav-item nav-dashboard <?= $currentPage === ($role == 'admin' ? 'dashboard.php' : 'dashboard_kasir.php') ? 'active' : '' ?>">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
        <rect x="3" y="3" width="8" height="8" rx="1.5" fill="currentColor"/>
        <rect x="13" y="3" width="8" height="8" rx="1.5" fill="currentColor" opacity=".6"/>
        <rect x="3" y="13" width="8" height="8" rx="1.5" fill="currentColor" opacity=".6"/>
        <rect x="13" y="13" width="8" height="8" rx="1.5" fill="currentColor" opacity=".6"/>
      </svg>
      Dashboard
    </a>

<?php if ($role == 'kasir'): ?>
<a href="javascript:void(0)"
   onclick="showSection('pos')"
   class="nav-item nav-pos <?= $currentSection == 'pos' ? 'active' : '' ?>">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
    <circle cx="9" cy="21" r="1.5" fill="currentColor"/>
    <circle cx="20" cy="21" r="1.5" fill="currentColor"/>
    <path d="M1 1h4l2.68 13.39a2 2 0 001.98 1.61h9.72a2 2 0 001.98-1.61L23 6H6"
          stroke="currentColor" stroke-width="1.8" fill="none"/>
  </svg>
  Transaksi / POS
</a>
<?php endif; ?>

    <?php if ($role === 'admin'): ?>
    <a href="restock.php" class="nav-item <?= $currentPage==='restock.php'?'active':'' ?>">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
        <polyline points="1 4 1 10 7 10" stroke="currentColor" stroke-width="1.8" fill="none"/>
        <path d="M3.51 15a9 9 0 102.13-9.36L1 10" stroke="currentColor" stroke-width="1.8" fill="none"/>
      </svg>
      Restock
    </a>
    <?php endif; ?>

      <?php if ($role === 'admin'): ?>
    <a href="pembelian.php" class="nav-item <?= $currentPage==='pembelian.php'?'active':'' ?>">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"
              stroke="currentColor"
              stroke-width="1.8"
              fill="none"/>
        <line x1="3" y1="6"
              x2="21"
              y2="6"
              stroke="currentColor"
              stroke-width="1.8"/>
      </svg>
      Pembelian
    </a>
    <?php endif; ?>

    <?php if ($role === 'admin'): ?>
    <div class="nav-label">Master Data</div>

    <a href="barang.php" class="nav-item <?= $currentPage==='barang.php'?'active':'' ?>">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
        <path d="M20 7L12 3 4 7v10l8 4 8-4V7z" stroke="currentColor" stroke-width="1.8" fill="none"/>
        <path d="M12 3v18M4 7l8 4 8-4" stroke="currentColor" stroke-width="1.8"/>
      </svg>
      Data Barang
    </a>

    <a href="kategori.php" class="nav-item <?= $currentPage==='kategori.php'?'active':'' ?>">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
        <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"
              stroke="currentColor" stroke-width="1.8" fill="none"/>
      </svg>
      Kategori
    </a>

    <a href="supplier.php" class="nav-item <?= $currentPage==='supplier.php'?'active':'' ?>">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"
              stroke="currentColor" stroke-width="1.8" fill="none"/>
      </svg>
      Supplier
    </a>

    <a href="pelanggan.php" class="nav-item <?= $currentPage==='pelanggan.php'?'active':'' ?>">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="1.8" fill="none"/>
        <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="1.8" fill="none"/>
        <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"
              stroke="currentColor" stroke-width="1.8" fill="none"/>
      </svg>
      Pelanggan
    </a>

    <div class="nav-label">Operasional</div>

    <a href="stock_opname.php" class="nav-item <?= $currentPage==='stock_opname.php'?'active':'' ?>">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
        <path d="M9 11l3 3L22 4" stroke="currentColor" stroke-width="1.8" fill="none"/>
        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"
              stroke="currentColor" stroke-width="1.8" fill="none"/>
      </svg>
      Stok Opname
    </a>

    <a href="laporan.php" class="nav-item <?= $currentPage==='laporan.php'?'active':'' ?>">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
        <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.8" fill="none"/>
        <path d="M8 12h8M8 8h8M8 16h5" stroke="currentColor" stroke-width="1.8"/>
      </svg>
      Laporan
    </a>

    <?php endif; ?>
  </nav>

  <div class="sidebar-footer">
    <div class="user-avatar"><?= strtoupper(substr($nama, 0, 2)) ?></div>
    <div class="user-info">
      <?= htmlspecialchars($nama) ?>
      <span><?= ucfirst($role) ?></span>
    </div>

<a href="#"
   title="Logout"
   class="logout-link"
   onclick="openLogoutModal(); return false;">

  <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"
          stroke="currentColor"
          stroke-width="1.8"/>
  </svg>
</a>

  </div>
</aside>

<div class="logout-modal-overlay" id="logoutModal">
  <div class="logout-modal-box">
    <h3>Konfirmasi Logout</h3>
    <p>Apakah Anda yakin ingin logout?</p>

    <div class="logout-modal-actions">
      <button type="button" class="btn-cancel" onclick="closeLogoutModal()">Batal</button>
      <a href="logout.php" class="btn-logout">Logout</a>
    </div>
  </div>
</div>

