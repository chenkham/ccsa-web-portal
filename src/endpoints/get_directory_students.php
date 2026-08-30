<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if (\['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

\ = __DIR__ . '/../src/cache/students_dept_25.json';
if (!file_exists(\)) {
    \ = __DIR__ . '/../../src/cache/students_dept_25.json';
}

\ = [];

if (file_exists(\)) {
    \ = file_get_contents(\);
    \ = json_decode(\, true);

    if (is_array(\)) {
        foreach (\ as \) {
            foreach ((\['programmes'] ?? []) as \) {
                \ = (string)(\['programme'] ?? '');
                \ = 'BCA';
                if (stripos(\, 'MCA') !== false) \ = 'MCA';
                elseif (stripos(\, 'PGDCA') !== false) \ = 'PGDCA';

                foreach ((\['semesters'] ?? []) as \) {
                    \ = (string)(\['semester'] ?? '');
                    \ = 1;
                    if (stripos(\, '1st') !== false) \ = 1;
                    elseif (stripos(\, '2nd') !== false) \ = 2;
                    elseif (stripos(\, '3rd') !== false) \ = 3;
                    elseif (stripos(\, '4th') !== false) \ = 4;
                    elseif (stripos(\, '5th') !== false) \ = 5;
                    elseif (stripos(\, '6th') !== false) \ = 6;
                    elseif (stripos(\, '7th') !== false) \ = 7;
                    elseif (stripos(\, '8th') !== false) \ = 8;

                    foreach ((\['students'] ?? []) as \) {
                        \ = trim((string)(\['name'] ?? ''));
                        if (!empty(\)) {
                            \[] = [
                                'name'     => \,
                                'course'   => \,
                                'semester' => \,
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
    'count'    => count(\),
    'students' => \,
]);
