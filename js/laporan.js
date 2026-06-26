document.addEventListener('DOMContentLoaded', function () {
  const btnPrint = document.getElementById('btnPrintLaporan');

  const urlParams = new URLSearchParams(window.location.search);
  const tab = urlParams.get('tab') || 'penjualan';

  const titleBrowser =
    tab === 'stok'
      ? 'Laporan Stok - PT Berkah Jaya Awing'
      : 'Laporan Penjualan - PT Berkah Jaya Awing';

  document.title = titleBrowser;

  if (btnPrint) {
    btnPrint.addEventListener('click', function () {
      const sekarang = new Date();

      const tgl = String(sekarang.getDate()).padStart(2, '0');
      const bln = String(sekarang.getMonth() + 1).padStart(2, '0');
      const tahun = sekarang.getFullYear();
      const jam = String(sekarang.getHours()).padStart(2, '0');
      const menit = String(sekarang.getMinutes()).padStart(2, '0');

      const jenisLaporan =
        tab === 'stok'
          ? 'Laporan_Stok_Barang'
          : 'Laporan_Penjualan';

      document.title =
        `PTBJA_${jenisLaporan}_${tgl}-${bln}-${tahun}_Jam_${jam}-${menit}`;

      window.print();

      setTimeout(function () {
        document.title = titleBrowser;
      }, 1000);
    });
  }
});