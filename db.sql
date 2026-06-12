-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 12, 2026 at 05:17 PM
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
-- Database: `dtr_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(120) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 3, 'login', 'User signed in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-11 04:47:19'),
(2, 3, 'xlsx_export', 'activity-export-20260611-064748.xlsx', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-11 04:47:48'),
(3, 3, 'docx_generate', 'document-20260611-064756.docx', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-11 04:47:56'),
(4, 3, 'logout', 'User signed out.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-11 05:07:17'),
(5, 3, 'login', 'User signed in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-11 05:07:27'),
(6, 3, 'login', 'User signed in.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-12 15:11:53');

-- --------------------------------------------------------

--
-- Table structure for table `batch_jobs`
--

CREATE TABLE `batch_jobs` (
  `id` int(11) NOT NULL,
  `job_name` varchar(100) DEFAULT NULL,
  `job_type` varchar(50) DEFAULT NULL,
  `status` enum('pending','running','completed','failed') DEFAULT 'pending',
  `total_items` int(11) DEFAULT 0,
  `processed_items` int(11) DEFAULT 0,
  `failed_items` int(11) DEFAULT 0,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `error_log` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `batch_jobs`
--

INSERT INTO `batch_jobs` (`id`, `job_name`, `job_type`, `status`, `total_items`, `processed_items`, `failed_items`, `started_at`, `completed_at`, `error_log`, `created_at`) VALUES
(1, 'Daily Import', 'daily', 'completed', 0, 0, 0, NULL, NULL, NULL, '2026-06-06 07:36:54'),
(2, 'Daily Import', 'daily', 'completed', 0, 0, 0, NULL, NULL, NULL, '2026-06-06 07:36:55'),
(3, 'Daily Import', 'daily', 'completed', 0, 0, 0, NULL, NULL, NULL, '2026-06-06 07:36:56'),
(4, 'Daily Import', 'daily', 'completed', 0, 0, 0, NULL, NULL, NULL, '2026-06-06 07:36:56'),
(5, 'Daily Import', 'daily', 'completed', 0, 0, 0, NULL, NULL, NULL, '2026-06-06 07:36:56'),
(6, 'Daily Import', 'daily', 'completed', 0, 0, 0, NULL, NULL, NULL, '2026-06-06 07:36:56'),
(7, 'Daily Import', 'daily', 'completed', 0, 0, 0, NULL, NULL, NULL, '2026-06-06 07:36:56'),
(8, 'Daily Import', 'daily', 'completed', 0, 0, 0, NULL, NULL, NULL, '2026-06-06 07:36:56'),
(9, 'Daily Import', 'daily', 'completed', 0, 0, 0, NULL, NULL, NULL, '2026-06-06 07:36:56'),
(10, 'Daily Import', 'daily', 'completed', 0, 0, 0, NULL, NULL, NULL, '2026-06-06 08:10:21'),
(11, 'Cleanup Old Files', 'cleanup', 'completed', 0, 0, 0, NULL, NULL, NULL, '2026-06-06 08:10:24');

-- --------------------------------------------------------

--
-- Table structure for table `data_imports`
--

CREATE TABLE `data_imports` (
  `id` int(11) NOT NULL,
  `upload_id` int(11) NOT NULL,
  `target_table` varchar(100) DEFAULT NULL,
  `total_records` int(11) DEFAULT NULL,
  `imported_records` int(11) DEFAULT NULL,
  `failed_records` int(11) DEFAULT NULL,
  `import_status` enum('pending','in_progress','completed','failed') DEFAULT 'pending',
  `import_errors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`import_errors`)),
  `imported_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dtr_logs`
--

