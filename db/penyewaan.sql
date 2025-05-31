-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 29, 2025 at 07:54 PM
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
-- Database: `penyewaan`
--

-- --------------------------------------------------------

--
-- Table structure for table `lapangan`
--

CREATE TABLE `lapangan` (
  `ID` int(10) NOT NULL,
  `Nama` varchar(255) NOT NULL,
  `Tipe` varchar(255) NOT NULL,
  `Jenis` varchar(255) NOT NULL,
  `Harga` int(255) NOT NULL,
  `status` int(1) NOT NULL,
  `foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lapangan`
--

INSERT INTO `lapangan` (`ID`, `Nama`, `Tipe`, `Jenis`, `Harga`, `status`, `foto`) VALUES
(1, 'Lapangan Skibidi Wut Wut', 'Indoor', 'Vinyl', 300000, 1, 'lapangan_1748539309_683897adbd070.jpeg'),
(2, 'Lapangan Ballerina rina rina', 'Indoor', 'Matras', 250000, 1, ''),
(3, 'Lapangan Kunci Biru', 'Indoor', 'Rumput', 200000, 1, ''),
(4, 'Lapangan Nagi masuk loser gate', 'Outdoor', 'Reguler', 250000, 1, ''),
(5, 'Lapangan Ketuntung', 'Outdoor', 'Matras', 200000, 1, ''),
(6, 'Lapangan dimana aku bertemu dia', 'Outdoor', 'Rumput', 150000, 1, ''),
(7, 'Lapangan dimana kekalahan bukanlah pilihan', 'Indoor', 'Rumput', 200000, 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `ID` int(255) NOT NULL,
  `id_user` int(10) NOT NULL,
  `ID_lapangan` int(255) NOT NULL,
  `Nama` varchar(20) NOT NULL,
  `Telepon` varchar(20) NOT NULL,
  `Jam` time NOT NULL,
  `durasi` int(10) NOT NULL,
  `Tanggal` date NOT NULL,
  `Tambahan_1` int(11) NOT NULL,
  `Tambahan_2` int(11) NOT NULL,
  `total_harga` int(255) NOT NULL,
  `bayar` int(255) NOT NULL,
  `kembali` int(255) NOT NULL,
  `status` int(1) NOT NULL,
  `Status_Pembayaran` varchar(25) NOT NULL DEFAULT '''Belum Lunas''' COMMENT '"Status pembayaran aktual (Lunas, Belum Lunas)"',
  `Tanggal_Pemesanan_Dibuat` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`ID`, `id_user`, `ID_lapangan`, `Nama`, `Telepon`, `Jam`, `durasi`, `Tanggal`, `Tambahan_1`, `Tambahan_2`, `total_harga`, `bayar`, `kembali`, `status`, `Status_Pembayaran`, `Tanggal_Pemesanan_Dibuat`) VALUES
(1, 1, 4, 'Lapangan A1', '2222222', '09:00:00', 2, '2024-03-28', 0, 0, 500000, 500000, 0, 0, 'Lunas', NULL),
(3, 1, 3, 'Lapangan A3', '123321', '11:00:00', 2, '2024-03-30', 0, 0, 200000, 200000, 0, 0, 'Lunas', NULL),
(6, 1, 2, 'Lapangan Ballerina r', '123456', '19:00:00', 2, '2025-05-29', 0, 0, 250000, 250000, 0, 1, 'Lunas', '2025-05-29 17:58:44'),
(8, 1, 2, 'Lapangan Ballerina r', '12345678', '09:00:00', 2, '2025-05-31', 0, 0, 405000, 405000, 0, 1, 'Lunas', '2025-05-29 18:45:28'),
(9, 1, 2, 'Lapangan Ballerina r', '12345678', '13:00:00', 2, '2025-05-31', 0, 0, 405000, 405000, 0, 1, 'Lunas', '2025-05-29 18:47:29');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan_detail_tambahan`
--

CREATE TABLE `pesanan_detail_tambahan` (
  `id_pdt` int(11) NOT NULL,
  `id_pesanan` int(255) NOT NULL,
  `id_tambahan` int(10) NOT NULL,
  `jumlah_item` int(11) NOT NULL,
  `harga_satuan_saat_pesan` int(255) NOT NULL,
  `subtotal_item` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan_detail_tambahan`
--

INSERT INTO `pesanan_detail_tambahan` (`id_pdt`, `id_pesanan`, `id_tambahan`, `jumlah_item`, `harga_satuan_saat_pesan`, `subtotal_item`) VALUES
(1, 8, 5, 1, 40000, 40000),
(2, 8, 2, 1, 50000, 50000),
(3, 8, 4, 1, 20000, 20000),
(4, 8, 3, 1, 45000, 45000),
(5, 9, 5, 1, 40000, 40000),
(6, 9, 2, 1, 50000, 50000),
(7, 9, 4, 1, 20000, 20000),
(8, 9, 3, 1, 45000, 45000);

-- --------------------------------------------------------

--
-- Table structure for table `tambahan`
--

CREATE TABLE `tambahan` (
  `ID` int(10) NOT NULL,
  `Nama` varchar(255) NOT NULL,
  `Harga` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tambahan`
--

INSERT INTO `tambahan` (`ID`, `Nama`, `Harga`) VALUES
(2, 'Jersey Futsal', 50000),
(3, 'Sepatu Futsal', 45000),
(4, 'Knee Guard', 20000),
(5, 'Glove', 40000);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID` int(10) NOT NULL,
  `Nama` varchar(20) NOT NULL,
  `Email` varchar(20) NOT NULL,
  `Password` varchar(20) NOT NULL,
  `Level` char(5) DEFAULT 'user',
  `FotoProfil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `Nama`, `Email`, `Password`, `Level`, `FotoProfil`) VALUES
(1, 'Kancut', 'Kancut@Gmail.com', 'Kancut', 'user', 'user_1_1748539923.jpg'),
(2, 'Badag', 'Badag@gmail.com', 'Badag', 'admin', ''),
(3, 'Joko', 'Joko@gmail.com', 'Joko', 'user', ''),
(5, 'Maklo  ', 'Maklo@gmail.com', 'maklo', 'user', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lapangan`
--
ALTER TABLE `lapangan`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `pesanan_detail_tambahan`
--
ALTER TABLE `pesanan_detail_tambahan`
  ADD PRIMARY KEY (`id_pdt`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_tambahan` (`id_tambahan`);

--
-- Indexes for table `tambahan`
--
ALTER TABLE `tambahan`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lapangan`
--
ALTER TABLE `lapangan`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pesanan_detail_tambahan`
--
ALTER TABLE `pesanan_detail_tambahan`
  MODIFY `id_pdt` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tambahan`
--
ALTER TABLE `tambahan`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`ID_lapangan`) REFERENCES `lapangan` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pesanan_detail_tambahan`
--
ALTER TABLE `pesanan_detail_tambahan`
  ADD CONSTRAINT `pesanan_detail_tambahan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pesanan_detail_tambahan_ibfk_2` FOREIGN KEY (`id_tambahan`) REFERENCES `tambahan` (`ID`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
