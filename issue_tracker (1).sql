-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 13, 2025 at 11:04 AM
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
-- Database: `issue_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `department` varchar(150) NOT NULL,
  `designation` varchar(150) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `dob` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `department`, `designation`, `mobile`, `dob`, `created_at`) VALUES
(2, 'KATHIRESAN B', '$2y$10$t0MGQqXyNRTPC55PWDWQ/OBxGT0QfYDyaQo34hzstdOEgLCEWPwSa', 'TNRD', 'Assistant Programmer', '9600413240', '1996-09-25', '2025-08-21 11:06:23');

-- --------------------------------------------------------

--
-- Table structure for table `issues`
--

CREATE TABLE `issues` (
  `id` int(11) NOT NULL,
  `token` varchar(20) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Pending',
  `solved_image` varchar(255) DEFAULT NULL,
  `solved_url` varchar(255) DEFAULT NULL,
  `solved_at` datetime DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `issues`
--

INSERT INTO `issues` (`id`, `token`, `title`, `description`, `image`, `url`, `district`, `created_at`, `status`, `solved_image`, `solved_url`, `solved_at`, `submitted_at`) VALUES
(4, 'ISSUE-688B1C356EC27', 'test', 'erroe', '1753947189_WhatsApp_Image_2025_07_14_at_2.47.21_PM.jpeg', 'https://ipms.tnrd.tn.gov.in/project/home.php', 'Ariyalur', '2025-07-31 07:33:09', 'Solved', '1755586408_salem AEE.jpeg', 'https://ipms.tnrd.tn.gov.in/project/forms/master/ActivityNameEntry.php', '2025-08-19 08:53:28', '2025-08-19 06:48:34'),
(5, 'ISSUE-688B1C45989C1', 'test', 'erroe', '1753947205_SIUS_BID_1.jpg', 'https://ipms.tnrd.tn.gov.in/project/home.php', 'Kanchipuram', '2025-07-31 07:33:25', 'Pending', NULL, NULL, NULL, '2025-08-19 06:48:34'),
(6, 'ISSUE-688B1C50EE4BC', 'test', 'erroe', '1753947216_SIUS_BID_1__1_.jpg', 'https://ipms.tnrd.tn.gov.in/project/home.php', 'Ramanathapuram', '2025-07-31 07:33:36', 'Pending', NULL, NULL, NULL, '2025-08-19 06:48:34'),
(7, 'ISSUE-688B1C8546688', 'sdg', 'erroegdf', '1753947269_SIUS_BID_1__2_.jpg', 'https://ipms.tnrd.tn.gov.in/project/in.php', 'Ramanathapuram', '2025-07-31 07:34:29', 'Solved', '1753947483_SIUS BID 1.jpg', 'https://ipms.tnrd.tn.gov.in/project/forms/master/ActivityNameEntry.php', '2025-07-31 09:38:03', '2025-08-19 06:48:34'),
(8, 'ISSUE-688B42DE2B18F', 'home page', 'the hame page login not working', '1753957086_WhatsApp_Image_2025_07_14_at_2.47.21_PM.jpeg', 'https://ipms.tnrd.tn.gov.in/project/home.php', 'Madurai', '2025-07-31 10:18:06', 'Solved', NULL, 'https://ipms.tnrd.tn.gov.in/project/forms/master/ActivityNameEntry.php', '2025-07-31 12:19:14', '2025-08-19 06:48:34'),
(9, 'ISSUE-688B718C24B99', 'test', 'test', '1753969036_dummy.jpeg', 'https://ipms.tnrd.tn.gov.in/project/home.php', 'Kallakurichi', '2025-07-31 13:37:16', 'Solved', '1753969090_133858335061689570.jpg', 'https://ipms.tnrd.tn.gov.in/project/forms/master/ActivityNameEntry.php', '2025-07-31 15:38:10', '2025-08-19 06:48:34'),
(10, 'ISSUE-68A421FB10B0D', 'dashboard issue', 'same', '1755587067_dd0d0479_f085_4b0b_82bb_673999dd590c.jpeg', 'https://ipms.tnrd.tn.gov.in/project/home.php', 'Tiruvannamalai', '2025-08-19 07:04:27', 'Solved', '1755587137_salem AEE.jpeg', 'https://ipms.tnrd.tn.gov.in/project/forms/master/ActivityNameEntry.php', '2025-08-19 09:05:37', '2025-08-19 07:04:27'),
(11, 'ISSUE-68A4387477AB0', 'vxv', 'xd', '1755592820_WhatsApp_Image_2025_07_01_at_1.12.28_PM.jpeg', 'https://ipms.tnrd.tn.gov.in/project/home.php', 'Tiruvannamalai', '2025-08-19 08:40:20', 'Pending', NULL, NULL, NULL, '2025-08-19 08:40:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `district` varchar(100) NOT NULL,
  `office` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `district`, `office`, `username`, `password`, `created_at`) VALUES
(1, 'Ariyalur', 'DRDA', 'testariyalur', '$2y$10$bAkjit2uh8h57rq0hjvN5uow9elF0YbfaSXaXDBQg0l2aNf45QnNS', '2025-08-19 06:29:30'),
(2, 'Tiruvannamalai', 'DRDA', 'test', '$2y$10$EaTtwgVqdZQk3JPn.OAcRuI9.AvpN.wE6RCf.9dWxrk/NoYNNNhfG', '2025-08-19 07:03:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `issues`
--
ALTER TABLE `issues`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `issues`
--
ALTER TABLE `issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
