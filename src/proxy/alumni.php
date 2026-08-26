<?php
declare(strict_types=1);

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Cache.php';

$cache = new Cache(CACHE_DIR, CACHE_TTL);
$cacheKey = 'alumni_dept_25';

$cachedData = $cache->get($cacheKey);
if ($cachedData !== null) {
    echo $cachedData;
    exit;
}

$cacheFile = __DIR__ . '/../cache/alumni_data.json';
if (file_exists($cacheFile)) {
    $data = file_get_contents($cacheFile);
    $cache->set($cacheKey, $data);
    echo $data;
    exit;
}

echo json_encode([]);
