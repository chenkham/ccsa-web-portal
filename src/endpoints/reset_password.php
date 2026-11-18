<?php
declare(strict_types=1);

/**
 * CCSA Smart Attendance - Reset Password in Hostinger MySQL
 * Centre for Computer Science and Applications, Dibrugarh University
 */

require_once __DIR__ . '/app_db_config.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$email       = trim((string)($input['email'] ?? ''));
$otp         = trim((string)($input['otp'] ?? ''));
$newPassword = (string)($input['new_password'] ?? '');

if (empty($email) || empty($otp) || empty($newPassword)) {
    echo json_encode(['success' => false, 'message' => 'Email, OTP, and New Password are required.']);
    exit;
}

if (strlen($newPassword) < 4) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 4 characters long.']);
    exit;
}

try {
    $pdo = AppDatabase::getConnection();

    // Verify OTP in app_otp_verifications
    $stmt = $pdo->prepare("
        SELECT id, expires_at, is_used, created_at
        FROM app_otp_verifications
        WHERE LOWER(email) = LOWER(?) AND otp_code = ?
        ORDER BY id DESC LIMIT 1
    ");
    $stmt->execute([$email, $otp]);
    $row = $stmt->fetch();

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'Invalid OTP code. Please request a new code.']);
        exit;
    }

    if ((int)$row['is_used'] === 1) {
        echo json_encode(['success' => false, 'message' => 'This OTP code has already been used.']);
        exit;
    }

    if (!empty($row['expires_at']) && strtotime($row['expires_at']) < time()) {
        echo json_encode(['success' => false, 'message' => 'This OTP code has expired. Please request a new code.']);
        exit;
    }

    // Mark OTP as used
    $updateOtp = $pdo->prepare("UPDATE app_otp_verifications SET is_used = 1 WHERE id = ?");
    $updateOtp->execute([$row['id']]);

    // Hash password and update app_users
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    $updateUser = $pdo->prepare("UPDATE app_users SET password = ? WHERE LOWER(email) = LOWER(?)");
    $updateUser->execute([$hashedPassword, $email]);

    echo json_encode([
        'success' => true,
        'message' => 'Password updated successfully in database! You can now sign in.'
    ]);

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to reset password: ' . $e->getMessage()]);
}
