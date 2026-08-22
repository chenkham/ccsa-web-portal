<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Security.php';

/**
 * Mailer Utility Class
 * 
 * Hand-crafted Raw TCP Socket SMTP Client.
 * Built from scratch because who needs 50MB of Composer dependencies
 * when you have insomnia, RFC 5321 specs, and strong Assam tea?
 */
class Mailer
{
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
            'host' => getenv('SMTP_HOST') ?: '',
            'port' => (int)(getenv('SMTP_PORT') ?: 587),
            'secure' => getenv('SMTP_SECURE') ?: 'tls', // 'tls', 'ssl', or 'none'
            'username' => getenv('SMTP_USER') ?: '',
            'password' => getenv('SMTP_PASS') ?: '',
            'from_email' => getenv('MAIL_FROM') ?: 'noreply@ccsdu.in',
            'from_name' => 'CCSA Portal'
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
    public static function sendViaSmtp(string $to, string $subject, string $body, string $replyTo = ''): array
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
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
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
            self::logDispatch('None (No alert recipients configured)', true, 'Inquiry saved to database. No alert recipients configured.');
            return ['success' => true, 'sent_to' => []];
        }

        $smtp = self::getSmtpConfig();
        $useSmtp = !empty($smtp['host']);

        $sentCount = 0;
        foreach ($recipients as $recipient) {
            if ($useSmtp) {
                $res = self::sendViaSmtp($recipient, $subject, $body, $email);
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
}
