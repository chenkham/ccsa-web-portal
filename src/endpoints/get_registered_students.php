<?php
declare(strict_types=1);

require_once __DIR__ . '/app_db_config.php';

$course   = trim((string)($_GET['course'] ?? 'BCA'));
$semester = (int)($_GET['semester'] ?? 1);

try {
    $pdo = AppDatabase::getConnection();

    $stmt = $pdo->prepare('
        SELECT 
            COALESCE(s.id, u.id) AS id,
            COALESCE(s.name, u.name) AS name,
            COALESCE(s.email, u.email) AS email,
            COALESCE(s.class_roll_no, u.student_roll_no, "") AS class_roll_no,
            COALESCE(s.du_exam_roll_no, u.du_exam_roll_no, "") AS du_exam_roll_no,
            COALESCE(s.course, u.course, ?) AS course,
            COALESCE(s.semester, u.semester, ?) AS semester,
            COALESCE(s.device_id, u.device_id, "") AS device_id,
            COALESCE(s.created_at, u.created_at) AS created_at
        FROM app_students s
        LEFT JOIN app_users u ON (s.id = u.id OR (s.email != "" AND LOWER(s.email) = LOWER(u.email)))
        WHERE s.course = ? AND s.semester = ? AND s.is_active = 1
        UNION
        SELECT 
            u.id, u.name, u.email, 
            COALESCE(u.student_roll_no, "") AS class_roll_no, 
            COALESCE(u.du_exam_roll_no, "") AS du_exam_roll_no, 
            u.course, u.semester, 
            COALESCE(u.device_id, "") AS device_id, 
            u.created_at
        FROM app_users u
        WHERE u.role = "student" AND u.course = ? AND u.semester = ? 
          AND LOWER(u.email) NOT IN (SELECT LOWER(email) FROM app_students WHERE email != "")
        ORDER BY CASE WHEN class_roll_no != "" THEN CAST(class_roll_no AS UNSIGNED) ELSE 9999 END ASC, name ASC
    ');
    $stmt->execute([$course, $semester, $course, $semester, $course, $semester]);
    $students = $stmt->fetchAll();

    echo json_encode([
        'success'  => true,
        'count'    => count($students),
        'students' => $students
    ]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch registered students: ' . $e->getMessage()
    ]);
}
