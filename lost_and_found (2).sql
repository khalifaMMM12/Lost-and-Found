-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 15, 2025 at 11:22 AM
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
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`admin_id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$fvvL8HaNhUd5bcNv4W0yH.lMv7Sr/giXSNj7x1vhADB2GqZP9Bgle', '2025-07-02 08:46:15');

-- --------------------------------------------------------

--
-- Table structure for table `claims`
--

CREATE TABLE `claims` (
  `claim_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','approved','denied') DEFAULT 'pending',
  `claim_name` varchar(100) NOT NULL,
  `claim_email` varchar(100) NOT NULL,
  `claim_identifier_type` enum('nin','email','matric') DEFAULT NULL,
  `claim_identifier_value` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `claims`
--

INSERT INTO `claims` (`claim_id`, `item_id`, `user_id`, `status`, `claim_name`, `claim_email`, `claim_identifier_type`, `claim_identifier_value`, `created_at`) VALUES
(1, 7, 3, 'pending', 'khalifa', 'Khalifa@gmail.com', NULL, NULL, '2025-08-14 15:36:20'),
(2, 8, 3, 'pending', 'khalifa', 'Khalifa@gmail.com', NULL, NULL, '2025-08-14 15:37:16'),
(3, 9, 3, 'pending', 'khalifa', 'Khalifa@gmail.com', NULL, NULL, '2025-08-14 15:39:33');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `type` enum('lost','found') NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `status` enum('pending','approved','returned') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `contact_phone` varchar(20) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `user_id`, `type`, `description`, `location`, `date`, `status`, `created_at`, `updated_at`, `contact_phone`, `contact_email`) VALUES
(1, 1, 'lost', 'apple watch', 'library', '2025-06-02', 'returned', '2025-06-22 14:00:28', '2025-07-19 22:46:12', NULL, NULL),
(2, 1, 'found', 'lost', 'a place', '2025-05-26', 'approved', '2025-06-24 15:17:47', '2025-07-02 14:51:43', NULL, NULL),
(4, 1, 'found', 'set', 'Room 30', '2025-06-04', 'approved', '2025-06-24 15:46:01', '2025-07-02 13:47:15', NULL, NULL),
(6, 1, 'lost', 'a black book', 'place', '2000-02-23', 'pending', '2025-07-02 15:11:42', '2025-07-02 15:11:42', NULL, NULL),
(7, 1, 'found', 'blue and black', 'library', '2025-07-01', 'returned', '2025-07-19 22:44:14', '2025-07-19 22:46:12', '07065419655', 'email@yahoo.com'),
(8, NULL, 'lost', 'a huge book with black covers', 'library', '2025-08-01', 'approved', '2025-08-14 14:59:14', '2025-08-14 15:24:22', '07065419655', 'khalifa@gmail.com'),
(9, 3, 'lost', 'missing', 'library', '2025-07-30', 'approved', '2025-08-14 15:39:27', '2025-08-14 17:04:58', '07065419655', '');

-- --------------------------------------------------------

--
-- Table structure for table `item_images`
--

CREATE TABLE `item_images` (
  `image_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_images`
--

INSERT INTO `item_images` (`image_id`, `item_id`, `image_path`, `uploaded_at`) VALUES
(1, 1, 'img_68580c7c2ac9d8.12525130.png', '2025-06-22 14:00:28'),
(2, 2, 'img_685ac19bab8781.12760271.png', '2025-06-24 15:17:47'),
(4, 4, 'img_685ac83985ab29.21942842.png', '2025-06-24 15:46:01'),
(6, 6, 'img_68654c2e0617c4.82487174.png', '2025-07-02 15:11:42'),
(7, 7, 'img_687c1fbe21e595.99696883.png', '2025-07-19 22:44:14'),
(8, 8, 'img_689df9c23ac388.51106571.jpg', '2025-08-14 14:59:14');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `item_id`, `recipient_id`, `message`, `timestamp`, `is_read`) VALUES
(1, 1, 1, 'Your lost/found item has been matched by admin. Please check your dashboard.', '2025-07-19 22:46:12', 0),
(2, 7, 1, 'Your lost/found item has been matched by admin. Please check your dashboard.', '2025-07-19 22:46:12', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `identifier_type` enum('nin','email','matric') DEFAULT NULL,
  `identifier_value` varchar(100) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `identifier_type`, `identifier_value`, `role`) VALUES
(1, 'khalifa', 'admin@admin.com', '$2y$10$QMYzZgu2SNANMbxORFBtfuGUmjTkiSBolNkkV3ZP2Ma01C0qKKsxO', NULL, NULL, 'user'),
(3, 'khalifa', 'Khalifa@gmail.com', '$2y$10$UY3UgoKlyRO/9oBg41nYru.Th1GYiuxZT.bKtiJkQb9SJlAs/GLz2', 'email', 'Khalifa@gmail.com', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `claims`
--
ALTER TABLE `claims`
  ADD PRIMARY KEY (`claim_id`),
  ADD UNIQUE KEY `unique_claim_per_user_item` (`item_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `item_images`
--
ALTER TABLE `item_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `recipient_id` (`recipient_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `users_identifier_value_unique` (`identifier_value`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `claims`
--
ALTER TABLE `claims`
  MODIFY `claim_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `item_images`
--
ALTER TABLE `item_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `claims`
--
ALTER TABLE `claims`
  ADD CONSTRAINT `claims_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `claims_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `item_images`
--
ALTER TABLE `item_images`
  ADD CONSTRAINT `item_images_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
