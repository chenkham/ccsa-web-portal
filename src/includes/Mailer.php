<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Security.php';

/**
 * Socket SMTP Mailer and Notification Dispatcher
 */
class Mailer
{
    /**
     * Retrieves active SMTP configuration.
     */
    public static function getSmtpConfig(): array
    {
        $configFile = __DIR__ . '/../cache/smtp_config.json';
        if (file_exists($configFile)) {
            $cfg = json_decode((string)file_get_contents($configFile), true);
            if (is_array($cfg) && !empty($cfg['host'])) {
                return $cfg;
            }
        }
        return [
            'host'       => getenv('SMTP_HOST') ?: '',
            'port'       => (int)(getenv('SMTP_PORT') ?: 587),
            'secure'     => getenv('SMTP_SECURE') ?: 'tls',
            'username'   => getenv('SMTP_USER') ?: '',
            'password'   => getenv('SMTP_PASS') ?: '',
            'from_email' => getenv('MAIL_FROM') ?: 'noreply@ccsdu.in',
            'from_name'  => 'CCSA Portal'
        ];
    }

    public static function saveSmtpConfig(array $data): bool
    {
        $cacheDir = __DIR__ . '/../cache';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        $cfg = [
            'host' => trim((string)($data['host'] ?? '')),
            'port' => (int)($data['port'] ?? 587),
            'secure' => trim((string)($data['secure'] ?? 'tls')),
            'username' => trim((string)($data['username'] ?? '')),
            'password' => (string)($data['password'] ?? ''),
            'from_email' => trim((string)($data['from_email'] ?? 'noreply@ccsdu.in')),
            'from_name' => trim((string)($data['from_name'] ?? 'CCSA Portal'))
        ];
        return file_put_contents($cacheDir . '/smtp_config.json', json_encode($cfg, JSON_PRETTY_PRINT)) !== false;
    }

    public static function getRecipients(): array
    {
        $recipients = [];
        $file = __DIR__ . '/../cache/notification_recipients.json';
        if (file_exists($file)) {
            $list = json_decode((string)file_get_contents($file), true) ?: [];
            foreach ($list as $em) {
                $em = trim((string)$em);
                if (filter_var($em, FILTER_VALIDATE_EMAIL) && !in_array($em, $recipients, true)) {
                    $recipients[] = $em;
                }
            }
        }
        return array_values(array_unique($recipients));
    }

