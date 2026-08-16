<?php
declare(strict_types=1);

/**
 * Admin Configuration Bridge
 * Connects to the shared Database singleton and provides $pdo for legacy admin files.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Session.php';

Session::start();

try {
    $pdo = Database::getInstance();
} catch (Throwable $e) {
    error_log('Admin database initialization failed: ' . $e->getMessage());
    $pdo = null;
}