CREATE TABLE `dtr_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time_in` datetime DEFAULT NULL,
  `time_out` datetime DEFAULT NULL,
  `log_date` date DEFAULT curdate(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_messages`
--

CREATE TABLE `email_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `sender` varchar(255) NOT NULL,
  `received_at` datetime DEFAULT NULL,
  `snippet` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `extracted_data`
--

CREATE TABLE `extracted_data` (
  `id` int(11) NOT NULL,
  `upload_id` int(11) NOT NULL,
  `row_number` int(11) DEFAULT NULL,
  `column_mapping` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`column_mapping`)),
  `raw_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`raw_data`)),
  `validated_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`validated_data`)),
  `validation_errors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`validation_errors`)),
  `is_valid` tinyint(1) DEFAULT 0,
  `imported` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `extracted_data`
--

INSERT INTO `extracted_data` (`id`, `upload_id`, `row_number`, `column_mapping`, `raw_data`, `validated_data`, `validation_errors`, `is_valid`, `imported`, `created_at`) VALUES
(1, 9, 1, NULL, '{\"1\":\"Field\",\"2\":\"Example Entry\"}', '{\"1\":\"Field\",\"2\":\"Example Entry\"}', NULL, 1, 0, '2026-06-06 08:09:50'),
(2, 9, 2, NULL, '{\"1\":\"Full Name (as per official records)\",\"2\":\"Maria Cristina D. Santos\"}', '{\"1\":\"Full Name (as per official records)\",\"2\":\"Maria Cristina D. Santos\"}', NULL, 1, 0, '2026-06-06 08:09:50'),
(3, 9, 3, NULL, '{\"1\":\"Employee ID\",\"2\":\"NX-10245\"}', '{\"1\":\"Employee ID\",\"2\":\"NX-10245\"}', NULL, 1, 0, '2026-06-06 08:09:50'),
(4, 9, 4, NULL, '{\"1\":\"Department\",\"2\":\"Finance & Accounting\"}', '{\"1\":\"Department\",\"2\":\"Finance & Accounting\"}', NULL, 1, 0, '2026-06-06 08:09:50'),
(5, 9, 5, NULL, '{\"1\":\"Job Title\",\"2\":\"Senior Accounts Analyst\"}', '{\"1\":\"Job Title\",\"2\":\"Senior Accounts Analyst\"}', NULL, 1, 0, '2026-06-06 08:09:50'),
(6, 9, 6, NULL, '{\"1\":\"Personal Contact Number\",\"2\":\"+63 912 345 6789\"}', '{\"1\":\"Personal Contact Number\",\"2\":\"+63 912 345 6789\"}', NULL, 1, 0, '2026-06-06 08:09:50'),
(7, 9, 7, NULL, '{\"1\":\"Office Contact Number\",\"2\":\"+63 2 8123 4567 loc. 345\"}', '{\"1\":\"Office Contact Number\",\"2\":\"+63 2 8123 4567 loc. 345\"}', NULL, 1, 0, '2026-06-06 08:09:50'),
(8, 9, 8, NULL, '{\"1\":\"Personal Email Address\",\"2\":\"maria.santos@email.com\"}', '{\"1\":\"Personal Email Address\",\"2\":\"maria.santos@email.com\"}', NULL, 1, 0, '2026-06-06 08:09:50'),
(9, 9, 9, NULL, '{\"1\":\"Date of Birth\",\"2\":\"1992-08-15\"}', '{\"1\":\"Date of Birth\",\"2\":\"1992-08-15\"}', NULL, 1, 0, '2026-06-06 08:09:50'),
(10, 9, 10, NULL, '{\"1\":\"Emergency Contact Name\",\"2\":\"Jose M. Santos\"}', '{\"1\":\"Emergency Contact Name\",\"2\":\"Jose M. Santos\"}', NULL, 1, 0, '2026-06-06 08:09:50'),
(11, 9, 11, NULL, '{\"1\":\"Emergency Contact Relationship\",\"2\":\"Spouse\"}', '{\"1\":\"Emergency Contact Relationship\",\"2\":\"Spouse\"}', NULL, 1, 0, '2026-06-06 08:09:50'),
(12, 9, 12, NULL, '{\"1\":\"Emergency Contact Number\",\"2\":\"+63 917 765 4321\"}', '{\"1\":\"Emergency Contact Number\",\"2\":\"+63 917 765 4321\"}', NULL, 1, 0, '2026-06-06 08:09:50');

-- --------------------------------------------------------

--
-- Table structure for table `file_uploads`
--

CREATE TABLE `file_uploads` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` enum('excel','word') NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `record_count` int(11) DEFAULT 0,
  `status` enum('pending','processing','completed','error') DEFAULT 'pending',
  `processing_type` varchar(50) DEFAULT NULL,
  `target_table` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `file_uploads`
