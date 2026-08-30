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
$markedBy    = trim((string)($input['marked_by'] ?? 'totp_qr'));

if (empty($sessionId) || (empty($studentId) && empty($email))) {
    echo json_encode(['success' => false, 'message' => 'Session ID and Student info are required.']);
    exit;
}

try {
    $pdo = AppDatabase::getConnection();

    // Check if session exists
    $sessStmt = $pdo->prepare("SELECT * FROM app_attendance_sessions WHERE id = ? LIMIT 1");
    $sessStmt->execute([$sessionId]);
    $session = $sessStmt->fetch();

    if (!$session) {
        echo json_encode(['success' => false, 'message' => 'Attendance session is no longer active.']);
        exit;
    }

    // Resolve student ID
    if (empty($studentId)) {
        $uStmt = $pdo->prepare("SELECT id, name, student_roll_no FROM app_users WHERE LOWER(email) = LOWER(?) LIMIT 1");
        $uStmt->execute([$email]);
        $uRow = $uStmt->fetch();
        if ($uRow) {
            $studentId = $uRow['id'];
            if (empty($name)) $name = $uRow['name'];
            if (empty($rollNo)) $rollNo = $uRow['student_roll_no'] ?? '';
        }
    }

    // Check if already recorded
    $checkStmt = $pdo->prepare("SELECT id FROM app_attendance_records WHERE session_id = ? AND (student_id = ? OR LOWER(student_name) = LOWER(?)) LIMIT 1");
    $checkStmt->execute([$sessionId, $studentId, $name]);
    $existing = $checkStmt->fetch();

    if ($existing) {
        $updateStmt = $pdo->prepare("UPDATE app_attendance_records SET status = 'present', marked_by = ?, marked_at = CURRENT_TIMESTAMP WHERE id = ?");
        $updateStmt->execute([$markedBy, $existing['id']]);
    } else {
        $recordId = bin2hex(random_bytes(16));
        $insertStmt = $pdo->prepare("
            INSERT INTO app_attendance_records (id, session_id, student_id, student_roll_no, student_name, status, marked_by, marked_at)
            VALUES (?, ?, ?, ?, ?, 'present', ?, CURRENT_TIMESTAMP)
        ");
        $insertStmt->execute([$recordId, $sessionId, $studentId, $rollNo, $name, $markedBy]);
    }

    // Update total turnout in session
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM app_attendance_records WHERE session_id = ? AND status = 'present'");
    $countStmt->execute([$sessionId]);
    $totalPresent = (int)$countStmt->fetchColumn();

    $upSess = $pdo->prepare("UPDATE app_attendance_sessions SET total_present = ? WHERE id = ?");
    $upSess->execute([$totalPresent, $sessionId]);

    echo json_encode([
        'success'       => true,
        'message'       => 'Attendance marked successfully!',
        'total_present' => $totalPresent
    ]);

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Attendance recording failed: ' . $e->getMessage()]);
}
