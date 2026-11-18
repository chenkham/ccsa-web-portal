<?php
declare(strict_types=1);

/**
 * CCSA Smart Attendance - Fetch Faculty Subjects from Hostinger MySQL
 * Centre for Computer Science and Applications, Dibrugarh University
 */

require_once __DIR__ . '/app_db_config.php';

$email = trim((string)($_GET['email'] ?? ($_POST['email'] ?? '')));

if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Teacher email is required.']);
    exit;
}

try {
    $pdo = AppDatabase::getConnection();

    $stmt = $pdo->prepare("
        SELECT * FROM app_subjects 
        WHERE LOWER(teacher_email) = LOWER(?) 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$email]);
    $subjects = $stmt->fetchAll();

    // Attach enrolled student IDs and profiles for each subject
    foreach ($subjects as &$sub) {
        $stdStmt = $pdo->prepare("
            SELECT 
                COALESCE(s.id, u.id, ss.student_id) AS id,
                COALESCE(s.name, u.name, 'Student') AS name,
                COALESCE(s.email, u.email, '') AS email,
                COALESCE(s.class_roll_no, u.student_roll_no, '') AS class_roll_no,
                COALESCE(s.du_exam_roll_no, u.du_exam_roll_no, '') AS du_exam_roll_no,
                COALESCE(s.course, u.course, 'BCA') AS course,
                COALESCE(s.semester, u.semester, 1) AS semester
            FROM app_subject_students ss
            LEFT JOIN app_students s ON ss.student_id = s.id
            LEFT JOIN app_users u ON ss.student_id = u.id OR (s.email IS NOT NULL AND s.email != '' AND LOWER(u.email) = LOWER(s.email))
            WHERE ss.subject_id = ?
            ORDER BY CASE WHEN s.class_roll_no IS NOT NULL AND s.class_roll_no != '' THEN CAST(s.class_roll_no AS UNSIGNED)
                          WHEN u.student_roll_no IS NOT NULL AND u.student_roll_no != '' THEN CAST(u.student_roll_no AS UNSIGNED)
                          ELSE 9999 END ASC, COALESCE(s.name, u.name) ASC
        ");
        $stdStmt->execute([$sub['id']]);
        $sub['enrolled_students'] = $stdStmt->fetchAll();
        $sub['enrolled_student_ids'] = array_column($sub['enrolled_students'], 'id');
    }
    unset($sub);

    echo json_encode([
        'success'  => true,
        'count'    => count($subjects),
        'subjects' => $subjects
    ]);

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch subjects: ' . $e->getMessage()]);
}
