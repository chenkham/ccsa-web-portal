<?php
declare(strict_types=1);

/**
 * CCSA Smart Attendance - Fetch Session Details & Student Records from Hostinger MySQL
 * Centre for Computer Science and Applications, Dibrugarh University
 */

require_once __DIR__ . '/app_db_config.php';

$sessionId = trim((string)($_GET['session_id'] ?? ($_POST['session_id'] ?? '')));

if (empty($sessionId)) {
    echo json_encode(['success' => false, 'message' => 'Session ID is required.']);
    exit;
}

try {
    $pdo = AppDatabase::getConnection();

    // Session metadata
    $stmt = $pdo->prepare("SELECT * FROM app_attendance_sessions WHERE id = ? LIMIT 1");
    $stmt->execute([$sessionId]);
    $session = $stmt->fetch();

    if (!$session) {
        echo json_encode(['success' => false, 'message' => 'Session not found.']);
        exit;
    }

    // Attendance records
    $recStmt = $pdo->prepare("
        SELECT r.*, s.class_roll_no, s.du_exam_roll_no, s.email 
        FROM app_attendance_records r
        LEFT JOIN app_students s ON r.student_id = s.id
        WHERE r.session_id = ?
        ORDER BY s.class_roll_no ASC
    ");
    $recStmt->execute([$sessionId]);
    $records = $recStmt->fetchAll();

    echo json_encode([
        'success'  => true,
        'session'  => $session,
        'records'  => $records
    ]);

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Query error: ' . $e->getMessage()]);
}
