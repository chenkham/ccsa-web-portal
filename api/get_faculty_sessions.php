<?php
declare(strict_types=1);

/**
 * CCSA Smart Attendance - Fetch Faculty Sessions from Hostinger MySQL
 * Centre for Computer Science and Applications, Dibrugarh University
 */

require_once __DIR__ . '/app_db_config.php';

$email     = trim((string)($_GET['email'] ?? ($_POST['email'] ?? '')));
$subjectId = trim((string)($_GET['subject_id'] ?? ($_POST['subject_id'] ?? '')));

if (empty($email) && empty($subjectId)) {
    echo json_encode(['success' => false, 'message' => 'Teacher email or Subject ID is required.']);
    exit;
}

try {
    $pdo = AppDatabase::getConnection();

    if (!empty($subjectId)) {
        $stmt = $pdo->prepare("
            SELECT * FROM app_attendance_sessions 
            WHERE subject_id = ? 
            ORDER BY session_date DESC
        ");
        $stmt->execute([$subjectId]);
    } else {
        $stmt = $pdo->prepare("
            SELECT * FROM app_attendance_sessions 
            WHERE LOWER(teacher_email) = LOWER(?) 
            ORDER BY session_date DESC
        ");
        $stmt->execute([$email]);
    }

    $sessions = $stmt->fetchAll();

    echo json_encode([
        'success'  => true,
        'count'    => count($sessions),
        'sessions' => $sessions
    ]);

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch sessions: ' . $e->getMessage()]);
}
