<?php
declare(strict_types=1);

/**
 * Shared configuration constants for CCSA university website and administrative suite.
 * Centre for Computer Science and Applications (CCSA), Dibrugarh University
 */

// Database Configuration (Production MySQL Parameters)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'ccsa_portal');
define('DB_USER', getenv('DB_USER') ?: 'ccsa_dbuser');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

// reCAPTCHA v3 Configuration (Public Site Key & Secret)
define('RECAPTCHA_SITE_KEY', getenv('RECAPTCHA_SITE_KEY') ?: '6LeEW94sAAAAAPFa_NXd8WemwqWn-SLlNjpnN0CH');
define('RECAPTCHA_SECRET', getenv('RECAPTCHA_SECRET') ?: '');

// Department Mail Configuration
define('MAIL_TO', getenv('MAIL_TO') ?: 'ccsduoffice@gmail.com');
define('MAIL_FROM', getenv('MAIL_FROM') ?: 'noreply@ccsdu.in');

// Site Identity & CORS Allowed Origins
define('SITE_NAME', 'Centre for Computer Science and Applications, Dibrugarh University');
define('ALLOWED_ORIGINS', ['https://www.ccsdu.in', 'https://ccsdu.in', 'http://localhost:8000', 'http://localhost:3000']);

// File Cache Configuration
define('CACHE_DIR', __DIR__ . '/../cache');
define('CACHE_TTL', 3600); // 1 hour

// Administrative & API Security Thresholds
define('API_KEY', getenv('API_KEY') ?: 'DU_CCSA_SECURE_API_KEY_2026');
define('SESSION_IDLE_TIMEOUT', 1800); // 30 minutes
define('MAX_LOGIN_ATTEMPTS', 5);      // Lock out after 5 consecutive failed attempts
define('LOGIN_LOCKOUT_TIME', 900);    // 15-minute lockout window
