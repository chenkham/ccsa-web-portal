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

    // Check if student name already has an account created
    if (!empty($name) && $role === 'student') {
        $stmt = $pdo->prepare('SELECT id, name, email FROM app_students WHERE LOWER(name) = LOWER(?) LIMIT 1');
        $stmt->execute([$name]);
        $existingByName = $stmt->fetch();

        if ($existingByName) {
            echo json_encode([
                'success' => true,
                'exists'  => true,
                'reason'  => 'name',
                'message' => "An account has already been registered for {$name}. Please log in with your registered email."
            ]);
            exit;
        }
    }

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
