-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 07, 2025 at 04:01 PM
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
-- Database: `sweet_sensations`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `created_at`) VALUES
(10, 'Cheese Cake', 'A rich and creamy dessert with a smooth texture and a buttery graham cracker crust.', 1000.00, 'cheese cake.png', '2025-02-07 06:56:59'),
(19, 'Baklava', 'A crispy, flaky pastry filled with spiced nuts and soaked in sweet honey syrup.', 200.00, 'Baklava.png', '2025-02-07 08:02:58'),
(20, 'Rainbow Layer Cake', 'A vibrant, multi-layered cake with fluffy sponge and creamy frosting.', 2000.00, 'rainbow layer cake.png', '2025-02-07 08:03:43'),
(21, 'Croissant', 'A buttery, flaky French pastry with a crisp exterior and soft, airy layers.', 75.00, 'Croissant.png', '2025-02-07 08:04:20'),
(22, 'Danish', 'A light, sweet pastry with a fruity or creamy filling, perfect for breakfast or dessert.', 500.00, 'danish.png', '2025-02-07 08:15:31'),
(23, 'Apple Strudel', ' A delicate, flaky pastry filled with spiced apples and a hint of cinnamon.', 100.00, 'Apple strudel.png', '2025-02-07 08:16:11'),
(24, 'Chocolate Cake', 'A moist and decadent cake made with rich cocoa and smooth chocolate frosting.', 1200.00, 'chocolate cake.png', '2025-02-07 08:25:45'),
(25, 'Red Velvet Cake', 'A soft and velvety cake with a hint of cocoa, topped with creamy frosting.', 1700.00, 'red velvet.png', '2025-02-07 08:26:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `user_type`, `created_at`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$aJTRUb/9BmgPHH16gyfzr.po.jNw9UP6.uC9fmDz.K5t5225ierD6', 'admin', '2025-02-07 04:01:30'),
(3, 'lawrence', 'lawrence@gmail.com', '$2y$10$/RnTu47sZ1D6oTBKgBo85.iTzVL0RpuDE80jRvmQG8KYjGzySYabK', 'user', '2025-02-07 04:35:15'),
(6, 'Dodet', 'bagan@gmail.com', '$2y$10$6cnCJzRKH8V0wSuhk.5F4uyEynpB1mTz2Ie68nONgLO9Z7.tsUute', 'user', '2025-02-07 14:45:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
