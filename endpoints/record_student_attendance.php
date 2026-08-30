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

    // 1. Resolve student details if missing
    if (empty($studentId) || empty($name) || empty($rollNo)) {
        if (!empty($email)) {
            $uStmt = $pdo->prepare("SELECT id, name, student_roll_no FROM app_users WHERE LOWER(email) = LOWER(?) LIMIT 1");
            $uStmt->execute([$email]);
            $uRow = $uStmt->fetch();
            if ($uRow) {
                if (empty($studentId)) $studentId = $uRow['id'];
                if (empty($name)) $name = $uRow['name'];
                if (empty($rollNo)) $rollNo = $uRow['student_roll_no'] ?? '';
            }
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
            VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, ?, 0, 1)
            ON DUPLICATE KEY UPDATE id = id
        ");
        $initSess->execute([
            $sessionId,
            $subjectId ?: 'UNKNOWN',
            $subjectCode ?: 'SUB',
            $subjectName ?: 'Lecture Session',
            $teacherEmail ?: ''
        ]);
    }

    // 3. Record or update student attendance
    $checkStmt = $pdo->prepare("SELECT id FROM app_attendance_records WHERE session_id = ? AND student_id = ? LIMIT 1");
    $checkStmt->execute([$sessionId, $studentId]);
    $existing = $checkStmt->fetch();

    if ($existing) {
        $updateStmt = $pdo->prepare("UPDATE app_attendance_records SET status = 'present', marked_by = ?, created_at = CURRENT_TIMESTAMP WHERE id = ?");
        $updateStmt->execute([$markedBy, $existing['id']]);
    } else {
        $recordId = bin2hex(random_bytes(16));
        $insertStmt = $pdo->prepare("
            INSERT INTO app_attendance_records (id, session_id, student_id, student_roll_no, student_name, status, marked_by, created_at)
            VALUES (?, ?, ?, ?, ?, 'present', ?, CURRENT_TIMESTAMP)
        ");
        $insertStmt->execute([$recordId, $sessionId, $studentId, $rollNo, $name, $markedBy]);
    }

    // 4. Update total turnout count in session
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM app_attendance_records WHERE session_id = ? AND status = 'present'");
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
    echo json_encode(['success' => false, 'message' => 'Attendance recording failed: ' . $e->getMessage()]);
}

