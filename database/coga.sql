-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2025 at 07:08 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `coga`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id_booking` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `id_service` char(5) NOT NULL,
  `id_kapster` char(5) NOT NULL,
  `id_slot` int(11) NOT NULL,
  `id_metode` int(11) NOT NULL,
  `tanggal_booking` date NOT NULL,
  `total_harga` int(11) NOT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `status_booking` enum('Menunggu Konfirmasi','Dikonfirmasi','Selesai','Batal') DEFAULT 'Menunggu Konfirmasi',
  `tanggal_dibuat` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id_booking`, `user_id`, `id_service`, `id_kapster`, `id_slot`, `id_metode`, `tanggal_booking`, `total_harga`, `bukti_transfer`, `status_booking`, `tanggal_dibuat`) VALUES
(20250003, 11, 'serv4', 'K0003', 1, 1, '2025-06-12', 70000, NULL, 'Selesai', '2025-06-12 16:07:47'),
(20250004, 12, 'serv4', 'K0001', 6, 1, '2025-06-21', 70000, NULL, 'Batal', '2025-06-12 16:45:24'),
(20250005, 12, 'serv1', 'K0002', 1, 2, '2025-06-14', 50000, 'transfer_684b087ba3fcf0.51120635.png', 'Menunggu Konfirmasi', '2025-06-12 17:03:55'),
(20250006, 11, 'serv3', 'K0001', 10, 1, '2025-06-14', 20000, NULL, 'Menunggu Konfirmasi', '2025-06-12 18:05:37'),
(20250007, 12, 'serv4', 'K0003', 4, 2, '2025-06-16', 70000, 'transfer_684cfab6cf0d16.22816472.png', 'Menunggu Konfirmasi', '2025-06-14 04:29:42'),
(20250008, 12, 'serv1', 'K0002', 12, 2, '2025-06-24', 50000, 'transfer_684cfb7d85c724.43714416.jpg', 'Menunggu Konfirmasi', '2025-06-14 04:33:01'),
(20250009, 11, 'serv4', 'K0001', 1, 1, '2025-06-16', 70000, NULL, 'Menunggu Konfirmasi', '2025-06-14 04:47:20'),
(20250010, 11, 'serv4', 'K0003', 2, 1, '2025-06-22', 70000, NULL, 'Menunggu Konfirmasi', '2025-06-14 04:51:52');

-- --------------------------------------------------------

--
-- Table structure for table `kapsters`
--

CREATE TABLE `kapsters` (
  `id_kapster` char(5) NOT NULL,
  `nama_kapster` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kapsters`
--

INSERT INTO `kapsters` (`id_kapster`, `nama_kapster`) VALUES
('K0001', 'NIKO'),
('K0002', 'VEMAS'),
('K0003', 'ADIT');

-- --------------------------------------------------------

--
-- Table structure for table `metode_pembayaran`
--

CREATE TABLE `metode_pembayaran` (
  `id_metode` int(11) NOT NULL,
  `nama_metode` varchar(50) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `metode_pembayaran`
--

INSERT INTO `metode_pembayaran` (`id_metode`, `nama_metode`, `is_active`) VALUES
(1, 'Bayar Langsung', 1),
(2, 'Transfer Bank (1234567890-BCA)', 1);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id_service` char(5) NOT NULL,
  `nama_service` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id_service`, `nama_service`, `harga`) VALUES
('serv1', 'Haircut', 50000),
('serv2', 'Creambath', 50000),
('serv3', 'Message', 20000),
('serv4', 'Coloring', 70000);

-- --------------------------------------------------------

--
-- Table structure for table `slot_waktu`
--

CREATE TABLE `slot_waktu` (
  `id_slot` int(11) NOT NULL,
  `jam` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `slot_waktu`
--

INSERT INTO `slot_waktu` (`id_slot`, `jam`) VALUES
(1, '11:00:00'),
(2, '12:00:00'),
(3, '13:00:00'),
(4, '14:00:00'),
(5, '15:00:00'),
(6, '16:00:00'),
(7, '17:00:00'),
(8, '18:00:00'),
(9, '19:00:00'),
(10, '20:00:00'),
(11, '21:00:00'),
(12, '22:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `phone_number`, `password`, `role`, `created_at`) VALUES
(10, 'admin', 'admin@gmail.com', '080000000000', '$2y$10$suXA4G6jA6xckswvi.Xn3uYFpSFDOpFlMTg/yEJiKUbD4bdpd5WQK', 'admin', '2025-06-12 07:49:00'),
(11, 'Afdal', 'rahmadatul68@gmail.com', '0895621964228', '$2y$10$yjK.7CJUhPz9tTDzzA.f9.b/ksWuITcbetoVsqrPNC3JXhbSXNysG', 'user', '2025-06-12 07:50:48'),
(12, 'nencycantik', 'nency@gmail.com', '08000000', '$2y$10$6p4Pii8S4RiDjp2mjSW8Q.A0n8Pp/i8236GNVUb6PK4muojWIU2R6', 'user', '2025-06-12 16:42:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id_booking`),
  ADD KEY `id_user` (`user_id`),
  ADD KEY `id_service` (`id_service`),
  ADD KEY `id_kapster` (`id_kapster`),
  ADD KEY `id_slot` (`id_slot`),
  ADD KEY `bookings_ibfk_5` (`id_metode`);

--
-- Indexes for table `kapsters`
--
ALTER TABLE `kapsters`
  ADD PRIMARY KEY (`id_kapster`);

--
-- Indexes for table `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  ADD PRIMARY KEY (`id_metode`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id_service`);

--
-- Indexes for table `slot_waktu`
--
ALTER TABLE `slot_waktu`
  ADD PRIMARY KEY (`id_slot`),
  ADD UNIQUE KEY `waktu_mulai` (`jam`),
  ADD UNIQUE KEY `jam` (`jam`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone_number` (`phone_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id_booking` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20250011;

--
-- AUTO_INCREMENT for table `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  MODIFY `id_metode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `slot_waktu`
--
ALTER TABLE `slot_waktu`
  MODIFY `id_slot` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`id_service`) REFERENCES `services` (`id_service`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`id_kapster`) REFERENCES `kapsters` (`id_kapster`),
  ADD CONSTRAINT `bookings_ibfk_4` FOREIGN KEY (`id_slot`) REFERENCES `slot_waktu` (`id_slot`),
  ADD CONSTRAINT `bookings_ibfk_5` FOREIGN KEY (`id_metode`) REFERENCES `metode_pembayaran` (`id_metode`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
