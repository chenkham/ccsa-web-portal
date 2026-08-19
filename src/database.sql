-- =====================================================================
-- CCSA Dibrugarh University - Complete MySQL Database Schema
-- Centre for Computer Science and Applications
-- Database Engine: InnoDB | Character Set: utf8mb4 | Collation: utf8mb4_unicode_ci
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+05:30";

-- ---------------------------------------------------------------------
-- 1. Table structure for `admin_users`
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','editor') NOT NULL DEFAULT 'admin',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default Super Admin (Email: admin@ccsdu.in | Password: admin123)
INSERT INTO `admin_users` (`id`, `name`, `email`, `password`, `role`, `status`, `createdAt`) VALUES
(1, 'CCSA Administrator', 'admin@ccsdu.in', '$2y$12$0giIjsnBt7QQWNjI1hhft.XP.fz.ifV2vTi6TbpxopRygW60/IjAG', 'super_admin', 'active', NOW());

-- ---------------------------------------------------------------------
-- 2. Table structure for `notifications` (Official Announcements)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'General',
  `creator_email` varchar(150) DEFAULT 'admin@ccsdu.in',
  `file_path` varchar(255) DEFAULT NULL,
  `file_url` varchar(500) DEFAULT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_created` (`createdAt`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. Table structure for `teaching_staff` (Faculty Directory)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `teaching_staff`;
CREATE TABLE `teaching_staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(50) DEFAULT NULL,
  `full_name` varchar(150) NOT NULL,
  `position` varchar(100) NOT NULL,
  `department` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `photo` varchar(255) DEFAULT 'faculty/default.png',
  `qualification` varchar(100) DEFAULT 'Ph.D.',
  `specialization` varchar(255) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_staff_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed teaching faculty
INSERT INTO `teaching_staff` (`id`, `employee_id`, `full_name`, `position`, `department`, `email`, `photo`, `qualification`, `specialization`) VALUES
(1, 'DU_CCSA_01', 'Dr. Rizwan Rehman', 'Assistant Professor', 'Cloud Computing & Big Data', 'rizwan@dibru.ac.in', 'faculty/Rizwan_SIr.png', 'Ph.D., M.Tech', 'Cloud Computing, Distributed Systems & Big Data'),
(2, 'DU_CCSA_02', 'Dr. Utpala Borgohain', 'Assistant Professor', 'Data Mining & NLP', 'uborgohain@dibru.ac.in', 'faculty/UtpolaMam.png', 'Ph.D., MCA', 'Data Mining, Machine Learning, Natural Language Processing'),
(3, 'DU_CCSA_03', 'Dr. Ujjal Saikia', 'Assistant Professor', 'Computer Vision & Image Processing', 'ujjalsaikia@dibru.ac.in', 'faculty/UjjalSir.png', 'Ph.D., M.Tech', 'Digital Image Processing, Pattern Recognition & AI'),
(4, 'DU_CCSA_04', 'Dr. Pranjal Kumar Bora', 'Assistant Professor', 'Wireless Networks & IoT', 'pranjalkumarbora@dibru.ac.in', 'faculty/PranjalSir.png', 'Ph.D., M.Tech', 'Wireless Sensor Networks, IoT & Embedded Systems'),
(5, 'DU_CCSA_05', 'Ms. Kimasha Borah', 'Assistant Professor', 'Programming & Web Technologies', 'kimasha@dibru.ac.in', 'faculty/KimashaMam.png', 'M.Tech, MCA', 'Software Engineering, Web Technologies & Algorithms'),
(6, 'DU_CCSA_06', 'Dr. Toralima Bora', 'Assistant Professor', 'Algorithms & Cryptography', 'toralima@dibru.ac.in', 'faculty/ToraliMam.png', 'Ph.D., M.Tech', 'Information Security, Cryptography & Computational Complexity');

-- ---------------------------------------------------------------------
-- 4. Table structure for `current_students` (Student Directory)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `current_students`;
CREATE TABLE `current_students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roll_no` varchar(50) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `course` varchar(50) NOT NULL,
  `semester` varchar(50) NOT NULL,
  `batch_year` varchar(20) DEFAULT '2024-2026',
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_roll_no` (`roll_no`),
  KEY `idx_course_sem` (`course`, `semester`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed student roster
INSERT INTO `current_students` (`id`, `roll_no`, `full_name`, `course`, `semester`, `batch_year`) VALUES
(1, '23MCA014', 'Ankur Jyoti Sharma', 'MCA', '4th Semester', '2023-2025'),
(2, '22BCA032', 'Priyanka Das', 'BCA', '6th Semester', '2022-2025'),
(3, '24PGDCA05', 'Nandita Saikia', 'PGDCA', '2nd Semester', '2024-2025'),
(4, '23BCA008', 'Rahul Borah', 'BCA', '4th Semester', '2023-2026'),
(5, '24MCA021', 'Debashish Gogoi', 'MCA', '2nd Semester', '2024-2026'),
(6, '24PHD001', 'Manash Pratim Dutta', 'Ph.D.', 'Course Work', '2024-2029');

-- ---------------------------------------------------------------------
-- 5. Table structure for `contact_messages` (Public Inquiries)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_msg_created` (`createdAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 6. Table structure for `audit_logs` (Security & Activity Logs)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_email` varchar(150) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_log_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Initial system audit log
INSERT INTO `audit_logs` (`user_email`, `ip_address`, `action`, `details`) VALUES
('system', '127.0.0.1', 'DATABASE_INITIALIZATION', 'CCSA Database schema initialized successfully with default records.');

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;
