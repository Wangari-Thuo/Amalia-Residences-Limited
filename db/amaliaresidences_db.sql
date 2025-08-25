-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 25, 2025 at 03:49 PM
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
-- Database: `amaliaresidences_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(11) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `name`, `email`, `role`, `password`) VALUES
(1, 'Kelvin Shisanya', 'kelvoshisanya@gmail.com', 'Admin', 'Kelvin');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `property_id` int(11) DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `check_in` date DEFAULT NULL,
  `check_out` date DEFAULT NULL,
  `num_guests` int(11) NOT NULL DEFAULT 1,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `totalprice` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `property_id`, `booking_date`, `check_in`, `check_out`, `num_guests`, `status`, `totalprice`) VALUES
(1, 1, 2, '2025-08-10', '2025-08-14', '2025-08-21', 1, '', 595000.00),
(2, 1, 3, '2025-08-10', '2025-08-13', '2025-08-28', 9, '', 225000.00),
(3, 6, 4, '2025-08-10', '2025-08-21', '2025-08-30', 3, NULL, 585000.00),
(4, 6, 4, '2025-08-10', '2025-08-21', '2025-08-30', 3, NULL, 585000.00),
(5, 6, 4, '2025-08-10', '2025-08-21', '2025-08-30', 3, NULL, 585000.00),
(6, 6, 4, '2025-08-10', '2025-08-21', '2025-08-30', 3, '', 585000.00),
(7, 6, 4, '2025-08-10', '2025-08-21', '2025-08-30', 3, '', 585000.00),
(8, 6, 7, '2025-08-10', '2025-08-27', '2025-08-31', 6, '', 480000.00),
(9, 1, 5, '2025-08-10', '2025-08-14', '2025-08-22', 1, '', 320000.00),
(10, 1, 8, '2025-08-10', '2025-08-18', '2025-08-21', 3, 'cancelled', 90000.00),
(11, 1, 8, '2025-08-10', '2025-08-18', '2025-08-21', 3, 'cancelled', 90000.00),
(12, 1, 2, '2025-08-11', '2025-08-20', '2025-08-28', 1, '', 680000.00),
(13, 11, 2, '2025-08-25', '2025-08-25', '2025-08-26', 1, '', 85000.00),
(14, 11, 2, '2025-08-25', '2025-08-25', '2025-08-26', 1, 'confirmed', 85000.00);

-- --------------------------------------------------------

--
-- Table structure for table `contact_queries`
--

CREATE TABLE `contact_queries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `response` text DEFAULT NULL,
  `status` enum('pending','responded') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
  `contact_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'General Inquiry',
  `message` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_us`
--

INSERT INTO `contact_us` (`contact_id`, `user_id`, `name`, `email`, `category`, `message`, `submitted_at`, `created_at`) VALUES
(1, NULL, 'Susan Wangari', 'swangari388@gmail.com', 'General Inquiry', 'Thank you for excellent service', '2025-08-10 13:39:17', '2025-08-10 15:41:31'),
(2, NULL, 'Susan Wangari', 'swangari388@gmail.com', 'General Inquiry', 'Thank you for excellent service', '2025-08-10 13:39:37', '2025-08-10 15:41:31'),
(3, NULL, 'Susan Wangari', 'swangari388@gmail.com', 'General Inquiry', 'Thank you for excellent service', '2025-08-10 15:16:06', '2025-08-10 15:41:31'),
(4, 1, 'Lilian Wanjiru', 'lwanjiru@gmail.com', 'General Inquiry', 'Younare the best', '2025-08-10 15:33:16', '2025-08-10 15:41:31'),
(5, 1, 'Lilian Wanjiru', 'lwanjiru@gmail.com', 'General Inquiry', 'Thank you for excellent service', '2025-08-10 15:34:57', '2025-08-10 15:41:31'),
(6, 1, 'Lilian Wanjiru', 'lwanjiru@gmail.com', 'General Inquiry', 'Thank you for excellent service', '2025-08-10 15:35:29', '2025-08-10 15:41:31'),
(7, 1, 'Lilian Wanjiru', 'lwanjiru@gmail.com', 'General Inquiry', 'Thank you for excellent service', '2025-08-10 15:39:51', '2025-08-10 15:41:31'),
(8, 1, 'Lilian Wanjiru', 'lwanjiru@gmail.com', 'General Inquiry', 'Thank you for excellent service', '2025-08-10 15:41:54', '2025-08-10 15:41:54'),
(9, 1, 'Teresia Njoki', 'tnjoki@gmail.com', 'General Inquiry', 'Thank you for excellent service', '2025-08-10 18:37:19', '2025-08-10 18:37:19'),
(10, 1, 'Susan Wangari', 'swangari388@gmail.com', 'General Inquiry', 'Thank you for excellent service', '2025-08-10 18:37:35', '2025-08-10 18:37:35');

