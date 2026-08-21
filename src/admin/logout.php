<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/Session.php';
Session::start();

require_once 'config.php';
if (file_exists('session_logger.php')) {
    require_once 'session_logger.php';
}

require_once __DIR__ . '/../includes/Security.php';

$userEmail = $_SESSION['user_email'] ?? 'admin@ccsdu.in';
if (isset($pdo)) {
    Security::logAudit($pdo, 'LOGOUT', 'Administrator signed out successfully.', $userEmail);
} else {
    Security::logAudit(null, 'LOGOUT', 'Administrator signed out successfully.', $userEmail);
}

// Secure session destruction
Session::destroy();

// Redirect to index page
header('Location: index.php');
exit;