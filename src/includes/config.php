<?php
declare(strict_types=1);

/**
 * Application Configuration
 * Centre for Computer Science and Applications, Dibrugarh University
 */

// Database Connection Auto-Detection (Hostinger Live vs Local Dev)
$isLiveHostinger = (
    (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'ccsdu.in') !== false) ||
    (isset($_SERVER['SERVER_NAME']) && strpos($_SERVER['SERVER_NAME'], 'ccsdu.in') !== false) ||
    (isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['DOCUMENT_ROOT'], 'u332237642') !== false) ||
    is_dir('/home/u332237642')
);

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: ($isLiveHostinger ? 'u332237642_ccs' : 'ccsa_db'));
define('DB_USER', getenv('DB_USER') ?: ($isLiveHostinger ? 'u332237642_ccs' : 'root'));
define('DB_PASS', getenv('DB_PASS') ?: ($isLiveHostinger ? '786004@Ccs' : ''));
define('DB_CHARSET', 'utf8mb4');

// Asset Release Version (for efficient browser caching)
define('ASSET_VERSION', '2.5.2');

// reCAPTCHA v3 Configuration
define('RECAPTCHA_SITE_KEY', getenv('RECAPTCHA_SITE_KEY') ?: '6LeEW94sAAAAAPFa_NXd8WemwqWn-SLlNjpnN0CH');
define('RECAPTCHA_SECRET', getenv('RECAPTCHA_SECRET') ?: '');

// Mail Configuration
define('MAIL_TO', getenv('MAIL_TO') ?: 'ccsduoffice@gmail.com');
define('MAIL_FROM', getenv('MAIL_FROM') ?: 'noreply@ccsdu.in');

// Site Identity & CORS Allowed Origins
define('SITE_NAME', 'Centre for Computer Science and Applications, Dibrugarh University');
define('ALLOWED_ORIGINS', [
    'https://www.ccsdu.in',
    'https://ccsdu.in',
    'http://localhost:8000',
    'http://localhost:3000'
]);

// File Cache Configuration
define('CACHE_DIR', __DIR__ . '/../cache');
define('CACHE_TTL', 3600); // 1 hour

// Security Thresholds
define('API_KEY', getenv('API_KEY') ?: 'DU_CCSA_SECURE_API_KEY_2026');
define('SESSION_IDLE_TIMEOUT', 1800); // 30 minutes
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900);    // 15 minutes
