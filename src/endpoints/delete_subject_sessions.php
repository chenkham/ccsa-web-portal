<?php
declare(strict_types=1);

/**
 * CCSA Smart Attendance - Delete Attendance Sessions for a Subject
 * Used by the physical-register baseline import so re-imports REPLACE the
 * previous baseline (matched by id prefix) instead of duplicating sessions.
 * Live QR sessions are never touched: only ids starting with id_prefix.
 */

require_once __DIR__ . '/app_db_config.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$subjectId = trim((string)($input['subject_id'] ?? ''));
$idPrefix  = trim((string)($input['id_prefix'] ?? ''));

if (empty($subjectId)) {
    echo json_encode(['success' => false, 'message' => 'Subject ID is required.']);
    exit;
}

try {
    $pdo = AppDatabase::getConnection();

    // Resolve which session ids qualify (optionally restricted to a prefix)
    if ($idPrefix !== '') {
        $like = $idPrefix . '%';
        $stmt = $pdo->prepare('SELECT id FROM app_attendance_sessions WHERE subject_id = ? AND id LIKE ?');
        $stmt->execute([$subjectId, $like]);
    } else {
        $stmt = $pdo->prepare('SELECT id FROM app_attendance_sessions WHERE subject_id = ?');
        $stmt->execute([$subjectId]);
    }
    $sessionIds = array_column($stmt->fetchAll(), 'id');

    if (empty($sessionIds)) {
        echo json_encode([
            'success'       => true,
            'message'       => 'No matching sessions to delete.',
            'deleted'       => 0,
            'session_ids'   => []
        ]);
        exit;
    }

    $placeholders = implode(',', array_fill(0, count($sessionIds), '?'));

    $pdo->beginTransaction();

    $delRec = $pdo->prepare("DELETE FROM app_attendance_records WHERE session_id IN ($placeholders)");
    $delRec->execute($sessionIds);

    $delSess = $pdo->prepare("DELETE FROM app_attendance_sessions WHERE id IN ($placeholders)");
    $delSess->execute($sessionIds);

    $pdo->commit();

    echo json_encode([
        'success'       => true,
        'message'       => count($sessionIds) . ' session(s) deleted.',
        'deleted'       => count($sessionIds),
        'session_ids'   => $sessionIds
    ]);

} catch (\Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Delete failed: ' . $e->getMessage()]);
}
