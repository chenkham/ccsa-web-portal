<?php
declare(strict_types=1);

require_once __DIR__ . '/app_db_config.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$session = $input['session'] ?? null;
$records = $input['records'] ?? [];

if (empty($session) || empty($session['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Attendance session data is required.']);
    exit;
}

try {
    $pdo = AppDatabase::getConnection();

    $pdo->beginTransaction();

    // 1. Insert or update session in app_attendance_sessions
    $stmt = $pdo->prepare('
        INSERT INTO app_attendance_sessions 
        (id, subject_id, subject_code, subject_name, session_date, is_lab, duration_hours, teacher_email, total_enrolled, total_present, geofence_enabled, session_lat, session_lng, session_radius)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            total_enrolled = VALUES(total_enrolled),
            total_present = VALUES(total_present),
            session_lat = VALUES(session_lat),
            session_lng = VALUES(session_lng)
    ');

    $stmt->execute([
        $session['id'],
        $session['subject_id'],
        $session['subject_code'],
        $session['subject_name'],
        $session['session_date'],
        !empty($session['is_lab']) ? 1 : 0,
        $session['duration_hours'] ?? 1,
        $session['teacher_email'] ?? '',
        $session['total_enrolled'] ?? 0,
        $session['total_present'] ?? 0,
        !empty($session['geofence_enabled']) ? 1 : 0,
        $session['session_lat'] ?? null,
        $session['session_lng'] ?? null,
        $session['session_radius'] ?? null,
    ]);

    // 2. Clear any intermediate scan records for this session before saving final roster
    $delStmt = $pdo->prepare('DELETE FROM app_attendance_records WHERE session_id = ?');
    $delStmt->execute([$session['id']]);

    // 3. Insert finalized records in app_attendance_records
    $recStmt = $pdo->prepare('
        INSERT INTO app_attendance_records 
        (id, session_id, student_id, student_roll_no, student_name, status, marked_by, marked_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ');

    foreach ($records as $r) {
        $recStmt->execute([
            $r['id'],
            $session['id'],
            $r['student_id'],
            $r['student_roll_no'] ?? '',
            $r['student_name'] ?? '',
            $r['status'] ?? 'present',
            $r['marked_by'] ?? 'dynamic_qr',
            $r['confidence'] ?? null,
            $r['created_at'] ?? date('Y-m-d H:i:s')
        ]);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Attendance session and ' . count($records) . ' records synced to Hostinger database.',
        'session_id' => $session['id']
    ]);

} catch (\Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to sync attendance session: ' . $e->getMessage()
    ]);
}
