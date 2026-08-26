<?php
declare(strict_types=1);

header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/admin/login.php';
    exit;
}

header('Location: /admin/index.php');
exit;
