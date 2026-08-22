


<?php
header('Content-Type: application/javascript');
//proxy might change later so keep an eye on the main dibru.ac.in website for any changes in the API endpoint or structure
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

$headers = [
    'User-Agent: Mozilla/5.0 (compatible; DibruProxy/1.0)',
    'Referer: https://dibru.ac.in/',
    'Origin: https://dibru.ac.in',
    'Accept: application/json',
    'Accept-Language: en-US,en;q=0.5'
];

function fetchData($url, $headers) {
    $ch = curl_init();
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_ENCODING => "",
        CURLOPT_TIMEOUT => 10, // 10 second timeout
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_FAILONERROR => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3
    ]);

    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        error_log('CURL Error: ' . curl_error($ch));
        curl_close($ch);
        return null;
    }
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        error_log("API returned HTTP code: $httpCode");
        return null;
    }

    return json_decode($response, true);
}

try {
    $facultyUrl = 'https://lionfish-app-3a378.ondigitalocean.app/api/website/public/department-faculty-members?department_id=25&page_slug=';
    
    // Validate URL
    if (!filter_var($facultyUrl, FILTER_VALIDATE_URL)) {
        throw new Exception('Invalid API URL');
    }

    $facultyData = fetchData($facultyUrl, $headers);
    
    // Basic data validation
    if (!$facultyData || !is_array($facultyData)) {
        throw new Exception('Invalid faculty data received from API');
    }
    
    // Sanitize output
    $json = $facultyData;
    
    if ($json === false) {
        throw new Exception('JSON encoding failed');
    }
    
    echo json_encode($json, JSON_UNESCAPED_SLASHES);


} catch (Exception $e) {
    error_log('Proxy Error: ' . $e->getMessage());
    http_response_code(500);
    echo 'window.facultyData = [];';
    echo 'console.error("Faculty data loading failed: ' . addslashes($e->getMessage()) . '");';
}
?>