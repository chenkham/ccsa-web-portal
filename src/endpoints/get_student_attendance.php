<?php
declare(strict_types=1);

/**
 * CCSA Smart Attendance - Fetch Student Attendance & Statistics from Cloud Database
 * Only returns attendance for subjects the student is actually enrolled in or has attendance for.
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

    $studentName = '';
    $studentRollNo = '';
    $studentEmail = strtolower($email);
    $resolvedIds = [];
    if (!empty($studentId)) {
        $resolvedIds[] = $studentId;
    }

    // 1. Look up in app_users
    if (!empty($studentId) || !empty($studentEmail)) {
        $uStmt = $pdo->prepare("SELECT id, name, email, student_roll_no FROM app_users WHERE id = ? OR (email != '' AND LOWER(email) = ?)");
        $uStmt->execute([$studentId, $studentEmail]);
        $uRows = $uStmt->fetchAll();
        foreach ($uRows as $ur) {
            $resolvedIds[] = $ur['id'];
            if (empty($studentEmail) && !empty($ur['email'])) $studentEmail = strtolower($ur['email']);
            if (empty($studentName) && !empty($ur['name'])) $studentName = $ur['name'];
            if (empty($studentRollNo) && !empty($ur['student_roll_no'])) $studentRollNo = $ur['student_roll_no'];
        }
    }

    // 2. Look up in app_students
    if (!empty($studentId) || !empty($studentEmail) || !empty($studentRollNo)) {
        $sStmt = $pdo->prepare("SELECT id, name, email, class_roll_no FROM app_students WHERE id = ? OR (email != '' AND LOWER(email) = ?) OR (class_roll_no != '' AND class_roll_no = ?)");
        $sStmt->execute([$studentId, $studentEmail, $studentRollNo]);
        $sRows = $sStmt->fetchAll();
        foreach ($sRows as $sr) {
            $resolvedIds[] = $sr['id'];
            if (empty($studentEmail) && !empty($sr['email'])) $studentEmail = strtolower($sr['email']);
            if (empty($studentName) && !empty($sr['name'])) $studentName = $sr['name'];
            if (empty($studentRollNo) && !empty($sr['class_roll_no'])) $studentRollNo = $sr['class_roll_no'];
        }
    }

    $resolvedIds = array_values(array_unique(array_filter($resolvedIds)));
    if (empty($resolvedIds) && !empty($studentId)) {
        $resolvedIds = [$studentId];
    }

    // 3. Find all enrolled subject IDs for this student
    $enrolledSubjectIds = [];
    if (!empty($resolvedIds)) {
        $subPlaceholders = implode(',', array_fill(0, count($resolvedIds), '?'));
        $enrolledSubStmt = $pdo->prepare("SELECT DISTINCT subject_id FROM app_subject_students WHERE student_id IN ($subPlaceholders)");
        $enrolledSubStmt->execute($resolvedIds);
        $enrolledSubjectIds = array_column($enrolledSubStmt->fetchAll(), 'subject_id');
    }

    // 4. Query sessions for enrolled subjects OR where student has attendance record
    $sessParams = [];
    $whereParts = [];

    if (!empty($enrolledSubjectIds)) {
        $subIn = implode(',', array_fill(0, count($enrolledSubjectIds), '?'));
        $whereParts[] = "sess.subject_id IN ($subIn)";
        $sessParams = array_merge($sessParams, $enrolledSubjectIds);
    }

    $recWhere = [];
    if (!empty($resolvedIds)) {
        $recWhere[] = "r.student_id IN (" . implode(',', array_fill(0, count($resolvedIds), '?')) . ")";
        $sessParams = array_merge($sessParams, $resolvedIds);
    }
    if (!empty($studentRollNo)) {
        $recWhere[] = "(r.student_roll_no != '' AND r.student_roll_no = ?)";
        $sessParams[] = $studentRollNo;
    }
    if (!empty($studentName)) {
        $recWhere[] = "(r.student_name != '' AND LOWER(r.student_name) = ?)";
        $sessParams[] = strtolower($studentName);
    }

    if (!empty($recWhere)) {
        $whereParts[] = "sess.id IN (SELECT session_id FROM app_attendance_records r WHERE " . implode(' OR ', $recWhere) . ")";
    }

    if (empty($whereParts)) {
        echo json_encode([
            'success'        => true,
            'student_id'     => $studentId,
            'total_sessions' => 0,
            'sessions'       => [],
            'stats'          => (object)[]
        ]);
        exit;
    }

    $sqlWhere = implode(' OR ', $whereParts);

    $sessStmt = $pdo->prepare("
        SELECT 
            sess.id AS session_id,
            sess.subject_id,
            sess.subject_code,
            sess.subject_name,
            sess.teacher_email,
            sess.session_date,
            sess.total_present,
            sess.total_enrolled,
            sess.is_lab
        FROM app_attendance_sessions sess
        WHERE $sqlWhere
        ORDER BY sess.session_date DESC
    ");
    $sessStmt->execute($sessParams);
    $allSessions = $sessStmt->fetchAll();

    // 5. Check attendance records for each session
    $sessions = [];
    foreach ($allSessions as $s) {
        $sid = $s['session_id'];
        $checkParams = [$sid];
        $checkClauses = [];

        if (!empty($resolvedIds)) {
            $checkClauses[] = "student_id IN (" . implode(',', array_fill(0, count($resolvedIds), '?')) . ")";
            $checkParams = array_merge($checkParams, $resolvedIds);
        }
        if (!empty($studentRollNo)) {
            $checkClauses[] = "(student_roll_no != '' AND student_roll_no = ?)";
            $checkParams[] = $studentRollNo;
        }
        if (!empty($studentName)) {
            $checkClauses[] = "(student_name != '' AND LOWER(student_name) = ?)";
            $checkParams[] = strtolower($studentName);
        }

        $rec = null;
        if (!empty($checkClauses)) {
            $rStmt = $pdo->prepare("
                SELECT * FROM app_attendance_records 
                WHERE session_id = ? AND (" . implode(' OR ', $checkClauses) . ")
                ORDER BY CASE WHEN status = 'present' THEN 0 ELSE 1 END, created_at DESC
                LIMIT 1
            ");
            $rStmt->execute($checkParams);
            $rec = $rStmt->fetch();
        }

        $status = ($rec && strtolower((string)$rec['status']) === 'present') ? 'present' : 'absent';
        $markedBy = $rec['marked_by'] ?? ($status === 'present' ? 'totp_qr' : 'system');
        $markedAt = $rec['created_at'] ?? $s['session_date'];

        $s['status'] = $status;
        $s['marked_by'] = $markedBy;
        $s['marked_at'] = $markedAt;
        $sessions[] = $s;
    }

    // 6. Calculate subject-wise class statistics
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
        'success'        => true,
        'student_id'     => $studentId,
        'total_sessions' => count($sessions),
        'sessions'       => $sessions,
        'stats'          => $subjectStats
    ]);

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Query error: ' . $e->getMessage()]);
}

