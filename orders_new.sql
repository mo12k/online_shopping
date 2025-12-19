-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2025 at 03:52 PM
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
-- Database: `bookstore`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','shipping') NOT NULL DEFAULT 'pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `address_id`, `total_amount`, `status`, `order_date`) VALUES
(27, 7, 5, 71.96, 'shipping', '2025-12-12 13:48:52'),
(35, 7, 5, 19.98, 'shipping', '2025-12-12 14:03:37'),
(36, 7, 5, 9.99, 'completed', '2025-12-12 14:05:53'),
(38, 7, 6, 29.00, 'pending', '2025-12-13 03:53:24'),
(39, 7, 6, 14.50, 'completed', '2025-12-13 03:53:51'),
(40, 7, 6, 14.50, 'shipping', '2025-12-13 03:54:33'),
(41, 7, 6, 19.98, 'completed', '2025-12-13 03:58:56'),
(42, 7, 6, 9.99, 'pending', '2025-12-13 04:39:18'),
(43, 7, 6, 14.50, 'completed', '2025-12-13 05:59:24'),
(44, 7, 5, 203.00, 'completed', '2025-12-13 07:54:06'),
(45, 7, 6, 14.50, 'completed', '2025-12-13 07:54:56'),
(46, 7, 6, 14.50, 'completed', '2025-12-13 07:58:27'),
(47, 7, 6, 9.99, 'completed', '2025-12-13 07:59:23'),
(48, 7, 5, 9.99, 'completed', '2025-12-13 08:00:49'),
(49, 7, 6, 14.50, 'completed', '2025-12-13 08:05:07'),
(50, 7, 6, 14.50, 'completed', '2025-12-13 08:07:03'),
(51, 7, 6, 9.99, 'completed', '2025-12-13 08:16:47'),
(52, 7, 6, 14.50, 'completed', '2025-12-13 08:18:41'),
(54, 7, 6, 14.50, 'completed', '2025-12-13 08:20:56'),
(56, 7, 6, 14.50, 'completed', '2025-12-13 08:21:13'),
(57, 7, 6, 14.50, 'completed', '2025-12-13 08:22:15'),
(58, 7, 6, 43.50, 'completed', '2025-12-13 08:22:29'),
(59, 7, 6, 9.99, 'pending', '2025-12-13 08:23:50'),
(60, 7, 6, 9.99, 'shipping', '2025-12-13 08:28:41'),
(61, 7, 1, 45.00, 'pending', '2025-12-13 08:47:20'),
(62, 7, 6, 14.50, 'pending', '2025-12-13 08:50:05'),
(63, 7, 6, 9.99, 'pending', '2025-12-13 08:56:17'),
(64, 7, 6, 14.50, 'pending', '2025-12-13 09:01:30'),
(65, 7, 6, 189.99, 'completed', '2025-12-13 09:03:22'),
(66, 7, 5, 9.99, 'pending', '2025-12-13 12:01:50'),
(67, 7, 6, 9.99, 'pending', '2025-12-13 12:03:38'),
(2001, 9, 1, 149.80, 'completed', '2025-11-02 02:15:00'),
(2002, 37, 5, 49.95, 'pending', '2025-12-15 02:38:19'),
(2003, 37, 5, 9.99, 'pending', '2025-12-15 02:40:45'),
(2004, 37, 5, 42.00, 'pending', '2025-12-15 02:42:39'),
(2005, 37, 5, 71.92, 'pending', '2025-12-15 02:44:27'),
(2011, 37, 5, 11.99, 'pending', '2025-12-15 03:50:32'),
(2012, 37, 5, 45.00, 'pending', '2025-12-15 03:51:45'),
(2013, 37, 5, 189.99, 'pending', '2025-12-15 03:52:32'),
(2014, 37, 5, 691.50, 'pending', '2025-12-15 03:53:45'),
(2015, 37, 5, 89.50, 'pending', '2025-12-15 03:54:41'),
(2016, 37, 5, 148.00, 'pending', '2025-12-15 03:55:48'),
(2017, 37, 5, 189.99, 'pending', '2025-12-15 03:57:30'),
(2019, 37, 5, 14.50, 'pending', '2025-12-16 11:01:35'),
(2020, 37, 5, 9.99, 'pending', '2025-12-17 11:17:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `address_id` (`address_id`),
  ADD KEY `customer_id` (`customer_id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2021;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
