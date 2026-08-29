<?php
declare(strict_types=1);

/**
 * CCSA Smart Attendance - Fetch Enrolled Subjects for Student from Hostinger MySQL
 * Centre for Computer Science and Applications, Dibrugarh University
 */

require_once __DIR__ . '/app_db_config.php';

$email     = trim((string)($_GET['email'] ?? ($_POST['email'] ?? '')));
$studentId = trim((string)($_GET['student_id'] ?? ($_POST['student_id'] ?? '')));

if (empty($email) && empty($studentId)) {
    echo json_encode(['success' => false, 'message' => 'Email or Student ID is required.']);
    exit;
}

try {
    $pdo = AppDatabase::getConnection();

    // Resolve student ID if needed
    if (empty($studentId) && !empty($email)) {
        $uStmt = $pdo->prepare("SELECT id FROM app_users WHERE LOWER(email) = LOWER(?) LIMIT 1");
        $uStmt->execute([$email]);
        $uRow = $uStmt->fetch();
        if ($uRow) {
            $studentId = $uRow['id'];
        }
    }

    $stmt = $pdo->prepare("
        SELECT DISTINCT s.* 
        FROM app_subjects s
        INNER JOIN app_subject_students ss ON s.id = ss.subject_id
        WHERE ss.student_id = ? OR ss.student_id = (SELECT id FROM app_students WHERE LOWER(email) = LOWER(?) LIMIT 1)
        ORDER BY s.code ASC
    ");
    $stmt->execute([$studentId, $email]);
    $subjects = $stmt->fetchAll();

    // Attach enrolled student IDs for each subject
    foreach ($subjects as &$sub) {
        $stdStmt = $pdo->prepare("
            SELECT student_id FROM app_subject_students WHERE subject_id = ?
        ");
        $stdStmt->execute([$sub['id']]);
        $sub['enrolled_student_ids'] = array_column($stdStmt->fetchAll(), 'student_id');
    }
    unset($sub);

    echo json_encode([
        'success'  => true,
        'count'    => count($subjects),
        'subjects' => $subjects
    ]);

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Query error: ' . $e->getMessage()]);
}
