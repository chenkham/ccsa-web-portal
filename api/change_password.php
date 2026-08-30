<?php
declare(strict_types=1);

/**
 * CCSA Smart Attendance - Change Password with Current Password Verification
 * Centre for Computer Science and Applications, Dibrugarh University
 */

require_once __DIR__ . '/app_db_config.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$email           = trim((string)($input['email'] ?? ''));
$currentPassword = (string)($input['current_password'] ?? '');
$newPassword     = (string)($input['new_password'] ?? '');

if (empty($email) || empty($currentPassword) || empty($newPassword)) {
    echo json_encode(['success' => false, 'message' => 'Email, current password, and new password are required.']);
    exit;
}

if (strlen($newPassword) < 4) {
    echo json_encode(['success' => false, 'message' => 'New password must be at least 4 characters long.']);
    exit;
}

try {
    $pdo = AppDatabase::getConnection();

    $stmt = $pdo->prepare("SELECT password FROM app_users WHERE LOWER(email) = LOWER(?) LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Account not found.']);
        exit;
    }

    $valid = password_verify($currentPassword, $user['password']) || ($currentPassword === $user['password']);

    if (!$valid) {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
        exit;
    }

    $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
    $upStmt = $pdo->prepare("UPDATE app_users SET password = ? WHERE LOWER(email) = LOWER(?)");
    $upStmt->execute([$newHash, $email]);

    echo json_encode([
        'success' => true,
        'message' => 'Password changed successfully in database!'
    ]);

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to change password: ' . $e->getMessage()]);
}
