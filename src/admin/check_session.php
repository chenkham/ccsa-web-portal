<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Session.php';

function check_auth(): void {
    Session::start();

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || empty($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }

    // Enforce 30-minute idle session timeout
    $now = time();
    $lastActivity = $_SESSION['last_activity'] ?? $now;
    if ($now - $lastActivity > SESSION_IDLE_TIMEOUT) {
        Session::destroy();
        header('Location: index.php?expired=1');
        exit;
    }

    // Refresh activity timestamp on valid request
    $_SESSION['last_activity'] = $now;

    // Log warning if IP or User-Agent changes significantly
    $current_ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $current_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    if (isset($_SESSION['ip_address'], $_SESSION['user_agent'])) {
        if ($current_ip !== $_SESSION['ip_address'] || $current_agent !== $_SESSION['user_agent']) {
            error_log("Session context changed for user ID " . ($_SESSION['user_id'] ?? 'unknown') . " - network switch or browser update");
        }
    }
}