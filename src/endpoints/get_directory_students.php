<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$jsonPath = __DIR__ . '/../src/cache/students_dept_25.json';
if (!file_exists($jsonPath)) {
    $jsonPath = __DIR__ . '/../../src/cache/students_dept_25.json';
}

$students = [];

if (file_exists($jsonPath)) {
    $content = file_get_contents($jsonPath);
    $data = json_decode($content ?: '', true);

    if (is_array($data)) {
        foreach ($data as $dept) {
            foreach (($dept['programmes'] ?? []) as $prog) {
                $progName = (string)($prog['programme'] ?? '');
                $course = 'BCA';
                if (stripos($progName, 'MCA') !== false) $course = 'MCA';
                elseif (stripos($progName, 'PGDCA') !== false) $course = 'PGDCA';

                foreach (($prog['semesters'] ?? []) as $sem) {
                    $semStr = (string)($sem['semester'] ?? '');
                    $semester = 1;
                    if (stripos($semStr, '1st') !== false) $semester = 1;
                    elseif (stripos($semStr, '2nd') !== false) $semester = 2;
                    elseif (stripos($semStr, '3rd') !== false) $semester = 3;
                    elseif (stripos($semStr, '4th') !== false) $semester = 4;
                    elseif (stripos($semStr, '5th') !== false) $semester = 5;
                    elseif (stripos($semStr, '6th') !== false) $semester = 6;
                    elseif (stripos($semStr, '7th') !== false) $semester = 7;
                    elseif (stripos($semStr, '8th') !== false) $semester = 8;

                    foreach (($sem['students'] ?? []) as $st) {
                        $name = trim((string)($st['name'] ?? ''));
                        if (!empty($name)) {
                            $students[] = [
                                'name'     => $name,
                                'course'   => $course,
                                'semester' => $semester,
                            ];
                        }
                    }
                }
            }
        }
    }
}

echo json_encode([
    'success'  => true,
    'count'    => count($students),
    'students' => $students,
]);