--

INSERT INTO `file_uploads` (`id`, `filename`, `file_path`, `file_type`, `file_size`, `record_count`, `status`, `processing_type`, `target_table`, `notes`, `error_message`, `uploaded_at`, `processed_at`, `created_by`) VALUES
(1, '1780731724_exasd.docx', 'C:xampphtdocsDTRautomationapi/../uploads/1780731724_exasd.docx', 'word', 16985, 0, 'error', 'extract', 'employees', 'asda', 'PHP Zip extension is required to read .docx files. Enable zip in php.ini and restart Apache.', '2026-06-06 07:42:04', NULL, NULL),
(2, '1780731725_exasd.docx', 'C:xampphtdocsDTRautomationapi/../uploads/1780731725_exasd.docx', 'word', 16985, 0, 'error', 'extract', 'employees', 'asda', 'PHP Zip extension is required to read .docx files. Enable zip in php.ini and restart Apache.', '2026-06-06 07:42:05', NULL, NULL),
(3, '1780731788_exasd.docx', 'C:xampphtdocsDTRautomationapi/../uploads/1780731788_exasd.docx', 'word', 16985, 0, 'error', 'extract', 'employees', 'asda', 'PHP Zip extension is required to read .docx files. Enable zip in php.ini and restart Apache.', '2026-06-06 07:43:08', NULL, NULL),
(4, '1780731789_exasd.docx', 'C:xampphtdocsDTRautomationapi/../uploads/1780731789_exasd.docx', 'word', 16985, 0, 'error', 'extract', 'employees', 'asda', 'PHP Zip extension is required to read .docx files. Enable zip in php.ini and restart Apache.', '2026-06-06 07:43:09', NULL, NULL),
(5, '1780731789_exasd.docx', 'C:xampphtdocsDTRautomationapi/../uploads/1780731789_exasd.docx', 'word', 16985, 0, 'error', 'extract', 'employees', 'asda', 'PHP Zip extension is required to read .docx files. Enable zip in php.ini and restart Apache.', '2026-06-06 07:43:09', NULL, NULL),
(6, '1780731789_exasd.docx', 'C:xampphtdocsDTRautomationapi/../uploads/1780731789_exasd.docx', 'word', 16985, 0, 'error', 'extract', 'employees', 'asda', 'PHP Zip extension is required to read .docx files. Enable zip in php.ini and restart Apache.', '2026-06-06 07:43:09', NULL, NULL),
(7, '1780731790_exasd.docx', 'C:xampphtdocsDTRautomationapi/../uploads/1780731790_exasd.docx', 'word', 16985, 0, 'error', 'extract', 'employees', 'asda', 'PHP Zip extension is required to read .docx files. Enable zip in php.ini and restart Apache.', '2026-06-06 07:43:10', NULL, NULL),
(8, '1780731800_exasd.docx', 'C:xampphtdocsDTRautomationapi/../uploads/1780731800_exasd.docx', 'word', 16985, 0, 'error', 'extract', 'employees', 'asd', 'PHP Zip extension is required to read .docx files. Enable zip in php.ini and restart Apache.', '2026-06-06 07:43:20', NULL, NULL),
(9, '1780733390_exasd.docx', 'C:xampphtdocsDTRautomationapi/../uploads/1780733390_exasd.docx', 'word', 16985, 12, 'completed', 'extract', 'employees', '', NULL, '2026-06-06 08:09:50', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `generated_files`
--

CREATE TABLE `generated_files` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('xlsx','docx') NOT NULL,
  `title` varchar(180) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `generated_files`
--

INSERT INTO `generated_files` (`id`, `user_id`, `type`, `title`, `file_path`, `created_at`) VALUES
(1, 3, 'xlsx', 'Activity Export', 'storage/exports/activity-export-20260611-064748.xlsx', '2026-06-11 04:47:48'),
(2, 3, 'docx', 'Automation Report', 'storage/documents/document-20260611-064756.docx', '2026-06-11 04:47:56');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(11) NOT NULL,
  `migration_name` varchar(150) NOT NULL,
  `batch` int(11) NOT NULL DEFAULT 1,
  `executed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration_name`, `batch`, `executed_at`) VALUES
