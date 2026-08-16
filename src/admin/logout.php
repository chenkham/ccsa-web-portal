<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/Session.php';
Session::start();

require_once 'config.php';
if (file_exists('session_logger.php')) {
    require_once 'session_logger.php';
}

if (isset($_SESSION['user_id']) && function_exists('logUserSession') && isset($pdo)) {
    // Log the logout
    try {
        logUserSession($pdo, $_SESSION['user_id'], 'logout');
    } catch (Throwable $e) {
        error_log("Session logout logging failed: " . $e->getMessage());
    }
}

// Secure session destruction
Session::destroy();

// Redirect to index page
header('Location: index.php');
exit;