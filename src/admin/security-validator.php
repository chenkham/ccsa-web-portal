<?php
// Security utilities for input validation and sanitization

class SecurityValidator {
    /**
     * Validate and sanitize email
     */
    public static function email($email) {
        $email = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        return $email ? $email : null;
    }

    /**
     * Validate and sanitize string (max length)
     */
    public static function string($value, $maxLength = 255) {
        $value = trim($value);
        if (strlen($value) > $maxLength) {
            return null;
        }
        return $value;
    }

    /**
     * Validate password strength
     */
    public static function password($password) {
        // Minimum 8 characters, at least 1 uppercase, 1 lowercase, 1 number
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
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validate CSRF token
     */
    public static function validateCSRFToken($token) {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Escape HTML safely
     */
    public static function htmlEscape($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate integer
     */
    public static function integer($value) {
        return filter_var($value, FILTER_VALIDATE_INT);
    }
}

?>
