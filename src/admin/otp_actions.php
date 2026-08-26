<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');
header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/security-validator.php';
require_once __DIR__ . '/../includes/Security.php';
require_once __DIR__ . '/../includes/Mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
    exit;
}

// 1. Parse JSON input or Form POST
$rawInput = file_get_contents('php://input');
$jsonInput = json_decode((string)$rawInput, true);
$data = is_array($jsonInput) ? $jsonInput : $_POST;

$action = (string)($data['action'] ?? '');
$csrfToken = (string)($data['csrf_token'] ?? '');

// 2. Validate CSRF token
if (empty($csrfToken) || (!SecurityValidator::validateCSRFToken($csrfToken) && !Security::validateCsrfToken($csrfToken))) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Security token expired or invalid. Please refresh the page.']);
    exit;
}

if (!isset($pdo) || $pdo === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection unavailable.']);
    exit;
}

// -------------------------------------------------------------
// Action 1: SEND LOGIN OTP
// -------------------------------------------------------------
if ($action === 'send_login_otp') {
    $email = strtolower(trim((string)($data['email'] ?? '')));

    if (!Security::validateEmail($email)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Please provide a valid administrator email address.']);
        exit;
    }

    // Rate Limiting: Max 3 requests per 5 minutes per email/IP
    $rateKey = 'otp_req_' . md5($email . '_' . ($_SERVER['REMOTE_ADDR'] ?? ''));
    if (!Security::checkRateLimit($rateKey, 3, 300)) {
        http_response_code(429);
        echo json_encode(['success' => false, 'error' => 'Too many OTP requests. Please wait 5 minutes before trying again.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare('SELECT id, name, email, status FROM admin_users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            Security::recordFailedAttempt($rateKey, 300);
            Security::logAudit($pdo, 'OTP_REQUEST_UNKNOWN_EMAIL', "OTP requested for unregistered email: $email", $email);
            usleep(200000);
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'No active administrator account found with this email.']);
            exit;
        }

        if (($user['status'] ?? 'active') !== 'active') {
            Security::logAudit($pdo, 'OTP_REQUEST_INACTIVE_USER', "OTP requested for inactive account: $email", $email);
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'This administrator account is currently inactive.']);
            exit;
        }

        // Clean up expired or older active login OTPs for this email
        $del = $pdo->prepare("DELETE FROM admin_otps WHERE email = ? AND purpose = 'login'");
        $del->execute([$email]);

        // Generate 6-digit OTP code & hash
        $otpCode = Security::generateOtp();
        $otpHash = Security::hashOtp($otpCode);

        // Store OTP with 10-minute expiry
        $ins = $pdo->prepare("INSERT INTO admin_otps (email, otp_hash, purpose, attempts, expires_at, created_at) 
            VALUES (?, ?, 'login', 0, DATE_ADD(NOW(), INTERVAL 10 MINUTE), NOW())");
        $ins->execute([$email, $otpHash]);

        // Send OTP Email via Hostinger Mailer / SMTP
        $mailResult = Mailer::sendOtpEmail($email, $otpCode, 'login', $user['name'] ?? 'Administrator');

        Security::logAudit($pdo, 'OTP_SENT_LOGIN', "Login OTP dispatched to $email", $email);

        echo json_encode([
            'success' => true,
            'message' => 'A 6-digit verification code has been sent to your email address.',
            'expires_in' => 600
        ]);
        exit;
    } catch (Throwable $e) {
        error_log('Send Login OTP Error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to generate verification code. Please try again.']);
        exit;
    }
}

// -------------------------------------------------------------
// Action 2: VERIFY LOGIN OTP
// -------------------------------------------------------------
if ($action === 'verify_login_otp') {
    $email = strtolower(trim((string)($data['email'] ?? '')));
    $otp = trim((string)($data['otp'] ?? ''));

    if (!Security::validateEmail($email) || strlen($otp) !== 6 || !ctype_digit($otp)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Please enter the full 6-digit verification code.']);
        exit;
    }

    $verifyRateKey = 'otp_verify_' . md5($email);
    if (!Security::checkRateLimit($verifyRateKey, 5, 300)) {
        http_response_code(429);
        echo json_encode(['success' => false, 'error' => 'Too many invalid attempts. Please wait 5 minutes or request a new code.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, otp_hash, attempts, expires_at FROM admin_otps 
            WHERE email = ? AND purpose = 'login' AND expires_at > NOW() ORDER BY id DESC LIMIT 1");
        $stmt->execute([$email]);
        $otpRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$otpRow) {
            Security::recordFailedAttempt($verifyRateKey, 300);
            Security::logAudit($pdo, 'OTP_EXPIRED_OR_NOT_FOUND', "Expired or missing OTP verification attempt for $email", $email);
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Verification code expired or not found. Please request a new code.']);
            exit;
        }

        $otpId = (int)$otpRow['id'];
        $attempts = (int)$otpRow['attempts'] + 1;

        if ($attempts > 5) {
            $del = $pdo->prepare("DELETE FROM admin_otps WHERE id = ?");
            $del->execute([$otpId]);
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Maximum verification attempts exceeded. Please request a fresh code.']);
            exit;
        }

        // Increment attempts counter
        $updAttempts = $pdo->prepare("UPDATE admin_otps SET attempts = ? WHERE id = ?");
        $updAttempts->execute([$attempts, $otpId]);

        // Verify the code
        if (!Security::verifyOtp($otp, $otpRow['otp_hash'])) {
            Security::recordFailedAttempt($verifyRateKey, 300);
            Security::logAudit($pdo, 'OTP_VERIFICATION_FAILED', "Incorrect OTP entered for $email (Attempt $attempts/5)", $email);
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => "Incorrect verification code. ($attempts/5 attempts used)"]);
            exit;
        }

        // Valid OTP! Delete OTP single-use record
        $del = $pdo->prepare("DELETE FROM admin_otps WHERE id = ?");
        $del->execute([$otpId]);

        // Fetch User and Start Authenticated Session
        $userStmt = $pdo->prepare("SELECT id, name, email, role, status FROM admin_users WHERE email = ? LIMIT 1");
        $userStmt->execute([$email]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || ($user['status'] ?? 'active') !== 'active') {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'This account is inactive.']);
            exit;
        }

        Security::resetRateLimit($verifyRateKey);
        Security::resetRateLimit('admin_login');

        Session::regenerate();
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['name'] ?? 'Administrator';
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'] ?? 'admin';
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $_SESSION['last_activity'] = time();

        $updLogin = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
        $updLogin->execute([$user['id']]);

        Security::logAudit($pdo, 'LOGIN_SUCCESS_OTP', "Administrator signed in securely via Email OTP verification.", $user['email']);

        echo json_encode([
            'success' => true,
            'message' => 'Authentication successful! Redirecting...',
            'redirect' => '/admin/index.php'
        ]);
        exit;
    } catch (Throwable $e) {
        error_log('Verify Login OTP Error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Verification server error. Please try again.']);
        exit;
    }
}

