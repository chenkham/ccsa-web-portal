<?php
declare(strict_types=1);

// Disable verbose error output in production API
ini_set('display_errors', '0');
error_reporting(E_ALL);

header('Content-Type: application/json; charset=UTF-8');

// ===== SECURITY HEADERS =====
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Cache-Control: no-store, no-cache, must-revalidate, proxy-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/config.php';

// ===== CORS WITH WHITELIST =====
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, ALLOWED_ORIGINS, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Credentials: true');
}

header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key");
header('Access-Control-Max-Age: 86400');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ===== AUTHENTICATION =====
function authenticate_api(): void {
    $providedKey = null;

    if (function_exists('getallheaders')) {
        $headers = getallheaders();
        $providedKey = $headers['Authorization'] ?? $headers['authorization'] ?? $headers['X-API-Key'] ?? $headers['x-api-key'] ?? null;
    }

    if (!$providedKey) {
        $providedKey = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['HTTP_X_API_KEY'] ?? null;
    }

    if (!$providedKey) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized - API authorization header required']);
        exit();
    }

    $cleanKey = str_replace(['Bearer ', 'RRDS_'], '', $providedKey);
    $expectedKey = str_replace('RRDS_', '', API_KEY);

    if (!hash_equals($expectedKey, $cleanKey)) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized - Invalid API credentials']);
        exit();
    }
}

authenticate_api();

// ===== INPUT VALIDATION =====
$entity = (string)($_GET['entity'] ?? '');
$allowedEntities = ['students', 'staff', 'notifications'];

if (!in_array($entity, $allowedEntities, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid entity specified. Allowed: ' . implode(', ', $allowedEntities)]);
    exit();
}

if (!isset($pdo) || $pdo === null) {
    http_response_code(503);
    echo json_encode(['error' => 'Service Temporarily Unavailable']);
    exit();
}

try {
    switch ($entity) {
        case 'students':
            $stmt = $pdo->prepare("SELECT id, roll_no, full_name, course, semester FROM current_students ORDER BY course, semester LIMIT 1000");
            $stmt->execute();
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            break;

        case 'staff':
            $stmt = $pdo->prepare("SELECT id, employee_id, full_name, department, position, email FROM teaching_staff ORDER BY id LIMIT 1000");
            $stmt->execute();
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            break;

        case 'notifications':
            $stmt = $pdo->prepare("SELECT id, title, description, creator_email, file_path, file_url, createdAt, updatedAt FROM notifications ORDER BY createdAt DESC LIMIT 100");
            $stmt->execute();
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            break;
    }
} catch (\PDOException $e) {
    error_log("Admin API Database Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database query execution failed']);
    exit();
}