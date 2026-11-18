<?php
declare(strict_types=1);

require_once __DIR__ . '/app_db_config.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$email = trim((string)($input['email'] ?? ''));
$name  = trim((string)($input['name'] ?? ''));
$role  = trim((string)($input['role'] ?? 'student'));

if (empty($email) && empty($name)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email or Name is required.']);
    exit;
}

try {
    $pdo = AppDatabase::getConnection();

    // Check if email already exists
    if (!empty($email)) {
        $stmt = $pdo->prepare('SELECT id, name, role FROM app_users WHERE LOWER(email) = LOWER(?) LIMIT 1');
        $stmt->execute([$email]);
        $existingByEmail = $stmt->fetch();

        if ($existingByEmail) {
            echo json_encode([
                'success' => true,
                'exists'  => true,
                'reason'  => 'email',
                'message' => "An account is already registered with email {$email}. Please log in."
            ]);
            exit;
        }
    }

    // NOTE: no name-based existence check — two students can legitimately
    // share a full name. Email is the unique identity; duplicates by name
    // are distinguished by their roll numbers on the faculty side.

    // No existing user found -> Safe to proceed with OTP signup
    echo json_encode([
        'success' => true,
        'exists'  => false,
        'message' => 'User does not exist. Proceed to OTP generation.'
    ]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error checking user existence: ' . $e->getMessage()
    ]);
}
