-- =====================================================================
-- CCSA Dibrugarh University - Complete MySQL Database Schema (v2.5.2)
-- Centre for Computer Science and Applications
-- Database Engine: InnoDB | Character Set: utf8mb4 | Collation: utf8mb4_unicode_ci
-- Architect & Developer: Chenkham Chowlu
-- Copyright (c) 2024-2026 Chenkham Chowlu. All rights reserved.
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+05:30";

-- ---------------------------------------------------------------------
-- 1. Table structure for `admin_users`
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admin_users` (
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

-- Seed default Super Admin (Email: ccsduoffice@gmail.com | Password: 786004@Ccs)
INSERT IGNORE INTO `admin_users` (`id`, `name`, `email`, `password`, `role`, `status`, `createdAt`) VALUES
(1, 'CCSA Super Administrator', 'ccsduoffice@gmail.com', '$2y$12$amJ/rdLNDOcdL3CpYXMv9OSSE1rCjCAE/.jUG6JURCjh/u0Wo8TBW', 'super_admin', 'active', NOW());

-- ---------------------------------------------------------------------
-- 2. Table structure for `notifications` (Official Announcements)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'General',
  `creator_email` varchar(150) DEFAULT 'ccsduoffice@gmail.com',
  `file_path` varchar(255) DEFAULT NULL,
  `file_url` varchar(500) DEFAULT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `is_new` tinyint(1) NOT NULL DEFAULT 1,
  `new_until` date DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_created` (`createdAt`),
  KEY `idx_category` (`category`),
  KEY `idx_new_until` (`new_until`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed essential department announcements
INSERT IGNORE INTO `notifications` (`id`, `title`, `description`, `category`, `creator_email`, `is_pinned`, `is_new`, `new_until`, `createdAt`) VALUES
(1, 'Notification regarding MCA, BCA and PGDCA Odd Semester Examinations 2026', 'Examination schedules, registration details, and instructions for all candidates appearing for the Odd Semester Examinations.', 'Examination', 'ccsduoffice@gmail.com', 1, 1, DATE_ADD(CURDATE(), INTERVAL 30 DAY), NOW()),
(2, 'Workshop on Machine Learning and Big Data Analytics at CCSA', 'Department of Computer Science invites students and scholars to a 3-day hands-on workshop on modern AI techniques.', 'Workshop', 'ccsduoffice@gmail.com', 0, 1, DATE_ADD(CURDATE(), INTERVAL 15 DAY), NOW()),
(3, 'Centre for Computer Science and Applications - Official Web Portal Live', 'The official CCSA Dibrugarh University portal is now active for notifications, academic schedules, and faculty resources.', 'General', 'ccsduoffice@gmail.com', 1, 0, NULL, NOW());

-- ---------------------------------------------------------------------
-- 3. Table structure for `teaching_staff` (Faculty Directory with Official DigitalOcean CDN Photos)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `teaching_staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) DEFAULT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `full_name` varchar(150) NOT NULL,
  `position` varchar(100) NOT NULL,
  `department` varchar(150) NOT NULL DEFAULT 'Centre for Computer Science and Applications',
  `email` varchar(150) NOT NULL,
  `photo` varchar(500) NOT NULL,
  `qualification` varchar(100) DEFAULT 'Ph.D.',
  `specialization` text DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `order_num` int(11) DEFAULT 0,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_staff_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed All 10 Assistant Professors + Chairperson with Official DigitalOcean University CDN URLs
INSERT IGNORE INTO `teaching_staff` (`id`, `staff_id`, `employee_id`, `full_name`, `position`, `department`, `email`, `photo`, `qualification`, `specialization`, `order_num`) VALUES
(1, 364, 'DU_CCSA_01', 'Dr. Utpala Borgohain', 'Assistant Professor', 'Centre for Computer Science and Applications', 'utpolaborgohain@dibru.ac.in', 'https://dibru-files.sgp1.cdn.digitaloceanspaces.com/staff/sj2Z0O1slmkpyxCR7FdzBDs5U9GjeWjKuM7IBssn.png', 'Ph.D., MCA', 'Wireless communication network, Artificial Intelligence, Cognitive Radio Network, Cooperative Game Theory', 1),
(2, 365, 'DU_CCSA_02', 'Dr. Rizwan Rehman', 'Assistant Professor', 'Centre for Computer Science and Applications', 'rizwanrehman@dibru.ac.in', 'https://dibru-files.sgp1.cdn.digitaloceanspaces.com/staff/WtWED8hVWYdBI5MFpYjwrCUdgVzXx7pstQBrPyxE.png', 'Ph.D., M.Tech, B.E.', 'Deep Learning (NLP, Computer Vision, Computational Biology), Programming (C/C++/Java/Python/R/MERN/PHP), Data Structures', 2),
(3, 398, 'DU_CCSA_03', 'Dr. Toralima Bora', 'Assistant Professor', 'Centre for Computer Science and Applications', 'toralibora@dibru.ac.in', 'https://dibru-files.sgp1.cdn.digitaloceanspaces.com/staff/Wyh0od9HyPZ1inzyv8dFS8UHCmwPCHzVj40viQZd.png', 'Ph.D., M.Tech', 'Fuzzy Mathematics, Numerical Methods, C Programming, Matlab', 3),
(4, 397, 'DU_CCSA_04', 'Dr. Ujjal Saikia', 'Assistant Professor', 'Centre for Computer Science and Applications', 'ujjalsaikia@dibru.ac.in', 'https://dibru-files.sgp1.cdn.digitaloceanspaces.com/staff/9inBJ2Udzt0YCp9DY92xcXFdHL7PLeQ6WJLY2QKg.png', 'Ph.D., MCA', 'Relational Database Management System, Statistical Methods, Web & Mobile Technologies, Data Analysis using R Programming', 4),
(5, 396, 'DU_CCSA_05', 'Dr. Kimasha Borah', 'Assistant Professor', 'Centre for Computer Science and Applications', 'kimashaborah@dibru.ac.in', 'https://dibru-files.sgp1.cdn.digitaloceanspaces.com/staff/U4mquF8hTiPkfIGsay0ygDE2FwcyxhEkKePf3mAB.png', 'Ph.D., MCA', 'Complex network, Social Network Analysis, Microprocessor, Digital System and Design', 5),
(6, 393, 'DU_CCSA_06', 'Dr. Pranjal Kumar Bora', 'Assistant Professor', 'Centre for Computer Science and Applications', 'pranjalkumarbora@dibru.ac.in', 'https://dibru-files.sgp1.cdn.digitaloceanspaces.com/staff/vVoEEIQjUFnC1MZQYEUTrtcwSg37cX7WYwKQvrbE.png', 'Ph.D., M.Tech', 'Machine Learning, Computational Biology, Graph theory, Complex Network', 6),
(7, 394, 'DU_CCSA_07', 'Mr. Ankumon Sarmah', 'Assistant Professor', 'Centre for Computer Science and Applications', 'ankumonsarmah@dibru.ac.in', 'https://dibru-files.sgp1.cdn.digitaloceanspaces.com/staff/6TNcXOg6UuHghb0NCpeWYffwugk6Px8N83QiHtHp.png', 'MCA', 'NLP, Speech Processing, Machine Learning', 7),
(8, 395, 'DU_CCSA_08', 'Ms. Kankana Dutta', 'Assistant Professor', 'Centre for Computer Science and Applications', 'kankanadutta@dibru.ac.in', 'https://dibru-files.sgp1.cdn.digitaloceanspaces.com/staff/8kleNUe0ajfrxoY0MB4v1WSjHrtFApiwJfe1h0E1.png', 'MCA', 'Machine Learning, Speech Processing', 8),
(9, 399, 'DU_CCSA_09', 'Dr. Sumpi Saikia', 'Assistant Professor', 'Centre for Computer Science and Applications', 'sumpisaikia@dibru.ac.in', 'https://dibru-files.sgp1.cdn.digitaloceanspaces.com/staff/1quSjPrHSlfJG6WnTaPC8fF3ZBI7SwKYEeaA3syd.png', 'Ph.D., MCA', 'Machine learning, Image processing', 9),
(10, 4626, 'DU_CCSA_10', 'Ms. Pinakshi Konwar', 'Assistant Professor', 'Centre for Computer Science and Applications', 'pinakshikonwar@dibru.ac.in', 'https://dibru-files.sgp1.cdn.digitaloceanspaces.com/staff/HGSPT0LLsriLLvcucNHxfQuhzWk5haZJyhCoGeTq.png', 'MCA', 'Machine Learning, Computational Biology', 10);

-- ---------------------------------------------------------------------
-- 4. Table structure for `current_students` (Student Directory)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `current_students` (
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

-- Seed sample student roster
INSERT IGNORE INTO `current_students` (`id`, `roll_no`, `full_name`, `course`, `semester`, `batch_year`) VALUES
(1, '23MCA014', 'Ankur Jyoti Sharma', 'MCA', '4th Semester', '2023-2025'),
(2, '22BCA032', 'Priyanka Das', 'BCA', '6th Semester', '2022-2025'),
(3, '24PGDCA05', 'Nandita Saikia', 'PGDCA', '2nd Semester', '2024-2025'),
(4, '23BCA008', 'Rahul Borah', 'BCA', '4th Semester', '2023-2026'),
(5, '24MCA021', 'Debashish Gogoi', 'MCA', '2nd Semester', '2024-2026'),
(6, '24PHD001', 'Manash Pratim Dutta', 'Ph.D.', 'Course Work', '2024-2029');

-- ---------------------------------------------------------------------
-- 5. Table structure for `contact_messages` (Public Inquiries)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
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
CREATE TABLE IF NOT EXISTS `audit_logs` (
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
INSERT IGNORE INTO `audit_logs` (`user_email`, `ip_address`, `action`, `details`) VALUES
('system', '127.0.0.1', 'DATABASE_INITIALIZATION', 'CCSA Database schema initialized successfully with modern tables and authentic faculty records.');

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;
