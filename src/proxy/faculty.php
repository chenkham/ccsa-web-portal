<?php
declare(strict_types=1);

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Cache.php';

$cache = new Cache(CACHE_DIR, CACHE_TTL);
$cacheKey = 'faculty_members_v2';

$cachedData = $cache->get($cacheKey);
if ($cachedData !== null) {
    echo $cachedData;
    exit;
}

$url = 'https://www.ccsdu.in/faculty-proxy.php';

$headers = [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
    'Referer: https://dibru.ac.in/',
    'Origin: https://dibru.ac.in',
    'Accept: application/json'
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
    error_log("Faculty proxy fetch failed");
    echo json_encode([]);
}

