<?php
declare(strict_types=1);

require_once __DIR__ . '/app_db_config.php';

if (file_exists(__DIR__ . '/../includes/Mailer.php')) {
    require_once __DIR__ . '/../includes/Mailer.php';
} elseif (file_exists(__DIR__ . '/includes/Mailer.php')) {
    require_once __DIR__ . '/includes/Mailer.php';
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$email = trim((string)($input['email'] ?? ''));
$name  = trim((string)($input['name'] ?? 'User'));
$type  = trim((string)($input['type'] ?? 'signup'));

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'A valid email address is required.']);
    exit;
}

try {
    $pdo = AppDatabase::getConnection();

    // 1. Generate 6-digit numeric OTP code
    $otp = (string)random_int(100000, 999999);

    // 2. Invalidate older OTPs for this email and save new OTP with 5-minute expiry
    $pdo->prepare('UPDATE app_otp_verifications SET is_used = 1 WHERE email = ? AND is_used = 0')->execute([$email]);

    $stmt = $pdo->prepare('INSERT INTO app_otp_verifications (email, otp_code, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 5 MINUTE))');
    $stmt->execute([$email, $otp]);

    // 3. Compose clean CCSA Branded Email
    $subject = "CCSA Smart Attendance - Your Verification Code: {$otp}";
    $htmlBody = "
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset='utf-8'>
      <style>
        body { font-family: 'Segoe UI', Helvetica, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 20px; color: #1e293b; }
        .container { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .header { background: #0f172a; padding: 24px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 18px; font-weight: 700; letter-spacing: 0.5px; }
        .header p { margin: 4px 0 0 0; font-size: 12px; color: #94a3b8; }
        .content { padding: 32px 24px; text-align: center; }
        .greeting { font-size: 15px; font-weight: 600; margin-bottom: 8px; color: #0f172a; text-align: left; }
        .desc { font-size: 13px; color: #64748b; line-height: 1.5; margin-bottom: 24px; text-align: left; }
        .otp-box { background: #f1f5f9; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 18px; font-size: 32px; font-weight: 800; letter-spacing: 8px; color: #0f172a; margin-bottom: 24px; font-family: monospace; display: inline-block; width: 80%; }
        .timer { font-size: 12px; color: #ef4444; font-weight: 600; margin-bottom: 24px; }
        .footer { background: #f8fafc; padding: 16px; font-size: 11px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; }
      </style>
    </head>
    <body>
      <div class='container'>
        <div class='header'>
          <h1>Centre for Computer Science and Applications</h1>
          <p>Dibrugarh University • Smart Attendance System</p>
        </div>
        <div class='content'>
          <div class='greeting'>Hello " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ",</div>
          <div class='desc'>Use the 6-digit verification code below to complete your account setup on the CCSA Smart Attendance app:</div>
          <div class='otp-box'>" . htmlspecialchars($otp, ENT_QUOTES, 'UTF-8') . "</div>
          <div class='timer'>⚠️ This code will expire in 5 minutes. Do not share it with anyone.</div>
        </div>
        <div class='footer'>
          © " . date('Y') . " Centre for Computer Science & Applications, Dibrugarh University, Assam.
        </div>
      </div>
    </body>
    </html>
    ";

    // 4. Send email using Mailer or PHP mail() fallback
    $sent = false;
    $errorMsg = '';
    
    // Try Socket SMTP first
    try {
        $res = Mailer::sendViaSmtp($email, $subject, $htmlBody, 'noreply@ccsdu.in', true);
        if ($res['success']) {
            $sent = true;
        } else {
            $errorMsg = $res['error'] ?? 'SMTP Error';
        }
    } catch (\Throwable $e) {
        $errorMsg = $e->getMessage();
    }

    // Fallback to PHP native mail()
    if (!$sent) {
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: CCSA Attendance Portal <noreply@ccsdu.in>\r\n";
        $headers .= "Reply-To: noreply@ccsdu.in\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        $sent = @mail($email, $subject, $htmlBody, $headers);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Verification code sent to ' . $email,
        'email'   => $email
    ]);

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to send OTP: ' . $e->getMessage()
    ]);
}
