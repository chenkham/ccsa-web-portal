<?php
declare(strict_types=1);

/**
 * CCSA Smart Attendance - Fetch Enrolled Subjects for Student from Hostinger MySQL
 * Centre for Computer Science and Applications, Dibrugarh University
 */

require_once __DIR__ . '/app_db_config.php';

$email     = trim((string)($_GET['email'] ?? ($_POST['email'] ?? '')));
$studentId = trim((string)($_GET['student_id'] ?? ($_POST['student_id'] ?? '')));

if (empty($email) && empty($studentId)) {
    echo json_encode(['success' => false, 'message' => 'Email or Student ID is required.']);
    exit;
}

try {
    $pdo = AppDatabase::getConnection();

    $cleanEmail = strtolower($email);
    $resolvedIds = [];
    if (!empty($studentId)) {
        $resolvedIds[] = $studentId;
    }

    if (!empty($cleanEmail) || !empty($studentId)) {
        $uStmt = $pdo->prepare("SELECT id FROM app_users WHERE (id = ? AND id != '') OR (email != '' AND LOWER(email) = ?)");
        $uStmt->execute([$studentId, $cleanEmail]);
        foreach ($uStmt->fetchAll() as $u) {
            $resolvedIds[] = $u['id'];
        }

        $sStmt = $pdo->prepare("SELECT id FROM app_students WHERE (id = ? AND id != '') OR (email != '' AND LOWER(email) = ?)");
        $sStmt->execute([$studentId, $cleanEmail]);
        foreach ($sStmt->fetchAll() as $s) {
            $resolvedIds[] = $s['id'];
        }
    }

    $resolvedIds = array_values(array_unique(array_filter($resolvedIds)));
    if (empty($resolvedIds) && !empty($studentId)) {
        $resolvedIds = [$studentId];
    }

    if (empty($resolvedIds)) {
        echo json_encode([
            'success'  => true,
            'count'    => 0,
            'subjects' => []
        ]);
        exit;
    }

    $placeholders = implode(',', array_fill(0, count($resolvedIds), '?'));

    $stmt = $pdo->prepare("
        SELECT DISTINCT s.* 
        FROM app_subjects s
        INNER JOIN app_subject_students ss ON s.id = ss.subject_id
        WHERE ss.student_id IN ($placeholders)
        ORDER BY s.code ASC
    ");
    $stmt->execute($resolvedIds);
    $subjects = $stmt->fetchAll();

    // Attach enrolled student IDs for each subject
    foreach ($subjects as &$sub) {
        $stdStmt = $pdo->prepare("
            SELECT student_id FROM app_subject_students WHERE subject_id = ?
        ");
        $stdStmt->execute([$sub['id']]);
        $sub['enrolled_student_ids'] = array_column($stdStmt->fetchAll(), 'student_id');
    }
    unset($sub);

    echo json_encode([
        'success'  => true,
        'count'    => count($subjects),
        'subjects' => $subjects
    ]);

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Query error: ' . $e->getMessage()]);
}
