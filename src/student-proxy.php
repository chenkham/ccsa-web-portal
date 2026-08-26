<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: public, max-age=3600');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

$allowedOrigins = ['https://www.ccsdu.in', 'https://ccsdu.in', 'http://localhost:8000', 'http://localhost:3000'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins, true)) {
    header("Access-Control-Allow-Origin: $origin");
}

$departmentId = isset($_GET['department_id']) ? (int)$_GET['department_id'] : 25;
$cacheDir = __DIR__ . '/cache';
$cacheFile = $cacheDir . "/students_dept_{$departmentId}.json";

if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 3600)) {
    $cached = file_get_contents($cacheFile);
    if ($cached && strlen($cached) > 20) {
        echo $cached;
        exit;
    }
}

$headers = [
    'Accept: application/json',
    'Accept-Language: en-US,en;q=0.9',
    'Origin: https://www.dibru.ac.in',
    'Referer: https://www.dibru.ac.in/',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36'
];

function fetchStudentData(string $url, array $headers): ?string {
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response !== false && $httpCode === 200 && !empty($response)) {
            return $response;
        }
    }

    $headerLines = [];
    foreach ($headers as $k => $v) {
        $headerLines[] = is_int($k) ? $v : "$k: $v";
    }

    $ctx = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => implode("\r\n", $headerLines),
            'timeout' => 8,
            'ignore_errors' => true
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ]);

    $response = @file_get_contents($url, false, $ctx);
    return ($response !== false && !empty($response)) ? $response : null;
}

try {
    $studentsUrl = 'https://lionfish-app-3a378.ondigitalocean.app/api/website/public/students?department_id=' . $departmentId;
    $raw = fetchStudentData($studentsUrl, $headers);

    if ($raw && strpos($raw, 'error') === false && strlen(trim($raw)) > 20) {
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }
        @file_put_contents($cacheFile, $raw);
        echo $raw;
        exit;
    }

    if (file_exists($cacheFile)) {
        echo file_get_contents($cacheFile);
        exit;
    }

    echo json_encode([]);
} catch (\Throwable $e) {
    error_log('Students proxy error: ' . $e->getMessage());
    echo json_encode([]);
}
