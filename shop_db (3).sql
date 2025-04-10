-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2025 at 05:09 PM
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
-- Database: `shop_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `pid` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(100) NOT NULL,
  `quantity` int(100) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `pid`, `name`, `price`, `quantity`, `image`) VALUES
(129, 14, 16, 'lavendor rose', 13, 1, 'lavendor rose.jpg'),
(130, 14, 18, 'red tulipa', 11, 1, 'red tulipa.jpg'),
(131, 14, 15, 'cottage rose', 15, 1, 'cottage rose.jpg'),
(138, 19, 19, 'Durex', 3500, 1, 'téléchargement4.jpeg'),
(139, 19, 13, 'Albendazole', 1200, 1, 'al.jpg'),
(177, 16, 13, 'Albendazole', 1200, 1, 'al.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `number` varchar(12) NOT NULL,
  `message` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `user_id`, `name`, `email`, `number`, `message`) VALUES
(15, 16, 'william', 'Eschosys@gmil.com', '3256412351', 'yojhgfdfghjhg'),
(18, 16, 'william', 'Eschosys@gmil.com', '745698545269', 'yofgjbhjfhbn'),
(19, 16, 'william', 'Eschosys@gmil.com', '745698545269', 'yofgjbhjfhbn'),
(20, 16, 'william', 'Eschosys@gmil.com', '8525845845', 'hello bro yeah'),
(21, 16, 'william', 'Eschosys@gmil.com', '15256985', 'azertyuiojhgfdfghjk'),
(22, 16, 'sdfghjkl', 'Will@gmail.com', '897654214356', 'Yo Bro');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(100) NOT NULL,
  `sender_id` int(100) NOT NULL,
  `receiver_id` int(100) NOT NULL,
  `message` text NOT NULL,
  `timestamp` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `is_read` tinyint(100) NOT NULL,
  `last_viewed` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `timestamp`, `is_read`, `last_viewed`) VALUES
(1, 16, 10, 'Yo', '0000-00-00 00:00:00.000000', 0, NULL),
(2, 16, 10, 'Yo', '0000-00-00 00:00:00.000000', 0, NULL),
(3, 16, 10, 'Yo', '0000-00-00 00:00:00.000000', 0, NULL),
(4, 16, 10, 'Yo', '0000-00-00 00:00:00.000000', 0, NULL),
(5, 16, 10, 'Yo', '0000-00-00 00:00:00.000000', 0, NULL),
(6, 16, 10, 'Yo', '0000-00-00 00:00:00.000000', 0, NULL),
(7, 16, 10, 'Yo', '0000-00-00 00:00:00.000000', 0, NULL),
(8, 16, 10, 'Yo', '0000-00-00 00:00:00.000000', 0, NULL),
(9, 16, 10, 'Yo', '0000-00-00 00:00:00.000000', 0, NULL),
(10, 16, 10, 'Yo', '0000-00-00 00:00:00.000000', 0, NULL),
(11, 16, 10, 'Yo', '0000-00-00 00:00:00.000000', 0, NULL),
(12, 16, 20, 'Yo', '0000-00-00 00:00:00.000000', 0, NULL),
(13, 16, 20, 'Yo', '0000-00-00 00:00:00.000000', 0, NULL),
(14, 16, 20, 'Yo', '0000-00-00 00:00:00.000000', 0, NULL),
(15, 16, 20, 'Yo', '0000-00-00 00:00:00.000000', 0, NULL),
(16, 16, 20, 'Yo', '0000-00-00 00:00:00.000000', 0, NULL),
(17, 16, 17, 'Yo', '0000-00-00 00:00:00.000000', 0, NULL),
(18, 16, 17, 'Yo', '0000-00-00 00:00:00.000000', 0, NULL),
(19, 16, 14, 'juuj', '0000-00-00 00:00:00.000000', 0, NULL),
(20, 16, 15, 'Yo', '0000-00-00 00:00:00.000000', 0, NULL),
(21, 16, 15, 'Yo', '2025-03-17 16:24:30.164260', 0, NULL),
(22, 16, 15, 'how bro', '2025-03-17 16:24:42.144356', 0, NULL),
(23, 15, 15, 'Yo', '2025-03-17 16:28:03.956752', 0, NULL),
(24, 15, 21, 'Yo', '2025-03-17 16:28:27.482968', 0, NULL),
(25, 15, 15, 'Yo', '2025-03-17 16:32:38.952729', 0, NULL),
(26, 15, 16, 'fine and you', '2025-03-17 16:33:23.867179', 0, NULL),
(27, 16, 15, 'Yo', '2025-03-17 16:39:34.462641', 0, NULL),
(28, 15, 16, 'wai tu do quoi?', '2025-03-17 16:41:12.943000', 0, NULL),
(29, 15, 10, 'Yo', '2025-03-17 16:47:46.959825', 0, NULL),
(30, 15, 10, 'Yo', '2025-03-17 18:55:39.353128', 0, NULL),
(31, 15, 10, 'Yo', '2025-03-17 18:55:41.046994', 0, NULL),
(32, 15, 14, 'hello', '2025-03-17 19:14:43.461871', 0, NULL),
(33, 15, 14, 'Yo', '2025-03-18 17:44:10.177734', 0, NULL),
(34, 16, 14, 'Yo', '2025-03-18 18:33:54.617474', 0, NULL),
(35, 16, 10, 'fine and you', '2025-03-18 18:34:04.639937', 0, NULL),
(36, 16, 10, 'Hello My Friend', '2025-03-18 22:36:34.806722', 0, NULL),
(37, 10, 16, 'hello', '2025-03-18 22:49:24.707773', 1, NULL),
(38, 16, 10, 'i have a problem', '2025-03-18 22:55:13.606563', 0, NULL),
(39, 10, 16, 'what is the issue?', '2025-03-18 22:55:36.348510', 1, NULL),
(40, 16, 10, 'what is the use of doliprane?', '2025-03-18 23:05:11.586770', 0, NULL),
(41, 10, 16, 'For headache', '2025-03-19 07:34:59.061816', 1, NULL),
(42, 10, 16, 'hello', '2025-03-19 08:04:01.100035', 1, NULL),
(43, 16, 10, 'hi', '2025-03-19 08:04:20.186126', 0, NULL),
(44, 10, 16, 'yo', '2025-03-19 10:12:07.211921', 1, NULL),
(45, 10, 16, 'Yo', '2025-03-19 12:12:03.609776', 1, NULL),
(46, 16, 10, 'hi', '2025-03-19 12:12:14.072464', 0, NULL),
(47, 10, 16, 'hello', '2025-03-20 12:59:18.842000', 1, NULL),
(48, 10, 16, 'Yo', '2025-03-20 15:00:05.828302', 1, NULL),
(49, 16, 10, 'how bro', '2025-03-20 15:01:04.253920', 0, NULL),
(50, 10, 16, 'fine and you', '2025-03-20 15:01:51.605306', 1, NULL),
(51, 16, 10, 'fine', '2025-03-20 19:36:02.462493', 0, NULL),
(52, 10, 16, 'fine', '2025-03-20 19:55:28.269764', 1, NULL),
(53, 16, 10, 'Yo', '2025-03-20 19:55:39.928124', 0, NULL),
(54, 10, 16, 'hello', '2025-03-22 14:02:48.955238', 1, NULL),
(55, 10, 16, 'hi', '2025-03-22 14:15:27.967323', 1, NULL),
(56, 10, 16, 'juuj', '2025-03-22 14:22:31.883146', 0, NULL),
(57, 14, 16, 'hello', '2025-03-25 13:32:59.320835', 0, NULL),
(58, 16, 14, 'hi', '2025-03-25 13:33:49.654402', 0, NULL),
(59, 14, 16, 'how', '2025-03-25 13:34:05.451061', 0, NULL),
(60, 16, 14, 'fine and you', '2025-03-25 13:34:15.126701', 0, NULL),
(61, 16, 10, 'Yo', '2025-03-25 16:11:26.101882', 0, NULL),
(62, 16, 14, 'Who are you?', '2025-03-25 16:29:45.488395', 0, NULL),
(63, 16, 10, 'Good', '2025-03-25 16:31:32.555292', 0, NULL),
(64, 10, 15, 'Yo', '2025-03-25 20:58:09.549929', 0, NULL),
(65, 16, 10, 'hello', '2025-03-28 13:02:18.425186', 0, NULL),
(66, 15, 10, 'Yo', '2025-04-01 18:45:15.551434', 0, NULL),
(67, 15, 10, 'how bro', '2025-04-01 18:45:22.292736', 0, NULL),
(68, 10, 15, 'yo', '2025-04-01 18:50:43.227647', 0, NULL),
(69, 16, 10, 'yo', '2025-04-09 13:32:59.709841', 0, NULL),
(70, 10, 16, 'how', '2025-04-09 13:33:22.415918', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `number` varchar(12) NOT NULL,
  `email` varchar(100) NOT NULL,
  `method` varchar(50) NOT NULL,
  `address` varchar(500) NOT NULL,
  `total_products` varchar(1000) NOT NULL,
  `total_price` int(100) NOT NULL,
  `placed_on` varchar(50) NOT NULL,
  `paypal_order_id` varchar(255) DEFAULT NULL,
  `payment_id` varchar(255) DEFAULT NULL,
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `name`, `number`, `email`, `method`, `address`, `total_products`, `total_price`, `placed_on`, `paypal_order_id`, `payment_id`, `payment_status`) VALUES
(29, 16, 'Williamson', '12456789', 'Eschosys@gmil.com', 'paypal', 'Hehheheh, Cameroun', ', Albendazole (4) , Fervex (5) , Drenoxol (4) , Mebendazole (6) , Loxair (4) , Durex (6) , Doliprane (6) ', 97700, '25-Mar-2025', NULL, NULL, 'completed'),
(39, 16, 'William Tchuisseu', '123456789', 'williamuchiwa325@gmail.com', 'mtn_momo', 'Hehheheh, Cameroun', ', Fervex (1) , Drenoxol (1) , Loxair (1) ', 13250, '26-Mar-2025', NULL, NULL, 'completed'),
(40, 16, 'William Tchuisseu', '123456789', 'williamuchiwa325@gmail.com', 'orange_money', 'Hehheheh, Cameroun', ', Fervex (1) , Drenoxol (1) , Loxair (1) ', 13250, '26-Mar-2025', NULL, NULL, 'completed'),
(41, 16, 'William Tchuisseu', '320112124154', 'william@gmail.com', 'mtn_momo', 'Hehheheh, Cameroun', ', Fervex (1) , Drenoxol (1) , Loxair (1) ', 13250, '26-Mar-2025', NULL, NULL, 'completed'),
(42, 16, 'William Tchuisseu', '42154231', 'williamuc@gmail.com', 'credit card', 'Hehheheh, Cameroun', ', Fervex (1) , Drenoxol (1) , Loxair (1) ', 13250, '26-Mar-2025', NULL, NULL, 'completed'),
(43, 16, 'William Tchuisseu', '42154231', 'williamuc@gmail.com', 'paypal', 'Hehheheh, Cameroun', ', Fervex (1) , Drenoxol (1) , Loxair (1) ', 13250, '26-Mar-2025', NULL, NULL, 'completed'),
(45, 16, 'William Tchuisseu', '12345666', 'will325@gmil.com', 'cash on delivery', 'Hehheheh, Cameroun', ', Albendazole (14) ', 16800, '26-Mar-2025', NULL, NULL, 'completed'),
(46, 16, 'William Tchuisseu', '98875631212', 'wil@gmail.com', 'Orange Money', 'Hehheheh, Cameroun', ', Drenoxol (14) ', 45500, '26-Mar-2025', NULL, NULL, 'completed'),
(48, 16, 'William Tchuisseu', '654123654', 'Eschosys@gmil.com', 'Orange Money', 'Hehheheh, Cameroun', ', Albendazole (12) , Drenoxol (1) ', 17650, '26-Mar-2025', NULL, NULL, 'completed'),
(50, 16, 'William Tchuisseu', '7894651320', 'williamuchiwa5@gmail.com', 'cash on delivery', 'Hehheheh, Cameroun', ', Doliprane (1) , Fervex (1) , Loxair (1) , Drenoxol (1) ', 14750, '26-Mar-2025', NULL, NULL, 'completed'),
(51, 16, 'William Tchuisseu', '678189735', 'williamuchiwa32@gmail.com', 'MTN Mobile Money', 'Hehheheh, Cameroun', ', Albendazole (1) , Fervex (6) , Mebendazole (12) ', 27000, '26-Mar-2025', NULL, NULL, 'completed'),
(52, 16, 'azert', '632547895', 'william@gmail.com', 'MTN Mobile Money', 'yaounde, cameroon', ', Mebendazole (1) , Albendazole (2) ', 3800, '09-Apr-2025', NULL, NULL, 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset`
--

