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

/**
 * Storage Helpers (MySQL Primary, local cache fallback for offline testing)
 */
function fetchAdminUser(string $email, ?PDO $pdo): ?array {
    if ($pdo !== null) {
        $stmt = $pdo->prepare('SELECT id, name, email, role, status FROM admin_users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    if (strcasecmp($email, 'ccsduoffice@gmail.com') === 0) {
        return ['id' => 1, 'name' => 'CCSA Super Administrator', 'email' => 'ccsduoffice@gmail.com', 'role' => 'super_admin', 'status' => 'active'];
    }
    return null;
}

function storeOtpRecord(string $email, string $otpHash, string $purpose, ?PDO $pdo): bool {
    if ($pdo !== null) {
        $del = $pdo->prepare("DELETE FROM admin_otps WHERE email = ? AND purpose = ?");
        $del->execute([$email, $purpose]);
        $ins = $pdo->prepare("INSERT INTO admin_otps (email, otp_hash, purpose, attempts, expires_at, created_at) 
            VALUES (?, ?, ?, 0, DATE_ADD(NOW(), INTERVAL 10 MINUTE), NOW())");
        return $ins->execute([$email, $otpHash, $purpose]);
    }
    $cacheDir = __DIR__ . '/../cache';
    if (!is_dir($cacheDir)) @mkdir($cacheDir, 0755, true);
    $file = $cacheDir . '/local_otps.json';
    $otps = file_exists($file) ? (json_decode((string)file_get_contents($file), true) ?: []) : [];
    $otps[$email . '_' . $purpose] = [
        'id' => time(),
        'email' => $email,
        'otp_hash' => $otpHash,
        'purpose' => $purpose,
        'attempts' => 0,
        'expires_at' => time() + 600,
        'created_at' => time()
    ];
    file_put_contents($file, json_encode($otps, JSON_PRETTY_PRINT));
    return true;
}

function fetchOtpRecord(string $email, string $purpose, ?PDO $pdo): ?array {
    if ($pdo !== null) {
        $stmt = $pdo->prepare("SELECT id, otp_hash, attempts, expires_at FROM admin_otps 
            WHERE email = ? AND purpose = ? AND expires_at > NOW() ORDER BY id DESC LIMIT 1");
        $stmt->execute([$email, $purpose]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    $file = __DIR__ . '/../cache/local_otps.json';
    if (!file_exists($file)) return null;
    $otps = json_decode((string)file_get_contents($file), true) ?: [];
    $row = $otps[$email . '_' . $purpose] ?? null;
    if (!$row || ($row['expires_at'] ?? 0) < time()) return null;
    return $row;
}

function removeOtpRecord(string $email, string $purpose, ?PDO $pdo): void {
    if ($pdo !== null) {
        $del = $pdo->prepare("DELETE FROM admin_otps WHERE email = ? AND purpose = ?");
        $del->execute([$email, $purpose]);
    } else {
        $file = __DIR__ . '/../cache/local_otps.json';
        if (file_exists($file)) {
            $otps = json_decode((string)file_get_contents($file), true) ?: [];
            unset($otps[$email . '_' . $purpose]);
            file_put_contents($file, json_encode($otps, JSON_PRETTY_PRINT));
        }
    }
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
        $user = fetchAdminUser($email, $pdo);

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

        // Generate 6-digit OTP code & hash
        $otpCode = Security::generateOtp();
        $otpHash = Security::hashOtp($otpCode);

        // Store OTP with 10-minute expiry
        storeOtpRecord($email, $otpHash, 'login', $pdo);

        // Send OTP Email via Hostinger Mailer / SMTP
        Mailer::sendOtpEmail($email, $otpCode, 'login', $user['name'] ?? 'Administrator');

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
        $otpRow = fetchOtpRecord($email, 'login', $pdo);

        if (!$otpRow) {
            Security::recordFailedAttempt($verifyRateKey, 300);
            Security::logAudit($pdo, 'OTP_EXPIRED_OR_NOT_FOUND', "Expired or missing OTP verification attempt for $email", $email);
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Verification code expired or not found. Please request a new code.']);
            exit;
        }

        $attempts = (int)($otpRow['attempts'] ?? 0) + 1;
        if ($attempts > 5) {
            removeOtpRecord($email, 'login', $pdo);
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Maximum verification attempts exceeded. Please request a fresh code.']);
            exit;
        }

        if (!Security::verifyOtp($otp, (string)$otpRow['otp_hash'])) {
            Security::recordFailedAttempt($verifyRateKey, 300);
            Security::logAudit($pdo, 'OTP_VERIFICATION_FAILED', "Incorrect OTP entered for $email (Attempt $attempts/5)", $email);
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => "Incorrect verification code. ($attempts/5 attempts used)"]);
            exit;
        }

        // Valid OTP! Delete OTP single-use record
        removeOtpRecord($email, 'login', $pdo);

        // Fetch User and Start Authenticated Session
        $user = fetchAdminUser($email, $pdo);

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

        if ($pdo !== null) {
            $updLogin = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
            $updLogin->execute([$user['id']]);
        }

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
        $user = fetchAdminUser($email, $pdo);

        if (!$user) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'No administrator account found with this email.']);
            exit;
        }

        $otpCode = Security::generateOtp();
        $otpHash = Security::hashOtp($otpCode);

        storeOtpRecord($email, $otpHash, 'password_reset', $pdo);

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
        $otpRow = fetchOtpRecord($email, 'password_reset', $pdo);

        if (!$otpRow) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Reset code expired or not found. Please request a new code.']);
            exit;
        }

        if (!Security::verifyOtp($otp, (string)$otpRow['otp_hash'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => "Incorrect verification code."]);
            exit;
        }

        // Code is verified! Delete OTP row
        removeOtpRecord($email, 'password_reset', $pdo);

        // Update password hash in admin_users
        if ($pdo !== null) {
            $newHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
            $upd = $pdo->prepare("UPDATE admin_users SET password = ?, updatedAt = NOW() WHERE email = ?");
            $upd->execute([$newHash, $email]);
        }

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
