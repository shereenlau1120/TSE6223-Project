-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 02, 2026 at 01:30 AM
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
-- Database: `property_rental_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `leases`
--

CREATE TABLE `leases` (
  `lease_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `lease_start_date` date NOT NULL,
  `lease_end_date` date NOT NULL,
  `monthly_rent` decimal(10,2) NOT NULL,
  `lease_document` varchar(255) DEFAULT NULL,
  `lease_status` enum('active','expired','terminated') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leases`
--

INSERT INTO `leases` (`lease_id`, `tenant_id`, `property_id`, `lease_start_date`, `lease_end_date`, `monthly_rent`, `lease_document`, `lease_status`, `created_at`) VALUES
(1, 3, 3, '2026-06-01', '2027-06-01', 2689.99, 'leases/1780266773_LeaseAgreementExample.pdf', 'active', '2026-05-31 22:32:53'),
(2, 4, 5, '2026-06-02', '2027-06-02', 2500.00, 'leases/1780331057_LeaseAgreement.pdf', 'expired', '2026-06-01 16:24:17'),
(3, 4, 5, '2026-06-06', '2027-06-06', 2500.00, 'uploads/leases/1780333118_Lease Agreement.pdf', 'active', '2026-06-01 16:58:38');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_requests`
--

CREATE TABLE `maintenance_requests` (
  `request_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `issue_title` varchar(100) NOT NULL,
  `issue_description` text DEFAULT NULL,
  `issue_image` varchar(255) DEFAULT NULL,
  `request_status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `priority_level` enum('low','medium','high') DEFAULT 'medium',
  `admin_remark` text NOT NULL,
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenance_requests`
--

INSERT INTO `maintenance_requests` (`request_id`, `tenant_id`, `property_id`, `issue_title`, `issue_description`, `issue_image`, `request_status`, `priority_level`, `admin_remark`, `request_date`, `completed_date`) VALUES
(1, 3, 1, 'Water Leak in Bathroom', 'Pipe under sink is leaking continuously. Water pooling on floor.', 'assets/img/waterleak.jpg', 'pending', 'high', '', '2026-06-01 19:06:41', NULL),
(2, 4, 3, 'Air Conditioner Not Working', 'Aircond not cooling properly, only blowing warm air.', 'assets/img/aircond.jpg', 'completed', 'medium', 'We will ask people to fix for you this afternoon. Thank you.', '2026-06-01 19:06:41', '2026-06-01 19:17:52'),
(3, 3, 4, 'Broken Light Bulb in Kitchen', 'Kitchen light bulb is broken and needs replacement.', 'assets/img/lighti.jpg', 'completed', 'low', 'Bulb replaced successfully', '2026-06-01 19:06:41', '2026-06-01 02:30:00'),
(4, 4, 1, 'Door Lock Stuck', 'Main door lock is difficult to open and close.', 'assets/img/doorlock.jpg', 'pending', 'medium', '', '2026-06-01 19:06:41', NULL),
(5, 3, 3, 'Roof Leakage During Rain', 'Water leaking from ceiling during heavy rain.', 'assets/img/roof.jpg', 'in_progress', 'high', 'Roof inspection in progress', '2026-06-01 19:06:41', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notification_title` varchar(100) DEFAULT NULL,
  `notification_message` text DEFAULT NULL,
  `notification_type` enum('payment','maintenance','system') DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `notification_title`, `notification_message`, `notification_type`, `is_read`, `created_at`) VALUES
