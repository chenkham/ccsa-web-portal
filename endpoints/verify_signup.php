<?php
declare(strict_types=1);

require_once __DIR__ . '/app_db_config.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$email       = trim((string)($input['email'] ?? ''));
$otp         = trim((string)($input['otp'] ?? ''));
$name        = trim((string)($input['name'] ?? ''));
$password    = (string)($input['password'] ?? '');
$role        = trim((string)($input['role'] ?? 'student'));
$course      = trim((string)($input['course'] ?? 'BCA'));
$semester    = (int)($input['semester'] ?? 1);
$classRoll   = trim((string)($input['class_roll_no'] ?? ''));
$examRoll    = trim((string)($input['du_exam_roll_no'] ?? ''));
$deviceId    = trim((string)($input['device_id'] ?? ''));
$designation = trim((string)($input['designation'] ?? 'Assistant Professor'));
$department  = trim((string)($input['department'] ?? 'CCSA, Dibrugarh University'));

if (empty($email) || empty($otp) || empty($name)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email, OTP, and Name are required.']);
    exit;
}

try {
    $pdo = AppDatabase::getConnection();

    // 1. Verify OTP code against app_otp_verifications
    $stmt = $pdo->prepare('
        SELECT id FROM app_otp_verifications 
        WHERE email = ? AND otp_code = ? AND is_used = 0 AND expires_at >= NOW() 
        ORDER BY id DESC LIMIT 1
    ');
    $stmt->execute([$email, $otp]);
    $validOtp = $stmt->fetch();

    if (!$validOtp) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid or expired OTP code. Please try again.']);
        exit;
    }

    // Mark OTP as used
    $pdo->prepare('UPDATE app_otp_verifications SET is_used = 1 WHERE id = ?')->execute([$validOtp['id']]);

    // 2. Generate unique user ID
    $userId = bin2hex(random_bytes(16));
    $hashedPassword = password_hash($password ?: 'ccsa1234', PASSWORD_BCRYPT);

    // 3. Insert into app_users
    $userStmt = $pdo->prepare('
        INSERT INTO app_users 
        (id, name, email, password, role, student_roll_no, du_exam_roll_no, course, semester, designation, department, device_id, profile_completed)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
        ON DUPLICATE KEY UPDATE 
            name = VALUES(name),
            password = VALUES(password),
            role = VALUES(role),
            student_roll_no = VALUES(student_roll_no),
            du_exam_roll_no = VALUES(du_exam_roll_no),
            course = VALUES(course),
            semester = VALUES(semester),
            designation = VALUES(designation),
            department = VALUES(department),
            device_id = VALUES(device_id)
    ');
    $userStmt->execute([
        $userId,
        $name,
        $email,
        $hashedPassword,
        $role,
        $classRoll,
        $examRoll,
        $role === 'student' ? $course : null,
        $role === 'student' ? $semester : null,
        $role !== 'student' ? $designation : null,
        $role !== 'student' ? $department : null,
        $deviceId,
    ]);

    // 4. If student, insert into app_students table
    if ($role === 'student') {
        $studentStmt = $pdo->prepare('
            INSERT INTO app_students 
            (id, name, email, class_roll_no, du_exam_roll_no, course, semester, device_id, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
            ON DUPLICATE KEY UPDATE 
                name = VALUES(name),
                class_roll_no = VALUES(class_roll_no),
                du_exam_roll_no = VALUES(du_exam_roll_no),
                course = VALUES(course),
                semester = VALUES(semester),
                device_id = VALUES(device_id)
        ');
        $studentStmt->execute([
            $userId,
            $name,
            $email,
            $classRoll,
            $examRoll,
            $course,
            $semester,
            $deviceId
        ]);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Account created successfully!',
        'user'    => [
            'id'               => $userId,
            'name'             => $name,
            'email'            => $email,
            'role'             => $role,
            'studentRollNo'    => $classRoll,
            'duExamRollNo'     => $examRoll,
            'course'           => $course,
            'semester'         => $semester,
            'designation'      => $designation,
            'department'       => $department,
            'deviceId'         => $deviceId,
            'profileCompleted' => true
        ]
    ]);

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to create account: ' . $e->getMessage()
    ]);
}
