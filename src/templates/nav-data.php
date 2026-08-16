<?php
declare(strict_types=1);

/**
 * Navigation data for the CCSA website.
 * Single source of truth for both desktop and mobile menus.
 * All links, download URLs, and nested structures are defined here.
 */

$navItems = [
    [
        'label' => 'Home',
        'href' => 'index.php',
    ],
    [
        'label' => 'About',
        'href' => 'index.php#aboutus',
    ],
    [
        'label' => 'Downloads',
        'href' => '#',
        'children' => [
            [
                'label' => 'Project Guidelines',
                'href' => 'downloads/PROJECTGUIDELINES.pdf',
                'download' => 'Project Guidelines.pdf',
                'view' => 'downloads/PROJECTGUIDELINES.pdf',
                'icon' => 'fas fa-file-pdf',
            ],
            [
                'label' => 'BOM Committee',
                'href' => 'https://drive.google.com/uc?export=download&id=1iQa96osBOWTw6oBQ2h2UvCEQ2N2Dg4xe',
                'download' => 'BOM committee.pdf',
                'view' => 'https://drive.google.com/file/d/1iQa96osBOWTw6oBQ2h2UvCEQ2N2Dg4xe/view?usp=sharing',
                'icon' => 'fas fa-users',
            ],
            [
                'label' => 'EOA Reports',
                'href' => '#',
                'icon' => 'fas fa-chart-line',
                'children' => [
                    [
                        'label' => 'EOA Report 2025',
                        'href' => 'https://drive.google.com/uc?export=download&id=1TeYrjK1qBNQxSuMt8rpHXek0gh90f5Pz',
                        'download' => '1735024754_EOA_Report_2025_26.pdf',
                        'view' => 'https://drive.google.com/file/d/1TeYrjK1qBNQxSuMt8rpHXek0gh90f5Pz/view?usp=drivesdk',
                    ],
                    [
                        'label' => 'EOA Report 2024',
                        'href' => 'https://drive.google.com/uc?export=download&id=1OeBRQP3McBuIUedc82uSQya9wrmN-Yey',
                        'download' => '1735024754_EOA_Report_2019_20.pdf',
                        'view' => 'https://drive.google.com/file/d/1OeBRQP3McBuIUedc82uSQya9wrmN-Yey/view?usp=sharing',
                    ],
                    [
                        'label' => 'EOA Report 2023',
                        'href' => 'https://drive.google.com/uc?export=download&id=1WCHodZ6y_gmK7BIpSGtnLiUt0CKJP2Gz',
                        'download' => '1735024819_EOA_Report__2023_24.pdf',
                        'view' => 'https://drive.google.com/file/d/1WCHodZ6y_gmK7BIpSGtnLiUt0CKJP2Gz/view?usp=sharing',
                    ],
                    [
                        'label' => 'EOA Report 2022',
                        'href' => 'https://drive.google.com/file/d/1hosWQIaCq69hWhByj0HqLAdJIBZuOZdt/view?usp=sharing',
                        'download' => '1735024782_EOA_Report_21_22.pdf',
                        'view' => 'https://drive.google.com/file/d/1TX3LviQqaLXInf3QdNw0E1-2hYiAj_5q/view?usp=sharing',
                    ],
                    [
                        'label' => 'EOA Report 2021',
                        'href' => 'https://drive.google.com/uc?export=download&id=1ZaYFU8gyK5E73x9BLH8ifeZTbq6clvG8',
                        'download' => '1735024721_EOA_Report_2018_19.pdf',
                        'view' => 'https://drive.google.com/file/d/1ZaYFU8gyK5E73x9BLH8ifeZTbq6clvG8/view?usp=sharing',
                    ],
                    [
                        'label' => 'EOA Report 2020',
                        'href' => 'https://drive.google.com/file/d/1oZTklVQcryJUwp7q5HCOlnD8O9TA1k9V/view?usp=sharing',
                        'download' => '1735024796_EOA_Report_2022_23.pdf',
                        'view' => 'https://drive.google.com/file/d/1TX3LviQqaLXInf3QdNw0E1-2hYiAj_5q/view?usp=sharing',
                    ],
                    [
                        'label' => 'EOA Report 2019',
                        'href' => 'https://drive.google.com/uc?export=download&id=1oZTklVQcryJUwp7q5HCOlnD8O9TA1k9V',
                        'download' => '1735024754_EOA_Report_2019_20.pdf',
                        'view' => 'https://drive.google.com/file/d/1oZTklVQcryJUwp7q5HCOlnD8O9TA1k9V/view?usp=sharing',
                    ],
                    [
                        'label' => 'EOA Report 2018',
                        'href' => 'https://drive.google.com/uc?export=download&id=1F5LzMzhrfK240Ljy4sOluridoSl6fyN9',
                        'download' => '1735024829_EOA_Report_2024_2025.pdf',
                        'view' => 'https://drive.google.com/file/d/1F5LzMzhrfK240Ljy4sOluridoSl6fyN9/view?usp=sharing',
                    ],
                ],
            ],
        ],
    ],
    [
        'label' => 'Programs',
        'href' => '#',
        'children' => [
            ['label' => 'Ph.D. in Computer Science', 'href' => 'phd.php'],
            ['label' => 'Master of Computer Applications (MCA)', 'href' => 'postgraduate.php'],
            ['label' => 'Bachelor of Computer Applications (BCA)', 'href' => 'undergraduate.php'],
            ['label' => 'Post Graduate Diploma (PGDCA)', 'href' => 'pgdca.php'],
        ],
    ],
    [
        'label' => 'Research',
        'href' => '#',
        'children' => [
            ['label' => 'Research Areas', 'href' => 'research.php'],
            ['label' => 'Publications & Conferences', 'href' => 'publication.php'],
        ],
    ],
    [
        'label' => 'Faculty',
        'href' => '#',
        'children' => [
            ['label' => 'ChairPerson', 'href' => 'https://www.dibru.ac.in/teachers-profile/paramananda-deka'],
            ['label' => 'Teaching Staff', 'href' => 'faculty.php'],
        ],
    ],
    [
        'label' => 'Students',
        'href' => '#',
        'children' => [
            ['label' => 'Present Students Directory', 'href' => 'Present_Stu.php'],
            ['label' => 'Alumni Network', 'href' => 'https://ccsaalumni.in/'],
        ],
    ],
    [
        'label' => 'Announcements',
        'href' => 'notices.php',
    ],
    [
        'label' => 'Contact',
        'href' => 'index.php#contact',
    ],
];

/** SVG icon for the "view" (eye) action next to download links */
$viewIcon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>';