(1, 4, 'Payment Update', 'Your payment was rejected. Reason: Please upload a clearer img/pdf. Thank you', 'payment', 0, '2026-06-01 18:30:47'),
(2, 3, 'Payment Update', 'Your payment has been approved.', 'payment', 0, '2026-06-01 18:36:41'),
(3, 4, 'Maintenance Update', 'Your maintenance request status updated to: completed', 'maintenance', 0, '2026-06-01 19:17:52');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `lease_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('bank_transfer','cash','online_banking','ewallet') DEFAULT NULL,
  `payment_status` enum('paid','pending','overdue','rejected') DEFAULT 'pending',
  `receipt_file` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `lease_id`, `payment_date`, `payment_amount`, `payment_method`, `payment_status`, `receipt_file`, `remarks`, `created_at`) VALUES
(1, 1, '2026-06-01', 2689.99, 'bank_transfer', 'paid', 'uploads/leases/Example Receipt.png', 'Payment verified', '2026-06-01 17:52:06'),
(2, 1, '2026-07-01', 2500.00, 'online_banking', 'paid', 'uploads/leases/Example Receipt.pdf', 'Payment verified', '2026-06-01 17:52:06'),
(3, 2, '2026-06-02', 2500.00, 'cash', 'rejected', 'uploads/leases/sample3.pdf', 'Please upload a clearer img/pdf. Thank you', '2026-06-01 17:52:06'),
(4, 2, '2026-05-01', 2500.00, 'bank_transfer', 'overdue', NULL, NULL, '2026-06-01 17:52:06'),
(5, 1, '2026-08-01', 2689.99, 'ewallet', 'pending', NULL, NULL, '2026-06-01 17:52:06');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `property_id` int(11) NOT NULL,
  `property_name` varchar(100) NOT NULL,
  `property_type` enum('residential','commercial') NOT NULL,
  `address` text NOT NULL,
  `rental_price` decimal(10,2) NOT NULL,
  `number_of_rooms` int(11) DEFAULT NULL,
  `property_description` text DEFAULT NULL,
  `occupancy_status` enum('available','rented') DEFAULT 'available',
  `property_image` varchar(255) DEFAULT NULL,
  `activation` enum('active','inactive') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`property_id`, `property_name`, `property_type`, `address`, `rental_price`, `number_of_rooms`, `property_description`, `occupancy_status`, `property_image`, `activation`, `created_at`) VALUES
(1, 'Taman Gembira Residential', 'residential', 'No.1, Jalan Pangsapuri, Taman Gembira, 75450 Ayer Keroh, Melaka', 5000.00, 5, 'This is a resindential located at Taman Gembira. It\'s monthly rental is RM1000.00', 'rented', 'assets/img/1780221111_hero_bg_3.jpg', 'active', '2026-05-31 00:18:38'),
(3, 'Residential House for Rent', 'residential', 'No.13, Jalan Pangsapuri, Bukit Beruang Permai Seksyen 1, 75450 Ayer Keroh, Melaka', 3500.00, 4, 'This is a resindential located at Bukit Beruang Permai. It\'s monthly rental is RM3500.00', 'rented', 'assets/img/1780246060_hero_bg_2.jpg', 'active', '2026-05-31 16:47:40'),
(4, 'Commercial Big House', 'commercial', 'Takana Apartment 75350 Mutiara Melaka, Bukit Beruang, Melaka', 1200.00, 2, 'This is a commercial house available for rent. It\'s montly rental is RM1200.00. Cover with WiFi facilities.', 'available', 'assets/img/1780251933_img_7.jpg', 'active', '2026-05-31 18:25:33'),
(5, 'Gold Commercial House ', 'commercial', '2222, Jalan Bbp 1, Taman Bukit Beruang Permai, 75450', 3500.00, 5, 'This is a big and beautiful gold commercial house ready for rent. It\'s montly rental is RM3500.00. Not covering the utility and WiFi.', 'available', 'assets/img/1780252052_img_4.jpg', 'active', '2026-05-31 18:27:32'),
(6, 'Normal Commercial House', 'commercial', 'No.5, Jalan Pangsapuri, Bukit Beruang Permai Seksyen 1, 75450 Ayer Keroh, Melaka', 1000.00, 3, 'This is a commercial house with RM1000.00 rental per month, located at Bukit Beruang, Melaka.', 'available', 'assets/img/1780252134_img_5.jpg', 'active', '2026-05-31 18:28:54');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `pictures` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','tenant') NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `pictures`, `email`, `phone_number`, `password`, `role`, `status`, `created_at`, `is_read`) VALUES
(1, 'System Admin', 'assets/img/1780347826_person_2-min.jpg', 'admin@gmail.com', '0123456789', '$2y$10$IOi2xEbrOwKgWQDGYXs6M.ZKIt6BhaLyui7QPDs.Y2yirN7BVNxKS', 'admin', 'active', '2026-05-29 17:44:11', 1),
(3, 'Ali', 'assets/img/profileimej.jpg', 'ali@gmail.com', '01245852369', '$2y$10$HCj2pKbMBcDa2U6g.utRjeW.YwGu.o3IjYLJY0FSXDwFbs7Tljece', 'tenant', 'active', '2026-05-30 08:17:49', 1),
(4, 'Abu', 'assets/img/profileimej.jpg', 'abu@gmail.com', '01254852369', '$2y$10$8ZlLRNgbyJ.OQrHJNc61EODySk9CtIQC9y6JomtR4Lo8p9q/epOU2', 'tenant', 'active', '2026-05-30 18:39:12', 1),
(5, 'Admin 2', 'assets/img/talha.jpg', 'kokmingming9@gmail.com', '01258453698', '$2y$10$ys0llVZRfW9TLxbx6tMlIeat6bcH0VWqSN6cUiTv4ZL7E1geiZ3ne', 'admin', 'active', '2026-05-31 20:33:56', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `leases`
--
ALTER TABLE `leases`
  ADD PRIMARY KEY (`lease_id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `lease_id` (`lease_id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`property_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `leases`
--
ALTER TABLE `leases`
  MODIFY `lease_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `property_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `leases`
--
ALTER TABLE `leases`
  ADD CONSTRAINT `leases_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leases_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD CONSTRAINT `maintenance_requests_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `maintenance_requests_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`lease_id`) REFERENCES `leases` (`lease_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
