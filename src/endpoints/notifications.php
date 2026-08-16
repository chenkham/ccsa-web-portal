<?php
declare(strict_types=1);

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Cache-Control: public, max-age=300');

$allowed_origins = ['https://www.ccsdu.in', 'https://ccsdu.in', 'http://localhost:8000', 'http://localhost:3000'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';

// Default curated department announcements based on uploads repository
$fallbackNotices = [
    [
        'id' => 1,
        'title' => 'DUAT-2026: Dibrugarh University Admission Test Notice',
        'description' => 'Detailed schedule, application procedure and guidelines for DUAT-2026 for admission into BCA and MCA programmes.',
        'file_path' => 'uploads/notification_docs/90298c59e8d525e4_DUAT_2026.pdf',
        'file_url' => '',
        'createdAt' => '2026-02-10 10:00:00'
    ],
    [
        'id' => 2,
        'title' => 'Class Routine (Feb - June 2025 Session)',
        'description' => 'Master time table and classroom allotment for Even Semester BCA, MCA, and PGDCA courses.',
        'file_path' => 'uploads/notification_docs/67b4678d7d616_Class Routine, 25(Feb-June).pdf',
        'file_url' => '',
        'createdAt' => '2025-02-18 11:30:00'
    ],
    [
        'id' => 3,
        'title' => 'Course Fees Notification for Even Semester Students (2024-2025)',
        'description' => 'Notification regarding payment portal and last dates for semester fee submission.',
        'file_path' => 'uploads/notification_docs/67b84189acdc2_Notification regarding the Course Fees for the Even Semester students for the session 2024-2025..pdf',
        'file_url' => '',
        'createdAt' => '2025-02-21 09:15:00'
    ],
    [
        'id' => 4,
        'title' => 'MCA 6th Semester Major Project Guidelines & Submission Schedule',
        'description' => 'Comprehensive instructions regarding synopsis approval, progress review, and final viva voce evaluation.',
        'file_path' => 'uploads/notification_docs/682c500de4834_MCA MAJOR PROJECT.pdf',
        'file_url' => '',
        'createdAt' => '2025-05-20 14:00:00'
    ],
    [
        'id' => 5,
        'title' => 'Programme for Ph.D. Course Work Examination 2025',
        'description' => 'Official examination timetable for registered Ph.D. research scholars in Computer Science.',
        'file_path' => 'uploads/notification_docs/682c528b96341_Programme_for_Ph._D._Course_Work_Examination_2025[1].pdf',
        'file_url' => '',
        'createdAt' => '2025-05-20 16:45:00'
    ],
    [
        'id' => 6,
        'title' => 'Google Gemini Ambassador Program Selection at CCSA',
        'description' => 'List of shortlisted student ambassadors representing Dibrugarh University in AI community outreach.',
        'file_path' => 'uploads/notification_docs/688dca2e2c26a_Google Gemini Ambassador Program.pdf',
        'file_url' => '',
        'createdAt' => '2025-08-02 12:00:00'
    ]
];

try {
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare('SELECT id, title, description, creator_email, file_path, file_url, createdAt, updatedAt FROM notifications ORDER BY createdAt DESC LIMIT 50');
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($rows)) {
        echo json_encode(['data' => $rows]);
        exit;
    }
} catch (Throwable $e) {
    error_log('Database notice fetch fallback triggered: ' . $e->getMessage());
}

// Return fallback notices when database table is unpopulated or in staging
echo json_encode(['data' => $fallbackNotices]);
