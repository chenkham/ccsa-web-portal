<?php
declare(strict_types=1);

/**
 * CCSA Smart Attendance - App Database Connector
 * Dedicated Database: u332237642_attendance
 * Centre for Computer Science and Applications, Dibrugarh University
 */

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

class AppDatabase
{
    private static ?\PDO $pdo = null;
    private static bool $schemaEnsured = false;

    public static function getConnection(): \PDO
    {
        if (self::$pdo === null) {
            $options = [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            // Dedicated Attendance App Database Credentials
            $connections = [
                ['host' => 'localhost', 'name' => 'u332237642_attendance', 'user' => 'u332237642_attendance', 'pass' => '786004@Ccs'],
                ['host' => '127.0.0.1', 'name' => 'u332237642_attendance', 'user' => 'u332237642_attendance', 'pass' => '786004@Ccs'],
                ['host' => 'localhost', 'name' => 'u332237642_attendance', 'user' => 'u332237642_attendance', 'pass' => '786004@ccs'],
            ];

            $lastError = '';
            foreach ($connections as $c) {
                try {
                    $dsn = "mysql:host={$c['host']};dbname={$c['name']};charset=utf8mb4";
                    self::$pdo = new \PDO($dsn, $c['user'], $c['pass'], $options);
                    break;
                } catch (\PDOException $e) {
                    $lastError = $e->getMessage();
                }
            }

            if (self::$pdo === null) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Database connection failed: ' . $lastError
                ]);
                exit;
            }
        }

        // Automatic Table Creation on first connect (Self-healing schema)
        if (!self::$schemaEnsured) {
            try {
                self::$pdo->exec("
                    CREATE TABLE IF NOT EXISTS app_otp_verifications (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        email VARCHAR(255) NOT NULL,
                        otp_code VARCHAR(10) NOT NULL,
                        expires_at DATETIME NOT NULL,
                        is_used TINYINT(1) DEFAULT 0,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        INDEX (email),
                        INDEX (otp_code)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                    CREATE TABLE IF NOT EXISTS app_users (
                        id VARCHAR(64) PRIMARY KEY,
                        name VARCHAR(255) NOT NULL,
                        email VARCHAR(255) NOT NULL UNIQUE,
                        password VARCHAR(255) NOT NULL,
                        role ENUM('student', 'faculty') NOT NULL DEFAULT 'student',
                        student_roll_no VARCHAR(50) NULL,
                        du_exam_roll_no VARCHAR(50) NULL,
                        course VARCHAR(50) NULL,
                        semester INT NULL,
                        designation VARCHAR(100) NULL,
                        department VARCHAR(150) NULL,
                        device_id VARCHAR(128) NULL,
                        profile_completed TINYINT(1) DEFAULT 0,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                    CREATE TABLE IF NOT EXISTS app_students (
                        id VARCHAR(64) PRIMARY KEY,
                        name VARCHAR(255) NOT NULL,
                        email VARCHAR(255) NOT NULL,
                        class_roll_no VARCHAR(50) DEFAULT '',
                        du_exam_roll_no VARCHAR(50) DEFAULT '',
                        course VARCHAR(50) NOT NULL,
                        semester INT NOT NULL,
                        device_id VARCHAR(128) DEFAULT '',
                        is_active TINYINT(1) DEFAULT 1,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                    CREATE TABLE IF NOT EXISTS app_subjects (
                        id VARCHAR(64) PRIMARY KEY,
                        code VARCHAR(50) NOT NULL,
                        name VARCHAR(255) NOT NULL,
                        course VARCHAR(50) NOT NULL,
                        semester INT NOT NULL,
                        teacher_name VARCHAR(255) NOT NULL,
                        teacher_email VARCHAR(255) NOT NULL,
                        geofence_lat DOUBLE NULL,
                        geofence_lng DOUBLE NULL,
                        geofence_radius DOUBLE DEFAULT 80.0,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                    CREATE TABLE IF NOT EXISTS app_subject_students (
                        subject_id VARCHAR(64) NOT NULL,
                        student_id VARCHAR(64) NOT NULL,
                        PRIMARY KEY (subject_id, student_id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                    CREATE TABLE IF NOT EXISTS app_attendance_sessions (
                        id VARCHAR(64) PRIMARY KEY,
                        subject_id VARCHAR(64) NOT NULL,
                        subject_code VARCHAR(50) NOT NULL,
                        subject_name VARCHAR(255) NOT NULL,
                        teacher_email VARCHAR(255) NOT NULL,
                        session_date DATETIME NOT NULL,
                        total_present INT DEFAULT 0,
                        total_enrolled INT DEFAULT 0,
                        is_lab TINYINT(1) DEFAULT 0,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

                    CREATE TABLE IF NOT EXISTS app_attendance_records (
                        id VARCHAR(64) PRIMARY KEY,
                        session_id VARCHAR(64) NOT NULL,
                        student_id VARCHAR(64) NOT NULL,
                        student_roll_no VARCHAR(50) DEFAULT '',
                        student_name VARCHAR(255) NOT NULL,
                        status ENUM('present', 'absent') NOT NULL,
                        marked_by VARCHAR(50) DEFAULT 'totp_qr',
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        UNIQUE KEY uq_session_student (session_id, student_id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                ");

                // Ensure missing columns exist on already-created tables
                try {
                    self::$pdo->exec("ALTER TABLE app_subjects ADD COLUMN IF NOT EXISTS geofence_enabled TINYINT(1) DEFAULT 0 AFTER geofence_radius");
                    self::$pdo->exec("ALTER TABLE app_attendance_sessions ADD COLUMN IF NOT EXISTS duration_hours INT DEFAULT 1 AFTER is_lab");
                    self::$pdo->exec("ALTER TABLE app_attendance_sessions ADD COLUMN IF NOT EXISTS geofence_enabled TINYINT(1) DEFAULT 0 AFTER total_present");
                    self::$pdo->exec("ALTER TABLE app_attendance_sessions ADD COLUMN IF NOT EXISTS session_lat DOUBLE NULL AFTER geofence_enabled");
                    self::$pdo->exec("ALTER TABLE app_attendance_sessions ADD COLUMN IF NOT EXISTS session_lng DOUBLE NULL AFTER session_lat");
                    self::$pdo->exec("ALTER TABLE app_attendance_sessions ADD COLUMN IF NOT EXISTS session_radius DOUBLE DEFAULT 80.0 AFTER session_lng");
                    self::$pdo->exec("ALTER TABLE app_attendance_records ADD COLUMN IF NOT EXISTS confidence DECIMAL(5,2) NULL AFTER marked_by");
                } catch (\Throwable $e) {}

                // Clean up any historical duplicate records
                self::$pdo->exec("
                    DELETE r1 FROM app_attendance_records r1
                    INNER JOIN app_attendance_records r2 
                    WHERE r1.id < r2.id AND r1.session_id = r2.session_id AND r1.student_id = r2.student_id;
                ");

                self::$schemaEnsured = true;
            } catch (\Throwable $e) {
                self::$schemaEnsured = true;
            }
        }

        return self::$pdo;
    }
}
