<?php
declare(strict_types=1);

/**
 * CCSA Smart Attendance - Cloud Login & Authentication
 * Centre for Computer Science and Applications, Dibrugarh University
 */

require_once __DIR__ . '/app_db_config.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$email    = trim((string)($input['email'] ?? ''));
$password = (string)($input['password'] ?? '');

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and Password are required.']);
    exit;
}

try {
    $pdo = AppDatabase::getConnection();

    $stmt = $pdo->prepare('SELECT * FROM app_users WHERE LOWER(email) = LOWER(?) LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'No account found with this email. Please sign up first.']);
        exit;
    }

    // Verify Password against stored hash, with one-time legacy plaintext fallback
    $storedPass   = (string)($user['password'] ?? '');
    $hashMatched  = password_verify($password, $storedPass);
    $legacyMatched = !$hashMatched && $storedPass !== '' && hash_equals($storedPass, $password);
    $validPass    = $hashMatched || $legacyMatched;

    // Transparently migrate legacy plaintext credentials to bcrypt on successful login
    if ($legacyMatched) {
        $rehashed = password_hash($password, PASSWORD_BCRYPT);
        $pdo->prepare('UPDATE app_users SET password = ? WHERE id = ?')->execute([$rehashed, $user['id']]);
        $user['password'] = $rehashed;
    }

    if (!$validPass) {
        echo json_encode(['success' => false, 'message' => 'Incorrect password. Please verify your credentials.']);
        exit;
    }

    // Fetch latest roll numbers from app_students table if student
    $classRoll = $user['student_roll_no'] ?? '';
    $examRoll  = $user['du_exam_roll_no'] ?? '';

    // Auto-bind device ID on student login if not yet bound
    $deviceId = trim((string)($input['device_id'] ?? ''));
    if (!empty($deviceId) && empty($user['device_id']) && $user['role'] === 'student') {
        $pdo->prepare('UPDATE app_users SET device_id = ? WHERE id = ?')->execute([$deviceId, $user['id']]);
        $pdo->prepare('UPDATE app_students SET device_id = ? WHERE id = ? OR LOWER(email) = LOWER(?)')->execute([$deviceId, $user['id'], $email]);
        $user['device_id'] = $deviceId;
    }

    if ($user['role'] === 'student') {
        // Ensure app_students ID matches app_users ID
        try {
            $pdo->prepare('UPDATE app_students SET id = ? WHERE LOWER(email) = LOWER(?) AND id != ?')->execute([$user['id'], $email, $user['id']]);
        } catch (\Throwable $e) {}

        $stdStmt = $pdo->prepare('SELECT class_roll_no, du_exam_roll_no FROM app_students WHERE LOWER(email) = LOWER(?) OR id = ? LIMIT 1');
        $stdStmt->execute([$email, $user['id']]);
        $stdRow = $stdStmt->fetch();
        if ($stdRow) {
            if (!empty($stdRow['class_roll_no'])) $classRoll = $stdRow['class_roll_no'];
            if (!empty($stdRow['du_exam_roll_no'])) $examRoll = $stdRow['du_exam_roll_no'];
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Login successful!',
        'user'    => [
            'id'               => $user['id'],
            'name'             => $user['name'],
            'email'            => $user['email'],
            'role'             => $user['role'],
            'student_roll_no'  => $classRoll,
            'du_exam_roll_no'  => $examRoll,
            'course'           => $user['course'],
            'semester'         => $user['semester'] ? (int)$user['semester'] : null,
            'designation'      => $user['designation'],
            'department'       => $user['department'],
            'device_id'        => $user['device_id'],
            'profile_completed'=> (bool)$user['profile_completed']
        ]
    ]);

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Login error: ' . $e->getMessage()]);
}
