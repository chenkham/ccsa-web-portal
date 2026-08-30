<?php
declare(strict_types=1);

/**
 * Fetch Student Attendance & Statistics from Cloud Database
 * Only returns attendance for subjects the student is actually enrolled in.
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

    // Also try to resolve via app_students table
    $resolvedStudentIds = [$studentId];
    if (!empty($email)) {
        $sStmt = $pdo->prepare("SELECT id FROM app_students WHERE LOWER(email) = LOWER(?) LIMIT 1");
        $sStmt->execute([$email]);
        $sRow = $sStmt->fetch();
        if ($sRow && $sRow['id'] !== $studentId) {
            $resolvedStudentIds[] = $sRow['id'];
        }
    }

    // Build placeholders for resolved student IDs
    $placeholders = implode(',', array_fill(0, count($resolvedStudentIds), '?'));

    // Query sessions for enrolled subjects or where student has attendance record
    $stmt = $pdo->prepare("
        SELECT 
            sess.id AS session_id,
            sess.subject_id,
            sess.subject_code,
            sess.subject_name,
            sess.teacher_email,
            sess.session_date,
            sess.total_present,
            sess.total_enrolled,
            sess.is_lab,
            COALESCE(MAX(r.status), 'absent') AS status,
            MAX(r.marked_by) AS marked_by,
            MAX(r.created_at) AS marked_at
        FROM app_attendance_sessions sess
        LEFT JOIN app_subject_students enroll 
            ON sess.subject_id = enroll.subject_id 
            AND enroll.student_id IN ($placeholders)
        LEFT JOIN app_attendance_records r 
            ON sess.id = r.session_id 
            AND r.student_id IN ($placeholders)
        WHERE enroll.student_id IS NOT NULL OR r.student_id IS NOT NULL
        GROUP BY sess.id, sess.subject_id, sess.subject_code, sess.subject_name, 
                 sess.teacher_email, sess.session_date, sess.total_present, 
                 sess.total_enrolled, sess.is_lab
        ORDER BY sess.session_date DESC
    ");

    // Bind student IDs twice: once for enrollment filter, once for attendance records
    $params = array_merge($resolvedStudentIds, $resolvedStudentIds);
    $stmt->execute($params);
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

