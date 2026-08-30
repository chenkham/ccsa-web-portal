<?php
declare(strict_types=1);

require_once __DIR__ . '/app_db_config.php';

$course   = trim((string)($_GET['course'] ?? 'BCA'));
$semester = (int)($_GET['semester'] ?? 1);

try {
    $pdo = AppDatabase::getConnection();

    $stmt = $pdo->prepare('
        SELECT id, name, email, class_roll_no, du_exam_roll_no, course, semester, device_id, created_at
        FROM app_students 
        WHERE course = ? AND semester = ? AND is_active = 1
        ORDER BY CASE WHEN class_roll_no != "" THEN CAST(class_roll_no AS UNSIGNED) ELSE 9999 END ASC, name ASC
    ');
    $stmt->execute([$course, $semester]);
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
