<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

/**
 * Security and Data Sanitization Utilities
 */
class Security
{
    /**
     * Escapes a string for safe HTML output.
     */
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generates and stores a cryptographic CSRF token in the session.
     */
    public static function generateCsrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validates a submitted CSRF token using timing-safe comparison.
     */
    public static function validateCsrfToken(string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Validates and returns a trimmed email address.
     */
    public static function validateEmail(string $email): ?string
    {
        $filtered = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        return $filtered !== false ? $filtered : null;
    }

    /**
     * Validates a string's UTF-8 character length.
     */
    public static function validateString(string $value, int $minLen = 1, int $maxLen = 500): ?string
    {
        $value = trim($value);
        $len = mb_strlen($value, 'UTF-8');
        if ($len >= $minLen && $len <= $maxLen) {
            return $value;
        }
        return null;
    }

    /**
     * Validates an integer within a given range.
     */
    public static function validateInteger(mixed $value, int $min = 0, int $max = PHP_INT_MAX): ?int
    {
        $filtered = filter_var($value, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => $min, 'max_range' => $max]
        ]);
        return $filtered !== false ? $filtered : null;
    }

    /**
     * Verifies a Google reCAPTCHA v3 response token.
     */
    public static function verifyRecaptcha(string $token, float $threshold = 0.5): bool
    {
        if (empty(RECAPTCHA_SECRET)) {
            return true;
        }

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret'   => RECAPTCHA_SECRET,
            'response' => $token,
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
                'timeout' => 5,
            ],
        ];

        $context = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
        if ($result === false) {
            return false;
        }

        $response = json_decode($result, true);
        return isset($response['success'], $response['score']) &&
               $response['success'] === true &&
               $response['score'] >= $threshold;
    }

    /**
     * Sanitizes a filename by removing path traversal patterns and invalid characters.
     */
    public static function sanitizeFilename(string $filename): string
    {
        $filename = basename($filename);
        return preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $filename);
    }

    /**
     * Validates an uploaded file against allowed extensions, MIME types, and size limits.
     *
     * @param array $file $_FILES entry
     * @param array $allowedExts Allowed lowercase extensions
     * @param array $allowedMimes Allowed MIME types
     * @param int $maxBytes Maximum file size in bytes
     * @return array{'valid': bool, 'error': string|null, 'ext': string}
     */
    public static function validateUploadedFile(
        array $file,
        array $allowedExts = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'],
        array $allowedMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/png'
        ],
        int $maxBytes = 15728640 // 15MB
    ): array {
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['valid' => false, 'error' => 'Invalid file upload parameters.', 'ext' => ''];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
                UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive.',
                UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
                UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder on the server.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
            ];
            $msg = $uploadErrors[$file['error']] ?? 'Unknown file upload error.';
            return ['valid' => false, 'error' => $msg, 'ext' => ''];
        }

        if ($file['size'] > $maxBytes) {
            $mb = round($maxBytes / (1024 * 1024));
            return ['valid' => false, 'error' => "File exceeds maximum allowed size of {$mb}MB.", 'ext' => ''];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExts, true)) {
            return ['valid' => false, 'error' => 'File extension is not permitted.', 'ext' => ''];
        }

        // Verify MIME type using finfo
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if ($mime === false || !in_array($mime, $allowedMimes, true)) {
                return ['valid' => false, 'error' => 'File content does not match permitted document or image types.', 'ext' => ''];
            }
        }

        return ['valid' => true, 'error' => null, 'ext' => $ext];
    }

    /**
     * Checks if an action is currently rate-limited by session and IP address.
     */
    public static function checkRateLimit(string $actionKey, int $maxAttempts = MAX_LOGIN_ATTEMPTS, int $decaySeconds = LOGIN_LOCKOUT_TIME): bool
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }

        $now = time();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $fullKey = $actionKey . '_' . md5($ip);

        // Check session-based rate limit
        $rateData = $_SESSION['rate_limit'][$fullKey] ?? null;

        if ($rateData) {
            if ($now < $rateData['lockout_until']) {
                return false;
            }
            if ($now > $rateData['first_attempt'] + $decaySeconds) {
                unset($_SESSION['rate_limit'][$fullKey]);
            } elseif ($rateData['attempts'] >= $maxAttempts) {
                $_SESSION['rate_limit'][$fullKey]['lockout_until'] = $now + $decaySeconds;
                return false;
            }
        }

        return true;
    }

    /**
     * Records a failed attempt for rate limiting.
     */
    public static function recordFailedAttempt(string $actionKey, int $decaySeconds = LOGIN_LOCKOUT_TIME): void
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }

        $now = time();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $fullKey = $actionKey . '_' . md5($ip);

        if (!isset($_SESSION['rate_limit'][$fullKey])) {
            $_SESSION['rate_limit'][$fullKey] = [
                'attempts' => 1,
                'first_attempt' => $now,
                'lockout_until' => 0
            ];
        } else {
            $_SESSION['rate_limit'][$fullKey]['attempts']++;
        }
    }

    /**
     * Resets the rate limiting counter for an action.
     */
    public static function resetRateLimit(string $actionKey): void
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $fullKey = $actionKey . '_' . md5($ip);
        unset($_SESSION['rate_limit'][$fullKey]);
    }

    /**
     * Records a security event in the database and audit log cache.
     */
    public static function logAudit(?\PDO $pdo, string $action, string $details = '', ?string $userEmail = null): void
    {
        $email = $userEmail ?: ($_SESSION['user_email'] ?? 'guest');
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);
        $timeStr = date('Y-m-d H:i:s');

        if ($pdo !== null) {
            try {
                $stmt = $pdo->prepare('INSERT INTO audit_logs (user_email, ip_address, action, details, user_agent, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
                $stmt->execute([$email, $ip, $action, $details, $ua]);
            } catch (\Throwable $e) {
                error_log('Audit log database error: ' . $e->getMessage());
            }
        }

        try {
            $cacheDir = __DIR__ . '/../cache';
            if (!is_dir($cacheDir)) {
                mkdir($cacheDir, 0755, true);
            }
            $cacheFile = $cacheDir . '/audit_logs.json';
            $logs = file_exists($cacheFile) ? (json_decode((string)file_get_contents($cacheFile), true) ?: []) : [];
            array_unshift($logs, [
                'id' => time() . rand(100, 999),
                'user_email' => $email,
                'ip_address' => $ip,
                'action' => $action,
                'details' => $details,
                'user_agent' => $ua,
                'created_at' => $timeStr
            ]);
            // Keep up to 200 recent entries in cache
            $logs = array_slice($logs, 0, 200);
            file_put_contents($cacheFile, json_encode($logs, JSON_PRETTY_PRINT));
        } catch (\Throwable $e) {
            error_log('Audit log cache error: ' . $e->getMessage());
        }
    }
}
