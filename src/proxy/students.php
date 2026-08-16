<?php
declare(strict_types=1);

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Cache.php';

$departmentId = filter_input(INPUT_GET, 'department_id', FILTER_VALIDATE_INT) ?: 25;

$cache = new Cache(CACHE_DIR, CACHE_TTL);
$cacheKey = 'students_dept_' . $departmentId;

$cachedData = $cache->get($cacheKey);
if ($cachedData !== null) {
    echo $cachedData;
    exit;
}

$url = 'https://ccsdu.in/student-proxy.php';

$headers = [
    'Authorization: RRDS_dKj8mP9vN3xQ7yH2fL5sW4tR8nB6cX9hV2kM5pY7wZ1aE4uT0iG3jD_2025',
    'Content-Type: application/json',
    'Accept: application/json',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36'
];

$response = false;

if (function_exists('curl_init')) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 12,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
        CURLOPT_HTTPHEADER => $headers
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        $response = false;
    }
} else {
    $headerString = implode("\r\n", $headers);
    $ctx = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => $headerString,
            'timeout' => 12,
            'ignore_errors' => true
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ]);
    $response = @file_get_contents($url, false, $ctx);
}

if ($response !== false && $response !== '') {
    $cache->set($cacheKey, $response);
    echo $response;
} else {
    error_log("Students proxy failed");
    echo json_encode([]);
}