// -------------------------------------------------------------
// Action 3: SEND PASSWORD RESET / CHANGE OTP
// -------------------------------------------------------------
if ($action === 'send_reset_otp') {
    $email = strtolower(trim((string)($data['email'] ?? '')));

    if (!Security::validateEmail($email)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Please provide a valid administrator email address.']);
        exit;
    }

    $rateKey = 'reset_otp_req_' . md5($email);
    if (!Security::checkRateLimit($rateKey, 3, 300)) {
        http_response_code(429);
        echo json_encode(['success' => false, 'error' => 'Too many requests. Please wait 5 minutes before trying again.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare('SELECT id, name, email, status FROM admin_users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'No administrator account found with this email.']);
            exit;
        }

        // Clean up prior reset OTPs
        $del = $pdo->prepare("DELETE FROM admin_otps WHERE email = ? AND purpose = 'password_reset'");
        $del->execute([$email]);

        $otpCode = Security::generateOtp();
        $otpHash = Security::hashOtp($otpCode);

        $ins = $pdo->prepare("INSERT INTO admin_otps (email, otp_hash, purpose, attempts, expires_at, created_at) 
            VALUES (?, ?, 'password_reset', 0, DATE_ADD(NOW(), INTERVAL 10 MINUTE), NOW())");
        $ins->execute([$email, $otpHash]);

        Mailer::sendOtpEmail($email, $otpCode, 'password_reset', $user['name'] ?? 'Administrator');

        Security::logAudit($pdo, 'OTP_SENT_PASSWORD_RESET', "Password reset OTP dispatched to $email", $email);

        echo json_encode([
            'success' => true,
            'message' => 'A password reset verification code has been sent to your email.',
            'expires_in' => 600
        ]);
        exit;
    } catch (Throwable $e) {
        error_log('Send Reset OTP Error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to send reset code. Please try again.']);
        exit;
    }
}

// -------------------------------------------------------------
// Action 4: VERIFY OTP AND RESET PASSWORD
// -------------------------------------------------------------
if ($action === 'verify_reset_password') {
    $email = strtolower(trim((string)($data['email'] ?? '')));
    $otp = trim((string)($data['otp'] ?? ''));
    $newPassword = (string)($data['new_password'] ?? '');
    $confirmPassword = (string)($data['confirm_password'] ?? '');

    if (!Security::validateEmail($email) || strlen($otp) !== 6 || !ctype_digit($otp)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Please provide a valid email and 6-digit verification code.']);
        exit;
    }

    if (empty($newPassword) || strlen($newPassword) < 8) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'New password must be at least 8 characters long.']);
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'New password and confirmation password do not match.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, otp_hash, attempts FROM admin_otps 
            WHERE email = ? AND purpose = 'password_reset' AND expires_at > NOW() ORDER BY id DESC LIMIT 1");
        $stmt->execute([$email]);
        $otpRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$otpRow) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Reset code expired or not found. Please request a new code.']);
            exit;
        }

        $otpId = (int)$otpRow['id'];
        $attempts = (int)$otpRow['attempts'] + 1;

        if ($attempts > 5) {
            $pdo->prepare("DELETE FROM admin_otps WHERE id = ?")->execute([$otpId]);
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Maximum attempts exceeded. Please request a fresh reset code.']);
            exit;
        }

        $pdo->prepare("UPDATE admin_otps SET attempts = ? WHERE id = ?")->execute([$attempts, $otpId]);

        if (!Security::verifyOtp($otp, $otpRow['otp_hash'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => "Incorrect verification code. ($attempts/5 attempts used)"]);
            exit;
        }

        // Code is verified! Delete OTP row
        $pdo->prepare("DELETE FROM admin_otps WHERE id = ?")->execute([$otpId]);

        // Update password hash in admin_users
        $newHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $upd = $pdo->prepare("UPDATE admin_users SET password = ?, updatedAt = NOW() WHERE email = ?");
        $upd->execute([$newHash, $email]);

        Security::logAudit($pdo, 'PASSWORD_RESET_SUCCESS', "Password successfully reset via verified Email OTP.", $email);

        echo json_encode([
            'success' => true,
            'message' => 'Password reset successfully! You can now sign in with your new password or OTP.'
        ]);
        exit;
    } catch (Throwable $e) {
        error_log('Verify Reset Password Error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Server error while resetting password.']);
        exit;
    }
}

// Default Fallback
http_response_code(400);
echo json_encode(['success' => false, 'error' => 'Invalid action specified.']);
exit;
