-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2026 at 01:20 AM
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
-- Database: `lost_and_found`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `categoryName` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `categoryName`) VALUES
(5, 'bags'),
(6, 'clothes'),
(4, 'documents'),
(1, 'electronic'),
(3, 'keys'),
(7, 'others'),
(2, 'wallet');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `report_type` enum('lost','found') NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `lost_found_date` date NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `user_id`, `category_id`, `report_type`, `status`, `title`, `description`, `lost_found_date`, `location`, `image`, `contact`, `created_at`) VALUES
(1, 2, 6, 'found', 'pending', 'In veniam id cum vo', 'Id minim voluptates', '0000-00-00', 'Neque ea deleniti be', '1779729013_WIN_20240914_13_33_35_Pro.jpg', 'Exercitation exceptu', '2026-05-25 17:10:13'),
(2, 2, 6, 'found', 'pending', 'In veniam id cum vo', 'Id minim voluptates', '0000-00-00', 'Neque ea deleniti be', '1779729017_WIN_20240914_13_33_35_Pro.jpg', 'Exercitation exceptu', '2026-05-25 17:10:17'),
(3, 2, 6, 'lost', 'pending', 'Sit sit in voluptat', 'Dolorum nisi facere', '0000-00-00', 'Est placeat iure n', '1779729038_WIN_20240914_13_33_34_Pro.jpg', 'Reiciendis ex aut co', '2026-05-25 17:10:38'),
(4, 2, 6, 'lost', 'pending', 'Sit sit in voluptat', 'Dolorum nisi facere', '0000-00-00', 'Est placeat iure n', '1779729136_WIN_20240914_13_33_34_Pro.jpg', 'Reiciendis ex aut co', '2026-05-25 17:12:16'),
(5, 2, 6, 'lost', 'pending', 'Sit sit in voluptat', 'Dolorum nisi facere', '0000-00-00', 'Est placeat iure n', '1779729171_WIN_20240914_13_33_34_Pro.jpg', 'Reiciendis ex aut co', '2026-05-25 17:12:51'),
(6, 2, 6, 'lost', 'pending', 'Sit sit in voluptat', 'Dolorum nisi facere', '0000-00-00', 'Est placeat iure n', '1779729273_WIN_20240914_13_33_34_Pro.jpg', 'Reiciendis ex aut co', '2026-05-25 17:14:33'),
(7, 2, 6, 'lost', 'pending', 'Sit sit in voluptat', 'Dolorum nisi facere', '0000-00-00', 'Est placeat iure n', '1779729285_WIN_20240914_13_33_34_Pro.jpg', 'Reiciendis ex aut co', '2026-05-25 17:14:45'),
(8, 2, 5, 'lost', 'pending', 'Mollitia harum sapie', 'Perferendis cupidata', '0000-00-00', 'Esse accusamus mole', '1779729300_WIN_20240917_23_55_11_Pro.jpg', 'Nihil omnis do autem', '2026-05-25 17:15:00'),
(9, 2, 5, 'lost', 'pending', 'Mollitia harum sapie', 'Perferendis cupidata', '0000-00-00', 'Esse accusamus mole', '1779729413_WIN_20240917_23_55_11_Pro.jpg', 'Nihil omnis do autem', '2026-05-25 17:16:53'),
(10, 2, 5, 'lost', 'pending', 'Mollitia harum sapie', 'Perferendis cupidata', '0000-00-00', 'Esse accusamus mole', '1779729511_WIN_20240917_23_55_11_Pro.jpg', 'Nihil omnis do autem', '2026-05-25 17:18:31'),
(11, 2, 7, 'lost', 'pending', 'Dolore magna optio', 'In officia consequat', '2008-09-28', 'Et consequuntur dict', '1779729890_WIN_20240917_23_55_11_Pro.jpg', 'Officiis omnis qui s', '2026-05-25 17:24:50'),
(12, 2, 2, 'found', 'pending', 'Voluptatem consectet', 'Aspernatur esse aut', '1998-09-02', 'Tempore quis molest', '1779729906_WIN_20250125_23_58_53_Pro.jpg', 'Laboris porro tempor', '2026-05-25 17:25:06'),
(13, 2, 5, 'lost', 'pending', 'Culpa culpa optio f', 'Maxime corrupti sit', '1984-11-04', 'Impedit omnis incid', '1779809881_WIN_20240917_23_55_11_Pro.jpg', 'Corporis blanditiis', '2026-05-26 15:38:01'),
(14, 3, 7, 'lost', 'pending', 'Qui velit aut disti', 'Earum quisquam sunt', '2010-08-17', 'Voluptates ea ab qui', '1779847639_wakana_gojou_marin_kitagawa_pink_background_4k_hd_my_dress_up_darling-1920x1080.jpg', 'In magni unde sunt', '2026-05-27 02:07:19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT 'default.png',
  `isAdmin` tinyint(1) NOT NULL DEFAULT 0,
  `isActive` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `profile_image`, `isAdmin`, `isActive`, `created_at`, `updated_at`) VALUES
(1, 'pytovy', 'zemut@mailinator.com', '$2y$10$Zp.yJhbOtnYJr9ZLLjM5SuuKfzb0hex1cfmEJQcmKYAwP32TFiVli', 'default.png', 1, 1, '2026-05-25 15:38:59', '2026-05-25 15:38:59'),
(2, 'admin', 'admin@gmail.com', '$2y$10$bWmUo4Eex/zEGGDbyhg8UeQMyIviPw9vQfvqnldfiIAFh.YB0xC6e', 'default.png', 0, 1, '2026-05-25 16:41:04', '2026-05-25 16:41:04'),
(3, 'matezi', 'vynixefu@mailinator.com', '$2y$10$Pk8b7b5RztPmDI49srasXeZ9fLBHlFiLdAHERV1VZCqyYIZ7cmPCq', 'default.png', 0, 1, '2026-05-27 02:06:57', '2026-05-27 02:06:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categoryName` (`categoryName`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