(1, '0001_create_migrations_table', 1, '2026-06-05 22:37:19'),
(2, '0002_create_users_table', 1, '2026-06-05 22:37:19'),
(3, '0003_create_file_uploads_table', 1, '2026-06-05 22:37:19'),
(4, '0004_create_extracted_data_table', 1, '2026-06-05 22:37:19'),
(5, '0005_create_data_imports_table', 1, '2026-06-05 22:37:19'),
(6, '0006_create_batch_jobs_table', 1, '2026-06-05 22:37:19'),
(7, '0007_create_processing_log_table', 1, '2026-06-05 22:37:19');

-- --------------------------------------------------------

--
-- Table structure for table `processing_log`
--

CREATE TABLE `processing_log` (
  `id` int(11) NOT NULL,
  `upload_id` int(11) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `processing_log`
--

INSERT INTO `processing_log` (`id`, `upload_id`, `action`, `details`, `status`, `created_at`) VALUES
(1, 9, 'extract', 'Extracted 12 records from Word document', 'completed', '2026-06-06 08:09:50');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(160) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','employee') DEFAULT 'employee',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `fullname`, `username`, `password`, `role`, `is_active`, `created_at`) VALUES
(1, 'System Administrator', 'admin@local.test', 'System Administrator', 'admin', '$2y$10$examplehashedpasswordhere', 'admin', 1, '2026-05-07 01:48:48'),
(2, 'Algeo Fernandez', 'Geopy2004@local.test', 'Algeo Fernandez', 'Geopy2004', '$2y$12$Ecil7i3xofcxqnw7agUHfuB2Zj6MGnE3/kvtrJIyxEojOHAhJBqNu', 'employee', 1, '2026-06-06 07:36:39'),
(3, 'Onii Chan', 'Oniichan@gmail.com', '', '', '$2y$10$mmIuPK3ZtQ8H1AfRMEU9ceuvgnfc927/lk4ZZd8i/E8iRyeoCRRCG', '', 1, '2026-06-11 04:47:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `batch_jobs`
--
ALTER TABLE `batch_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_imports`
--
ALTER TABLE `data_imports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `upload_id` (`upload_id`);

--
-- Indexes for table `dtr_logs`
--
ALTER TABLE `dtr_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `email_messages`
--
ALTER TABLE `email_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `extracted_data`
--
ALTER TABLE `extracted_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `upload_id` (`upload_id`);

--
-- Indexes for table `file_uploads`
--
ALTER TABLE `file_uploads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `generated_files`
--
ALTER TABLE `generated_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `migration_name` (`migration_name`);

--
-- Indexes for table `processing_log`
--
ALTER TABLE `processing_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `upload_id` (`upload_id`);

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
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `batch_jobs`
--
ALTER TABLE `batch_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `data_imports`
--
ALTER TABLE `data_imports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dtr_logs`
--
ALTER TABLE `dtr_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_messages`
--
ALTER TABLE `email_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `extracted_data`
--
ALTER TABLE `extracted_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `file_uploads`
--
ALTER TABLE `file_uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `generated_files`
--
ALTER TABLE `generated_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=841;

--
-- AUTO_INCREMENT for table `processing_log`
--
ALTER TABLE `processing_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `data_imports`
--
ALTER TABLE `data_imports`
  ADD CONSTRAINT `data_imports_ibfk_1` FOREIGN KEY (`upload_id`) REFERENCES `file_uploads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dtr_logs`
--
ALTER TABLE `dtr_logs`
  ADD CONSTRAINT `dtr_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `email_messages`
--
ALTER TABLE `email_messages`
  ADD CONSTRAINT `fk_email_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `extracted_data`
--
ALTER TABLE `extracted_data`
  ADD CONSTRAINT `extracted_data_ibfk_1` FOREIGN KEY (`upload_id`) REFERENCES `file_uploads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `generated_files`
--
ALTER TABLE `generated_files`
  ADD CONSTRAINT `fk_file_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `processing_log`
--
ALTER TABLE `processing_log`
  ADD CONSTRAINT `processing_log_ibfk_1` FOREIGN KEY (`upload_id`) REFERENCES `file_uploads` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
