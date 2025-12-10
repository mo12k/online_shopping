-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2025-12-10 11:32:18
-- 服务器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `bookstore`
--

-- --------------------------------------------------------

--
-- 表的结构 `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `email`, `password`, `photo`) VALUES
(1, 'admin1', 'leemh-wm24@student.tarc.edu.my', '6c7ca345f63f835cb353ff15bd6c5e052ec08e7a', 'default_pic.jpg');

-- --------------------------------------------------------

--
-- 表的结构 `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `cart`
--

INSERT INTO `cart` (`cart_id`, `customer_id`, `product_id`, `quantity`, `added_date`) VALUES
(1, 37, 1, 1, '2025-12-10 18:11:41'),
(2, 37, 2, 1, '2025-12-10 18:19:20');

-- --------------------------------------------------------

--
-- 表的结构 `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_code` char(3) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `category`
--

INSERT INTO `category` (`category_id`, `category_code`, `category_name`, `sort_order`) VALUES
(1, 'NOV', 'Novel', 1),
(2, 'COM', 'Comics & Manga', 2),
(3, 'ART', 'Art Book', 3),
(4, 'FIG', 'Figure & Model', 4),
(5, 'COS', 'Cosplay', 5),
(6, 'GME', 'Game & Strategy Guide', 6),
(7, 'MAG', 'Magazine', 7),
(8, 'ACD', 'Academic & Textbook', 8),
(9, 'CHL', 'Children Book', 9),
(10, 'OTH', 'Others', 10);

-- --------------------------------------------------------

--
-- 表的结构 `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `password` varchar(255) NOT NULL,
  `failed_attempt` int(11) NOT NULL DEFAULT 0,
  `last_failed_at` datetime DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `birthdate` date NOT NULL,
  `gender` char(1) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `photo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `customer`
--

