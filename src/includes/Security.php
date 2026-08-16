<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

/**
 * Security Utility Class
 */
class Security
{
    /**
     * Escapes a string for safe HTML output.
     *
     * @param string $value The value to escape
     * @return string
     */
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generates and stores a CSRF token.
     *
     * @return string
     */
    public static function generateCsrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validates a CSRF token.
     *
     * @param string $token The token to validate
     * @return bool
     */
    public static function validateCsrfToken(string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Validates an email address.
     *
     * @param string $email
     * @return string|null
     */
    public static function validateEmail(string $email): ?string
    {
        $filtered = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        return $filtered !== false ? $filtered : null;
    }

    /**
     * Validates a string based on length.
     *
     * @param string $value
     * @param int $minLen
     * @param int $maxLen
     * @return string|null
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
     * Validates an integer based on range.
     *
     * @param mixed $value
     * @param int $min
     * @param int $max
     * @return int|null
     */
    public static function validateInteger(mixed $value, int $min = 0, int $max = PHP_INT_MAX): ?int
    {
        $filtered = filter_var($value, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => $min, 'max_range' => $max]
        ]);
        return $filtered !== false ? $filtered : null;
    }

    /**
     * Verifies a reCAPTCHA v3 token.
     *
     * @param string $token
     * @param float $threshold
     * @return bool
     */
    public static function verifyRecaptcha(string $token, float $threshold = 0.5): bool
    {
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
            ],
        ];

        $context  = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
        if ($result === false) {
            return false;
        }

        $response = json_decode($result, true);
        if (isset($response['success'], $response['score']) && $response['success'] === true && $response['score'] >= $threshold) {
            return true;
        }

        return false;
    }

    /**
     * Sanitizes a filename.
     *
     * @param string $filename
     * @return string
     */
    public static function sanitizeFilename(string $filename): string
    {
        // Remove path traversals and dangerous chars
        $filename = basename($filename);
        $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $filename);
        return $filename;
    }

    /**
     * Validates an uploaded file by extension, MIME content type, and byte size.
     *
     * @param array $file $_FILES entry
     * @param array $allowedExts Array of lowercase allowed extensions (e.g. ['pdf', 'jpg', 'png'])
     * @param array $allowedMimes Array of allowed MIME types
     * @param int $maxBytes Max file size in bytes (default 15MB)
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

        // Verify genuine MIME type using finfo
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if ($mime === false || !in_array($mime, $allowedMimes, true)) {
                return ['valid' => false, 'error' => 'File content does not match allowed document/image MIME types.', 'ext' => ''];
            }
        }

        return ['valid' => true, 'error' => null, 'ext' => $ext];
    }

    /**
     * Checks if an action is currently rate-limited based on IP/Key.
     *
     * @param string $actionKey Unique action identifier (e.g. 'login_attempt')
     * @param int $maxAttempts Maximum allowed attempts
     * @param int $decaySeconds Lockout duration in seconds
     * @return bool True if allowed, false if rate limited
     */
    public static function checkRateLimit(string $actionKey, int $maxAttempts = MAX_LOGIN_ATTEMPTS, int $decaySeconds = LOGIN_LOCKOUT_TIME): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $now = time();
        $rateData = $_SESSION['rate_limit'][$actionKey] ?? null;

        if ($rateData) {
            if ($now < $rateData['lockout_until']) {
                return false; // Still locked out
            }
            if ($now > $rateData['first_attempt'] + $decaySeconds) {
                // Reset window
                unset($_SESSION['rate_limit'][$actionKey]);
            } elseif ($rateData['attempts'] >= $maxAttempts) {
                // Apply lockout
                $_SESSION['rate_limit'][$actionKey]['lockout_until'] = $now + $decaySeconds;
                return false;
            }
        }

        return true;
    }

    /**
     * Records a failed attempt for rate limiting.
     *
     * @param string $actionKey
     * @param int $decaySeconds
     * @return void
     */
    public static function recordFailedAttempt(string $actionKey, int $decaySeconds = LOGIN_LOCKOUT_TIME): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $now = time();
        if (!isset($_SESSION['rate_limit'][$actionKey])) {
            $_SESSION['rate_limit'][$actionKey] = [
                'attempts' => 1,
                'first_attempt' => $now,
                'lockout_until' => 0
            ];
        } else {
            $_SESSION['rate_limit'][$actionKey]['attempts']++;
        }
    }

    /**
     * Resets rate limiting counter upon successful authentication.
     *
     * @param string $actionKey
     * @return void
     */
    public static function resetRateLimit(string $actionKey): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['rate_limit'][$actionKey]);
    }

    /**
     * Records a security event in the audit_logs table.
     *
     * @param PDO|null $pdo
     * @param string $action
     * @param string $details
     * @param string|null $userEmail
     * @return void
     */
    public static function logAudit(?PDO $pdo, string $action, string $details = '', ?string $userEmail = null): void
    {
        if ($pdo === null) {
            return;
        }

        $email = $userEmail ?: ($_SESSION['user_email'] ?? 'guest');
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);

        try {
            $stmt = $pdo->prepare('INSERT INTO audit_logs (user_email, ip_address, action, details, user_agent, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
            $stmt->execute([$email, $ip, $action, $details, $ua]);
        } catch (Throwable $e) {
            error_log('Audit log recording error: ' . $e->getMessage());
        }
    }
}