-- --------------------------------------------------------

--
-- Table structure for table `hosts`
--

CREATE TABLE `hosts` (
  `host_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hosts`
--

INSERT INTO `hosts` (`host_id`, `name`, `email`, `phone`) VALUES
(1, 'James Mwangi', 'james@example.com', '0712345678'),
(2, 'Mary Wanjiku', 'mary@example.com', '0723456789'),
(3, 'Peter Kamau', 'peter@example.com', '0734567890'),
(4, 'Sarah Njeri', 'sarah@example.com', '0745678901'),
(5, 'David Otieno', 'david@example.com', '0756789012'),
(6, 'Jane Achieng', 'jane@example.com', '0767890123'),
(7, 'Michael Kariuki', 'michael@example.com', '0778901234'),
(8, 'Catherine Wambui', 'catherine@example.com', '0789012345'),
(9, 'Samuel Kiptoo', 'samuel@example.com', '0790123456'),
(10, 'Lucy Muthoni', 'lucy@example.com', '0701234567');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `method` varchar(50) DEFAULT NULL,
  `mpesa_receipt` varchar(50) DEFAULT NULL,
  `checkout_request_id` varchar(50) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_id`, `amount`, `payment_date`, `method`, `mpesa_receipt`, `checkout_request_id`, `payment_status`) VALUES
(1, 1, 595000.00, '2025-08-10 13:09:41', 'mpesa', 'THA8PWDS32', NULL, 'success'),
(2, 1, 595000.00, '2025-08-10 13:27:21', 'mpesa', 'THA8PWDS32', NULL, 'success'),
(3, 1, 595000.00, '2025-08-10 13:27:55', 'mpesa', 'THA8PWDS32', NULL, 'success'),
(4, 1, 595000.00, '2025-08-10 13:28:14', 'mpesa', 'THA8PWDS32', NULL, 'success'),
(5, 2, 225000.00, '2025-08-11 19:15:01', 'mpesa', 'THA8PWDS32', NULL, 'success'),
(6, 12, 680000.00, '2025-08-11 19:23:39', 'mpesa', 'THA8PWDS32', NULL, 'success'),
(7, 13, 85000.00, '2025-08-25 07:27:54', 'mpesa', '3454343', NULL, 'success');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `property_id` int(11) NOT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `status` enum('available','booked') DEFAULT 'available',
  `image` varchar(255) NOT NULL,
  `maxguests` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`property_id`, `owner_id`, `description`, `location`, `price`, `status`, `image`, `maxguests`) VALUES
(2, 1, 'Spacious 6-bedroom bungalow with private garden and swimming pool.', 'Karen, Nairobi', 85000.00, 'available', 'uploads/bungalow.jpg', 1),
(3, 2, 'Compact and modern studio apartment, perfect for one person.', 'Westlands, Nairobi', 15000.00, 'available', 'uploads/studio.jpg', 9),
(4, 3, 'Large 4-bedroom family house with backyard and garage.', 'Runda, Nairobi', 65000.00, 'available', 'uploads/familyhouse.jpg', 3),
(5, 4, 'Fully furnished serviced apartment ideal for short-term stays.', 'Kilimani, Nairobi', 40000.00, 'available', 'uploads/servicedapt.jpg', 1),
(6, 5, 'Affordable bedsitter close to public transport.', 'Thika Road, Nairobi', 8000.00, 'available', 'uploads/bedsitter.jpg', 8),
(7, 6, 'Beachfront villa with panoramic ocean views.', 'Diani, Mombasa', 120000.00, 'available', 'uploads/beachfront.jpg', 6),
(8, 7, 'Rustic wooden cabin surrounded by nature.', 'Nanyuki, Laikipia', 30000.00, 'available', 'uploads/cabin.jpg', 5),
(9, 8, 'Modern 3-bedroom duplex with balcony and parking.', 'Syokimau, Machakos', 55000.00, 'available', 'uploads/duplex.jpg', 2),
(10, 9, 'Cozy 1-bedroom apartment for singles or couples.', 'Langata, Nairobi', 20000.00, 'available', 'uploads/onebed.jpg', 10),
(11, 10, 'Traditional farmhouse with spacious land for farming.', 'Nyeri, Central Kenya', 45000.00, 'available', 'uploads/farmhouse.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `property_id` int(11) DEFAULT NULL,
  `review_text` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `user_id`, `property_id`, `review_text`, `rating`, `created_at`) VALUES
(3, 1, 11, 'Excellent serene environment, top notch customer service with an experience of feeling at home away from home.', 5, '2025-08-10 15:09:14'),
(4, 2, 6, 'Excellent experience.', 5, '2025-08-10 15:11:28'),
(5, 1, 7, 'excellence', 5, '2025-08-10 18:38:09');

-- --------------------------------------------------------

--
-- Table structure for table `support_requests`
--

CREATE TABLE `support_requests` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('open','in_progress','resolved') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `id_number` varchar(50) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('host','guest') NOT NULL DEFAULT 'guest'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `id_number`, `profile_photo`, `password`, `role`) VALUES
(1, 'Susan Wangari Thuo', 'swangari388@gmail.com', '35966546', NULL, '$2y$10$x6pkNNZrC1zmkEKJzKIEvep0mtZ4fpnOEvkb6l12YvkQ539DtYgEa', 'guest'),
(2, 'Daniel Thuo', 'dguchu123@gmail.com', '13413821', NULL, '$2y$10$cpIEXzI9IHBaADMbEkp5U.3yusheW1KbfkkdNGYzLpwQynr0GTp0y', 'guest'),
(3, 'Nancy Nyathira', 'nnyathira@gmail.com', '123456', NULL, '$2y$10$1X49lMIvg3gF5xFtF/X5k.b.T.We8Wgy/wEIfS1X/v1VXg04y.OAW', 'guest'),
(4, 'Lilian Wanjiru', 'lwanjiru@gmail.com', '12345678', NULL, '$2y$10$Z3zthAqvZaTOFRlOkx41OOGEqCWo9yYbmNAnbiyeJ1spf99l8Izm.', 'guest'),
(6, 'Teresia Njoki', 'tnjoki@gmail.com', '780206', NULL, '$2y$10$4IkCdna.NOcIodAiO/B46O/0hKuExcPeiBsbikbIPZTxVvtNQYe8G', 'guest'),
(7, 'Madam Jane', 'mjane@gmail.com', '810304', NULL, '$2y$10$ng34ErZCXVUAE/Y48U/Wm.qtI2nYCQ.RiOtD/.S3ZLAZL6ZSjBvoe', 'host'),
(8, 'Paul Thuo', 'pthuo@gmail.com', '429087', NULL, '$2y$10$xRme3CeTS1epbfpsLcZLPO/xrmbgT0CnQ7aA0v7ekiXpohUa/ZER.', 'guest'),
(11, 'Kelvin Shisanya', 'kelvoshisanya@gmail.com', '37062180', 'user_11.jpg', '$2y$10$LLxrtxUKRR8EcT8S1h27vOcihF0a5ORVdSjiJ2nlIh53wQjTXm2Oq', 'guest');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `contact_queries`
--
ALTER TABLE `contact_queries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`contact_id`);

--
-- Indexes for table `hosts`
--
ALTER TABLE `hosts`
  ADD PRIMARY KEY (`host_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`property_id`),
  ADD KEY `host_id` (`owner_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `support_requests`
--
ALTER TABLE `support_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `id_number` (`id_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `contact_queries`
--
ALTER TABLE `contact_queries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `hosts`
--
ALTER TABLE `hosts`
  MODIFY `host_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `property_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `support_requests`
--
ALTER TABLE `support_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`);

--
-- Constraints for table `contact_queries`
--
ALTER TABLE `contact_queries`
  ADD CONSTRAINT `contact_queries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`);

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `hosts` (`host_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`);

--
-- Constraints for table `support_requests`
--
ALTER TABLE `support_requests`
  ADD CONSTRAINT `support_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
