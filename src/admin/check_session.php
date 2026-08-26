<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Session.php';

/**
 * Validates admin authentication status and enforces idle session timeout.
 */
function check_auth(): void {
    Session::start();

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || empty($_SESSION['user_id'])) {
        header('Location: /admin/index.php');
        exit;
    }

    $now = time();
    $lastActivity = $_SESSION['last_activity'] ?? $now;
    if ($now - $lastActivity > SESSION_IDLE_TIMEOUT) {
        Session::destroy();
        header('Location: /admin/index.php?expired=1');
        exit;
    }

    $_SESSION['last_activity'] = $now;
}