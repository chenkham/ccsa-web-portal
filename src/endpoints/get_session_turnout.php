<?php
declare(strict_types=1);

/**
 * CCSA Smart Attendance - Fetch Live Attendance Turnout for Active QR Session
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

    $stmt = $pdo->prepare("
        SELECT student_id, student_name, student_roll_no, created_at 
        FROM app_attendance_records 
        WHERE session_id = ? AND status = 'present' 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$sessionId]);
    $presentList = $stmt->fetchAll();

    echo json_encode([
        'success'             => true,
        'session_id'          => $sessionId,
        'total_present'       => count($presentList),
        'present_student_ids' => array_column($presentList, 'student_id'),
        'present_students'    => $presentList
    ]);

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Query error: ' . $e->getMessage()]);
}
