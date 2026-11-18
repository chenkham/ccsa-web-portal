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

    // Fetch all attendance records for these sessions in ONE query so the app
    // can compute per-student attended/total on the subject details roster.
    $sessionIds = array_column($sessions, 'id');
    $recordsBySession = [];
    if (!empty($sessionIds)) {
        $ph = implode(',', array_fill(0, count($sessionIds), '?'));
        $recStmt = $pdo->prepare("
            SELECT id, session_id, student_id, student_roll_no, student_name, status, marked_by, created_at
            FROM app_attendance_records
            WHERE session_id IN ($ph)
            ORDER BY created_at ASC
        ");
        $recStmt->execute($sessionIds);
        foreach ($recStmt->fetchAll() as $r) {
            $recordsBySession[$r['session_id']][] = $r;
        }
    }

    foreach ($sessions as &$sess) {
        // Attach records for client-side roster computation
        $sess['records'] = $recordsBySession[$sess['id']] ?? [];

        // Recount turnout from UNIQUE students marked present (protects
        // against any historical duplicate rows inflating counts like 3/2)
        $sess['total_present'] = 0;
        $seenStudents = [];
        foreach ($sess['records'] as $r) {
            if (strtolower((string)$r['status']) !== 'present') continue;
            $key = !empty($r['student_id']) ? 'id:' . $r['student_id']
                 : (!empty($r['student_roll_no']) ? 'roll:' . $r['student_roll_no'] : 'name:' . strtolower($r['student_name']));
            if (!isset($seenStudents[$key])) {
                $seenStudents[$key] = true;
                $sess['total_present']++;
            }
        }
        if (!empty($sess['records'])) {
            $sess['total_enrolled'] = max((int)$sess['total_enrolled'], count($sess['records']));
        }
    }
    unset($sess);

    echo json_encode([
        'success'  => true,
        'count'    => count($sessions),
        'sessions' => $sessions
    ]);

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch sessions: ' . $e->getMessage()]);
}
