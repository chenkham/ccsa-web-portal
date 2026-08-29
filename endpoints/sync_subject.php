<?php
declare(strict_types=1);

require_once __DIR__ . '/app_db_config.php';

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$id             = trim((string)($input['id'] ?? ''));
$code           = trim((string)($input['code'] ?? ''));
$name           = trim((string)($input['name'] ?? ''));
$course         = trim((string)($input['course'] ?? 'BCA'));
$semester       = (int)($input['semester'] ?? 1);
$teacherName    = trim((string)($input['teacher_name'] ?? ''));
$teacherEmail   = trim((string)($input['teacher_email'] ?? ''));
$geofenceLat    = isset($input['geofence_lat']) ? (float)$input['geofence_lat'] : null;
$geofenceLng    = isset($input['geofence_lng']) ? (float)$input['geofence_lng'] : null;
$geofenceRadius = isset($input['geofence_radius']) ? (float)$input['geofence_radius'] : null;
$studentIds     = is_array($input['enrolled_student_ids'] ?? null) ? $input['enrolled_student_ids'] : [];

if (empty($id) || empty($code) || empty($name)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Subject ID, Code, and Name are required.']);
    exit;
}

try {
    $pdo = AppDatabase::getConnection();

    $pdo->beginTransaction();

    // 1. Insert or update subject in app_subjects
    $stmt = $pdo->prepare('
        INSERT INTO app_subjects 
        (id, code, name, course, semester, teacher_name, teacher_email, geofence_lat, geofence_lng, geofence_radius)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            code = VALUES(code),
            name = VALUES(name),
            course = VALUES(course),
            semester = VALUES(semester),
            teacher_name = VALUES(teacher_name),
            teacher_email = VALUES(teacher_email),
            geofence_lat = VALUES(geofence_lat),
            geofence_lng = VALUES(geofence_lng),
            geofence_radius = VALUES(geofence_radius)
    ');
    $stmt->execute([
        $id,
        $code,
        $name,
        $course,
        $semester,
        $teacherName,
        $teacherEmail,
        $geofenceLat,
        $geofenceLng,
        $geofenceRadius
    ]);

    // 2. Sync enrolled students in app_subject_students
    // First delete ALL existing enrollments for this subject, then insert the current list
    $pdo->prepare('DELETE FROM app_subject_students WHERE subject_id = ?')->execute([$id]);

    $enrollStmt = $pdo->prepare('
        INSERT IGNORE INTO app_subject_students (subject_id, student_id)
        VALUES (?, ?)
    ');
    foreach ($studentIds as $sid) {
        if (!empty($sid)) {
            $enrollStmt->execute([$id, (string)$sid]);
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Subject synced successfully with Hostinger database.',
        'id'      => $id
    ]);
} catch (\Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to sync subject: ' . $e->getMessage()
    ]);
}
