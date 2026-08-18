<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/Session.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Security.php';
require_once __DIR__ . '/../includes/Mailer.php';

Session::start();

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON input']);
    exit;
}

$csrfToken = $data['csrf_token'] ?? '';
$recaptchaToken = $data['recaptcha_token'] ?? '';
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$message = $data['message'] ?? '';

if (!Security::validateCsrfToken($csrfToken)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid or expired security token. Please refresh the page.']);
    exit;
}

// Anti-Spam Rate Limiting: Max 10 messages per 5 minutes
if (!Security::checkRateLimit('public_contact', 10, 300)) {
    http_response_code(429);
    echo json_encode(['error' => 'Too many message submissions. Please wait a few minutes before submitting again.']);
    exit;
}

if (!empty(RECAPTCHA_SECRET) && !Security::verifyRecaptcha($recaptchaToken)) {
    http_response_code(403);
    echo json_encode(['error' => 'reCAPTCHA verification failed. Please try again.']);
    exit;
}

if (!Security::validateString($name, 2, 100)) {
    http_response_code(400);
    echo json_encode(['error' => 'Please provide a valid name (2-100 characters).']);
    exit;
}

if (!Security::validateEmail($email)) {
    http_response_code(400);
    echo json_encode(['error' => 'Please provide a valid email address.']);
    exit;
}

if (!Security::validateString($message, 10, 2000)) {
    http_response_code(400);
    echo json_encode(['error' => 'Please provide a message between 10 and 2000 characters.']);
    exit;
}

require_once __DIR__ . '/../includes/Database.php';

$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$savedToDb = false;

try {
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, message, ip_address, createdAt) VALUES (?, ?, ?, ?, NOW())');
    $stmt->execute([$name, $email, $message, $ip]);
    $savedToDb = true;
} catch (Throwable $e) {
    error_log('Contact form DB insert notice: ' . $e->getMessage());
}

$cacheDir = __DIR__ . '/../cache';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}
$fallbackFile = $cacheDir . '/messages_fallback.json';
$existingMessages = [];
if (file_exists($fallbackFile)) {
    $existingMessages = json_decode((string)file_get_contents($fallbackFile), true) ?: [];
}
$newMessage = [
    'id' => time() . rand(100, 999),
    'name' => $name,
    'email' => $email,
    'message' => $message,
    'ip_address' => $ip,
    'createdAt' => date('Y-m-d H:i:s')
];
array_unshift($existingMessages, $newMessage);
file_put_contents($fallbackFile, json_encode($existingMessages, JSON_PRETTY_PRINT));

try {
    $result = Mailer::send($name, $email, $message);
    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    error_log('Contact form mail notice: ' . $e->getMessage());
    echo json_encode(['success' => true]);
}
