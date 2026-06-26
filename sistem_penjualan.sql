-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 26, 2026 at 04:45 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistem_penjualan`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id` int(11) NOT NULL,
  `nama_barang` varchar(100) DEFAULT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `id_supplier` int(11) DEFAULT NULL,
  `harga_beli` int(11) DEFAULT 0,
  `harga_jual` int(11) DEFAULT 0,
  `stok` int(11) DEFAULT 0,
  `min_stok` int(11) DEFAULT 5,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `gambar` varchar(100) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id`, `nama_barang`, `barcode`, `id_kategori`, `id_supplier`, `harga_beli`, `harga_jual`, `stok`, `min_stok`, `created_at`, `updated_at`, `gambar`) VALUES
(1, 'Cat Perfume Jeruk', 'CP001', 1, NULL, 15000, 25000, 60, 5, '2026-04-19 19:13:10', '2026-06-14 17:34:53', 'jeruk.jpg'),
(2, 'Cat Perfume Melon', 'CP002', 1, NULL, 15000, 25000, 14, 5, '2026-04-19 19:13:10', '2026-06-14 17:34:53', 'melon.jpg'),
(3, 'Cat Perfume Strawberry', 'CP003', 1, NULL, 20000, 25000, 11, 5, '2026-04-19 19:13:10', '2026-06-20 18:42:01', 'stroberi.jpg'),
(4, 'Parfum Mukena Vanilla', 'PM001', 2, NULL, 20000, 35000, 81, 6, '2026-04-19 19:13:10', '2026-06-16 06:58:06', 'vanilla.png'),
(8, 'Parfum Helm & Jaket Bubblegum', 'PHJ001', 2, NULL, 25000, 35000, 4, 5, '2026-05-25 11:52:07', '2026-06-20 18:44:27', 'parfum-helm-jaket.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `detail_penjualan`
--

CREATE TABLE `detail_penjualan` (
  `id` int(11) NOT NULL,
  `id_penjualan` int(11) DEFAULT NULL,
  `id_barang` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `harga` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_penjualan`
--

INSERT INTO `detail_penjualan` (`id`, `id_penjualan`, `id_barang`, `jumlah`, `harga`) VALUES
(1, 1, 2, 1, 25000),
(2, 1, 1, 1, 25000),
(3, 2, 4, 2, 35000);

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama`, `deskripsi`, `created_at`) VALUES
(1, 'Kosmetik Untuk Hewan', 'Produk perawatan hewan peliharaan', '2026-04-19 19:13:10'),
(2, 'Kosmetik Untuk Manusia', 'Produk perawatan manusia', '2026-04-19 19:13:10'),
(3, 'Lainnya', 'Produk lain-lain', '2026-04-19 19:13:10');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id`, `nama`, `telepon`, `email`, `alamat`, `created_at`) VALUES
(1, 'wahyu', '08213424223', 'unoam124@gmail.com', 'Jl. Ngawi Selatan No 1 ambarawa', '2026-05-23 12:51:09'),
(5, 'Faisal', '10028391', 'faisal@gmail.com', 'JL.Ahmad Yani, Jakarta Pusat', '2026-06-09 07:13:10');

-- --------------------------------------------------------

--
-- Table structure for table `pembelian`
--

