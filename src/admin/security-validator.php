<?php
declare(strict_types=1);

/**
 * Admin Security and Input Validation Helpers
 */
class SecurityValidator
{
    /**
     * Validates and returns a sanitized email.
     */
    public static function email(string $email): ?string
    {
        $filtered = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        return $filtered !== false ? $filtered : null;
    }

    /**
     * Validates string character length.
     */
    public static function string(string $value, int $maxLength = 255): ?string
    {
        $value = trim($value);
        if (mb_strlen($value, 'UTF-8') > $maxLength) {
            return null;
        }
        return $value;
    }

    /**
     * Checks password complexity (minimum 8 chars, 1 uppercase, 1 lowercase, 1 digit).
     */
    public static function password(string $password): bool
    {
        if (strlen($password) < 8) {
            return false;
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }
        return true;
    }

    /**
     * Generates or retrieves the session CSRF token.
     */
    public static function generateCSRFToken(): string
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
     * Validates a submitted CSRF token.
     */
    public static function validateCSRFToken(string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Escapes a string for safe HTML display.
     */
    public static function htmlEscape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validates an integer.
     */
    public static function integer(mixed $value): ?int
    {
        $filtered = filter_var($value, FILTER_VALIDATE_INT);
        return $filtered !== false ? $filtered : null;
    }
}
