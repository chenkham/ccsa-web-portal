<?php
declare(strict_types=1);

/**
 * Ph.D. Pre-Registration Course Work Page
 * Centre for Computer Science and Applications (CCSA), Dibrugarh University
 */

$pageTitle = 'Ph.D. Programme in Computer Science - CCSA';
$currentPage = 'programs';
include 'templates/header.php';
?>

<section class="py-12 sm:py-16 px-4 bg-slate-50 min-h-screen relative overflow-hidden">
    <!-- 🎓 Doctoral & Research Doodle Background Pattern -->
    <div class="absolute inset-0 pointer-events-none opacity-[0.06] select-none">
        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="doodle-phd" width="260" height="260" patternUnits="userSpaceOnUse" patternTransform="rotate(-2)">
                    <g stroke="#1a365d" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <!-- Open Book / Thesis -->
                        <g transform="translate(10, 8) rotate(3)">
                            <path d="M2 7C2 7 7 4 14 4C21 4 26 7 26 7V20C26 20 21 17 14 17C7 17 2 20 2 20V7Z" />
                            <path d="M14 4V17" />
                            <path d="M5 8C5 8 8 7 12 7M5 11C5 11 8 10 12 10M5 14C5 14 8 13 12 13" />
                        </g>
                        <!-- Graduation Cap -->
                        <g transform="translate(140, 10) rotate(-4)">
                            <path d="M2 8L15 2L28 8L15 14Z" />
                            <path d="M7 11V17C7 17 11 20 15 20C19 20 23 17 23 17V11" />
                            <path d="M26 8V15" /><circle cx="26" cy="16.5" r="1.3" fill="#1a365d" />
                        </g>
                        <!-- Microscope -->
                        <g transform="translate(75, 48) rotate(4)">
                            <path d="M12 20A6 6 0 0 1 6 14V8" />
                            <rect x="10" y="2" width="6" height="11" transform="rotate(30 13 8)" />
                            <path d="M3 23H21M9 23V20H15V23" />
                            <circle cx="16" cy="14" r="2" />
                        </g>
                        <!-- Open Journal / Research Paper -->
                        <g transform="translate(205, 52) rotate(-3)">
                            <path d="M6 5V20C6 20 9 18 14 18C19 18 22 20 22 20V5C22 5 19 3 14 3C9 3 6 5 6 5Z" />
                            <path d="M14 3V18" />
                            <path d="M9 8H12M9 11H12M16 8H19M16 11H19" />
                        </g>
                        <!-- Atom / Molecule -->
                        <g transform="translate(10, 95) rotate(5)">
                            <ellipse cx="12" cy="12" rx="11" ry="4" transform="rotate(30 12 12)" />
                            <ellipse cx="12" cy="12" rx="11" ry="4" transform="rotate(90 12 12)" />
                            <ellipse cx="12" cy="12" rx="11" ry="4" transform="rotate(150 12 12)" />
                            <circle cx="12" cy="12" r="2" fill="#1a365d" />
                        </g>
                        <!-- Test Tube & Flask -->
                        <g transform="translate(145, 98) rotate(-6)">
                            <path d="M8 3V16C8 19 10.5 22 13.5 22C16.5 22 19 19 19 16V3" />
                            <path d="M6 3H21M8 11H19" />
                            <circle cx="13.5" cy="17" r="1.5" fill="#1a365d" />
                        </g>
                        <!-- Brain with Neural Path -->
                        <g transform="translate(70, 150) rotate(6)">
                            <path d="M14 6C10 6 7 9 7 13C7 17 10 20 14 20C18 20 21 17 21 13C21 9 18 6 14 6Z" stroke-dasharray="2 1.5" />
                            <circle cx="11" cy="12" r="1.8" /> <circle cx="17" cy="14" r="1.8" />
                        </g>
                        <!-- Diploma Scroll -->
                        <g transform="translate(195, 155) rotate(-6)">
                            <path d="M6 4C6 4 3 4 3 7C3 10 6 10 6 10H20C20 10 23 10 23 13C23 16 20 16 20 16" />
                            <path d="M6 4V18C6 18 3 18 3 21C3 24 6 24 6 24H20C20 24 23 24 23 21C23 18 20 18 20 18V10" />
                            <path d="M10 12H16" />
                        </g>
                        <!-- DNA Double Helix -->
                        <g transform="translate(12, 195) rotate(8)">
                            <path d="M4 3C8 8 16 12 20 17M20 3C16 8 8 12 4 17"/>
                            <line x1="6" y1="5" x2="18" y2="5"/><line x1="7" y1="10" x2="17" y2="10"/><line x1="6" y1="15" x2="18" y2="15"/>
                        </g>
                        <!-- Lightbulb Idea -->
                        <g transform="translate(135, 200) rotate(4)">
                            <path d="M12 2C8 2 6 5 6 8C6 10.5 8 12.5 9 14H15C16 12.5 18 10.5 18 8C18 5 16 2 12 2Z"/>
                            <path d="M9 16H15M10 18H14"/>
                        </g>
                    </g>
                    <!-- Fillers -->
                    <text x="68" y="28" font-family="'Courier New',monospace" font-size="13" font-weight="900" fill="#1a365d">Ph.D.</text>
                    <text x="195" y="30" font-family="'Courier New',monospace" font-size="14" font-weight="900" fill="#1a365d">∑</text>
                    <text x="12" y="72" font-family="'Courier New',monospace" font-size="10" font-weight="bold" fill="#1a365d">E=mc²</text>
                    <text x="125" y="75" font-family="'Courier New',monospace" font-size="11" font-weight="bold" fill="#1a365d">H₂O</text>
                    <text x="75" y="125" font-family="'Courier New',monospace" font-size="12" font-weight="bold" fill="#1a365d">λ</text>
                    <text x="210" y="130" font-family="'Courier New',monospace" font-size="11" font-weight="900" fill="#1a365d">IEEE</text>
                    <text x="128" y="175" font-family="'Courier New',monospace" font-size="12" font-weight="bold" fill="#1a365d">∫f(x)</text>
                    <text x="15" y="172" font-family="'Courier New',monospace" font-size="11" font-weight="bold" fill="#1a365d">SCOPUS</text>
                    <text x="68" y="230" font-family="'Courier New',monospace" font-size="11" font-weight="900" fill="#1a365d">ACM</text>
                    <text x="195" y="235" font-family="'Courier New',monospace" font-size="11" font-weight="bold" fill="#1a365d">DU</text>
                    <!-- Dots & Connectors -->
                    <circle cx="50" cy="50" r="1.3" fill="#1a365d"/><circle cx="115" cy="105" r="1.6" fill="#1a365d"/><circle cx="175" cy="85" r="1.3" fill="#1a365d"/>
                    <circle cx="58" cy="188" r="1.5" fill="#1a365d"/><circle cx="180" cy="198" r="1.3" fill="#1a365d"/><circle cx="245" cy="98" r="1.5" fill="#1a365d"/>
                    <path d="M50 50h10v8" stroke="#1a365d" stroke-width="1" stroke-dasharray="2 2" fill="none"/>
                    <path d="M175 85v10h10" stroke="#1a365d" stroke-width="1" stroke-dasharray="2 2" fill="none"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#doodle-phd)"/>
        </svg>
    </div>

    <div class="container mx-auto max-w-6xl relative z-10">
        <!-- Breadcrumb -->
        <div class="mb-4">
            <a href="index.php" class="text-[#1a365d] hover:text-blue-700 text-xs sm:text-sm font-semibold inline-flex items-center gap-1.5 transition-colors">
                <i class="fas fa-arrow-left text-xs"></i> Back to Home
            </a>
        </div>

        <!-- Hero Header Card -->
        <div class="bg-gradient-to-r from-[#1a365d] via-[#1e3a8a] to-[#2563eb] rounded-2xl sm:rounded-3xl p-6 sm:p-10 text-white shadow-lg mb-8 relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center justify-between gap-3 mb-3 flex-wrap">
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="bg-[#fbbf24] text-slate-900 text-xs font-extrabold px-3.5 py-1 rounded-full uppercase tracking-wider">Doctoral Research</span>
                        <span class="bg-white/20 backdrop-blur-md text-white text-xs font-semibold px-3 py-1 rounded-full">2 &ndash; 5 Years &bull; Full-Time</span>
                    </div>
                    <button onclick="window.print()" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white/15 hover:bg-white/25 backdrop-blur-md rounded-xl text-xs font-bold text-white border border-white/20 transition-all cursor-pointer shadow-sm">
                        <i class="fas fa-print"></i>
                        <span>Print / Save PDF</span>
                    </button>
                </div>
                <h1 class="text-2xl sm:text-4xl font-extrabold font-display tracking-tight mb-3">
                    Ph.D. Programme in Computer Science
                </h1>
                <p class="text-blue-100 text-xs sm:text-base max-w-3xl leading-relaxed">
                    Six-Monthly Pre-Registration Coursework and Frontier Doctoral Research covering Artificial Intelligence, Generative AI, Cloud Security, Data Analytics, and Theoretical Computer Science.
                </p>
            </div>
        </div>

        <!-- Key Metrics Strip -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mb-8">
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1">Coursework</span>
                <span class="text-lg sm:text-xl font-extrabold text-[#1a365d]">6 Months</span>
                <span class="text-[11px] text-slate-500 block">Pre-Registration</span>
            </div>
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1">Intake Capacity</span>
                <span class="text-lg sm:text-xl font-extrabold text-[#1a365d]">Per Vacancy</span>
                <span class="text-[11px] text-slate-500 block">Supervisor Based</span>
            </div>
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1">Admission Basis</span>
                <span class="text-lg sm:text-xl font-extrabold text-[#1a365d]">DURAT / NET</span>
                <span class="text-[11px] text-slate-500 block">+ Personal Viva</span>
            </div>
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1">Research Areas</span>
                <span class="text-lg sm:text-xl font-extrabold text-[#1a365d]">AI &bull; ML &bull; NLP</span>
                <span class="text-[11px] text-slate-500 block">Funded Labs</span>
            </div>
        </div>

        <!-- 2x2 Clean Content Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
            <!-- 1. About Card -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm flex flex-col justify-between">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold font-display uppercase tracking-wide text-[#1a365d] mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <i class="fas fa-microscope text-[#fbbf24]"></i> About the Ph.D. Programme
                    </h2>
                    <p class="text-slate-700 text-sm sm:text-[15px] leading-relaxed mb-4">
                        This is a mandatory <strong class="text-slate-900 font-bold">Six-Month Pre-Registration Course Work</strong> for scholars pursuing a doctoral degree in Computer Science. The coursework ensures candidates develop rigorous research methodology, critical literature review synthesis, and advanced domain expertise before submitting their formal research proposal.
                    </p>
                    <p class="text-slate-700 text-sm sm:text-[15px] leading-relaxed">
                        The coursework features advanced scholarly seminars, peer presentations, journal assessments, and thesis formulation under recognized university research supervisors.
                    </p>
                </div>
            </div>

            <!-- 2. Eligibility & Admission -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm flex flex-col justify-between">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold font-display uppercase tracking-wide text-[#1a365d] mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <i class="fas fa-user-check text-[#fbbf24]"></i> Eligibility &amp; Admission
                    </h2>
                    <p class="text-slate-700 text-sm sm:text-[15px] leading-relaxed mb-4">
                        Candidates holding a Master's degree (M.Tech / M.Sc. / MCA) in Computer Science or allied disciplines with a minimum of <strong class="text-slate-900 font-bold">50% marks in aggregate</strong>.
                    </p>
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200/70 mb-3 text-xs sm:text-sm text-slate-700 leading-relaxed space-y-1.5">
                        <p><span class="font-bold text-[#1a365d]">&bull; Entrance Requirement:</span> Must qualify in the Dibrugarh University Research Admission Test (DURAT) or hold UGC-NET (including JRF) / SLET / GATE.</p>
                        <p><span class="font-bold text-[#1a365d]">&bull; Supervisor Allotment:</span> Final admission is subject to vacancy availability with recognized faculty supervisors.</p>
                    </div>
                </div>
            </div>

            <!-- 3. Duration & Research Timeline -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm flex flex-col justify-between">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold font-display uppercase tracking-wide text-[#1a365d] mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <i class="fas fa-hourglass-half text-[#fbbf24]"></i> Programme Duration &amp; Milestones
                    </h2>
                    <p class="text-slate-700 text-sm sm:text-[15px] leading-relaxed mb-4">
                        The Ph.D. Programme spans between <strong class="text-slate-900 font-bold">2 to 5 years</strong> based on research momentum and publication progress:
                    </p>
                    <div class="space-y-2.5 text-xs sm:text-sm text-slate-700 font-medium">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/60 flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-[#1a365d] text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">1</span>
                            <div><strong class="text-[#1a365d]">Semester 1:</strong> Pre-Registration Coursework (Research Methodology &amp; Domain Elective)</div>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/60 flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-[#1a365d] text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">2</span>
                            <div><strong class="text-[#1a365d]">Formal Registration:</strong> Title Defense &amp; DRC Proposal Approval</div>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/60 flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-[#1a365d] text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">3</span>
                            <div><strong class="text-[#1a365d]">Execution:</strong> Scopus / SCI Journal Publications &amp; Colloquium Presentations</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Course Work Syllabus & Download -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm flex flex-col justify-between">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold font-display uppercase tracking-wide text-[#1a365d] mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <i class="fas fa-file-alt text-[#fbbf24]"></i> Coursework Syllabus
                    </h2>
                    <p class="text-slate-700 text-sm sm:text-[15px] leading-relaxed mb-4">
                        The Pre-Registration Coursework covers rigorous scientific frameworks:
                    </p>
                    <div class="grid grid-cols-2 gap-2.5 mb-6 text-xs sm:text-sm text-slate-700 font-medium">
                        <div class="flex items-center gap-2 p-2 bg-slate-50 rounded-lg border border-slate-200/60">
                            <i class="fas fa-check text-emerald-600 text-xs"></i> Research Methodologies
                        </div>
                        <div class="flex items-center gap-2 p-2 bg-slate-50 rounded-lg border border-slate-200/60">
                            <i class="fas fa-check text-emerald-600 text-xs"></i> Literature Review Ethics
                        </div>
                        <div class="flex items-center gap-2 p-2 bg-slate-50 rounded-lg border border-slate-200/60">
                            <i class="fas fa-check text-emerald-600 text-xs"></i> Quantitative Analysis
                        </div>
                        <div class="flex items-center gap-2 p-2 bg-slate-50 rounded-lg border border-slate-200/60">
                            <i class="fas fa-check text-emerald-600 text-xs"></i> Domain Specific Elective
                        </div>
                    </div>
                </div>

                <a href="downloads/PhDSyllabus.pdf" download="PhD_Coursework_Syllabus.pdf"
                    class="inline-flex items-center justify-center gap-2 w-full px-5 py-3.5 bg-[#1a365d] hover:bg-[#152c4d] text-white font-bold text-sm rounded-xl transition-colors shadow-sm">
                    <i class="fas fa-file-pdf text-[#fbbf24] text-base"></i>
                    <span>Download Coursework Syllabus (PDF)</span>
                </a>
            </div>
        </div>
    </div>
</section>

<?php
include 'templates/footer.php';
?>
