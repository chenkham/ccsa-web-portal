<?php
declare(strict_types=1);

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Cache.php';

$endpoint = $_GET['endpoint'] ?? 'staff';
if (!in_array($endpoint, ['staff', 'students'], true)) {
    $endpoint = 'staff';
}

$staffId = filter_input(INPUT_GET, 'staff_id', FILTER_VALIDATE_INT) ?: 0;
$departmentId = filter_input(INPUT_GET, 'department_id', FILTER_VALIDATE_INT) ?: 25;

$cache = new Cache(CACHE_DIR, CACHE_TTL);

function fetchUrl(string $url): string {
    $headers = [
        'User-Agent: Mozilla/5.0 (compatible; DibruProxy/1.0)',
        'Referer: https://dibru.ac.in/',
        'Origin: https://dibru.ac.in',
        'Accept: application/json'
    ];

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_HTTPHEADER => $headers
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($response !== false && $httpCode === 200) {
            return $response;
        }
    } else {
        $headerString = implode("\r\n", $headers);
        $ctx = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => $headerString,
                'timeout' => 10,
                'ignore_errors' => true
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]);
        $response = @file_get_contents($url, false, $ctx);
        if ($response !== false && $response !== '') {
            return $response;
        }
    }

    return '[]';
}

if ($endpoint === 'staff') {
    header('Content-Type: application/javascript');
    
    $cacheKey = 'publications_staff_' . $staffId;
    $cachedData = $cache->get($cacheKey);
    
    if ($cachedData !== null) {
        echo $cachedData;
        exit;
    }
    
    $journalUrl = "https://lionfish-app-3a378.ondigitalocean.app/api/website/public/profile/journal-publications?staff_id={$staffId}";
    $conferenceUrl = "https://lionfish-app-3a378.ondigitalocean.app/api/website/public/profile/conference-seminars?staff_id={$staffId}";
    
    $journalData = fetchUrl($journalUrl);
    $conferenceData = fetchUrl($conferenceUrl);
    
    $jsData = "window.publicationsData = {$journalData};\nwindow.conferenceData = {$conferenceData};\n";
    $cache->set($cacheKey, $jsData);
    
    echo $jsData;
} else {
    header('Content-Type: application/json');
    
    $cacheKey = 'students_dept_' . $departmentId;
    $cachedData = $cache->get($cacheKey);
    
    if ($cachedData !== null) {
        echo $cachedData;
        exit;
    }
    
    $studentsUrl = "https://lionfish-app-3a378.ondigitalocean.app/api/website/public/students?department_id={$departmentId}";
    $studentsData = fetchUrl($studentsUrl);
    
    $cache->set($cacheKey, $studentsData);
    echo $studentsData;
}
