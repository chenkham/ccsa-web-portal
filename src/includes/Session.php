<?php
declare(strict_types=1);

/**
 * Session Security and State Manager
 */
class Session
{
    /**
     * Starts and configures the session securely with cookie flags.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $isHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
                       (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'domain'   => '',
                'secure'   => $isHttps,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            ini_set('session.use_strict_mode', '1');
            ini_set('session.gc_maxlifetime', '3600');

            session_start();
        }
    }

    /**
     * Regenerates the session ID to mitigate session fixation.
     */
    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }

    /**
     * Destroys the current session and clears the session cookie.
     */
    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    /**
     * Sets a session variable.
     */
    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Gets a session variable.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Checks if a session key exists.
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Checks if an authenticated user session exists.
     */
    public static function isAuthenticated(): bool
    {
        self::start();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Sets a cryptographic session fingerprint based on IP and User-Agent.
     */
    public static function setFingerprint(): void
    {
        self::start();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN_IP';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN_UA';
        $_SESSION['fingerprint'] = hash('sha256', $ip . $userAgent);
    }

    /**
     * Validates the session fingerprint against the current client request.
     */
    public static function validateFingerprint(): bool
    {
        self::start();
        if (!isset($_SESSION['fingerprint'])) {
            return false;
        }
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN_IP';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN_UA';
        $currentFingerprint = hash('sha256', $ip . $userAgent);

        return hash_equals($_SESSION['fingerprint'], $currentFingerprint);
    }
}
