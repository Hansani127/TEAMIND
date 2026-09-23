-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2026 at 11:26 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `teamind`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, NULL, 'login_failed', 'Failed login attempt for email: hansanipremathilaka222@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 19:03:46'),
(2, 2, 'register', 'New user registered', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 19:04:05'),
(3, 2, 'login', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 19:04:14'),
(4, 2, 'create_plantation', 'Created plantation: wilpita', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 19:06:40'),
(5, 2, 'demand_forecast', 'Generated demand forecast for Uva Province - Orthodox - Aug 2025', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 19:07:09'),
(6, 2, 'grade_classification', 'Classified tea as Grade A (PF1) - Broken Orange Pekoe Fanning\'s', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 19:07:38'),
(7, 2, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 19:10:32'),
(8, NULL, 'login_failed', 'Failed login attempt for email: admin@teamind.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 19:13:51'),
(9, NULL, 'login_failed', 'Failed login attempt for email: admin@teamind.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 19:16:00'),
(10, NULL, 'login_failed', 'Failed login attempt for email: admin@teamind.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 19:17:56'),
(11, NULL, 'login_failed', 'Failed login attempt for email: admin@teamind.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 19:18:10'),
(12, NULL, 'login_failed', 'Failed login attempt for email: admin@teamind.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 19:20:06'),
(13, NULL, 'login_failed', 'Failed login attempt for email: admin@teamind.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 19:24:30'),
(14, NULL, 'login_failed', 'Failed login attempt for email: admin@teamind.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 19:24:53'),
(15, 1, 'login', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 19:25:51'),
(16, 1, 'admin_toggle_user', 'Changed user 2 status to inactive', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 19:29:22'),
(17, 1, 'admin_toggle_user', 'Changed user 2 status to active', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 19:29:27'),
(18, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 19:36:46'),
(19, 1, 'login', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 19:38:14'),
(20, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 19:38:55'),
(21, 2, 'login', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-22 19:39:03'),
(22, 2, 'login', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:12:05'),
(23, 2, 'settings_update', 'Updated appearance settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:20:15'),
(24, 2, 'settings_update', 'Updated notification preferences', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:20:25'),
(25, 2, 'settings_update', 'Updated appearance settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:29:12'),
(26, 2, 'settings_update', 'Updated appearance settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:31:26'),
(27, 2, 'settings_update', 'Updated appearance settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:31:43'),
(28, 2, 'settings_update', 'Updated appearance settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:31:52'),
(29, 2, 'settings_update', 'Updated notification preferences', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:34:50'),
(30, 2, 'settings_update', 'Updated notification preferences', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:35:16'),
(31, 2, 'settings_update', 'Updated appearance settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:36:37'),
(32, 2, 'settings_update', 'Updated notification preferences', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:36:52'),
(33, 2, 'settings_update', 'Updated notification preferences', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:37:13'),
(34, 2, 'settings_update', 'Updated notification preferences', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:37:41'),
(35, 2, 'settings_update', 'Updated notification preferences', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:37:50'),
(36, 2, 'settings_update', 'Updated appearance settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:37:53'),
(37, 2, 'settings_update', 'Updated appearance settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:38:51'),
(38, 2, 'settings_update', 'Updated appearance settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:42:58'),
(39, 2, 'settings_update', 'Updated appearance settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:43:07'),
(40, 2, 'settings_update', 'Updated notification preferences', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:43:15'),
(41, 2, 'settings_update', 'Updated notification preferences', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:43:20'),
(42, 2, 'settings_update', 'Updated appearance settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:43:45'),
(43, 2, 'settings_update', 'Updated appearance settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:50:11'),
(44, 2, 'settings_update', 'Updated appearance settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:50:15'),
(45, 2, 'settings_update', 'Updated appearance settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:50:19'),
(46, 2, 'settings_update', 'Updated appearance settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:50:34'),
(47, 2, 'settings_update', 'Updated appearance settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:51:42'),
(48, 2, 'settings_update', 'Updated appearance settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:51:45'),
(49, 2, 'settings_update', 'Updated appearance settings (dark_mode=1)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:57:49'),
(50, 2, 'settings_update', 'Updated appearance settings (dark_mode=0)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:58:40'),
(51, 2, 'settings_update', 'Updated notification preferences', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 18:58:44'),
(52, 2, 'settings_update', 'Updated appearance settings (dark_mode=1)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:02:38'),
(53, 2, 'settings_update', 'Updated appearance settings (dark_mode=0)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:06:52'),
(54, 2, 'settings_update', 'Updated notification preferences', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:07:32'),
(55, 2, 'settings_update', 'Updated notification preferences', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:07:50'),
(56, 2, 'settings_update', 'Updated profile information', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:15:51'),
(57, 2, 'settings_update', 'Updated application preferences', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:18:14'),
(58, 2, 'settings_update', 'Updated security settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:18:24'),
(59, 2, 'settings_update', 'Updated security settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:22:08'),
(60, 2, 'settings', 'Changed password', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:26:13'),
(61, 2, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:26:19'),
(62, 2, 'login', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:26:26'),
(63, 2, 'demand_forecast', 'Generated demand forecast for Uva Province - Orthodox - Sep 2025', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:28:22'),
(64, 2, 'settings', 'Enabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:29:40'),
(65, 2, 'settings', 'Disabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:29:42'),
(66, 2, 'settings', 'Enabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:29:55'),
(67, 2, 'settings', 'Disabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:30:28'),
(68, 2, 'settings', 'Enabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:31:57'),
(69, 2, 'settings', 'Disabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:31:59'),
(70, 2, 'update_profile', 'User updated profile information', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:36:38'),
(71, 2, 'update_profile', 'Updated profile', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:38:11'),
(72, 2, 'settings', 'Enabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:38:19'),
(73, 2, 'settings', 'Disabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:38:23'),
(74, 2, 'settings', 'Disabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:48:36'),
(75, 2, 'settings', 'Enabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:48:40'),
(76, 2, 'settings', 'Disabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:48:42'),
(77, 2, 'update_profile', 'Updated profile', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:48:58'),
(78, 2, 'settings', 'Enabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:50:06'),
(79, 2, 'settings', 'Enabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:52:21'),
(80, 2, 'settings', 'Disabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:52:23'),
(81, 2, 'settings', 'Enabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 19:58:45'),
(82, 2, 'settings', 'Disabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 20:04:39'),
(83, 2, 'settings', 'Enabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 20:20:56'),
(84, 2, 'settings', 'Disabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 20:20:57'),
(85, 2, 'settings', 'Updated language/timezone settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 20:21:03'),
(86, 2, 'settings', 'Updated notification preferences', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 20:21:13'),
(87, 2, 'settings', 'Updated notification preferences', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 20:21:20'),
(88, 2, 'demand_forecast', 'Generated demand forecast for Central Province - White Tea - Jun 2025', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 20:25:53'),
(89, 2, 'grade_classification', 'Classified tea as Grade D (Dust 1) - Broken Orange Pekoe Fanning\'s', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 20:26:22'),
(90, 2, 'password_change', 'Password changed successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 20:27:34'),
(91, 2, 'settings', 'Enabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 20:32:28'),
(92, 2, 'settings', 'Disabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 20:32:29'),
(93, 2, 'password_change', 'Password changed successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 20:32:46'),
(94, 2, 'settings', 'Enabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 20:33:09'),
(95, 2, 'settings', 'Disabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 20:33:33'),
(96, 2, 'settings', 'Enabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 20:45:32'),
(97, 2, 'settings', 'Disabled dark mode', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 20:48:00'),
(98, 2, 'report_generated', 'Generated disease report: july', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 21:00:14'),
(99, 2, 'report_generated', 'Generated overall report: july', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 21:12:14');

-- --------------------------------------------------------

--
-- Table structure for table `dark_mode`
--

CREATE TABLE `dark_mode` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `dark_mode` tinyint(1) DEFAULT 0 COMMENT '0 = light, 1 = dark',
  `email_notifications` tinyint(1) DEFAULT 1,
  `push_notifications` tinyint(1) DEFAULT 1,
  `language` varchar(10) DEFAULT 'en',
  `timezone` varchar(50) DEFAULT 'Asia/Colombo',
  `dashboard_layout` varchar(20) DEFAULT 'default',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dark_mode`
--

INSERT INTO `dark_mode` (`id`, `user_id`, `dark_mode`, `email_notifications`, `push_notifications`, `language`, `timezone`, `dashboard_layout`, `created_at`, `updated_at`) VALUES
(1, 1, 0, 1, 1, 'en', 'Asia/Colombo', 'default', '2026-07-23 20:14:04', '2026-07-23 20:14:04'),
(2, 2, 0, 0, 0, 'si', 'Asia/Colombo', 'default', '2026-07-23 20:14:04', '2026-07-23 20:48:00');

-- --------------------------------------------------------

--
-- Table structure for table `demand_forecasts`
--

CREATE TABLE `demand_forecasts` (
  `id` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `region` varchar(50) DEFAULT 'All Regions',
  `tea_type` varchar(50) DEFAULT 'All Types',
  `historical_demand` decimal(10,2) DEFAULT NULL COMMENT 'Historical demand in MT',
  `forecasted_demand` decimal(10,2) NOT NULL COMMENT 'Forecasted demand in MT',
  `growth_rate` decimal(5,2) DEFAULT NULL COMMENT 'Growth rate percentage',
  `revenue_forecast` decimal(12,2) DEFAULT NULL COMMENT 'Revenue forecast in USD',
  `confidence_score` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `demand_forecasts`
--

INSERT INTO `demand_forecasts` (`id`, `month`, `year`, `region`, `tea_type`, `historical_demand`, `forecasted_demand`, `growth_rate`, `revenue_forecast`, `confidence_score`, `created_at`) VALUES
(1, 1, 2025, 'All Regions', 'All Types', 1450.00, 1620.00, 11.70, 4200000.00, 92.00, '2026-07-22 18:49:00'),
(2, 2, 2025, 'All Regions', 'All Types', 1480.00, 1650.00, 11.50, 4300000.00, 91.00, '2026-07-22 18:49:00'),
(3, 3, 2025, 'All Regions', 'All Types', 1520.00, 1700.00, 11.80, 4450000.00, 93.00, '2026-07-22 18:49:00'),
(4, 4, 2025, 'All Regions', 'All Types', 1580.00, 1750.00, 10.80, 4600000.00, 90.00, '2026-07-22 18:49:00'),
(5, 5, 2025, 'All Regions', 'All Types', 1620.00, 1910.00, 17.90, 4820000.00, 94.00, '2026-07-22 18:49:00'),
(6, 6, 2025, 'All Regions', 'All Types', 1550.00, 1800.00, 16.10, 4700000.00, 89.00, '2026-07-22 18:49:00'),
(7, 7, 2025, 'All Regions', 'All Types', 1500.00, 1720.00, 14.70, 4500000.00, 88.00, '2026-07-22 18:49:00'),
(8, 8, 2025, 'Uva Province', 'Orthodox', 25191.00, 25191.00, 14.00, 62977500.00, 93.00, '2026-07-22 19:07:09'),
(9, 9, 2025, 'Uva Province', 'Orthodox', 26846.00, 26846.00, 16.00, 67115000.00, 92.00, '2026-07-23 19:28:22'),
(10, 6, 2025, 'Central Province', 'White Tea', 20022.00, 20022.00, 14.00, 50055000.00, 97.00, '2026-07-23 20:25:53');

-- --------------------------------------------------------

--
-- Table structure for table `disease_predictions`
--

CREATE TABLE `disease_predictions` (
  `prediction_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plantation_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `disease_name` varchar(100) NOT NULL,
  `confidence` decimal(5,2) NOT NULL,
  `severity` enum('Low','Moderate','High','Critical') DEFAULT 'Low',
  `recommendation` text DEFAULT NULL,
  `affected_area_percentage` decimal(5,2) DEFAULT NULL,
  `prediction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `disease_predictions`
--

INSERT INTO `disease_predictions` (`prediction_id`, `user_id`, `plantation_id`, `image_path`, `disease_name`, `confidence`, `severity`, `recommendation`, `affected_area_percentage`, `prediction_date`) VALUES
(1, 2, NULL, 'uploads/disease/d616d29d6871576f.jpg', 'Blight', 96.00, 'Moderate', 'Remove infected leaves immediately; Improve field drainage; Apply copper-based fungicide; Regular monitoring', NULL, '2026-07-22 19:05:58'),
(2, 2, NULL, 'uploads/disease/0be8632e8ba818c6.jpg', 'Healthy', 98.00, 'Low', 'Remove infected leaves immediately; Improve field drainage; Apply copper-based fungicide; Regular monitoring', NULL, '2026-07-23 20:24:53');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','warning','success','danger') DEFAULT 'info',
  `status` enum('read','unread') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `status`, `created_at`) VALUES
(1, 2, 'Welcome to TEAMIND!', 'Your account has been created successfully. Start exploring the AI-powered features.', 'success', 'read', '2026-07-22 19:04:05'),
(2, 2, 'Disease Detection Complete', 'Detected Blight with 96% confidence.', 'success', 'read', '2026-07-22 19:05:58'),
(3, 2, 'Yield Prediction Complete', 'Predicted yield: 471 kg with 91% confidence.', 'success', 'read', '2026-07-22 19:06:55'),
(4, 2, 'Demand Forecast Generated', 'Forecast for Orthodox in Uva Province (Aug 2025): 25,191 MT with 93% confidence.', 'success', 'read', '2026-07-22 19:07:09'),
(5, 2, 'Grade Classification Complete', 'Tea graded as Grade A (PF1) (Broken Orange Pekoe Fanning\'s) - Good Quality', 'success', 'read', '2026-07-22 19:07:38'),
(6, 2, 'Yield Prediction Complete', 'Predicted yield: 449 kg with 97% confidence.', 'success', 'read', '2026-07-23 18:18:21'),
(7, 2, 'Yield Prediction Complete', 'Predicted yield: 565 kg with 88% confidence.', 'success', 'read', '2026-07-23 19:28:03'),
(8, 2, 'Demand Forecast Generated', 'Forecast for Orthodox in Uva Province (Sep 2025): 26,846 MT with 92% confidence.', 'success', 'read', '2026-07-23 19:28:22'),
(9, 2, 'Yield Prediction Complete', 'Predicted yield: 543 kg with 90% confidence.', 'success', 'read', '2026-07-23 20:23:07'),
(10, 2, 'Disease Detection Complete', 'Detected Healthy with 98% confidence.', 'success', 'read', '2026-07-23 20:24:53'),
(11, 2, 'Yield Prediction Complete', 'Predicted yield: 405 kg with 86% confidence.', 'success', 'read', '2026-07-23 20:25:32'),
(12, 2, 'Demand Forecast Generated', 'Forecast for White Tea in Central Province (Jun 2025): 20,022 MT with 97% confidence.', 'success', 'read', '2026-07-23 20:25:53'),
(13, 2, 'Grade Classification Complete', 'Tea graded as Grade D (Dust 1) (Broken Orange Pekoe Fanning\'s) - Good Quality', 'success', 'read', '2026-07-23 20:26:22'),
(14, 2, 'Yield Prediction Complete', 'Predicted yield: 511 kg with 90% confidence.', 'success', 'read', '2026-07-23 20:41:09'),
(15, 2, 'Yield Prediction Complete', 'Predicted yield: 325 kg with 92% confidence.', 'success', 'read', '2026-07-23 20:59:16');

-- --------------------------------------------------------

--
-- Table structure for table `password_history`
--

CREATE TABLE `password_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `old_password_hash` varchar(255) NOT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `changed_by` varchar(100) DEFAULT 'user',
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_history`
--

INSERT INTO `password_history` (`id`, `user_id`, `old_password_hash`, `changed_at`, `changed_by`, `ip_address`) VALUES
(1, 2, '$2y$12$9t1CFp8ThWgueLV7.L5RmupQMn1Byr8y.hpp/P/GdA/zZWV6.PicG', '2026-07-23 20:27:33', 'user', '::1'),
(2, 2, '$2y$12$GT6qg73fJumWwxQbiYUDsu9cxIO61obFzzLRzsEcfkqfUxfAj..0G', '2026-07-23 20:32:46', 'user', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `plantations`
--

CREATE TABLE `plantations` (
  `plantation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plantation_name` varchar(100) NOT NULL,
  `district` varchar(50) NOT NULL,
  `estate` varchar(100) DEFAULT NULL,
  `tea_type` enum('CTC','Orthodox','Green Tea','White Tea','Oolong') DEFAULT 'CTC',
  `area` decimal(10,2) DEFAULT NULL COMMENT 'Area in acres',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `soil_type` varchar(50) DEFAULT NULL,
  `elevation` int(11) DEFAULT NULL COMMENT 'Elevation in meters',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plantations`
--

INSERT INTO `plantations` (`plantation_id`, `user_id`, `plantation_name`, `district`, `estate`, `tea_type`, `area`, `latitude`, `longitude`, `soil_type`, `elevation`, `created_at`, `updated_at`) VALUES
(1, 2, 'wilpita', 'matara', 'wilpita', 'Green Tea', 0.01, 0.00000001, 0.00000001, 'loamy', 1, '2026-07-22 19:06:40', '2026-07-22 19:06:40');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `data_type` enum('string','integer','boolean','json','float') DEFAULT 'string',
  `is_editable` tinyint(1) DEFAULT 1,
  `is_public` tinyint(1) DEFAULT 0,
  `setting_group` varchar(50) DEFAULT 'general',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `description`, `data_type`, `is_editable`, `is_public`, `setting_group`, `updated_at`) VALUES
(1, 'site_name', 'TEAMIND', 'Application display name', 'string', 1, 1, 'general', '2026-07-23 20:14:04'),
(2, 'site_logo', 'logo.png', 'Site logo filename', 'string', 1, 0, 'general', '2026-07-23 20:14:04'),
(3, 'default_language', 'en', 'Default application language', 'string', 1, 1, 'general', '2026-07-23 20:14:04'),
(4, 'timezone', 'Asia/Colombo', 'Default timezone', 'string', 1, 1, 'general', '2026-07-23 20:14:04'),
(5, 'disease_model_path', 'python/disease_model.h5', 'Path to disease detection model', 'string', 0, 0, 'ai', '2026-07-23 20:14:04'),
(6, 'yield_model_path', 'python/yield_model.pkl', 'Path to yield prediction model', 'string', 0, 0, 'ai', '2026-07-23 20:14:04'),
(7, 'demand_model_path', 'python/demand_model.pkl', 'Path to demand forecasting model', 'string', 0, 0, 'ai', '2026-07-23 20:14:04'),
(8, 'grade_model_path', 'python/grade_model.h5', 'Path to tea grade classification model', 'string', 0, 0, 'ai', '2026-07-23 20:14:04'),
(9, 'flask_api_url', 'http://localhost:5000', 'Flask API endpoint URL', 'string', 1, 0, 'ai', '2026-07-23 20:14:04'),
(10, 'session_timeout', '3600', 'Session timeout in seconds', 'integer', 1, 0, 'security', '2026-07-23 20:14:04'),
(11, 'max_upload_size', '10485760', 'Maximum file upload size in bytes', 'integer', 1, 0, 'upload', '2026-07-23 20:14:04'),
(12, 'allowed_image_types', 'jpg,jpeg,png', 'Comma-separated allowed image extensions', 'string', 1, 0, 'upload', '2026-07-23 20:14:04'),
(13, 'site_tagline', 'AI Powered Tea Management', 'Site tagline shown in header', 'string', 1, 1, 'general', '2026-07-23 20:14:04'),
(14, 'maintenance_mode', '0', 'Enable/disable maintenance mode', 'boolean', 1, 0, 'general', '2026-07-23 20:14:04'),
(15, 'registration_enabled', '1', 'Allow new user registrations', 'boolean', 1, 0, 'general', '2026-07-23 20:14:04'),
(16, 'default_theme', 'light', 'Default theme for new users', 'string', 1, 1, 'appearance', '2026-07-23 20:14:04'),
(17, 'primary_color', '#1a5c2e', 'Primary brand color', 'string', 1, 1, 'appearance', '2026-07-23 20:14:04'),
(18, 'accent_color', '#d4a843', 'Accent/secondary color', 'string', 1, 1, 'appearance', '2026-07-23 20:14:04'),
(19, 'font_family', 'Inter', 'Default font family', 'string', 1, 1, 'appearance', '2026-07-23 20:14:04'),
(20, 'sidebar_collapsed', '0', 'Default sidebar state', 'boolean', 1, 1, 'appearance', '2026-07-23 20:14:04'),
(21, 'model_confidence_threshold', '0.75', 'Minimum confidence threshold for predictions', 'float', 1, 0, 'ai', '2026-07-23 20:14:04'),
(22, 'enable_ai_predictions', '1', 'Enable/disable AI prediction features', 'boolean', 1, 0, 'ai', '2026-07-23 20:14:04'),
(23, 'max_login_attempts', '5', 'Maximum failed login attempts before lockout', 'integer', 1, 0, 'security', '2026-07-23 20:14:04'),
(24, 'lockout_duration', '900', 'Account lockout duration in seconds', 'integer', 1, 0, 'security', '2026-07-23 20:14:04'),
(25, 'password_min_length', '6', 'Minimum password length', 'integer', 1, 0, 'security', '2026-07-23 20:14:04'),
(26, 'password_require_uppercase', '0', 'Require uppercase letters in password', 'boolean', 1, 0, 'security', '2026-07-23 20:14:04'),
(27, 'password_require_number', '0', 'Require numbers in password', 'boolean', 1, 0, 'security', '2026-07-23 20:14:04'),
(28, 'password_require_special', '0', 'Require special characters in password', 'boolean', 1, 0, 'security', '2026-07-23 20:14:04'),
(29, 'password_expiry_days', '90', 'Force password change after N days (0 = disabled)', 'integer', 1, 0, 'security', '2026-07-23 20:14:04'),
(30, 'password_history_count', '5', 'Prevent reusing last N passwords', 'integer', 1, 0, 'security', '2026-07-23 20:14:04'),
(31, 'two_factor_auth', '0', 'Enable two-factor authentication', 'boolean', 1, 0, 'security', '2026-07-23 20:14:04'),
(32, 'image_max_width', '2048', 'Maximum image width in pixels', 'integer', 1, 0, 'upload', '2026-07-23 20:14:04'),
(33, 'image_max_height', '2048', 'Maximum image height in pixels', 'integer', 1, 0, 'upload', '2026-07-23 20:14:04'),
(34, 'image_quality', '85', 'JPEG compression quality (1-100)', 'integer', 1, 0, 'upload', '2026-07-23 20:14:04'),
(35, 'email_from_address', 'noreply@teamind.com', 'Default sender email address', 'string', 1, 0, 'notification', '2026-07-23 20:14:04'),
(36, 'email_from_name', 'TEAMIND System', 'Default sender name', 'string', 1, 0, 'notification', '2026-07-23 20:14:04'),
(37, 'enable_email_notifications', '1', 'Enable email notifications globally', 'boolean', 1, 0, 'notification', '2026-07-23 20:14:04'),
(38, 'enable_push_notifications', '1', 'Enable push notifications globally', 'boolean', 1, 0, 'notification', '2026-07-23 20:14:04'),
(39, 'notification_retention_days', '30', 'Days to keep notification history', 'integer', 1, 0, 'notification', '2026-07-23 20:14:04'),
(40, 'enable_disease_detection', '1', 'Enable disease detection feature', 'boolean', 1, 0, 'features', '2026-07-23 20:14:04'),
(41, 'enable_yield_prediction', '1', 'Enable yield prediction feature', 'boolean', 1, 0, 'features', '2026-07-23 20:14:04'),
(42, 'enable_demand_forecast', '1', 'Enable demand forecasting feature', 'boolean', 1, 0, 'features', '2026-07-23 20:14:04'),
(43, 'enable_grade_classification', '1', 'Enable tea grade classification feature', 'boolean', 1, 0, 'features', '2026-07-23 20:14:04'),
(44, 'enable_reports', '1', 'Enable report generation feature', 'boolean', 1, 0, 'features', '2026-07-23 20:14:04');

-- --------------------------------------------------------

--
-- Table structure for table `tea_grade_classifications`
--

CREATE TABLE `tea_grade_classifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `grade` varchar(10) NOT NULL COMMENT 'A+, A, B, C, etc.',
  `grade_label` varchar(50) DEFAULT NULL COMMENT 'Premium Quality, Good Quality, etc.',
  `appearance_score` decimal(5,2) DEFAULT NULL,
  `aroma_score` decimal(5,2) DEFAULT NULL,
  `liquor_color_score` decimal(5,2) DEFAULT NULL,
  `taste_score` decimal(5,2) DEFAULT NULL,
  `leaf_texture_score` decimal(5,2) DEFAULT NULL,
  `overall_score` decimal(5,2) NOT NULL,
  `moisture_content` decimal(5,2) DEFAULT NULL,
  `particle_size` varchar(20) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `aroma` varchar(50) DEFAULT NULL,
  `taste` varchar(50) DEFAULT NULL,
  `classification_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tea_grade_classifications`
--

INSERT INTO `tea_grade_classifications` (`id`, `user_id`, `image_path`, `grade`, `grade_label`, `appearance_score`, `aroma_score`, `liquor_color_score`, `taste_score`, `leaf_texture_score`, `overall_score`, `moisture_content`, `particle_size`, `color`, `aroma`, `taste`, `classification_date`) VALUES
(1, 2, 'uploads/tea/69b2ae2ef3045659.png', 'Grade A (P', 'Good Quality', 77.00, 75.00, 80.00, 78.00, 72.00, 76.00, 6.00, 'Medium', 'Black', 'Moderate', 'Good', '2026-07-22 19:07:38'),
(2, 2, 'uploads/tea/d8a106b05b7acf54.png', 'Grade D (D', 'Good Quality', 75.00, 69.00, 81.00, 76.00, 72.00, 75.00, 6.00, 'Medium', 'Black', 'Mild', 'Good', '2026-07-23 20:26:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('farmer','admin') DEFAULT 'farmer',
  `profile_image` varchar(255) DEFAULT 'default-avatar.png',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `dark_mode` tinyint(1) DEFAULT 0,
  `language` varchar(10) DEFAULT 'en'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `phone`, `password`, `role`, `profile_image`, `status`, `created_at`, `updated_at`, `last_login`, `dark_mode`, `language`) VALUES
(1, 'System Administrator', 'admin@teamind.com', '+94 77 123 4567', '$2y$10$da91JXPhVD64sAKU3loVr.5U92uBwV6Gc8ql.QnLcidUag2oONbTS', 'admin', 'default-avatar.png', 'active', '2026-07-22 18:49:00', '2026-07-22 19:38:14', '2026-07-22 19:38:14', 0, 'en'),
(2, 'Hansani Premathilaka', 'hansanipremathilaka222@gmail.com', '0760196576', '$2y$12$eggIXgMsnHmRcopldqQ/TukSvalfZdtdlCfHIu07wRo82EqPGgnJa', 'farmer', 'uploads/profile/9418b267dbabda83.jpeg', 'active', '2026-07-22 19:04:05', '2026-07-23 20:32:46', '2026-07-23 19:26:26', 1, 'en');

-- --------------------------------------------------------

--
-- Table structure for table `yield_predictions`
--

CREATE TABLE `yield_predictions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plantation_id` int(11) DEFAULT NULL,
  `temperature` decimal(5,2) NOT NULL COMMENT 'Temperature in Celsius',
  `humidity` decimal(5,2) NOT NULL COMMENT 'Humidity percentage',
  `rainfall` decimal(8,2) NOT NULL COMMENT 'Rainfall in mm',
  `sunlight` decimal(5,2) NOT NULL COMMENT 'Sunlight hours per day',
  `wind_speed` decimal(5,2) DEFAULT 0.00 COMMENT 'Wind speed in km/h',
  `soil_ph` decimal(4,2) DEFAULT 6.50,
  `predicted_yield` decimal(10,2) NOT NULL COMMENT 'Predicted yield in kg',
  `confidence` decimal(5,2) NOT NULL,
  `yield_per_acre` decimal(10,2) DEFAULT NULL,
  `prediction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `yield_predictions`
--

INSERT INTO `yield_predictions` (`id`, `user_id`, `plantation_id`, `temperature`, `humidity`, `rainfall`, `sunlight`, `wind_speed`, `soil_ph`, `predicted_yield`, `confidence`, `yield_per_acre`, `prediction_date`) VALUES
(1, 2, 1, 10.00, 21.00, 384.00, 6.00, 12.00, 5.20, 471.28, 91.00, 94.26, '2026-07-22 19:06:55'),
(2, 2, NULL, 7.00, 23.00, 232.00, 6.00, 12.00, 5.30, 449.44, 97.00, 89.89, '2026-07-23 18:18:21'),
(3, 2, NULL, 15.00, 75.00, 312.00, 6.00, 12.00, 5.20, 565.17, 88.00, 113.03, '2026-07-23 19:28:03'),
(4, 2, NULL, 11.00, 75.00, 265.00, 6.00, 12.00, 6.50, 543.22, 90.00, 108.64, '2026-07-23 20:23:07'),
(5, 2, 1, 8.00, 22.00, 296.00, 3.00, 25.00, 4.90, 404.69, 86.00, 80.94, '2026-07-23 20:25:32'),
(6, 2, NULL, 12.00, 22.00, 300.00, 8.00, 68.00, 7.90, 511.13, 90.00, 102.23, '2026-07-23 20:41:09'),
(7, 2, NULL, 5.00, 12.00, 63.00, 1.00, 15.00, 4.70, 325.38, 92.00, 65.08, '2026-07-23 20:59:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activity_user` (`user_id`);

--
-- Indexes for table `dark_mode`
--
ALTER TABLE `dark_mode`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_user_settings_user` (`user_id`);

--
-- Indexes for table `demand_forecasts`
--
ALTER TABLE `demand_forecasts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_forecast` (`month`,`year`,`region`,`tea_type`);

--
-- Indexes for table `disease_predictions`
--
ALTER TABLE `disease_predictions`
  ADD PRIMARY KEY (`prediction_id`),
  ADD KEY `plantation_id` (`plantation_id`),
  ADD KEY `idx_disease_user` (`user_id`),
  ADD KEY `idx_disease_date` (`prediction_date`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notifications_user` (`user_id`,`status`);

--
-- Indexes for table `password_history`
--
ALTER TABLE `password_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_password_history_user` (`user_id`);

--
-- Indexes for table `plantations`
--
ALTER TABLE `plantations`
  ADD PRIMARY KEY (`plantation_id`),
  ADD KEY `idx_plantation_user` (`user_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_system_settings_group` (`setting_group`),
  ADD KEY `idx_system_settings_editable` (`is_editable`);

--
-- Indexes for table `tea_grade_classifications`
--
ALTER TABLE `tea_grade_classifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_grade_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `yield_predictions`
--
ALTER TABLE `yield_predictions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plantation_id` (`plantation_id`),
  ADD KEY `idx_yield_user` (`user_id`),
  ADD KEY `idx_yield_date` (`prediction_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `dark_mode`
--
ALTER TABLE `dark_mode`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `demand_forecasts`
--
ALTER TABLE `demand_forecasts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `disease_predictions`
--
ALTER TABLE `disease_predictions`
  MODIFY `prediction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `password_history`
--
ALTER TABLE `password_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `plantations`
--
ALTER TABLE `plantations`
  MODIFY `plantation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `tea_grade_classifications`
--
ALTER TABLE `tea_grade_classifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `yield_predictions`
--
ALTER TABLE `yield_predictions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `dark_mode`
--
ALTER TABLE `dark_mode`
  ADD CONSTRAINT `dark_mode_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `disease_predictions`
--
ALTER TABLE `disease_predictions`
  ADD CONSTRAINT `disease_predictions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `disease_predictions_ibfk_2` FOREIGN KEY (`plantation_id`) REFERENCES `plantations` (`plantation_id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_history`
--
ALTER TABLE `password_history`
  ADD CONSTRAINT `password_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `plantations`
--
ALTER TABLE `plantations`
  ADD CONSTRAINT `plantations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tea_grade_classifications`
--
ALTER TABLE `tea_grade_classifications`
  ADD CONSTRAINT `tea_grade_classifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `yield_predictions`
--
ALTER TABLE `yield_predictions`
  ADD CONSTRAINT `yield_predictions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `yield_predictions_ibfk_2` FOREIGN KEY (`plantation_id`) REFERENCES `plantations` (`plantation_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