INSERT INTO `customer` (`customer_id`, `username`, `email`, `is_verified`, `password`, `failed_attempt`, `last_failed_at`, `phone`, `birthdate`, `gender`, `created_at`, `photo`) VALUES
(1, 'asdf', '123@gmail.com', 1, '$2y$10$DiD49ihsk.toJYHVV/mxNOrsFwC9/mdrIPgQKkewOdFvpm2w5PgwG', 0, NULL, '0123456789', '2025-10-30', 'F', '2025-11-26 09:30:34', 'default_pic.jpg'),
(2, 'afsdgasf', 'lclyjiushi@gmail.com', 1, '$2y$10$9PNrWlSvlozguI8e13UtROnUkxveuxL3/e3rkRNc5jd7AhYpxeUoi', 0, NULL, '0123456789', '2025-10-29', 'F', '2025-11-26 18:25:37', 'default_pic.jpg'),
(3, '2413492', '123456@gmail.com', 1, '$2y$10$w8nCPhbATzf9NXrjsE0gP.4rJ0ahXcZqDewnY3J90zc82Q.TRgr4y', 0, NULL, '01298765543', '2025-10-26', 'M', '2025-11-26 21:21:36', 'default_pic.jpg'),
(4, 'Bing_123', 'mokchun549@gmail.com', 1, '$2y$10$NPwrelq4Uc986r684jGNJeEBg9pM1g5sNuyZP8CYI9kBcNMwmZlOO', 0, NULL, '0164564996', '2024-04-08', 'M', '2025-11-27 14:59:36', 'default_pic.jpg'),
(5, 'mok', 'mokbingchun@gmail.com', 1, '01bc634ce656696cff9dd426464c0d21e32d5433', 0, NULL, '', '0000-00-00', '', '2025-12-02 12:59:51', 'default_pic.jpg'),
(6, 'mo12k', 'mokcb-wm24@student.tarc.edu.my', 1, '$2y$10$kzupMP5VEcrTWhygHfeezOUAqBiVQtXStALR9r1DSp9LiRbQV7zSq', 0, NULL, '', '0000-00-00', '', '2025-12-02 13:27:27', 'default_pic.jpg'),
(7, 'user01', 'user01@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456701', '1999-01-10', 'M', '2025-12-09 19:54:55', 'default_pic.jpg'),
(8, 'user02', 'user02@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456702', '1998-04-12', 'F', '2025-12-09 19:54:55', 'default_pic.jpg'),
(9, 'user03', 'user03@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456703', '2000-09-20', 'M', '2025-12-09 19:54:55', 'default_pic.jpg'),
(10, 'user04', 'user04@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456704', '2001-03-25', 'F', '2025-12-09 19:54:55', 'default_pic.jpg'),
(11, 'user05', 'user05@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456705', '1997-07-14', 'M', '2025-12-09 19:54:55', 'default_pic.jpg'),
(12, 'user06', 'user06@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456706', '2002-11-30', 'F', '2025-12-09 19:54:55', 'default_pic.jpg'),
(13, 'user07', 'user07@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456707', '1996-05-08', 'M', '2025-12-09 19:54:55', 'default_pic.jpg'),
(14, 'user08', 'user08@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456708', '1995-12-12', 'F', '2025-12-09 19:54:55', 'default_pic.jpg'),
(15, 'user09', 'user09@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456709', '2003-02-18', 'M', '2025-12-09 19:54:55', 'default_pic.jpg'),
(16, 'user10', 'user10@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456710', '1994-10-01', 'F', '2025-12-09 19:54:55', 'default_pic.jpg'),
(17, 'user11', 'user11@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456711', '1998-08-22', 'M', '2025-12-09 19:54:55', 'default_pic.jpg'),
(18, 'user12', 'user12@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456712', '1999-06-11', 'F', '2025-12-09 19:54:55', 'default_pic.jpg'),
(19, 'user13', 'user13@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456713', '2001-09-09', 'M', '2025-12-09 19:54:55', 'default_pic.jpg'),
(20, 'user14', 'user14@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456714', '1996-03-17', 'F', '2025-12-09 19:54:55', 'default_pic.jpg'),
(21, 'user15', 'user15@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456715', '2000-12-25', 'M', '2025-12-09 19:54:55', 'default_pic.jpg'),
(22, 'user16', 'user16@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456716', '1997-09-05', 'F', '2025-12-09 19:54:55', 'default_pic.jpg'),
(23, 'user17', 'user17@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456717', '1995-01-01', 'M', '2025-12-09 19:54:55', 'default_pic.jpg'),
(24, 'user18', 'user18@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456718', '1994-05-27', 'F', '2025-12-09 19:54:55', 'default_pic.jpg'),
(25, 'user19', 'user19@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456719', '1999-07-16', 'M', '2025-12-09 19:54:55', 'default_pic.jpg'),
(26, 'user20', 'user20@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456720', '2003-11-11', 'F', '2025-12-09 19:54:55', 'default_pic.jpg'),
(27, 'user21', 'user21@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456721', '1998-02-28', 'M', '2025-12-09 19:54:55', 'default_pic.jpg'),
(28, 'user22', 'user22@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456722', '1996-10-19', 'F', '2025-12-09 19:54:55', 'default_pic.jpg'),
(29, 'user23', 'user23@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456723', '1997-06-06', 'M', '2025-12-09 19:54:55', 'default_pic.jpg'),
(30, 'user24', 'user24@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456724', '1998-12-03', 'F', '2025-12-09 19:54:55', 'default_pic.jpg'),
(31, 'user25', 'user25@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456725', '2002-03-15', 'M', '2025-12-09 19:54:55', 'default_pic.jpg'),
(32, 'user26', 'user26@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456726', '1995-04-21', 'F', '2025-12-09 19:54:55', 'default_pic.jpg'),
(33, 'user27', 'user27@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456727', '1999-11-30', 'M', '2025-12-09 19:54:55', 'default_pic.jpg'),
(34, 'user28', 'user28@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456728', '2000-05-02', 'F', '2025-12-09 19:54:55', 'default_pic.jpg'),
(35, 'user29', 'user29@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456729', '1997-09-09', 'M', '2025-12-09 19:54:55', 'default_pic.jpg'),
(36, 'user30', 'user30@example.com', 1, 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, NULL, '0123456730', '1996-02-14', 'F', '2025-12-09 19:54:55', 'default_pic.jpg'),
(37, 'idkwhatname', 'jiading2005@gmail.com', 0, '$2y$10$afGoIB1c0IMW0IQ2EgQEQejvWI4Q0fF5vvDcnHo2cmQwMTrnVZDwK', 0, NULL, '0105158347', '2005-05-08', 'M', '2025-12-10 18:11:33', 'default_pic.jpg');

