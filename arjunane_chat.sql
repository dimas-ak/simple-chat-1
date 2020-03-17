-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Okt 2019 pada 22.55
-- Versi server: 10.1.35-MariaDB
-- Versi PHP: 7.2.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `arjunane_chat`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `chat`
--

CREATE TABLE `chat` (
  `id` int(11) NOT NULL,
  `_to` int(11) NOT NULL,
  `_from` int(11) NOT NULL,
  `text` text,
  `photo` text,
  `delete_by` int(11) NOT NULL,
  `delete_all_by` int(20) DEFAULT NULL,
  `reply` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `chat`
--

INSERT INTO `chat` (`id`, `_to`, `_from`, `text`, `photo`, `delete_by`, `delete_all_by`, `reply`) VALUES
(84, 2, 1, 'hallo kang', NULL, 0, NULL, 0),
(85, 2, 1, 'tes\r\n', NULL, 0, NULL, 0),
(86, 1, 2, 'hallo kang\r\n', NULL, 0, NULL, 0),
(87, 2, 1, '-=[ ini reply ]=-', NULL, 0, NULL, 86),
(88, 2, 1, '-=[ hallo juga kang wkwkwkwk ]=-\r\n', NULL, 0, NULL, 86),
(89, 2, 1, 'ddd\r\n', NULL, 0, NULL, 0),
(90, 2, 1, 'mencoba forward ini kang\r\n', NULL, 0, NULL, 0),
(91, 2, 1, 'mencoba forward ini kang\r\n', NULL, 0, NULL, 0),
(101, 1, 3, '\r\n-=[ mencob teks yang sangat panjang sekali ini kang wkwkwkwk sekali lagi kang wkwkwk ]=-', NULL, 0, NULL, 0),
(102, 3, 1, 'hallo kang\r\n', NULL, 0, NULL, 0),
(103, 2, 1, 'hallo kang\r\n', NULL, 0, NULL, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `level` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `photo` text,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id`, `username`, `level`, `name`, `photo`, `password`) VALUES
(1, 'admin', 1, 'Admin Arjunane', NULL, '0d9e5f2ec3bc9440c9731365e2cd170c05e1f7f200401828e8204a790ea0932991d3d605aea96b52a465531c2503a8c283e0e2204359f138f01ae12e433e9494KWDuQsE2X+TFrjunVKUEaOPUvjgcz0K24d9SzGGP22Q='),
(2, 'user', 2, 'User Arjunane', NULL, 'ddb53fe60634addff7fa5df59570738af00951cb284ca6612375c0b8edb3ed9c3c16f32c7855924fc49fcea6f5bffce897d4e147816fb8e76023a9f0c96f13bcDwjjz9+BlL1Ev1CdSYWck5PPE3pMe/JscaumZkwF1Bg='),
(3, 'user2', 2, 'Orang Ketiga', NULL, 'ddb53fe60634addff7fa5df59570738af00951cb284ca6612375c0b8edb3ed9c3c16f32c7855924fc49fcea6f5bffce897d4e147816fb8e76023a9f0c96f13bcDwjjz9+BlL1Ev1CdSYWck5PPE3pMe/JscaumZkwF1Bg='),
(4, 'yow', 2, 'Mencoba yow saja', NULL, 'cc5ca23ce7c639d44fe141cf668ba31941c240d7beb21297ae68035415388a83409548f81bb29ebc1d224bee95865c395908e063e62459911ad7b4da6ea9143c4PXYGdb/xkz54oI0wOjFrbcpXS0Vh3ehmxmEFAkCxrY=');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
