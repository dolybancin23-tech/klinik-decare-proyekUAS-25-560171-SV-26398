-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 14 Jun 2026 pada 11.04
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `decare_db`
--

DELIMITER $$
--
-- Fungsi
--
CREATE DEFINER=`root`@`localhost` FUNCTION `fk_label_status` (`p_status` VARCHAR(20)) RETURNS VARCHAR(50) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC BEGIN
    DECLARE v_label VARCHAR(50);
    IF p_status = 'Pending' THEN SET v_label = 'Menunggu Persetujuan';
    ELSEIF p_status = 'Disetujui' THEN SET v_label = 'Jadwal Dikonfirmasi';
    ELSEIF p_status = 'Selesai' THEN SET v_label = 'Pemeriksaan Selesai';
    ELSE SET v_label = 'Dibatalkan atau Ditolak';
    END IF;
    RETURN v_label;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fk_total_reservasi_pasien` (`p_id_pasien` INT) RETURNS INT(11) DETERMINISTIC BEGIN
    DECLARE v_total INT;
    SELECT COUNT(*) INTO v_total FROM reservasi WHERE id_pasien = p_id_pasien;
    RETURN v_total;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokter`
--

CREATE TABLE `dokter` (
  `id_dokter` int(11) NOT NULL,
  `id_layanan` int(11) NOT NULL,
  `nama_dokter` varchar(100) NOT NULL,
  `foto_dokter` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `dokter`
--

INSERT INTO `dokter` (`id_dokter`, `id_layanan`, `nama_dokter`, `foto_dokter`) VALUES
(1, 2, 'Drg. Aris Pratama, Sp.KG', 'dokter-aris.png'),
(2, 1, 'Drg. Rizky Wijaya', 'dokter-rizky.png'),
(3, 5, 'Drg. Budi Santoso, Sp.BM', 'dokter-budi.png'),
(4, 6, 'Drg. Hendra Kusuma', 'dokter-hendra.png'),
(5, 6, 'Drg. Farhan Aditya', 'dokter-farhan.png'),
(6, 4, 'Drg. Siti Nurhaliza, Sp.Pros', 'dokter-siti.png'),
(7, 3, 'Drg. Hendra Kusuma', 'dokter-hendra.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal`
--

CREATE TABLE `jadwal` (
  `id_jadwal` int(11) NOT NULL,
  `id_dokter` int(11) NOT NULL,
  `hari` varchar(50) NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jadwal`
--

INSERT INTO `jadwal` (`id_jadwal`, `id_dokter`, `hari`, `jam_mulai`, `jam_selesai`) VALUES
(1, 1, 'Senin dan Rabu', '10:00:00', '14:00:00'),
(2, 1, 'Kamis', '13:00:00', '17:00:00'),
(3, 2, 'Selasa', '10:00:00', '14:00:00'),
(4, 2, 'Jumat', '13:00:00', '17:00:00'),
(5, 3, 'Senin sampai Jumat (On Call)', '09:00:00', '12:00:00'),
(6, 4, 'Senin dan Kamis', '08:00:00', '12:00:00'),
(7, 4, 'Sabtu', '10:00:00', '15:00:00'),
(8, 5, 'Selasa dan Jumat', '08:00:00', '12:00:00'),
(9, 5, 'Sabtu', '10:00:00', '15:00:00'),
(10, 6, 'Rabu', '13:00:00', '17:00:00'),
(11, 6, 'Jumat', '09:00:00', '13:00:00'),
(12, 7, 'Kamis dan Sabtu', '10:00:00', '14:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `layanan`
--

CREATE TABLE `layanan` (
  `id_layanan` int(11) NOT NULL,
  `nama_layanan` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `layanan`
--

INSERT INTO `layanan` (`id_layanan`, `nama_layanan`, `harga`) VALUES
(1, 'Pemutihan Gigi (Bleaching)', 2500000),
(2, 'Perawatan Saluran Akar Gigi', 3500000),
(3, 'Perawatan Gigi Darurat', 850000),
(4, 'Estetika Gigi (Veneer/Crown)', 5000000),
(5, 'Prosedur Implan Gigi Per Gigi', 25000000),
(6, 'Pencegahan Rutin (Scaling & Checkup)', 650000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pasien`
--

CREATE TABLE `pasien` (
  `id_pasien` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `nomor_hp` varchar(20) NOT NULL,
  `alamat` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pasien`
--

INSERT INTO `pasien` (`id_pasien`, `id_user`, `nama_lengkap`, `nomor_hp`, `alamat`, `foto`) VALUES
(7, 8, 'Mika Yamaha Berutu', '0812345671', 'Yogyakarta', NULL),
(8, 8, 'Terio Kovana', '08335333111', 'Sidikalang', NULL),
(9, 9, 'Endawana Jose', '0879654311', 'Yogyakarta', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `reservasi`
--

CREATE TABLE `reservasi` (
  `id_reservasi` int(11) NOT NULL,
  `id_pasien` int(11) NOT NULL,
  `id_layanan` int(11) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `tanggal_reservasi` date NOT NULL,
  `keluhan` text NOT NULL,
  `foto_gigi` varchar(255) DEFAULT NULL,
  `status_periksa` enum('Pending','Disetujui','Selesai','Dibatalkan','Ditolak') DEFAULT 'Pending',
  `catatan_dokter` text DEFAULT NULL,
  `waktu_input` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `reservasi`
--

INSERT INTO `reservasi` (`id_reservasi`, `id_pasien`, `id_layanan`, `id_jadwal`, `tanggal_reservasi`, `keluhan`, `foto_gigi`, `status_periksa`, `catatan_dokter`, `waktu_input`) VALUES
(2, 7, 2, 1, '2026-07-01', 'Sakit', '6a2cff2dad5ec.png', 'Dibatalkan', NULL, '2026-06-13 06:56:45'),
(5, 8, 6, 8, '2026-06-16', 'Sakit kali', NULL, 'Disetujui', NULL, '2026-06-13 07:14:50'),
(6, 8, 1, 3, '2026-06-16', 'sa', NULL, 'Dibatalkan', NULL, '2026-06-13 07:54:58');

--
-- Trigger `reservasi`
--
DELIMITER $$
CREATE TRIGGER `trg_kapitalisasi_keluhan` BEFORE INSERT ON `reservasi` FOR EACH ROW BEGIN
    SET NEW.keluhan = UPPER(NEW.keluhan);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_validasi_tanggal_reservasi` BEFORE INSERT ON `reservasi` FOR EACH ROW BEGIN
    IF NEW.tanggal_reservasi < CURDATE() THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Error: Tidak boleh membuat reservasi medis di tanggal masa lalu!';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `sandi` varchar(255) NOT NULL,
  `role` enum('admin','pasien') DEFAULT 'pasien'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `username`, `email`, `sandi`, `role`) VALUES
(1, 'admin_decare', 'dolybancin23@gmail.com', '$2y$10$mlo.ZlQdlj8XjN3iO.umxuM0AKXPcF1iQW8MAL60Y/N2NQKOxezvi', 'admin'),
(8, 'MikaYamaha', 'Mika1@gmail.com', '$2y$10$vYYKsxRSF9zIfGfRnRkn3udJxwjS58jO7W6hHxUvl4DxSQD6n5gni', 'pasien'),
(9, 'Endawana', 'enda@gmail.com', '$2y$10$wsyMW3juys8L37rjywfLnu68S9mqS1deiy4QP938jzrWzlHPzCfSO', 'pasien');

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `view_antrean_pending`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `view_antrean_pending` (
`id_reservasi` int(11)
,`nama_pasien` varchar(100)
,`nama_layanan` varchar(100)
,`tanggal_reservasi` date
,`status_periksa` enum('Pending','Disetujui','Selesai','Dibatalkan','Ditolak')
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `view_laporan_omzet`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `view_laporan_omzet` (
`tanggal_reservasi` date
,`jumlah_pasien` bigint(21)
,`total_pendapatan` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Struktur untuk view `view_antrean_pending`
--
DROP TABLE IF EXISTS `view_antrean_pending`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_antrean_pending`  AS SELECT `r`.`id_reservasi` AS `id_reservasi`, `p`.`nama_lengkap` AS `nama_pasien`, `l`.`nama_layanan` AS `nama_layanan`, `r`.`tanggal_reservasi` AS `tanggal_reservasi`, `r`.`status_periksa` AS `status_periksa` FROM ((`reservasi` `r` join `pasien` `p` on(`r`.`id_pasien` = `p`.`id_pasien`)) join `layanan` `l` on(`r`.`id_layanan` = `l`.`id_layanan`)) WHERE `r`.`status_periksa` = 'Pending' ;

-- --------------------------------------------------------

--
-- Struktur untuk view `view_laporan_omzet`
--
DROP TABLE IF EXISTS `view_laporan_omzet`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_laporan_omzet`  AS SELECT `r`.`tanggal_reservasi` AS `tanggal_reservasi`, count(`r`.`id_reservasi`) AS `jumlah_pasien`, sum(`l`.`harga`) AS `total_pendapatan` FROM (`reservasi` `r` join `layanan` `l` on(`r`.`id_layanan` = `l`.`id_layanan`)) WHERE `r`.`status_periksa` = 'Selesai' GROUP BY `r`.`tanggal_reservasi` ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `dokter`
--
ALTER TABLE `dokter`
  ADD PRIMARY KEY (`id_dokter`),
  ADD KEY `id_layanan` (`id_layanan`);

--
-- Indeks untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `id_dokter` (`id_dokter`);

--
-- Indeks untuk tabel `layanan`
--
ALTER TABLE `layanan`
  ADD PRIMARY KEY (`id_layanan`);

--
-- Indeks untuk tabel `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`id_pasien`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `reservasi`
--
ALTER TABLE `reservasi`
  ADD PRIMARY KEY (`id_reservasi`),
  ADD KEY `id_pasien` (`id_pasien`),
  ADD KEY `id_layanan` (`id_layanan`),
  ADD KEY `id_jadwal` (`id_jadwal`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `dokter`
--
ALTER TABLE `dokter`
  MODIFY `id_dokter` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `layanan`
--
ALTER TABLE `layanan`
  MODIFY `id_layanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id_pasien` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `reservasi`
--
ALTER TABLE `reservasi`
  MODIFY `id_reservasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `dokter`
--
ALTER TABLE `dokter`
  ADD CONSTRAINT `dokter_ibfk_1` FOREIGN KEY (`id_layanan`) REFERENCES `layanan` (`id_layanan`);

--
-- Ketidakleluasaan untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id_dokter`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pasien`
--
ALTER TABLE `pasien`
  ADD CONSTRAINT `pasien_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `reservasi`
--
ALTER TABLE `reservasi`
  ADD CONSTRAINT `reservasi_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id_pasien`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservasi_ibfk_2` FOREIGN KEY (`id_layanan`) REFERENCES `layanan` (`id_layanan`),
  ADD CONSTRAINT `reservasi_ibfk_3` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal` (`id_jadwal`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
