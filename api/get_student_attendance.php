<?php
declare(strict_types=1);

/**
 * Fetch Student Attendance & Statistics from Cloud Database
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

    // Resolve student ID if only email is provided
    if (empty($studentId) && !empty($email)) {
        $uStmt = $pdo->prepare("SELECT id FROM app_users WHERE LOWER(email) = LOWER(?) LIMIT 1");
        $uStmt->execute([$email]);
        $uRow = $uStmt->fetch();
        if ($uRow) {
            $studentId = $uRow['id'];
        }
    }

    // Query distinct sessions to prevent duplicates
    $stmt = $pdo->prepare("
        SELECT 
            s.id AS session_id,
            s.subject_id,
            s.subject_code,
            s.subject_name,
            s.teacher_email,
            s.session_date,
            s.total_present,
            s.total_enrolled,
            s.is_lab,
            COALESCE(MAX(r.status), 'absent') AS status,
            MAX(r.marked_by) AS marked_by,
            MAX(r.marked_at) AS marked_at
        FROM app_attendance_sessions s
        LEFT JOIN app_attendance_records r 
            ON s.id = r.session_id AND (r.student_id = ? OR r.student_id = (SELECT id FROM app_students WHERE LOWER(email) = LOWER(?) LIMIT 1))
        GROUP BY s.id, s.subject_id, s.subject_code, s.subject_name, s.teacher_email, s.session_date, s.total_present, s.total_enrolled, s.is_lab
        ORDER BY s.session_date DESC
    ");
    $stmt->execute([$studentId, $email]);
    $sessions = $stmt->fetchAll();

    // Calculate subject-wise class statistics
    $subjectStats = [];
    foreach ($sessions as $row) {
        $subId = $row['subject_id'] ?: $row['subject_code'];
        if (!isset($subjectStats[$subId])) {
            $subjectStats[$subId] = [
                'subject_id'       => $subId,
                'subject_code'     => $row['subject_code'],
                'subject_name'     => $row['subject_name'],
                'total_held'       => 0,
                'total_attended'   => 0,
                'sessions'         => []
            ];
        }

        // Each session represents 1 class
        $subjectStats[$subId]['total_held'] += 1;
        if ($row['status'] === 'present') {
            $subjectStats[$subId]['total_attended'] += 1;
        }

        $subjectStats[$subId]['sessions'][] = $row;
    }

    // Compute percentage and missed classes
    foreach ($subjectStats as $subId => &$stat) {
        $held = $stat['total_held'];
        $att  = $stat['total_attended'];
        $stat['percentage'] = $held > 0 ? round(($att / $held) * 100, 1) : 100.0;
        $stat['missed'] = $held - $att;
    }
    unset($stat);

    echo json_encode([
        'success'       => true,
        'student_id'    => $studentId,
        'total_sessions'=> count($sessions),
        'sessions'      => $sessions,
        'stats'         => $subjectStats
    ]);

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Query error: ' . $e->getMessage()]);
}
