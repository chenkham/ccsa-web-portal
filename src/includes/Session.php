<?php
declare(strict_types=1);

/**
 * Session Management Class
 */
class Session
{
    /**
     * Starts and configures the session securely.
     *
     * @return void
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => $isHttps,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);

            ini_set('session.use_strict_mode', '1');
            ini_set('session.gc_maxlifetime', '3600');

            session_start();
        }
    }

    /**
     * Regenerates the session ID.
     *
     * @return void
     */
    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }

    /**
     * Destroys the session.
     *
     * @return void
     */
    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
    }

    /**
     * Sets a value in the session.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Gets a value from the session.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Checks if a key exists in the session.
     *
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Checks if the user is authenticated.
     *
     * @return bool
     */
    public static function isAuthenticated(): bool
    {
        self::start();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Sets a fingerprint for the session based on IP and User-Agent.
     *
     * @return void
     */
    public static function setFingerprint(): void
    {
        self::start();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN_IP';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN_UA';
        $_SESSION['fingerprint'] = hash('sha256', $ip . $userAgent);
    }

    /**
     * Validates the session fingerprint.
     *
     * @return bool
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