    public static function logDispatch(string $to, bool $success, string $details): void
    {
        $cacheDir = __DIR__ . '/../cache';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        $logFile = $cacheDir . '/mail_dispatch_log.json';
        $logs = file_exists($logFile) ? (json_decode((string)file_get_contents($logFile), true) ?: []) : [];
        array_unshift($logs, [
            'timestamp' => date('Y-m-d H:i:s'),
            'to' => $to,
            'success' => $success,
            'details' => $details
        ]);
        $logs = array_slice($logs, 0, 50); // Keep last 50
        file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT));
    }

    public static function getRecentLogs(): array
    {
        $logFile = __DIR__ . '/../cache/mail_dispatch_log.json';
        if (file_exists($logFile)) {
            return json_decode((string)file_get_contents($logFile), true) ?: [];
        }
        return [];
    }

    /**
     * Send email via Socket SMTP (Supports TLS/SSL)
     */
    public static function sendViaSmtp(string $to, string $subject, string $body, string $replyTo = '', bool $isHtml = false): array
    {
        $cfg = self::getSmtpConfig();
        if (empty($cfg['host'])) {
            return ['success' => false, 'error' => 'SMTP Host not configured.'];
        }

        $host = $cfg['host'];
        $port = (int)$cfg['port'];
        $secure = strtolower($cfg['secure']);
        $fromEmail = $cfg['from_email'] ?: 'noreply@ccsdu.in';
        $fromName = $cfg['from_name'] ?: 'CCSA Portal';

        $socketHost = ($secure === 'ssl') ? 'ssl://' . $host : $host;
        $timeout = 10;
        $fp = @fsockopen($socketHost, $port, $errno, $errstr, $timeout);

        if (!$fp) {
            return ['success' => false, 'error' => "Could not connect to SMTP host $host:$port ($errstr)"];
        }

        $read = function() use ($fp) {
            $data = '';
            while ($str = fgets($fp, 515)) {
                $data .= $str;
                if (substr($str, 3, 1) === ' ') break;
            }
            return $data;
        };

        $sendCmd = function(string $cmd) use ($fp, $read) {
            fputs($fp, $cmd . "\r\n");
            return $read();
        };

        $resp = $read();
        if (substr($resp, 0, 3) !== '220') {
            fclose($fp);
            return ['success' => false, 'error' => 'SMTP handshake failed: ' . $resp];
        }

        $sendCmd("EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost'));

        if ($secure === 'tls') {
            $tlsResp = $sendCmd("STARTTLS");
            if (substr($tlsResp, 0, 3) === '220') {
                stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                $sendCmd("EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
            }
        }

        if (!empty($cfg['username']) && !empty($cfg['password'])) {
            $authResp = $sendCmd("AUTH LOGIN");
            if (substr($authResp, 0, 3) !== '334') {
                fclose($fp);
                return ['success' => false, 'error' => 'AUTH LOGIN error: ' . $authResp];
            }
            $sendCmd(base64_encode($cfg['username']));
            $passResp = $sendCmd(base64_encode($cfg['password']));
            if (substr($passResp, 0, 3) !== '235') {
                fclose($fp);
                return ['success' => false, 'error' => 'SMTP Authentication failed. Check Username/Password.'];
            }
        }

        $sendCmd("MAIL FROM: <$fromEmail>");
        $rcptResp = $sendCmd("RCPT TO: <$to>");
        if (substr($rcptResp, 0, 3) !== '250') {
            fclose($fp);
            return ['success' => false, 'error' => 'Recipient rejected: ' . $rcptResp];
        }

        $sendCmd("DATA");

        $headers = "From: $fromName <$fromEmail>\r\n";
        $headers .= "To: <$to>\r\n";
        if ($replyTo) {
            $headers .= "Reply-To: <$replyTo>\r\n";
        }
        $headers .= "Subject: $subject\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: " . ($isHtml ? "text/html; charset=UTF-8" : "text/plain; charset=UTF-8") . "\r\n";
        $headers .= "Content-Transfer-Encoding: 8bit\r\n\r\n";

        $fullMsg = $headers . $body . "\r\n.";
        $dataResp = $sendCmd($fullMsg);

        $sendCmd("QUIT");
        fclose($fp);

        if (substr($dataResp, 0, 3) === '250') {
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'SMTP DATA send error: ' . $dataResp];
    }

    public static function send(string $name, string $email, string $message): array
    {
        $name = str_replace(["\r", "\n"], '', $name);
        $name = Security::validateString($name, 1, 100);
        $email = str_replace(["\r", "\n"], '', $email);
        $email = Security::validateEmail($email);
        $message = Security::validateString($message, 1, 5000);

        if (!$name || !$email || !$message) {
            return ['success' => false, 'error' => 'Invalid input data.'];
        }

        $subject = sprintf("[CCSA Portal Inquiry] New Message from %s", $name);
        $dateStr = date('d M Y, h:i A (T)');
        $body = "New Inquiry Submitted via CCSA Portal\n";
        $body .= "----------------------------------------\n";
        $body .= "Sender Name : $name\n";
        $body .= "Sender Email: $email\n";
        $body .= "Received At : $dateStr\n\n";
        $body .= "Message Body:\n$message\n\n";
        $body .= "----------------------------------------\n";
        $body .= "To reply directly to the sender, simply reply to this email.";

        $recipients = self::getRecipients();
        if (empty($recipients)) {
            $defaultRecipient = defined('MAIL_TO') ? MAIL_TO : 'ccsduoffice@gmail.com';
            $recipients = [$defaultRecipient];
        }

        $smtp = self::getSmtpConfig();
        $useSmtp = !empty($smtp['host']);

        $sentCount = 0;
        foreach ($recipients as $recipient) {
            if ($useSmtp) {
                $res = self::sendViaSmtp($recipient, $subject, $body, $email, false);
                if ($res['success']) {
                    $sentCount++;
                    self::logDispatch($recipient, true, 'Delivered via SMTP (' . $smtp['host'] . ')');
                } else {
                    self::logDispatch($recipient, false, 'SMTP Error: ' . ($res['error'] ?? 'Unknown'));
                }
            } else {
                $headers = [
                    'From' => MAIL_FROM,
                    'Reply-To' => $email,
                    'Content-Type' => 'text/plain; charset=UTF-8',
                    'X-Mailer' => 'PHP/' . phpversion()
                ];
                $headersString = '';
                foreach ($headers as $k => $v) {
                    $headersString .= "$k: $v\r\n";
                }
                $ok = @mail($recipient, $subject, $body, $headersString);
                if ($ok) {
                    $sentCount++;
                    self::logDispatch($recipient, true, 'Dispatched via PHP mail()');
                } else {
                    self::logDispatch($recipient, false, 'PHP mail() rejected (Local server requires SMTP setup)');
                }
            }
        }

        return ['success' => true, 'sent_to' => $recipients, 'delivered_count' => $sentCount];
    }

    /**
     * Send OTP Verification Email for Admin Login / Password Reset
     */
    public static function sendOtpEmail(string $toEmail, string $otpCode, string $purpose = 'login', string $recipientName = 'Administrator'): array
    {
        $toEmail = trim($toEmail);
        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Invalid email address.'];
        }

        $purposeLabel = match ($purpose) {
            'password_reset' => 'PASSWORD RESET',
            'password_change' => 'PASSWORD CHANGE',
            default => 'LOGIN'
        };

        $subject = sprintf("[CCSA Admin] Your %s Verification Code: %s", ucfirst(str_replace('_', ' ', $purpose)), $otpCode);
        $reqIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $reqTime = date('d M Y, h:i A (T)');
        $year = date('Y');

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 24px 12px; }
  .wrapper { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08); border: 1px solid #e2e8f0; }
  .header { background: linear-gradient(135deg, #1a365d 0%, #1e3a8a 100%); padding: 32px 24px; text-align: center; color: #ffffff; }
  .header h1 { margin: 0; font-size: 20px; font-weight: 800; letter-spacing: -0.5px; }
  .header p { margin: 6px 0 0; font-size: 12px; color: #93c5fd; font-weight: 500; }
  .badge { display: inline-block; background: #fbbf24; color: #0f172a; font-size: 10px; font-weight: 800; text-transform: uppercase; padding: 3px 10px; margin-bottom: 12px; letter-spacing: 0.5px; border-radius: 12px; }
  .content { padding: 32px 28px; color: #334155; }
  .greeting { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 10px; }
  .desc { font-size: 14px; line-height: 1.6; color: #475569; margin-bottom: 24px; }
  .otp-box { background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 14px; text-align: center; padding: 22px 14px; margin: 24px 0; }
  .otp-label { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: #64748b; margin-bottom: 10px; }
  .otp-code { font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace; font-size: 38px; font-weight: 900; letter-spacing: 10px; color: #1a365d; margin: 0; }
  .expiry { font-size: 12px; color: #dc2626; font-weight: 700; margin-top: 10px; }
  .warning { font-size: 12px; color: #64748b; line-height: 1.5; background: #fffbeb; border-left: 4px solid #f59e0b; padding: 12px 16px; border-radius: 6px; margin-top: 24px; }
  .footer { background: #f8fafc; padding: 20px 24px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #f1f5f9; line-height: 1.5; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <div class="badge">Security Subsystem</div>
    <h1>Centre for Computer Science and Applications</h1>
    <p>Dibrugarh University &bull; Official Admin Portal</p>
  </div>
  <div class="content">
    <div class="greeting">Hello {$recipientName},</div>
    <div class="desc">A request was made to authenticate into your administrator account on the CCSA Web Portal. Use the 6-digit verification code below:</div>
    <div class="otp-box">
      <div class="otp-label">{$purposeLabel} VERIFICATION CODE</div>
      <div class="otp-code">{$otpCode}</div>
      <div class="expiry">&#9200; Valid for 10 minutes only</div>
    </div>
    <div class="warning">
      <strong>Security Notice:</strong> Never share this OTP with anyone. CCSA Administrators will never ask for your verification code. If you did not initiate this request, please secure your account immediately.
    </div>
  </div>
  <div class="footer">
    &copy; {$year} CCSA, Dibrugarh University. All rights reserved.<br>
    Request Origin IP: {$reqIp} &bull; {$reqTime}
  </div>
</div>
</body>
</html>
HTML;

        $smtp = self::getSmtpConfig();
        $useSmtp = !empty($smtp['host']);

        if ($useSmtp) {
            $res = self::sendViaSmtp($toEmail, $subject, $htmlBody, '', true);
            self::logDispatch($toEmail, $res['success'], $res['success'] ? 'OTP delivered via Hostinger SMTP' : 'SMTP OTP Error: ' . ($res['error'] ?? ''));
            return $res;
        }

        $fromMail = defined('MAIL_FROM') ? MAIL_FROM : 'noreply@ccsdu.in';
        $headers = [
            'From' => 'CCSA Portal <' . $fromMail . '>',
            'MIME-Version' => '1.0',
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Transfer-Encoding' => '8bit',
            'X-Mailer' => 'PHP/' . phpversion()
        ];
        $headersStr = '';
        foreach ($headers as $k => $v) {
            $headersStr .= "$k: $v\r\n";
        }
        $ok = @mail($toEmail, $subject, $htmlBody, $headersStr);
        self::logDispatch($toEmail, (bool)$ok, $ok ? 'OTP sent via PHP mail()' : 'PHP mail() dispatch error');
        return ['success' => (bool)$ok, 'error' => $ok ? null : 'Failed to send email.'];
    }
}
