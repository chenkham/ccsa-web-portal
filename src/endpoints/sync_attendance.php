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
            subject_id = VALUES(subject_id),
            subject_code = VALUES(subject_code),
            subject_name = VALUES(subject_name),
            session_date = VALUES(session_date),
            is_lab = VALUES(is_lab),
            duration_hours = VALUES(duration_hours),
            teacher_email = VALUES(teacher_email),
            total_enrolled = VALUES(total_enrolled),
            total_present = VALUES(total_present),
            geofence_enabled = VALUES(geofence_enabled),
            session_lat = VALUES(session_lat),
            session_lng = VALUES(session_lng),
            session_radius = VALUES(session_radius)
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
        $session['total_enrolled'] ?? count($records),
        $session['total_present'] ?? 0,
        !empty($session['geofence_enabled']) ? 1 : 0,
        $session['session_lat'] ?? null,
        $session['session_lng'] ?? null,
        $session['session_radius'] ?? null,
    ]);

    // 2. Deduplicate records per student
    $dedupedRecords = [];
    $seenKeys = [];
    foreach ($records as $r) {
        $sid = trim((string)($r['student_id'] ?? ''));
        $sroll = trim((string)($r['student_roll_no'] ?? ''));
        $sname = strtolower(trim((string)($r['student_name'] ?? '')));
        $key = $sid ?: ($sroll ?: $sname);

        if (!empty($key) && !isset($seenKeys[$key])) {
            $seenKeys[$key] = true;
            $dedupedRecords[] = $r;
        }
    }

    // 3. Upsert finalized records without destroying live cloud scans
    $recStmt = $pdo->prepare('
        INSERT INTO app_attendance_records 
        (id, session_id, student_id, student_roll_no, student_name, status, marked_by, confidence, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            status = VALUES(status),
            student_roll_no = VALUES(student_roll_no),
            student_name = VALUES(student_name),
            marked_by = VALUES(marked_by),
            confidence = VALUES(confidence)
    ');

    foreach ($dedupedRecords as $r) {
        $status = strtolower((string)($r['status'] ?? 'present'));
        if ($status !== 'present' && $status !== 'absent' && $status !== 'late') {
            $status = 'present';
        }

        $recStmt->execute([
            $r['id'] ?? bin2hex(random_bytes(16)),
            $session['id'],
            $r['student_id'] ?? '',
            $r['student_roll_no'] ?? '',
            $r['student_name'] ?? '',
            $status,
            $r['marked_by'] ?? 'dynamic_qr',
            $r['confidence'] ?? null,
            $r['created_at'] ?? date('Y-m-d H:i:s')
        ]);
    }

    // 4. Recount turnout from the database (cloud is the single source of truth)
    $cntStmt = $pdo->prepare("
        SELECT 
            COUNT(*) AS total_records,
            SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) AS present_records
        FROM app_attendance_records 
        WHERE session_id = ?
    ");
    $cntStmt->execute([$session['id']]);
    $cntRow = $cntStmt->fetch();
    $finalPresentCount = $cntRow ? (int)$cntRow['present_records'] : 0;
    $finalEnrolledCount = max(
        (int)($session['total_enrolled'] ?? 0),
        $cntRow ? (int)$cntRow['total_records'] : 0,
        count($dedupedRecords)
    );

    // 5. Update accurate turnout counts in session table
    $upSess = $pdo->prepare('UPDATE app_attendance_sessions SET total_present = ?, total_enrolled = ? WHERE id = ?');
    $upSess->execute([$finalPresentCount, $finalEnrolledCount, $session['id']]);

    $pdo->commit();

    echo json_encode([
        'success'        => true,
        'message'        => 'Attendance session and ' . count($dedupedRecords) . ' records synced to Hostinger database.',
        'session_id'     => $session['id'],
        'total_present'  => $finalPresentCount,
        'total_enrolled' => $finalEnrolledCount
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