<?php
// Set JSON content type
header('Content-Type: application/json');

// Restrict CORS to allowed origins
$allowed_origins = ['https://www.ccsdu.in', 'https://ccsdu.in'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
}

// Cache control headers (cache for 1 hour)
header('Cache-Control: public, max-age=3600');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

// Get department_id from query parameter (default to 25)
$department_id = isset($_GET['department_id']) ? (int)$_GET['department_id'] : 25;

// Headers matching the curl command
$headers = [
    'Accept: application/json',
    'Accept-Language: en-US,en;q=0.9',
    'Origin: https://www.dibru.ac.in',
    'Priority: u=1, i',
    'Referer: https://www.dibru.ac.in/',
    'Sec-CH-UA: "Chromium";v="146", "Not-A.Brand";v="24", "Google Chrome";v="146"',
    'Sec-CH-UA-Mobile: ?1',
    'Sec-CH-UA-Platform: "Android"',
    'Sec-Fetch-Dest: empty',
    'Sec-Fetch-Mode: cors',
    'Sec-Fetch-Site: cross-site',
    'User-Agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36'
];

function fetchStudents($url, $headers) {
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_ENCODING => "",
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_FAILONERROR => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        error_log('Students Proxy CURL Error: ' . curl_error($ch));
        curl_close($ch);
        return null;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        error_log("Students API returned HTTP code: $httpCode");
        return null;
    }

    return json_decode($response, true);
}

try {
    $studentsUrl = 'https://lionfish-app-3a378.ondigitalocean.app/api/website/public/students?department_id=' . $department_id;

    // Validate URL
    if (!filter_var($studentsUrl, FILTER_VALIDATE_URL)) {
        throw new Exception('Invalid API URL');
    }

    $studentData = fetchStudents($studentsUrl, $headers);

    if (!$studentData || !is_array($studentData)) {
        throw new Exception('Invalid student data received from API');
    }

    echo json_encode($studentData, JSON_UNESCAPED_SLASHES);

} catch (Exception $e) {
    error_log('Students Proxy Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([]);
}
?>
