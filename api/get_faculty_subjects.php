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

    // Attach enrolled student IDs for each subject
    foreach ($subjects as &$sub) {
        $stdStmt = $pdo->prepare("
            SELECT s.* FROM app_students s
            INNER JOIN app_subject_students ss ON s.id = ss.student_id
            WHERE ss.subject_id = ?
            ORDER BY s.class_roll_no ASC
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
