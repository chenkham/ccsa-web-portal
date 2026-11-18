<?php
declare(strict_types=1);

/**
 * CCSA Smart Attendance - Fetch Session Details & Student Records from Hostinger MySQL
 * Centre for Computer Science and Applications, Dibrugarh University
 */

require_once __DIR__ . '/app_db_config.php';

$sessionId = trim((string)($_GET['session_id'] ?? ($_POST['session_id'] ?? '')));

if (empty($sessionId)) {
    echo json_encode(['success' => false, 'message' => 'Session ID is required.']);
    exit;
}

try {
    $pdo = AppDatabase::getConnection();

    // 1. Session metadata
    $stmt = $pdo->prepare("SELECT * FROM app_attendance_sessions WHERE id = ? LIMIT 1");
    $stmt->execute([$sessionId]);
    $session = $stmt->fetch();

    if (!$session) {
        echo json_encode(['success' => false, 'message' => 'Session not found.']);
        exit;
    }

    $subjectId = $session['subject_id'];

    // 2. Fetch all enrolled students for this subject
    $enrolledStmt = $pdo->prepare("
        SELECT 
            COALESCE(s.id, u.id, ss.student_id) AS id,
            COALESCE(s.name, u.name, 'Student') AS name,
            COALESCE(s.email, u.email, '') AS email,
            COALESCE(s.class_roll_no, u.student_roll_no, '') AS class_roll_no,
            COALESCE(s.du_exam_roll_no, u.du_exam_roll_no, '') AS du_exam_roll_no,
            COALESCE(s.course, u.course, 'BCA') AS course,
            COALESCE(s.semester, u.semester, 1) AS semester
        FROM app_subject_students ss
        LEFT JOIN app_students s ON ss.student_id = s.id
        LEFT JOIN app_users u ON ss.student_id = u.id OR (s.email IS NOT NULL AND s.email != '' AND LOWER(u.email) = LOWER(s.email))
        WHERE ss.subject_id = ?
        ORDER BY CASE WHEN s.class_roll_no IS NOT NULL AND s.class_roll_no != '' THEN CAST(s.class_roll_no AS UNSIGNED)
                      WHEN u.student_roll_no IS NOT NULL AND u.student_roll_no != '' THEN CAST(u.student_roll_no AS UNSIGNED)
                      ELSE 9999 END ASC, COALESCE(s.name, u.name) ASC
    ");
    $enrolledStmt->execute([$subjectId]);
    $enrolledStudents = $enrolledStmt->fetchAll();

    // 3. Fetch all attendance records for this session
    $recStmt = $pdo->prepare("
        SELECT * FROM app_attendance_records 
        WHERE session_id = ?
        ORDER BY created_at ASC
    ");
    $recStmt->execute([$sessionId]);
    $rawRecords = $recStmt->fetchAll();

    // Helper to check if a record matches an enrolled student
    $matchesStudent = function(array $rec, array $std): bool {
        if (!empty($rec['student_id']) && !empty($std['id']) && $rec['student_id'] === $std['id']) {
            return true;
        }
        if (!empty($rec['student_roll_no']) && !empty($std['class_roll_no']) && strtolower(trim((string)$rec['student_roll_no'])) === strtolower(trim((string)$std['class_roll_no']))) {
            return true;
        }
        if (!empty($rec['student_name']) && !empty($std['name']) && strtolower(trim((string)$rec['student_name'])) === strtolower(trim((string)$std['name']))) {
            return true;
        }
        if (!empty($rec['student_id']) && !empty($std['email']) && strtolower(trim((string)$rec['student_id'])) === strtolower(trim((string)$std['email']))) {
            return true;
        }
        if (!empty($rec['student_id']) && !empty($std['du_exam_roll_no']) && strtolower(trim((string)$rec['student_id'])) === strtolower(trim((string)$std['du_exam_roll_no']))) {
            return true;
        }
        if (!empty($rec['student_roll_no']) && !empty($std['du_exam_roll_no']) && strtolower(trim((string)$rec['student_roll_no'])) === strtolower(trim((string)$std['du_exam_roll_no']))) {
            return true;
        }
        return false;
    };

    $unifiedRecords = [];
    $presentCount = 0;

    // If there are pre-enrolled students, build records for each enrolled student
    if (!empty($enrolledStudents)) {
        foreach ($enrolledStudents as $std) {
            // Find best matching attendance record (prefer 'present')
            $matchedRec = null;
            foreach ($rawRecords as $r) {
                if ($matchesStudent($r, $std)) {
                    if ($matchedRec === null || $r['status'] === 'present') {
                        $matchedRec = $r;
                    }
                }
            }

            $isPresent = ($matchedRec !== null && strtolower((string)($matchedRec['status'] ?? '')) === 'present');
            if ($isPresent) {
                $presentCount++;
            }

            $unifiedRecords[] = [
                'id'              => $matchedRec['id'] ?? ('rec_' . ($std['id'] ?? bin2hex(random_bytes(8)))),
                'session_id'      => $sessionId,
                'student_id'      => $std['id'],
                'student_name'    => !empty($std['name']) ? $std['name'] : ($matchedRec['student_name'] ?? 'Student'),
                'student_roll_no' => !empty($std['class_roll_no']) ? $std['class_roll_no'] : ($matchedRec['student_roll_no'] ?? ''),
                'du_exam_roll_no' => $std['du_exam_roll_no'] ?? '',
                'email'           => $std['email'] ?? '',
                'status'          => $isPresent ? 'present' : 'absent',
                'marked_by'       => $matchedRec['marked_by'] ?? ($isPresent ? 'totp_qr' : 'system'),
                'created_at'      => $matchedRec['created_at'] ?? $session['session_date']
            ];
        }

        // Add any extra raw records that did not match pre-enrolled students
        foreach ($rawRecords as $r) {
            $alreadyIncluded = false;
            foreach ($unifiedRecords as $ur) {
                if ($ur['student_id'] === $r['student_id'] || 
                    (!empty($r['student_roll_no']) && $ur['student_roll_no'] === $r['student_roll_no']) ||
                    (!empty($r['student_name']) && strtolower((string)$ur['student_name']) === strtolower((string)$r['student_name']))) {
                    $alreadyIncluded = true;
                    break;
                }
            }
            if (!$alreadyIncluded) {
                $isPresent = (strtolower((string)($r['status'] ?? '')) === 'present');
                if ($isPresent) {
                    $presentCount++;
                }
                $unifiedRecords[] = [
                    'id'              => $r['id'],
                    'session_id'      => $sessionId,
                    'student_id'      => $r['student_id'],
                    'student_name'    => $r['student_name'],
                    'student_roll_no' => $r['student_roll_no'] ?? '',
                    'du_exam_roll_no' => '',
                    'email'           => '',
                    'status'          => $isPresent ? 'present' : 'absent',
                    'marked_by'       => $r['marked_by'] ?? 'totp_qr',
                    'created_at'      => $r['created_at'] ?? $session['session_date']
                ];
            }
        }
    } else {
        // No enrolled students found: use raw records directly
        foreach ($rawRecords as $r) {
            $isPresent = (strtolower((string)($r['status'] ?? '')) === 'present');
            if ($isPresent) {
                $presentCount++;
            }
            $unifiedRecords[] = [
                'id'              => $r['id'],
                'session_id'      => $sessionId,
                'student_id'      => $r['student_id'],
                'student_name'    => $r['student_name'],
                'student_roll_no' => $r['student_roll_no'] ?? '',
                'du_exam_roll_no' => '',
                'email'           => '',
                'status'          => $isPresent ? 'present' : 'absent',
                'marked_by'       => $r['marked_by'] ?? 'totp_qr',
                'created_at'      => $r['created_at'] ?? $session['session_date']
            ];
        }
    }

    // Sort unified records by roll number numeric ascending
    usort($unifiedRecords, function($a, $b) {
        $aNum = (int)preg_replace('/[^0-9]/', '', (string)$a['student_roll_no']);
        $bNum = (int)preg_replace('/[^0-9]/', '', (string)$b['student_roll_no']);
        if ($aNum > 0 && $bNum > 0) {
            return $aNum <=> $bNum;
        }
        return strcasecmp((string)$a['student_name'], (string)$b['student_name']);
    });

    $totalEnrolled = count($unifiedRecords);
    $totalPresent = $presentCount;
    $percentage = $totalEnrolled > 0 ? round(($totalPresent / $totalEnrolled) * 100, 1) : 100.0;

    // Keep session object synced with accurate counts
    $session['total_present'] = $totalPresent;
    $session['total_enrolled'] = $totalEnrolled;
    $session['attendance_percentage'] = $percentage;

    echo json_encode([
        'success'               => true,
        'session'               => $session,
        'records'               => $unifiedRecords,
        'total_present'         => $totalPresent,
        'total_enrolled'        => $totalEnrolled,
        'attendance_percentage' => $percentage
    ]);

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Query error: ' . $e->getMessage()]);
}
