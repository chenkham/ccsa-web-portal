<?php
declare(strict_types=1);

$pageTitle = 'Faculty - Centre for Computer Science & Applications';
$currentPage = 'faculty';
include 'templates/header.php';
?>

<main class="min-h-screen bg-slate-50/60 py-12 sm:py-16 relative overflow-hidden">
    <!-- 🎓 Faculty & Research Doodle Pattern Background -->
    <div class="absolute inset-0 pointer-events-none opacity-[0.06] select-none">
        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="doodle-faculty-page" width="260" height="260" patternUnits="userSpaceOnUse" patternTransform="rotate(-2)">
                    <g stroke="#1a365d" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <!-- Microscope -->
                        <g transform="translate(10, 8) rotate(4)">
                            <path d="M12 20A6 6 0 0 1 6 14V8" />
                            <rect x="10" y="2" width="6" height="11" transform="rotate(30 13 8)" />
                            <path d="M3 23H21M9 23V20H15V23" />
                            <circle cx="16" cy="14" r="2" />
                        </g>
                        <!-- Test Tube & Flask -->
                        <g transform="translate(140, 10) rotate(-6)">
                            <path d="M8 3V16C8 19 10.5 22 13.5 22C16.5 22 19 19 19 16V3" />
                            <path d="M6 3H21M8 11H19" />
                            <circle cx="13.5" cy="17" r="1.5" fill="#1a365d" />
                        </g>
                        <!-- Atom / Molecule -->
                        <g transform="translate(75, 48) rotate(5)">
                            <ellipse cx="12" cy="12" rx="11" ry="4" transform="rotate(30 12 12)" />
                            <ellipse cx="12" cy="12" rx="11" ry="4" transform="rotate(90 12 12)" />
                            <ellipse cx="12" cy="12" rx="11" ry="4" transform="rotate(150 12 12)" />
                            <circle cx="12" cy="12" r="2" fill="#1a365d" />
                        </g>
                        <!-- Open Journal / Research Paper -->
                        <g transform="translate(205, 52) rotate(-3)">
                            <path d="M6 5V20C6 20 9 18 14 18C19 18 22 20 22 20V5C22 5 19 3 14 3C9 3 6 5 6 5Z" />
                            <path d="M14 3V18" />
                            <path d="M9 8H12M9 11H12M16 8H19M16 11H19" />
                        </g>
                        <!-- Lecture Podium -->
                        <g transform="translate(10, 95) rotate(4)">
                            <path d="M6 8H20L17 22H9L6 8Z" />
                            <path d="M4 8H22M9 2H17V8H9V2Z" />
                            <circle cx="13" cy="5" r="1.5" fill="#1a365d" />
                        </g>
                        <!-- Spectacles -->
                        <g transform="translate(145, 98) rotate(-7)">
                            <rect x="2" y="8" width="9" height="6" rx="2" />
                            <rect x="15" y="8" width="9" height="6" rx="2" />
                            <path d="M11 11C11 11 12.5 9.5 14 9.5C15.5 9.5 17 11 17 11" />
                        </g>
                        <!-- Brain with Neural Path -->
                        <g transform="translate(70, 150) rotate(6)">
                            <path d="M14 6C10 6 7 9 7 13C7 17 10 20 14 20C18 20 21 17 21 13C21 9 18 6 14 6Z" stroke-dasharray="2 1.5" />
                            <circle cx="11" cy="12" r="1.8" /> <circle cx="17" cy="14" r="1.8" />
                            <path d="M11 13.5L17 12.5" />
                        </g>
                        <!-- Whiteboard -->
                        <g transform="translate(195, 155) rotate(-4)">
                            <rect x="3" y="3" width="20" height="13" rx="1"/>
                            <path d="M7 16V22M19 16V22M4 22H22" />
                            <path d="M6 7H13M6 10H10" />
                        </g>
                        <!-- DNA Double Helix -->
                        <g transform="translate(12, 195) rotate(8)">
                            <path d="M4 3C8 8 16 12 20 17M20 3C16 8 8 12 4 17"/>
                            <line x1="6" y1="5" x2="18" y2="5"/><line x1="7" y1="10" x2="17" y2="10"/><line x1="6" y1="15" x2="18" y2="15"/>
                        </g>
                        <!-- Lightbulb -->
                        <g transform="translate(135, 200) rotate(-3)">
                            <circle cx="12" cy="9" r="6"/>
                            <path d="M9 15H15M10 18H14"/>
                        </g>
                    </g>
                    <!-- Fillers -->
                    <text x="68" y="28" font-family="'Courier New',monospace" font-size="12" font-weight="900" fill="#1a365d">PhD</text>
                    <text x="195" y="30" font-family="'Courier New',monospace" font-size="14" font-weight="900" fill="#1a365d">∑</text>
                    <text x="12" y="72" font-family="'Courier New',monospace" font-size="10" font-weight="bold" fill="#1a365d">E=mc²</text>
                    <text x="125" y="75" font-family="'Courier New',monospace" font-size="11" font-weight="bold" fill="#1a365d">H₂O</text>
                    <text x="75" y="125" font-family="'Courier New',monospace" font-size="12" font-weight="bold" fill="#1a365d">λ</text>
                    <text x="210" y="130" font-family="'Courier New',monospace" font-size="11" font-weight="900" fill="#1a365d">IEEE</text>
                    <text x="128" y="175" font-family="'Courier New',monospace" font-size="12" font-weight="bold" fill="#1a365d">∫f(x)</text>
                    <text x="15" y="172" font-family="'Courier New',monospace" font-size="11" font-weight="bold" fill="#1a365d">ACM</text>
                    <text x="68" y="230" font-family="'Courier New',monospace" font-size="11" font-weight="900" fill="#1a365d">SCOPUS</text>
                    <text x="195" y="235" font-family="'Courier New',monospace" font-size="11" font-weight="bold" fill="#1a365d">NLP</text>
                    <!-- Dots & Connectors -->
                    <circle cx="50" cy="50" r="1.3" fill="#1a365d"/><circle cx="115" cy="105" r="1.6" fill="#1a365d"/><circle cx="175" cy="85" r="1.3" fill="#1a365d"/>
                    <circle cx="58" cy="188" r="1.5" fill="#1a365d"/><circle cx="180" cy="198" r="1.3" fill="#1a365d"/><circle cx="245" cy="98" r="1.5" fill="#1a365d"/>
                    <path d="M50 50h10v8" stroke="#1a365d" stroke-width="1" stroke-dasharray="2 2" fill="none"/>
                    <path d="M175 85v10h10" stroke="#1a365d" stroke-width="1" stroke-dasharray="2 2" fill="none"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#doodle-faculty-page)"/>
        </svg>
    </div>

    <div class="container mx-auto px-4 lg:px-10 relative z-10">
        <!-- Section Header (Reference Style) -->
        <div class="mb-10 text-center">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold font-display uppercase tracking-wide text-[#1a365d]">
                Faculty at CCSA
            </h1>
            <div class="w-20 h-1 bg-[#fbbf24] mt-4 rounded-full mx-auto"></div>
        </div>

        <!-- Faculty Grid -->
        <div id="students-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6 sm:gap-8"></div>
    </div>
</main>

<?php
$extraScripts = '<script src="assets/js/faculty.js" defer></script>';
include 'templates/footer.php';
?>
