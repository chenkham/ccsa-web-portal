<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/Security.php';
require_once __DIR__ . '/includes/Database.php';

// Honeypot: Log probe attempt
try {
    $pdo = Database::getInstance();
    Security::logAudit($pdo, 'SCANNER_PROBE', 'Automated scanner probe on wp-admin honeypot.');
} catch (Throwable $e) {}

http_response_code(403);
header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

echo json_encode([
    'error' => 'Forbidden',
    'message' => 'Access denied. Endpoint monitored and logged.'
]);
exit;