CREATE TABLE `pembelian` (
  `id` int(11) NOT NULL,
  `id_supplier` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_beli` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `jatuh_tempo` date DEFAULT NULL,
  `tanggal` datetime NOT NULL,
  `status` enum('lunas','hutang') DEFAULT 'lunas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembelian`
--

INSERT INTO `pembelian` (`id`, `id_supplier`, `id_barang`, `jumlah`, `harga_beli`, `total`, `keterangan`, `jatuh_tempo`, `tanggal`, `status`) VALUES
(1, 1, 3, 2, 20000, 40000, 'Stock Barang Hampir Habis', '2026-10-31', '2026-06-21 01:42:01', 'lunas');

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `id` int(11) NOT NULL,
  `tanggal` datetime NOT NULL DEFAULT current_timestamp(),
  `id_pelanggan` int(11) DEFAULT NULL,
  `total` int(11) DEFAULT 0,
  `id_kasir` int(11) DEFAULT NULL,
  `status` enum('pending','selesai','batal') DEFAULT 'selesai'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjualan`
--

INSERT INTO `penjualan` (`id`, `tanggal`, `id_pelanggan`, `total`, `id_kasir`, `status`) VALUES
(1, '2026-06-14 17:34:53', NULL, 50000, 2, 'selesai'),
(2, '2026-06-16 06:58:06', NULL, 70000, 2, 'selesai');

-- --------------------------------------------------------

--
-- Table structure for table `restock_log`
--

CREATE TABLE `restock_log` (
  `id` int(11) NOT NULL,
  `tanggal` datetime NOT NULL DEFAULT current_timestamp(),
  `id_barang` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `id_supplier` int(11) DEFAULT NULL,
  `no_faktur` varchar(50) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restock_log`
--

INSERT INTO `restock_log` (`id`, `tanggal`, `id_barang`, `jumlah`, `id_supplier`, `no_faktur`, `keterangan`, `id_user`) VALUES
(1, '2026-06-21 01:42:01', 3, 2, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stok_opname`
--

CREATE TABLE `stok_opname` (
  `id` int(11) NOT NULL,
  `tanggal` datetime NOT NULL DEFAULT current_timestamp(),
  `id_barang` int(11) NOT NULL,
  `stok_sistem` int(11) NOT NULL,
  `stok_fisik` int(11) NOT NULL,
  `selisih` int(11) GENERATED ALWAYS AS (`stok_fisik` - `stok_sistem`) STORED,
  `keterangan` varchar(255) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stok_opname_log`
--

CREATE TABLE `stok_opname_log` (
  `id` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `stok_sistem` int(11) NOT NULL,
  `stok_fisik` int(11) NOT NULL,
  `selisih` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stok_opname_log`
--

INSERT INTO `stok_opname_log` (`id`, `id_barang`, `stok_sistem`, `stok_fisik`, `selisih`, `keterangan`, `tanggal`) VALUES
(1, 8, 3, 4, 1, 'pembelian rutin', '2026-06-21 01:44:27');

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `id` int(11) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id`, `nama`, `email`, `telepon`, `alamat`, `created_at`) VALUES
(1, 'PT Mitra Distributor', 'Budi@gmail.com', '021-5551234', 'Jl.Ngawi barat No 1.RT67.RW 69', '2026-04-19 19:13:10'),
(2, 'CV Wangi Nusantara', 'Sari@gmail.com', '022-7778888', 'NGANJUK JL NGAWI 23', '2026-04-19 19:13:10'),
(5, 'CV. PetHealty', 'pethealty@gmail.com', '0812345678', 'Jl. Gatot Subroto', '2026-06-07 04:53:24'),
(8, 'PT. Pet Office', 'petoffice@gmail.com', '08123456712', 'Jl. Kencana, Pondok Petir', '2026-06-09 07:01:55'),
(9, 'CV. PetHouse', 'pethouse@gmail.com', '0812345678', 'JL.Pancoran Mas, Kota Depok', '2026-06-09 07:03:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','kasir') NOT NULL DEFAULT 'kasir',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'Administrator', 'admin', '$2y$10$/A3YnZSz/WQqWbc1frFeUO0ANGli5twzGO5UgMeVBDm1B0lOgU81.', 'admin', '2026-04-19 19:13:09'),
(2, 'Kasir Toko', 'kasir', '$2y$10$LqBoejIcOlbIoZUDS3ozKuLXN2nNjIMJUIevIVcqpY8dyskV48.Z6', 'kasir', '2026-04-19 19:13:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_barcode` (`barcode`),
  ADD KEY `idx_stok` (`stok`),
  ADD KEY `fk_barang_kategori` (`id_kategori`);

--
-- Indexes for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_supplier` (`id_supplier`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tanggal` (`tanggal`);

--
-- Indexes for table `restock_log`
--
ALTER TABLE `restock_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tanggal` (`tanggal`);

--
-- Indexes for table `stok_opname`
--
ALTER TABLE `stok_opname`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tanggal` (`tanggal`);

--
-- Indexes for table `stok_opname_log`
--
ALTER TABLE `stok_opname_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pembelian`
--
ALTER TABLE `pembelian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `restock_log`
--
ALTER TABLE `restock_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stok_opname`
--
ALTER TABLE `stok_opname`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stok_opname_log`
--
ALTER TABLE `stok_opname_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `fk_barang_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id`);

--
-- Constraints for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD CONSTRAINT `pembelian_ibfk_1` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `pembelian_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
