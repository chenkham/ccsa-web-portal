<?php
declare(strict_types=1);

/**
 * CCSA Smart Attendance - Real-time Student QR Attendance Scan Endpoint
 * Centre for Computer Science and Applications, Dibrugarh University
 */

require_once __DIR__ . '/app_db_config.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$sessionId   = trim((string)($input['session_id'] ?? ''));
$studentId   = trim((string)($input['student_id'] ?? ''));
$email       = trim((string)($input['email'] ?? ''));
$name        = trim((string)($input['name'] ?? ''));
$rollNo      = trim((string)($input['roll_no'] ?? ($input['student_roll_no'] ?? '')));
$subjectId   = trim((string)($input['subject_id'] ?? ''));
$subjectCode = trim((string)($input['subject_code'] ?? ''));
$subjectName = trim((string)($input['subject_name'] ?? ''));
$teacherEmail= trim((string)($input['teacher_email'] ?? ''));
$markedBy    = trim((string)($input['marked_by'] ?? 'totp_qr'));

if (empty($sessionId) || (empty($studentId) && empty($email))) {
    echo json_encode(['success' => false, 'message' => 'Session ID and Student info are required.']);
    exit;
}

try {
    $pdo = AppDatabase::getConnection();

    // 1. Resolve student details across app_users and app_students
    if (!empty($email) || !empty($studentId)) {
        $uStmt = $pdo->prepare("SELECT id, name, email, student_roll_no FROM app_users WHERE (id = ? AND id != '') OR (email != '' AND LOWER(email) = LOWER(?)) LIMIT 1");
        $uStmt->execute([$studentId, $email]);
        $uRow = $uStmt->fetch();
        if ($uRow) {
            if (empty($studentId)) $studentId = $uRow['id'];
            if (empty($name)) $name = $uRow['name'];
            if (empty($rollNo)) $rollNo = $uRow['student_roll_no'] ?? '';
            if (empty($email)) $email = $uRow['email'];
        }

        $sStmt = $pdo->prepare("SELECT id, name, email, class_roll_no FROM app_students WHERE (id = ? AND id != '') OR (email != '' AND LOWER(email) = LOWER(?)) LIMIT 1");
        $sStmt->execute([$studentId, $email]);
        $sRow = $sStmt->fetch();
        if ($sRow) {
            if (empty($name)) $name = $sRow['name'];
            if (empty($rollNo)) $rollNo = $sRow['class_roll_no'] ?? '';
            if (empty($email)) $email = $sRow['email'];
        }
    }

    // 2. Ensure session exists in app_attendance_sessions
    $sessStmt = $pdo->prepare("SELECT * FROM app_attendance_sessions WHERE id = ? LIMIT 1");
    $sessStmt->execute([$sessionId]);
    $session = $sessStmt->fetch();

    if (!$session) {
        // Fetch subject details if not provided
        if (!empty($subjectId) && (empty($subjectCode) || empty($subjectName))) {
            $sSub = $pdo->prepare("SELECT code, name, teacher_email FROM app_subjects WHERE id = ? LIMIT 1");
            $sSub->execute([$subjectId]);
            $subRow = $sSub->fetch();
            if ($subRow) {
                $subjectCode = $subjectCode ?: $subRow['code'];
                $subjectName = $subjectName ?: $subRow['name'];
                $teacherEmail = $teacherEmail ?: $subRow['teacher_email'];
            }
        }

        // Auto-create active session row
        $initSess = $pdo->prepare("
            INSERT INTO app_attendance_sessions 
            (id, subject_id, subject_code, subject_name, session_date, teacher_email, total_enrolled, total_present)
            VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, ?, 0, 0)
            ON DUPLICATE KEY UPDATE id = id
        ");
        $initSess->execute([
            $sessionId,
            $subjectId ?: 'UNKNOWN',
            $subjectCode ?: 'SUB',
            $subjectName ?: 'Lecture Session',
            $teacherEmail ?: ''
        ]);
    } else {
        $subjectId = $session['subject_id'];
    }

    // Auto-enroll student into subject if not already enrolled
    if (!empty($subjectId) && !empty($studentId) && $subjectId !== 'UNKNOWN') {
        $pdo->prepare("INSERT IGNORE INTO app_subject_students (subject_id, student_id) VALUES (?, ?)")->execute([$subjectId, $studentId]);
    }

    // 3. Record or update student attendance
    // NOTE: The WHERE clause must compare columns to bound params only.
    // An earlier version evaluated `? != ''` with bound params, which MySQL
    // rejects on mixed server/column collations (error 1267) and broke live
    // QR scan marking.
    $existing = null;
    if (!empty($identityClauses)) {
        // Strict identity match: same student_id OR same class roll number.
        // Never rewrite an existing row's identifiers (that caused duplicate
        // rows + inflated turnout like 3/2 when one phone scanned repeatedly),
        // and never match on name (two students can share a name).
        $checkStmt = $pdo->prepare(
            'SELECT id, student_id FROM app_attendance_records WHERE session_id = ? AND (student_id = ?' .
            ($rollNo !== '' ? ' OR (student_roll_no != \'\' AND student_roll_no = ?)' : '') .
            ') LIMIT 1'
        );
        $checkParams = [$sessionId, $studentId];
        if ($rollNo !== '') {
            $checkParams[] = $rollNo;
        }
        $checkStmt->execute($checkParams);
        $existing = $checkStmt->fetch();
    }

    if ($existing) {
        // Already counted this student for this session — refresh metadata only.
        $updateStmt = $pdo->prepare("
            UPDATE app_attendance_records 
            SET status = 'present', student_name = ?, marked_by = ?, created_at = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");
        $updateStmt->execute([$name, $markedBy, $existing['id']]);
    } else {
        $recordId = bin2hex(random_bytes(16));
        $insertStmt = $pdo->prepare("
            INSERT INTO app_attendance_records (id, session_id, student_id, student_roll_no, student_name, status, marked_by, created_at)
            VALUES (?, ?, ?, ?, ?, 'present', ?, CURRENT_TIMESTAMP)
            ON DUPLICATE KEY UPDATE
                status = 'present',
                student_name = VALUES(student_name),
                marked_by = VALUES(marked_by),
                created_at = CURRENT_TIMESTAMP
        ");
        $insertStmt->execute([$recordId, $sessionId, $studentId, $rollNo, $name, $markedBy]);
    }

    // 4. Update total turnout from UNIQUE students marked present
    // (COUNT(DISTINCT) protects against any historical duplicate rows)
    $countStmt = $pdo->prepare("
        SELECT COUNT(DISTINCT COALESCE(NULLIF(student_id, ''), student_roll_no))
        FROM app_attendance_records 
        WHERE session_id = ? AND status = 'present'
    ");
    $countStmt->execute([$sessionId]);
    $totalPresent = (int)$countStmt->fetchColumn();

    $upSess = $pdo->prepare("UPDATE app_attendance_sessions SET total_present = ? WHERE id = ?");
    $upSess->execute([$totalPresent, $sessionId]);

    echo json_encode([
        'success'       => true,
        'message'       => 'Attendance marked successfully!',
        'student_id'    => $studentId,
        'session_id'    => $sessionId,
        'total_present' => $totalPresent
    ]);

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Attendance recording failed: ' . $e->getMessage()]);
}

