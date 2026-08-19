<?php
declare(strict_types=1);

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Cache-Control: public, max-age=300');

$allowed_origins = ['https://www.ccsdu.in', 'https://ccsdu.in', 'http://localhost:8000', 'http://localhost:3000'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';

$notices = [];

try {
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare('SELECT id, title, description, is_pinned, creator_email, file_path, file_url, createdAt, updatedAt FROM notifications ORDER BY is_pinned DESC, createdAt DESC LIMIT 50');
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($rows)) {
        $notices = $rows;
    }
} catch (Throwable $e) {
    error_log('Database notice fetch notice: ' . $e->getMessage());
}

echo json_encode(['data' => $notices]);