-- --------------------------------------------------------

--
-- 表的结构 `customer_address`
--

CREATE TABLE `customer_address` (
  `address_id` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `postcode` varchar(10) NOT NULL,
  `customer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `customer_address`
--

INSERT INTO `customer_address` (`address_id`, `address`, `city`, `state`, `postcode`, `customer_id`) VALUES
(1, '1, Light Street', 'George Town', 'Terengganu', '24678', 1),
(2, '', '', '', '', 2),
(3, '', '', '', '', 3),
(4, '', '', '', '', 4),
(5, '123', '123', 'Johor', '22', 37);

-- --------------------------------------------------------

--
-- 表的结构 `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','shipped','completed','cancelled') DEFAULT 'pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `order_item`
--

CREATE TABLE `order_item` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_each` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `method` enum('card','fpx','ewallet') NOT NULL,
  `status` enum('pending','success','failed') DEFAULT 'pending',
  `amount` decimal(10,2) NOT NULL,
  `paid_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `photo_name` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `updated_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `admin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `product`
--

INSERT INTO `product` (`id`, `photo_name`, `title`, `author`, `category_id`, `price`, `stock`, `status`, `description`, `created_date`, `updated_date`, `admin_id`) VALUES
(1, 'product1.jpg', 'The Great Gatsby', 'F. Scott Fitzgerald', 1, 12.99, 45, 1, 'Classic American novel about the Jazz Age', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(2, 'product2.jpg', 'To Kill a Mockingbird', 'Harper Lee', 1, 14.50, 32, 1, 'Pulitzer Prize-winning story of racial injustice', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(3, 'product3.jpg', 'One Piece Vol. 101', 'Eiichiro Oda', 2, 9.99, 88, 1, 'Latest volume of the legendary pirate adventure', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(4, 'product4.jpg', 'Attack on Titan Final Season Art Book', 'Hajime Isayama', 3, 45.00, 15, 1, 'Official art collection with character designs', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(5, 'product5.jpg', 'Genshin Impact Raiden Shogun Figure', 'miHoYo Official', 4, 189.99, 8, 1, '1/7 Scale PVC Figure - Limited Edition', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(6, 'product6.jpg', 'Fate/Stay Night Saber Lily Figure', 'Good Smile Company', 4, 220.00, 3, 1, 'Premium 1/7 scale figure with Excalibur', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(7, 'product7.jpg', 'Jujutsu Kaisen Vol. 20', 'Gege Akutami', 2, 10.50, 67, 1, 'Latest manga volume with Shibuya arc', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(8, 'product8.jpg', 'Neon Genesis Evangelion Art Book', 'Yoshiyuki Sadamoto', 3, 58.00, 12, 1, 'Complete character and mecha design collection', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(9, 'product9.jpg', 'Hatsune Miku 15th Anniversary Figure', 'Good Smile Company', 4, 280.00, 5, 1, 'Celebrating 15 years of virtual idol', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(10, 'product10.jpg', '1984', 'George Orwell', 1, 11.99, 29, 1, 'Dystopian masterpiece about totalitarianism', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(11, 'product11.jpg', 'Chainsaw Man Vol. 12', 'Tatsuki Fujimoto', 2, 11.99, 92, 1, 'Latest volume of the hit dark action series', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(12, 'product12.jpg', 'Spy x Family Vol. 10', 'Tatsuya Endo', 2, 9.99, 104, 1, 'Anya and the Forger family adventures continue', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(13, 'product13.jpg', 'Demon Slayer Complete Box Set', 'Koyoharu Gotouge', 2, 159.99, 7, 1, 'All 23 volumes in premium box', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(14, 'product14.jpg', 'Your Name Official Visual Book', 'Makoto Shinkai', 3, 42.00, 18, 1, 'Complete movie art and production materials', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(15, 'product15.jpg', 'Violet Evergarden Gaiden Art Book', 'Kyoto Animation', 3, 68.00, 9, 1, 'Beautiful watercolor-style illustrations', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(16, 'product16.jpg', 'Asuka Langley Soryu Figure', 'Kotobukiya', 4, 168.00, 6, 1, '1/6 scale figure from Rebuild of Evangelion', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(17, 'product17.jpg', 'Zero Two Darling in the Franxx Figure', 'Good Smile Company', 4, 198.00, 4, 1, 'Iconic pink-haired waifu figure', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(18, 'product18.jpg', 'The Alchemist', 'Paulo Coelho', 1, 16.99, 38, 1, 'Inspirational novel about following your dreams', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(19, 'product19.jpg', 'Atomic Habits', 'James Clear', 8, 24.99, 51, 1, 'Practical guide to building good habits', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(20, 'product20.jpg', 'Dune', 'Frank Herbert', 1, 18.99, 27, 1, 'Epic science fiction masterpiece', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(21, 'product21.jpg', 'Blue Lock Vol. 22', 'Muneyuki Kaneshiro', 2, 10.99, 73, 1, 'Intense soccer manga about ego and victory', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(22, 'product22.jpg', 'Tokyo Revengers Vol. 31', 'Ken Wakui', 2, 11.99, 41, 1, 'Final volume of the time-leap delinquent saga', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(23, 'product23.jpg', 'My Dress-Up Darling Marin Kitagawa Figure', 'Aniplex', 4, 248.00, 2, 1, '1/7 scale swimsuit version', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(24, 'product24.jpg', 'Rem Re:Zero Figure', 'Good Smile Company', 4, 188.00, 7, 1, 'Classic maid outfit version', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(25, 'product25.jpg', 'The Little Prince', 'Antoine de Saint-Exupéry', 9, 8.99, 120, 1, 'Beloved children classic with beautiful illustrations', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(26, 'product26.jpg', 'Harry Potter Complete Collection', 'J.K. Rowling', 1, 89.99, 19, 1, 'All 7 books in hardcover box set', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(27, 'product27.jpg', 'One Punch Man Vol. 28', 'ONE & Yusuke Murata', 2, 10.99, 58, 1, 'Saitama vs Garou reaches its climax', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(28, 'product28.jpg', 'Bocchi the Rock! Hitori Gotoh Figure', 'Good Smile Company', 4, 178.00, 11, 1, 'Shy guitarist in her iconic pink tracksuit', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(29, 'product29.jpg', 'Kubo Won\'t Let Me Be Invisible Vol. 12', 'Nene Yukimori', 2, 9.99, 64, 1, 'Wholesome romance comedy', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(30, 'product30.jpg', 'The Art of Spirited Away', 'Studio Ghibli', 3, 55.00, 14, 1, 'Official concept art and background paintings', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(31, 'product31.jpg', 'Nier Automata 2B YoRHa Figure', 'Square Enix', 4, 298.00, 3, 1, 'Deluxe version with multiple weapons', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(32, 'product32.jpg', 'Pride and Prejudice', 'Jane Austen', 1, 9.99, 42, 1, 'Timeless romantic classic', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(33, 'product33.jpg', 'Oshi no Ko Vol. 11', 'Aka Akasaka', 2, 11.99, 89, 1, 'The entertainment industry dark mystery continues', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(34, 'product34.jpg', 'Kaguya-sama Love is War Complete Set', 'Aka Akasaka', 2, 98.00, 8, 1, 'All volumes of the romantic comedy masterpiece', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(35, 'product35.jpg', 'Moriarty the Patriot Art Book', 'Ryosuke Takeuchi', 3, 48.00, 16, 1, 'Victorian era Sherlock Holmes rival story', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(36, 'product36.jpg', 'Sailor Moon Eternal Edition Vol. 1-10', 'Naoko Takeuchi', 2, 189.99, 5, 1, 'Remastered complete collection', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(37, 'product37.jpg', 'The Hobbit', 'J.R.R. Tolkien', 1, 15.99, 31, 1, 'The classic prelude to Lord of the Rings', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(38, 'product38.jpg', 'Steins;Gate Visual Novel Art Book', '5pb. & Nitroplus', 3, 62.00, 9, 1, 'Complete illustration collection', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(39, 'product39.jpg', 'Makima Chainsaw Man Figure', 'SH Figuarts', 4, 135.00, 12, 1, 'Highly detailed and posable figure', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(40, 'product40.jpg', 'Charlotte\'s Web', 'E.B. White', 9, 7.99, 87, 1, 'Heartwarming children classic about friendship', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(41, 'product41.jpg', 'Dr. STONE Complete Manga Set', 'Riichiro Inagaki', 2, 168.00, 6, 1, 'All 26 volumes of the science adventure', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(42, 'product42.jpg', 'Weathering With You Art Book', 'Makoto Shinkai', 3, 52.00, 13, 1, 'Beautiful sky and cloud illustrations', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(43, 'product43.jpg', 'Anya Forger Spy x Family Figure', 'Banpresto', 4, 38.00, 42, 1, 'Cute chibi style with peanut accessory', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(44, 'product44.jpg', 'The Catcher in the Rye', 'J.D. Salinger', 1, 13.99, 36, 1, 'Iconic coming-of-age novel', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(45, 'product45.jpg', 'Solo Leveling Vol. 8', 'Chugong', 2, 19.99, 71, 1, 'Korean web novel masterpiece', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(46, 'product46.jpg', 'Lord of the Rings Trilogy', 'J.R.R. Tolkien', 1, 48.99, 14, 1, 'Complete epic fantasy saga', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(47, 'product47.jpg', 'Howl\'s Moving Castle Art Book', 'Studio Ghibli', 3, 68.00, 11, 1, 'Magical illustrations by Hayao Miyazaki', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(48, 'product48.jpg', 'Emilia Re:Zero Crystal Dress Figure', 'Shibuya Scramble', 4, 398.00, 2, 1, 'Stunning 1/7 scale luxury figure', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(49, 'product49.jpg', 'Where the Wild Things Are', 'Maurice Sendak', 9, 18.99, 53, 1, 'Beloved children picture book classic', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(50, 'product50.jpg', 'The Complete Calvin and Hobbes', 'Bill Watterson', 2, 135.00, 4, 1, 'All strips in beautiful hardcover collection', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1);

-- --------------------------------------------------------

--
-- 表的结构 `token`
--

CREATE TABLE `token` (
  `Token_Id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `token_hash` varchar(255) NOT NULL,
  `token_type` enum('verify','reset','remember') NOT NULL,
  `type` enum('otp','link') NOT NULL DEFAULT 'link',
  `otp_code` varchar(6) DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转储表的索引
--

--
-- 表的索引 `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `gmail` (`email`);

--
-- 表的索引 `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `id` (`product_id`),
  ADD UNIQUE KEY `product_id` (`product_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- 表的索引 `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_code` (`category_code`);

--
-- 表的索引 `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- 表的索引 `customer_address`
--
ALTER TABLE `customer_address`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- 表的索引 `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `address_id` (`address_id`);

--
-- 表的索引 `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- 表的索引 `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- 表的索引 `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_title` (`title`),
  ADD KEY `idx_category` (`category_id`);

--
-- 表的索引 `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`Token_Id`),
  ADD UNIQUE KEY `unique_token` (`token_hash`,`token_type`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用表AUTO_INCREMENT `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- 使用表AUTO_INCREMENT `customer_address`
--
ALTER TABLE `customer_address`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用表AUTO_INCREMENT `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `order_item`
--
ALTER TABLE `order_item`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- 使用表AUTO_INCREMENT `token`
--
ALTER TABLE `token`
  MODIFY `Token_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 限制导出的表
--

--
-- 限制表 `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);

--
-- 限制表 `customer_address`
--
ALTER TABLE `customer_address`
  ADD CONSTRAINT `customer_address_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- 限制表 `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `customer_address` (`address_id`) ON UPDATE CASCADE;

--
-- 限制表 `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `order_item_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON UPDATE CASCADE;

--
-- 限制表 `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON UPDATE CASCADE;

--
-- 限制表 `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);

--
-- 限制表 `token`
--
ALTER TABLE `token`
  ADD CONSTRAINT `token_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `token_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
