<?php
declare(strict_types=1);

/**
 * CCSA Smart Attendance - Verify Reset OTP in Hostinger MySQL
 * Centre for Computer Science and Applications, Dibrugarh University
 */

require_once __DIR__ . '/app_db_config.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$email = trim((string)($input['email'] ?? ''));
$otp   = trim((string)($input['otp'] ?? ''));

if (empty($email) || empty($otp)) {
    echo json_encode(['success' => false, 'message' => 'Email and OTP code are required.']);
    exit;
}

try {
    $pdo = AppDatabase::getConnection();

    // Check if user exists in app_users
    $userStmt = $pdo->prepare("SELECT id FROM app_users WHERE LOWER(email) = LOWER(?) LIMIT 1");
    $userStmt->execute([$email]);
    if (!$userStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'No account found with this email address.']);
        exit;
    }

    // Check OTP in app_otp_verifications
    $stmt = $pdo->prepare("
        SELECT id, expires_at, is_used, created_at
        FROM app_otp_verifications
        WHERE LOWER(email) = LOWER(?) AND otp_code = ?
        ORDER BY id DESC LIMIT 1
    ");
    $stmt->execute([$email, $otp]);
    $row = $stmt->fetch();

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'Invalid OTP code. Please check your email.']);
        exit;
    }

    if ((int)$row['is_used'] === 1) {
        echo json_encode(['success' => false, 'message' => 'This OTP code has already been used. Please request a new one.']);
        exit;
    }

    // 15-minute validity window
    $expiresAt = strtotime($row['expires_at']);
    $createdAt = strtotime($row['created_at']);
    $now = time();

    if ($expiresAt && $now > $expiresAt && ($now - $createdAt > 900)) {
        echo json_encode(['success' => false, 'message' => 'This OTP code has expired. Please request a new code.']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'OTP verified successfully! You can now set your new password.'
    ]);

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Verification error: ' . $e->getMessage()]);
}
