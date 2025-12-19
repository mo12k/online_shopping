-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2025 at 03:53 PM
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
-- Table structure for table `product`
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
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `photo_name`, `title`, `author`, `category_id`, `price`, `stock`, `status`, `description`, `created_date`, `updated_date`, `admin_id`) VALUES
(2, 'product2.jpg', 'To Kill a Mockingbird', 'Harper Lee', 1, 14.50, 31, 1, 'Pulitzer Prize-winning story of racial injustice', '2025-11-30 17:14:10', '2025-12-18 13:27:36', 1),
(3, 'product3.jpg', 'One Piece Vol. 101', 'Eiichiro Oda', 2, 9.99, 87, 0, 'Latest volume of the legendary pirate adventure', '2025-11-30 17:14:10', '2025-12-19 21:10:32', 1),
(4, 'product4.jpg', 'Attack on Titan Final Season Art Book', 'Hajime Isayama', 3, 45.00, 12, 1, 'Official art collection with character designs', '2025-11-30 17:14:10', '2025-12-15 11:55:48', 1),
(5, 'product5.jpg', 'Genshin Impact Raiden Shogun Figure', 'miHoYo Official', 4, 189.99, 6, 0, '1/7 Scale PVC Figure - Limited Edition', '2025-11-30 17:14:10', '2025-12-19 18:03:45', 1),
(6, 'product6.jpg', 'Fate/Stay Night Saber Lily Figure', 'Good Smile Company', 4, 220.00, 0, 0, 'Premium 1/7 scale figure with Excalibur', '2025-11-30 17:14:10', '2025-12-19 18:04:00', 1),
(7, 'product7.jpg', 'Jujutsu Kaisen Vol. 20', 'Gege Akutami', 2, 10.50, 2, 1, 'Latest manga volume with Shibuya arc', '2025-11-30 17:14:10', '2025-12-19 22:46:59', 1),
(8, 'product8.jpg', 'Neon Genesis Evangelion Art Book', 'Yoshiyuki Sadamoto', 3, 58.00, 10, 0, 'Complete character and mecha design collection', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(9, 'product9.jpg', 'Hatsune Miku 15th Anniversary Figure', 'Good Smile Company', 4, 280.00, 5, 1, 'Celebrating 15 years of virtual idol', '2025-11-30 17:14:10', '2025-12-19 22:46:42', 1),
(10, 'product10.jpg', '1984', 'George Orwell', 1, 11.99, 25, 1, 'Dystopian masterpiece about totalitarianism', '2025-11-30 17:14:10', '2025-12-16 18:36:17', 1),
(11, 'product11.jpg', 'Chainsaw Man Vol. 12', 'Tatsuki Fujimoto', 2, 11.99, 92, 1, 'Latest volume of the hit dark action series', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(12, 'product12.jpg', 'Spy x Family Vol. 10', 'Tatsuya Endo', 2, 9.99, 98, 1, 'Anya and the Forger family adventures continue', '2025-11-30 17:14:10', '2025-12-15 10:40:45', 1),
(13, 'product13.jpg', 'Demon Slayer Complete Box Set', 'Koyoharu Gotouge', 2, 159.99, 7, 0, 'All 23 volumes in premium box', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(14, 'product14.jpg', 'Your Name Official Visual Book', 'Makoto Shinkai', 3, 42.00, 18, 1, 'Complete movie art and production materials', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(15, 'product15.jpg', 'Violet Evergarden Gaiden Art Book', 'Kyoto Animation', 3, 68.00, 9, 0, 'Beautiful watercolor-style illustrations', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(16, 'product16.jpg', 'Asuka Langley Soryu Figure', 'Kotobukiya', 4, 168.00, 6, 0, '1/6 scale figure from Rebuild of Evangelion', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(17, 'product17.jpg', 'Zero Two Darling in the Franxx Figure', 'Good Smile Company', 4, 198.00, 4, 0, 'Iconic pink-haired waifu figure', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(18, 'product18.jpg', 'The Alchemist', 'Paulo Coelho', 1, 16.99, 38, 1, 'Inspirational novel about following your dreams', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(19, 'product19.jpg', 'Atomic Habits', 'James Clear', 8, 24.99, 51, 1, 'Practical guide to building good habits', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(20, 'product20.jpg', 'Dune', 'Frank Herbert', 1, 18.99, 27, 1, 'Epic science fiction masterpiece', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(21, 'product21.jpg', 'Blue Lock Vol. 22', 'Muneyuki Kaneshiro', 2, 10.99, 73, 1, 'Intense soccer manga about ego and victory', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(22, 'product22.jpg', 'Tokyo Revengers Vol. 31', 'Ken Wakui', 2, 11.99, 41, 1, 'Final volume of the time-leap delinquent saga', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(23, 'product23.jpg', 'My Dress-Up Darling Marin Kitagawa Figure', 'Aniplex', 4, 248.00, 2, 1, '1/7 scale swimsuit version', '2025-11-30 17:14:10', '2025-12-19 21:43:32', 1),
(24, 'product24.jpg', 'Rem Re:Zero Figure', 'Good Smile Company', 4, 188.00, 7, 0, 'Classic maid outfit version', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(25, 'product25.jpg', 'The Little Prince', 'Antoine de Saint-Exupéry', 9, 8.99, 112, 1, 'Beloved children classic with beautiful illustrations', '2025-11-30 17:14:10', '2025-12-15 10:44:27', 1),
(26, 'product26.jpg', 'Harry Potter Complete Collection', 'J.K. Rowling', 1, 89.99, 19, 1, 'All 7 books in hardcover box set', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(27, 'product27.jpg', 'One Punch Man Vol. 28', 'ONE & Yusuke Murata', 2, 10.99, 58, 1, 'Saitama vs Garou reaches its climax', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(28, 'product28.jpg', 'Bocchi the Rock! Hitori Gotoh Figure', 'Good Smile Company', 4, 178.00, 11, 1, 'Shy guitarist in her iconic pink tracksuit', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(29, 'product29.jpg', 'Kubo Won\'t Let Me Be Invisible Vol. 12', 'Nene Yukimori', 2, 9.99, 64, 1, 'Wholesome romance comedy', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(30, 'product30.jpg', 'The Art of Spirited Away', 'Studio Ghibli', 3, 55.00, 14, 1, 'Official concept art and background paintings', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(31, 'product31.jpg', 'Nier Automata 2B YoRHa Figure', 'Square Enix', 4, 298.00, 3, 0, 'Deluxe version with multiple weapons', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(32, 'product32.jpg', 'Pride and Prejudice', 'Jane Austen', 1, 9.99, 42, 1, 'Timeless romantic classic', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(33, 'product33.jpg', 'Oshi no Ko Vol. 11', 'Aka Akasaka', 2, 11.99, 89, 1, 'The entertainment industry dark mystery continues', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(34, 'product34.jpg', 'Kaguya-sama Love is War Complete Set', 'Aka Akasaka', 2, 98.00, 8, 0, 'All volumes of the romantic comedy masterpiece', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(35, 'product35.jpg', 'Moriarty the Patriot Art Book', 'Ryosuke Takeuchi', 3, 48.00, 16, 1, 'Victorian era Sherlock Holmes rival story', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(36, 'product36.jpg', 'Sailor Moon Eternal Edition Vol. 1-10', 'Naoko Takeuchi', 2, 189.99, 5, 0, 'Remastered complete collection', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(37, 'product37.jpg', 'The Hobbit', 'J.R.R. Tolkien', 1, 15.99, 31, 1, 'The classic prelude to Lord of the Rings', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(38, 'product38.jpg', 'Steins;Gate Visual Novel Art Book', '5pb. & Nitroplus', 3, 62.00, 9, 0, 'Complete illustration collection', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(39, 'product39.jpg', 'Makima Chainsaw Man Figure', 'SH Figuarts', 4, 135.00, 12, 1, 'Highly detailed and posable figure', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(40, 'product40.jpg', 'Charlotte\'s Web', 'E.B. White', 9, 7.99, 87, 1, 'Heartwarming children classic about friendship', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(41, 'product41.jpg', 'Dr. STONE Complete Manga Set', 'Riichiro Inagaki', 2, 168.00, 6, 0, 'All 26 volumes of the science adventure', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(42, 'product42.jpg', 'Weathering With You Art Book', 'Makoto Shinkai', 3, 52.00, 13, 1, 'Beautiful sky and cloud illustrations', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(43, 'product43.jpg', 'Anya Forger Spy x Family Figure', 'Banpresto', 4, 38.00, 42, 1, 'Cute chibi style with peanut accessory', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(44, 'product44.jpg', 'The Catcher in the Rye', 'J.D. Salinger', 1, 13.99, 36, 1, 'Iconic coming-of-age novel', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(45, 'product45.jpg', 'Solo Leveling Vol. 8', 'Chugong', 2, 19.99, 71, 1, 'Korean web novel masterpiece', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(46, 'product46.jpg', 'Lord of the Rings Trilogy', 'J.R.R. Tolkien', 1, 48.99, 14, 1, 'Complete epic fantasy saga', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(47, 'product47.jpg', 'Howl\'s Moving Castle Art Book', 'Studio Ghibli', 3, 68.00, 11, 1, 'Magical illustrations by Hayao Miyazaki', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(48, 'product48.jpg', 'Emilia Re:Zero Crystal Dress Figure', 'Shibuya Scramble', 4, 398.00, 2, 0, 'Stunning 1/7 scale luxury figure', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1),
(49, 'product49.jpg', 'Where the Wild Things Are', 'Maurice Sendak', 9, 18.99, 53, 1, 'Beloved children picture book classic', '2025-11-30 17:14:10', '2025-12-07 19:26:07', 1),
(50, 'product50.jpg', 'The Complete Calvin and Hobbes', 'Bill Watterson', 2, 135.00, 4, 0, 'All strips in beautiful hardcover collection', '2025-11-30 17:14:10', '2025-12-19 18:21:54', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_title` (`title`),
  ADD KEY `idx_category` (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