CREATE TABLE `password_reset` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expiry` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_reset`
--

INSERT INTO `password_reset` (`id`, `user_id`, `token`, `expiry`, `created_at`) VALUES
(1, 10, 'dc0c01cd5d71d81aa22a7c2ac4d76228', '2025-02-28 10:58:23', '2025-02-25 11:54:38'),
(4, 18, 'ce6791e9573e79fe570683a1f2570767', '2025-02-25 14:51:39', '2025-02-25 12:51:39');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp` varchar(10) NOT NULL,
  `expiry` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `otp`, `expiry`, `created_at`) VALUES
(2, 'Eschosys@gmil.com', '708475', '2025-03-20 13:18:46', '2025-03-15 10:21:29'),
(3, 'williamuchiwa325@gmail.com', '152610', '2025-03-15 11:36:42', '2025-03-15 10:21:42'),
(5, 'wilfriedkamtchoum@gmail.com', '376291', '2025-03-20 16:11:26', '2025-03-15 11:38:12'),
(6, 'trojan@gmail.com', '627085', '2025-03-17 15:51:53', '2025-03-17 14:05:37');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `details` varchar(500) NOT NULL,
  `price` int(100) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `details`, `price`, `image`) VALUES
(13, 'Albendazole', 'Albendazole is an essential antiparasitic medication used to treat a wide range of intestinal and systemic parasitic infections. Effective against various worm infestations including roundworms, hookworms, and tapeworms, this medication works by preventing parasites from absorbing nutrients, ultimately eliminating them from the body. Prescribed by healthcare professionals, albendazole is a critical treatment option in both human and veterinary medicine.', 1200, 'al.jpg'),
(15, 'Fervex', 'Fervex is a comprehensive cold and flu remedy designed to alleviate multiple symptoms associated with upper respiratory tract infections. The combination medication helps reduce fever, relieve nasal congestion, and minimize body aches. Formulated to provide fast-acting relief, Fervex supports patients in managing uncomfortable cold and flu symptoms, helping them recover more comfortably and quickly.', 1500, 'fervex.png'),
(16, 'Drenoxol', 'Drenoxol is a specialized pharmaceutical product typically used for managing specific medical conditions. While specific details would require verification from medical professionals, it is likely prescribed for targeted therapeutic purposes. Patients should always consult their healthcare provider for precise information about dosage, indications, and potential side effects.', 3250, 'dreno.png'),
(17, 'Mebendazole', 'Mebendazole is a highly effective antiparasitic medication used to treat various intestinal worm infections. This powerful anthelmintic drug works by preventing parasitic worms from absorbing glucose, effectively destroying them and eliminating them from the body. Commonly prescribed for treating pinworms, roundworms, whipworms, and hookworms, mebendazole is suitable for both adults and children. Available in tablet and suspension forms, it provides a reliable solution for treating parasitic inf', 1400, 'té.jpg'),
(18, 'Loxair', 'Loxair is a respiratory medication designed to provide relief for patients experiencing bronchial and pulmonary conditions. Typically used to manage symptoms of asthma, chronic obstructive pulmonary disease (COPD), and other respiratory tract disorders, this medication helps to open airways, reduce inflammation, and improve breathing', 8500, 'loxair.png'),
(19, 'Durex', 'Durex is a leading brand of personal protective health products, specifically high-quality condoms designed to provide protection during sexual activity. Manufactured with rigorous quality control standards, Durex condoms help prevent sexually transmitted infections (STIs) and unwanted pregnancies. Available in various sizes, textures, and styles to enhance user comfort and satisfaction while prioritizing sexual health and safety.', 3500, 'téléchargement4.jpeg'),
(20, 'Doliprane', 'Doliprane is a widely used pain relief and fever reduction medication containing paracetamol (acetaminophen). Effective for treating mild to moderate pain, including headaches, dental pain, muscle aches, menstrual cramps, and fever. Doliprane provides quick and reliable symptom relief with a well-established safety profile. Available in various dosage forms including tablets, capsules, and liquid suspension, it offers flexible treatment options for different age groups.', 1500, 'téléchargement9.jpg'),
(23, 'Naproxen 220mg', 'Another NSAID that provides long-lasting pain relief and reduces inflammation. Particularly effective for arthritis, menstrual cramps, and muscle aches. Typically available in tablet form.', 2400, 'naprox.jpg'),
(24, 'paracetamol', 'dsfghjkrdbvngbhgfcfvgbjh', 500, 'author-1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `user_type` varchar(20) NOT NULL DEFAULT 'user',
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `user_type`, `last_login`) VALUES
(10, 'admin A', 'admin01@gmail.com', '+237678189735', '25f9e794323b453885f5181f1b624d0b', 'admin', '2025-04-10 15:20:09'),
(14, 'Admin Ages', 'admin02@gmail.com', '+237678189735', '25f9e794323b453885f5181f1b624d0b', 'admin', NULL),
(15, 'user B', 'user02@gmail.com', '+237678189735', '25f9e794323b453885f5181f1b624d0b', 'user', NULL),
(16, 'william', 'Eschosys@gmil.com', '+237695410384', '25f9e794323b453885f5181f1b624d0b', 'user', NULL),
(19, 'Wilfried ', 'wilfriedcart@gmail.com', NULL, '25d55ad283aa400af464c76d713c07ad', 'user', NULL),
(22, 'Ange', 'William@gmail.com', '+237678189735', '1697918c7f9551712f531143df2f8a37', 'user', NULL),
(25, 'AND', 'QSDFGHJKL@GMAIL.COM', '123456789741', '25f9e794323b453885f5181f1b624d0b', 'user', NULL),
(26, 'ANF', 'AZERTY@GM.COM', '123456789741', '25f9e794323b453885f5181f1b624d0b', 'user', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `pid` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(100) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `pid`, `name`, `price`, `image`) VALUES
(60, 14, 19, 'pink bouquet', 15, 'pink bouquet.jpg'),
(62, 19, 13, 'Albendazole', 1200, 'al.jpg'),
(63, 19, 18, 'Loxair', 8500, 'loxair.png'),
(68, 16, 15, 'Fervex', 1500, 'fervex.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset`
--
ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

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
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=178;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `password_reset`
--
ALTER TABLE `password_reset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
