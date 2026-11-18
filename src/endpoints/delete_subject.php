<?php
declare(strict_types=1);

/**
 * Delete Subject & Associated Data from MySQL Cloud Database
 */

require_once __DIR__ . '/app_db_config.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$subjectId = trim((string)($input['subject_id'] ?? ($input['id'] ?? ($_GET['subject_id'] ?? ($_GET['id'] ?? '')))));

if (empty($subjectId)) {
    echo json_encode(['success' => false, 'message' => 'Subject ID is required.']);
    exit;
}

try {
    $pdo = AppDatabase::getConnection();

    $pdo->beginTransaction();

    // Cascading deletes across all dependent tables
    $pdo->prepare("DELETE FROM app_subject_students WHERE subject_id = ?")->execute([$subjectId]);
    $pdo->prepare("DELETE FROM app_attendance_records WHERE session_id IN (SELECT id FROM app_attendance_sessions WHERE subject_id = ?)")->execute([$subjectId]);
    $pdo->prepare("DELETE FROM app_attendance_sessions WHERE subject_id = ?")->execute([$subjectId]);
    $pdo->prepare("DELETE FROM app_subjects WHERE id = ?")->execute([$subjectId]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Subject deleted successfully from database.'
    ]);

} catch (\Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Delete failed: ' . $e->getMessage()]);
}
