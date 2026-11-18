<?php
declare(strict_types=1);

/**
 * CCSA Smart Attendance - Update Profile & Roll Numbers in Hostinger MySQL
 * Centre for Computer Science and Applications, Dibrugarh University
 */

require_once __DIR__ . '/app_db_config.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$id              = trim((string)($input['id'] ?? ''));
$email           = trim((string)($input['email'] ?? ''));
$name            = trim((string)($input['name'] ?? ''));
$classRollNo     = trim((string)($input['class_roll_no'] ?? ($input['student_roll_no'] ?? '')));
$duExamRollNo    = trim((string)($input['du_exam_roll_no'] ?? ''));
$course          = trim((string)($input['course'] ?? 'BCA'));
$semester        = (int)($input['semester'] ?? 1);
$designation     = trim((string)($input['designation'] ?? ''));
$department      = trim((string)($input['department'] ?? ''));
$role            = trim((string)($input['role'] ?? 'student'));

if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Email address is required.']);
    exit;
}

try {
    $pdo = AppDatabase::getConnection();

    // 1. Fetch current user: match by id when provided (authoritative — this
    // is what makes email CHANGES possible; the new email doesn't exist yet),
    // otherwise fall back to the email.
    $userStmt = $pdo->prepare(
        !empty($id)
            ? "SELECT * FROM app_users WHERE id = ? LIMIT 1"
            : "SELECT * FROM app_users WHERE LOWER(email) = LOWER(?) LIMIT 1"
    );
    $userStmt->execute(!empty($id) ? [$id] : [$email]);
    $existingUser = $userStmt->fetch();

    $finalName = !empty($name) ? $name : ($existingUser['name'] ?? '');

    if ($existingUser) {
        $stmtUser = $pdo->prepare("
            UPDATE app_users SET
                name = ?,
                email = ?,
                student_roll_no = ?,
                du_exam_roll_no = ?,
                course = ?,
                semester = ?,
                designation = ?,
                department = ?,
                profile_completed = 1
            WHERE id = ? OR LOWER(email) = LOWER(?)
        ");
        $stmtUser->execute([
            $finalName,
            $email,
            $classRollNo,
            $duExamRollNo,
            $course,
            $semester,
            $designation,
            $department,
            $existingUser['id'],
            $email
        ]);
    }

    // 2. Update app_students table (only for students, not faculty)
    if ($role === 'student' || ($existingUser && $existingUser['role'] === 'student')) {
        $userId = $existingUser['id'] ?? (!empty($id) ? $id : bin2hex(random_bytes(16)));
        $stdCheck = $pdo->prepare("SELECT id FROM app_students WHERE id = ? OR LOWER(email) = LOWER(?) LIMIT 1");
        $stdCheck->execute([$userId, $email]);
        $existingStd = $stdCheck->fetch();

        if ($existingStd) {
            $stmtStudent = $pdo->prepare("
                UPDATE app_students SET
                    id = ?,
                    name = ?,
                    class_roll_no = ?,
                    du_exam_roll_no = ?,
                    course = ?,
                    semester = ?
                WHERE id = ? OR LOWER(email) = LOWER(?)
            ");
            $stmtStudent->execute([
                $userId,
                $finalName,
                $classRollNo,
                $duExamRollNo,
                $course,
                $semester,
                $userId,
                $email
            ]);
        } else {
            $insertStmt = $pdo->prepare("
                INSERT INTO app_students (id, name, email, class_roll_no, du_exam_roll_no, course, semester, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, 1)
            ");
            $insertStmt->execute([
                $userId,
                $finalName,
                $email,
                $classRollNo,
                $duExamRollNo,
                $course,
                $semester
            ]);
        }
    }

    echo json_encode([
        'success'         => true,
        'message'         => 'Profile and Roll Numbers updated successfully in MySQL database.',
        'class_roll_no'   => $classRollNo,
        'du_exam_roll_no' => $duExamRollNo,
        'email'           => $email
    ]);

} catch (\Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update profile in database: ' . $e->getMessage()
    ]);
}
