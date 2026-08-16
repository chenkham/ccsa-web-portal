<?php
declare(strict_types=1);

/**
 * Universal API Proxy for CCSA Portal
 * Handles Faculty Publications, Conferences, and Student Roster requests
 * with dual cURL/stream-context and upstream fallback.
 */

$endpoint = $_GET['endpoint'] ?? 'staff';
$staff_id = isset($_GET['staff_id']) ? (int)$_GET['staff_id'] : 0;
$department_id = isset($_GET['department_id']) ? (int)$_GET['department_id'] : 25;

if ($endpoint === 'students') {
    header('Content-Type: application/json; charset=UTF-8');
} else {
    header('Content-Type: application/javascript; charset=UTF-8');
}

// CORS headers
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed_origins = ['https://www.ccsdu.in', 'https://ccsdu.in', 'http://localhost:8000', 'http://localhost:3000'];
if (in_array($origin, $allowed_origins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}

header('Cache-Control: public, max-age=3600');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

function fetchRemoteUrl(string $url, array $headers): ?string {
    // Attempt via cURL if available
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_ENCODING => '',
            CURLOPT_TIMEOUT => 8,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
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

    // Stream context fallback for PHP environments without php_curl.dll
    $headerLines = [];
    foreach ($headers as $k => $v) {
        $headerLines[] = is_int($k) ? $v : "$k: $v";
    }

    $context = stream_context_create([
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

    $response = @file_get_contents($url, false, $context);
    if ($response !== false && !empty($response)) {
        return $response;
    }

    return null;
}

$staffHeaders = [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
    'Referer: https://dibru.ac.in/',
    'Origin: https://dibru.ac.in',
    'Accept: application/json'
];

try {
    if ($endpoint === 'students') {
        $studentsUrl = 'https://lionfish-app-3a378.ondigitalocean.app/api/website/public/students?department_id=' . $department_id;
        $raw = fetchRemoteUrl($studentsUrl, $staffHeaders);
        
        // Fallback to production mirror if upstream digitalocean cluster blocks IP
        if (!$raw || strpos($raw, 'error') !== false) {
            $prodUrl = 'https://www.ccsdu.in/proxy.php?endpoint=students&department_id=' . $department_id;
            $raw = fetchRemoteUrl($prodUrl, ['User-Agent: Mozilla/5.0']);
        }

        if ($raw) {
            echo $raw;
            exit;
        }
        echo json_encode([]);
        exit;
    }

    // Default: Faculty publications and conferences
    if ($staff_id <= 0) {
        echo 'window.publicationsData = {"data":[]}; window.conferenceData = {"data":[]};';
        exit;
    }

    // Fetch publications & conferences from upstream or live mirror
    $publicationsUrl = 'https://lionfish-app-3a378.ondigitalocean.app/api/website/public/profile/journal-publications?staff_id=' . $staff_id;
    $conferenceUrl = 'https://lionfish-app-3a378.ondigitalocean.app/api/website/public/profile/conference-seminars?staff_id=' . $staff_id;

    $pubRaw = fetchRemoteUrl($publicationsUrl, $staffHeaders);
    $confRaw = fetchRemoteUrl($conferenceUrl, $staffHeaders);

    $pubData = $pubRaw ? json_decode($pubRaw, true) : null;
    $confData = $confRaw ? json_decode($confRaw, true) : null;

    // If upstream direct link was blocked, fetch from live production mirror
    if (!$pubData || isset($pubData['error'])) {
        $mirrorUrl = 'https://www.ccsdu.in/proxy.php?staff_id=' . $staff_id;
        $mirrorRaw = fetchRemoteUrl($mirrorUrl, ['User-Agent: Mozilla/5.0']);
        if ($mirrorRaw && strpos($mirrorRaw, 'window.publicationsData') !== false) {
            echo $mirrorRaw;
            exit;
        }
    }

    $pubPayload = json_encode($pubData ?: ['data' => []], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $confPayload = json_encode($confData ?: ['data' => []], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    echo "window.publicationsData = $pubPayload;\nwindow.conferenceData = $confPayload;";
} catch (Throwable $e) {
    error_log('Proxy exception: ' . $e->getMessage());
    echo 'window.publicationsData = {"data":[]}; window.conferenceData = {"data":[]};';
}
