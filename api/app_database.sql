-- =============================================================
-- CCSA SMART ATTENDANCE APP - HOSTINGER MYSQL DATABASE SCHEMA
-- Centre for Computer Science and Applications, Dibrugarh University
-- Target Database: Separate App Database in phpMyAdmin (e.g. u332237642_ccsa_app)
-- =============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+05:30";

-- --------------------------------------------------------
-- 1. Users Table (Authentication & Roles)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `app_users` (
  `id` varchar(64) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','faculty') NOT NULL DEFAULT 'student',
  `student_roll_no` varchar(50) DEFAULT '',
  `du_exam_roll_no` varchar(50) DEFAULT '',
  `course` varchar(50) DEFAULT NULL,
  `semester` int(11) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `department` varchar(150) DEFAULT 'CCSA, Dibrugarh University',
  `device_id` varchar(100) DEFAULT NULL,
  `profile_completed` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_app_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Students Table (Enrolled Academic Profiles)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `app_students` (
  `id` varchar(64) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `class_roll_no` varchar(50) DEFAULT '',
  `du_exam_roll_no` varchar(50) DEFAULT '',
  `course` varchar(50) NOT NULL,
  `semester` int(11) NOT NULL,
  `device_id` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_app_students_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. Subjects Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `app_subjects` (
  `id` varchar(64) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(200) NOT NULL,
  `course` varchar(50) NOT NULL,
  `semester` int(11) NOT NULL,
  `teacher_name` varchar(150) NOT NULL,
  `teacher_email` varchar(150) NOT NULL,
  `geofence_lat` decimal(10,7) DEFAULT NULL,
  `geofence_lng` decimal(10,7) DEFAULT NULL,
  `geofence_radius` decimal(8,2) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. Subject Student Enrolled Mapping Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `app_subject_students` (
  `subject_id` varchar(64) NOT NULL,
  `student_id` varchar(64) NOT NULL,
  `enrolled_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`subject_id`,`student_id`),
  KEY `idx_app_ss_student` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 5. Attendance Sessions Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `app_attendance_sessions` (
  `id` varchar(64) NOT NULL,
  `subject_id` varchar(64) NOT NULL,
  `subject_code` varchar(50) NOT NULL,
  `subject_name` varchar(200) NOT NULL,
  `session_date` datetime NOT NULL,
  `is_lab` tinyint(1) NOT NULL DEFAULT 0,
  `duration_hours` int(11) NOT NULL DEFAULT 1,
  `teacher_email` varchar(150) NOT NULL,
  `total_enrolled` int(11) NOT NULL DEFAULT 0,
  `total_present` int(11) NOT NULL DEFAULT 0,
  `geofence_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `session_lat` decimal(10,7) DEFAULT NULL,
  `session_lng` decimal(10,7) DEFAULT NULL,
  `session_radius` decimal(8,2) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_app_as_subject` (`subject_id`),
  KEY `idx_app_as_teacher` (`teacher_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 6. Attendance Records Table (Individual Student Present/Absent)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `app_attendance_records` (
  `id` varchar(64) NOT NULL,
  `session_id` varchar(64) NOT NULL,
  `student_id` varchar(64) NOT NULL,
  `student_roll_no` varchar(50) DEFAULT '',
  `student_name` varchar(150) NOT NULL,
  `status` enum('present','absent') NOT NULL DEFAULT 'present',
  `marked_by` varchar(50) NOT NULL DEFAULT 'dynamic_qr',
  `confidence` decimal(5,2) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_app_ar_session` (`session_id`),
  KEY `idx_app_ar_student` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 7. OTP Verifications Table (Real Email OTP with 5-Min Expiry)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `app_otp_verifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_app_otp_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
