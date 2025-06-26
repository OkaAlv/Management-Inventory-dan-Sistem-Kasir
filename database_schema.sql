-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 26, 2025 at 05:05 PM
-- Server version: 8.0.30
-- PHP Version: 7.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `amira_bakery_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `merek` varchar(100) DEFAULT NULL,
  `varian` varchar(100) DEFAULT NULL,
  `stok` int NOT NULL DEFAULT '0',
  `harga` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `nama_barang`, `merek`, `varian`, `stok`, `harga`, `created_at`) VALUES
(1, 'Roti Jala Maklimah', 'Jala', 'Kari Rusa', 19, '5000.00', '2025-06-25 16:39:14'),
(2, 'Roti skubang', 'Amira', 'keju', 16, '7000.00', '2025-06-25 16:49:25'),
(3, 'Roti O', 'Amira', 'Ice Cream', 14, '20000.00', '2025-06-26 04:43:51'),
(4, 'Roti Murah', 'Lempuyungan', 'Coklat Kedu', 0, '10.00', '2025-06-26 06:24:31');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int NOT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `nama_customer` varchar(255) DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `kuantitas` int NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `order_id_midtrans` varchar(255) DEFAULT NULL,
  `status_pembayaran` varchar(20) NOT NULL DEFAULT 'lunas',
  `metode_pembayaran` varchar(50) NOT NULL DEFAULT 'Tunai',
  `waktu_transaksi` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `order_id`, `user_id`, `nama_customer`, `product_id`, `kuantitas`, `sub_total`, `order_id_midtrans`, `status_pembayaran`, `metode_pembayaran`, `waktu_transaksi`) VALUES
(1, NULL, 1, 'Pelanggan', 2, 2, '14000.00', NULL, 'lunas', 'Tunai', '2025-06-25 18:19:31'),
(2, NULL, 1, 'Pelanggan', 1, 2, '10000.00', NULL, 'lunas', 'Tunai', '2025-06-25 18:19:31'),
(5, NULL, 1, 'Pelanggan', 1, 3, '15000.00', NULL, 'lunas', 'Tunai', '2025-06-25 18:22:48'),
(6, NULL, 1, 'Pelanggan', 2, 1, '7000.00', NULL, 'lunas', 'Tunai', '2025-06-25 18:22:48'),
(9, NULL, 1, 'kula', 1, 1, '5000.00', NULL, 'lunas', 'Tunai', '2025-06-26 04:42:29'),
(10, NULL, 1, 'kula', 2, 3, '21000.00', NULL, 'lunas', 'Tunai', '2025-06-26 04:42:29'),
(11, NULL, 4, 'Pelanggan', 3, 5, '100000.00', NULL, 'lunas', 'Tunai', '2025-06-26 04:45:54'),
(12, NULL, 4, 'Pelanggan', 1, 1, '5000.00', NULL, 'lunas', 'Tunai', '2025-06-26 04:45:54'),
(13, NULL, 1, 'mau', 4, 2, '20.00', 'ORDER-1750926096-1', 'paid', 'Tunai', '2025-06-26 08:22:09'),
(14, NULL, 1, 'pulang', 4, 1, '10.00', 'ORDER-1750926171-1', 'paid', 'Tunai', '2025-06-26 08:23:28'),
(15, NULL, 1, 'stuntur', 3, 1, '20000.00', 'ORDER-1750952639-1', 'paid', 'Tunai', '2025-06-26 15:45:33'),
(16, NULL, 1, 'stuntur', 1, 1, '5000.00', 'ORDER-1750952639-1', 'paid', 'Tunai', '2025-06-26 15:45:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(255) NOT NULL,
  `nip` varchar(50) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `jabatan` enum('Owner','Karyawan') NOT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `alamat` text,
  `tanggal_bergabung` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `nip`, `username`, `password`, `jabatan`, `foto_profil`, `telepon`, `email`, `alamat`, `tanggal_bergabung`, `created_at`) VALUES
(1, 'Fairuz Admin', '411550343', 'admin', '$2y$10$pQZsuK7Vazt6AWy8Ot1qC.cw/ACpTldWlmI8qvRmQFr4v4mfNmKme', 'Owner', 'user-1-1750914418.jpg', '', '', '', '2025-06-26', '2025-06-25 15:25:17'),
(4, 'Vansy', '4233250036', 'vansy', '$2y$10$RMONeOTPxUIcTw/p6ncARejFwWLDC7UAgIF9h0BL2xkmFLy5tUT4e', 'Karyawan', NULL, '082177741592', '', '', '2025-06-26', '2025-06-25 18:11:42'),
(5, 'Inayah Alia Putri', '230104495', 'Inayah', '$2y$10$WacSjgGi/DP/BcrTYLQN6uG5.i2jU9DL.bs4cwuzmlNrP3AhM98/u', 'Karyawan', NULL, '', '', '', '2025-06-26', '2025-06-25 21:41:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `nip` (`nip`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
