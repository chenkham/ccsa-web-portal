<?php
declare(strict_types=1);

/**
 * Bachelor of Computer Applications (BCA) Program Page
 * Centre for Computer Science and Applications (CCSA), Dibrugarh University
 */

$pageTitle = 'Bachelor of Computer Applications (BCA) - CCSA';
$currentPage = 'programs';
include 'templates/header.php';
?>

<section class="py-12 sm:py-16 px-4 bg-slate-50 min-h-screen relative overflow-hidden">
    <!-- 🎓 Academic Doodle Background Pattern -->
    <div class="absolute inset-0 pointer-events-none opacity-[0.06] select-none">
        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="doodle-ug" width="260" height="260" patternUnits="userSpaceOnUse" patternTransform="rotate(-1)">
                    <g stroke="#1a365d" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <!-- Open Book -->
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
                        <!-- Pencil -->
                        <g transform="translate(75, 48) rotate(45)">
                            <path d="M4 18L13 3L19 9L10 24L3 25L4 18Z" />
                            <path d="M10 7L15 12" />
                        </g>
                        <!-- Diploma Scroll -->
                        <g transform="translate(205, 52) rotate(-6)">
                            <path d="M6 4C6 4 3 4 3 7C3 10 6 10 6 10H20C20 10 23 10 23 13C23 16 20 16 20 16" />
                            <path d="M6 4V18C6 18 3 18 3 21C3 24 6 24 6 24H20C20 24 23 24 23 21C23 18 20 18 20 18V10" />
                            <path d="M10 12H16" />
                        </g>
                        <!-- Chalkboard -->
                        <g transform="translate(10, 95) rotate(4)">
                            <rect x="3" y="4" width="22" height="16" rx="1.5" />
                            <path d="M6 20V24M22 20V24M2 20H26" />
                            <text x="7" y="15" font-family="sans-serif" font-size="8" font-weight="bold" fill="#1a365d" stroke="none">ABC</text>
                        </g>
                        <!-- Trophy -->
                        <g transform="translate(145, 95) rotate(-3)">
                            <path d="M8 5V11C8 14 10.5 16.5 13.5 16.5C16.5 16.5 19 14 19 11V5H8Z" />
                            <path d="M5 5H8V9H5C3.5 9 2.5 7.8 2.5 6.5C2.5 5.2 3.5 5 5 5" />
                            <path d="M19 5H22C23.5 5 24.5 5.2 24.5 6.5C24.5 7.8 23.5 9 22 9H19" />
                            <path d="M13.5 16.5V22M9 22H18" />
                        </g>
                        <!-- Calculator -->
                        <g transform="translate(70, 150) rotate(3)">
                            <rect x="3" y="2" width="18" height="23" rx="2" />
                            <rect x="6" y="5" width="12" height="5" />
                            <circle cx="8" cy="14" r="0.9" /> <circle cx="12" cy="14" r="0.9" /> <circle cx="16" cy="14" r="0.9" />
                            <circle cx="8" cy="18" r="0.9" /> <circle cx="12" cy="18" r="0.9" /> <circle cx="16" cy="18" r="0.9" />
                        </g>
                        <!-- Notebook -->
                        <g transform="translate(195, 155) rotate(-4)">
                            <rect x="5" y="3" width="18" height="22" rx="2" />
                            <path d="M2 6H8M2 10H8M2 14H8M2 18H8" />
                            <path d="M10 8H19M10 12H19M10 16H16" />
                        </g>
                        <!-- Certificate Ribbon -->
                        <g transform="translate(12, 195) rotate(-5)">
                            <circle cx="12" cy="10" r="7"/>
                            <path d="M8 15L6 23L12 20L18 23L16 15"/>
                        </g>
                        <!-- Lightbulb Idea -->
                        <g transform="translate(135, 200) rotate(4)">
                            <path d="M12 2C8 2 6 5 6 8C6 10.5 8 12.5 9 14H15C16 12.5 18 10.5 18 8C18 5 16 2 12 2Z"/>
                            <path d="M9 16H15M10 18H14"/>
                        </g>
                    </g>
                    <!-- Fillers -->
                    <text x="68" y="28" font-family="'Courier New',monospace" font-size="13" font-weight="900" fill="#1a365d">BCA</text>
                    <text x="195" y="30" font-family="'Courier New',monospace" font-size="11" font-weight="bold" fill="#1a365d">GPA</text>
                    <text x="12" y="72" font-family="'Courier New',monospace" font-size="14" font-weight="900" fill="#1a365d">∞</text>
                    <text x="125" y="75" font-family="'Courier New',monospace" font-size="12" font-weight="bold" fill="#1a365d">π</text>
                    <text x="75" y="125" font-family="'Courier New',monospace" font-size="11" font-weight="bold" fill="#1a365d">∫dx</text>
                    <text x="210" y="130" font-family="'Courier New',monospace" font-size="12" font-weight="900" fill="#1a365d">100%</text>
                    <text x="128" y="175" font-family="'Courier New',monospace" font-size="12" font-weight="bold" fill="#1a365d">√x</text>
                    <text x="15" y="172" font-family="'Courier New',monospace" font-size="14" font-weight="900" fill="#1a365d">+</text>
                    <text x="68" y="230" font-family="'Courier New',monospace" font-size="11" font-weight="900" fill="#1a365d">A+</text>
                    <text x="195" y="235" font-family="'Courier New',monospace" font-size="11" font-weight="bold" fill="#1a365d">CCSA</text>
                    <!-- Dots & Connectors -->
                    <circle cx="50" cy="50" r="1.3" fill="#1a365d"/><circle cx="115" cy="105" r="1.6" fill="#1a365d"/><circle cx="175" cy="85" r="1.3" fill="#1a365d"/>
                    <circle cx="58" cy="188" r="1.5" fill="#1a365d"/><circle cx="180" cy="198" r="1.3" fill="#1a365d"/><circle cx="245" cy="98" r="1.5" fill="#1a365d"/>
                    <path d="M50 50h10v8" stroke="#1a365d" stroke-width="1" stroke-dasharray="2 2" fill="none"/>
                    <path d="M175 85v10h10" stroke="#1a365d" stroke-width="1" stroke-dasharray="2 2" fill="none"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#doodle-ug)"/>
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
                        <span class="bg-[#fbbf24] text-slate-900 text-xs font-extrabold px-3.5 py-1 rounded-full uppercase tracking-wider">Undergraduate Degree</span>
                        <span class="bg-white/20 backdrop-blur-md text-white text-xs font-semibold px-3 py-1 rounded-full">3 Years &bull; 6 Semesters</span>
                    </div>
                    <button onclick="window.print()" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white/15 hover:bg-white/25 backdrop-blur-md rounded-xl text-xs font-bold text-white border border-white/20 transition-all cursor-pointer shadow-sm">
                        <i class="fas fa-print"></i>
                        <span>Print / Save PDF</span>
                    </button>
                </div>
                <h1 class="text-2xl sm:text-4xl font-extrabold font-display tracking-tight mb-3">
                    Bachelor of Computer Applications (BCA)
                </h1>
                <p class="text-blue-100 text-xs sm:text-base max-w-3xl leading-relaxed">
                    A premier undergraduate programme preparing graduates for technological leadership, software engineering, and advanced academic pursuits in computer science.
                </p>
            </div>
        </div>

        <!-- Key Metrics Strip -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mb-8">
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1">Duration</span>
                <span class="text-lg sm:text-xl font-extrabold text-[#1a365d]">3 Years</span>
                <span class="text-[11px] text-slate-500 block">6 Semesters</span>
            </div>
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1">Intake Capacity</span>
                <span class="text-lg sm:text-xl font-extrabold text-[#1a365d]">60 Seats</span>
                <span class="text-[11px] text-slate-500 block">+6 Endowments</span>
            </div>
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1">Admission Basis</span>
                <span class="text-lg sm:text-xl font-extrabold text-[#1a365d]">Merit List</span>
                <span class="text-[11px] text-slate-500 block">10+2 Aggregate</span>
            </div>
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1">Affiliation</span>
                <span class="text-lg sm:text-xl font-extrabold text-[#1a365d]">Dibrugarh Univ.</span>
                <span class="text-[11px] text-slate-500 block">Approved by AICTE</span>
            </div>
        </div>

        <!-- 2x2 Clean Content Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
            <!-- 1. About Card -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm flex flex-col justify-between">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold font-display uppercase tracking-wide text-[#1a365d] mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <i class="fas fa-info-circle text-[#fbbf24]"></i> About the Programme
                    </h2>
                    <p class="text-slate-700 text-sm sm:text-[15px] leading-relaxed mb-4">
                        The Bachelor of Computer Applications (BCA) is a Three (03) Year Degree Program structured across Six (06) Semesters. Selection procedure for the BCA program is strictly conducted on a <strong class="text-slate-900 font-bold">Merit Basis</strong>.
                    </p>
                    <p class="text-slate-700 text-sm sm:text-[15px] leading-relaxed">
                        A BCA degree provides a solid foundation for a successful career in the modern IT industry. It equips students with essential technical expertise, algorithmic problem-solving abilities, and practical engineering experience needed to excel in software engineering, full-stack development, cloud computing, and post-graduate studies.
                    </p>
                </div>
            </div>

            <!-- 2. Eligibility & Admission -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm flex flex-col justify-between">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold font-display uppercase tracking-wide text-[#1a365d] mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <i class="fas fa-user-check text-[#fbbf24]"></i> Eligibility Criteria
                    </h2>
                    <p class="text-slate-700 text-sm sm:text-[15px] leading-relaxed mb-4">
                        Candidates must have passed the Higher Secondary (10+2) Examination with a minimum of <strong class="text-slate-900 font-bold">45% marks in aggregate</strong>. Applicants who have studied Mathematics/Commercial Arithmetic/Statistics as one of the subjects shall be considered eligible.
                    </p>
                    <p class="text-slate-600 text-xs sm:text-sm leading-relaxed mb-3 bg-slate-50 p-3.5 rounded-xl border border-slate-200/70">
                        <span class="font-bold text-[#1a365d]">Non-Math Applicants:</span> Candidates who have not studied Mathematics or have not qualified in the subject may also be eligible, subject to taking additional audit classes in Mathematics concurrent with regular BCA coursework.
                    </p>
                    <p class="text-slate-500 text-xs font-medium">
                        * A relaxation of 5% in aggregate marks is applicable to candidates belonging to reserved categories under prevailing university rules.
                    </p>
                </div>
            </div>

            <!-- 3. Curriculum & Syllabus Download -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm flex flex-col justify-between">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold font-display uppercase tracking-wide text-[#1a365d] mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <i class="fas fa-book-open text-[#fbbf24]"></i> Curriculum &amp; Core Areas
                    </h2>
                    <p class="text-slate-700 text-sm sm:text-[15px] leading-relaxed mb-4">
                        The curriculum provides a balanced blend of fundamental computing science and applied software development skills:
                    </p>
                    <div class="grid grid-cols-2 gap-2.5 mb-6 text-xs sm:text-sm text-slate-700 font-medium">
                        <div class="flex items-center gap-2 p-2 bg-slate-50 rounded-lg border border-slate-200/60">
                            <i class="fas fa-check text-emerald-600 text-xs"></i> C, C++, Java &amp; Python
                        </div>
                        <div class="flex items-center gap-2 p-2 bg-slate-50 rounded-lg border border-slate-200/60">
                            <i class="fas fa-check text-emerald-600 text-xs"></i> Data Structures &amp; Algorithms
                        </div>
                        <div class="flex items-center gap-2 p-2 bg-slate-50 rounded-lg border border-slate-200/60">
                            <i class="fas fa-check text-emerald-600 text-xs"></i> DBMS &amp; SQL Systems
                        </div>
                        <div class="flex items-center gap-2 p-2 bg-slate-50 rounded-lg border border-slate-200/60">
                            <i class="fas fa-check text-emerald-600 text-xs"></i> Web Technologies
                        </div>
                        <div class="flex items-center gap-2 p-2 bg-slate-50 rounded-lg border border-slate-200/60">
                            <i class="fas fa-check text-emerald-600 text-xs"></i> Operating Systems
                        </div>
                        <div class="flex items-center gap-2 p-2 bg-slate-50 rounded-lg border border-slate-200/60">
                            <i class="fas fa-check text-emerald-600 text-xs"></i> Software Engineering
                        </div>
                    </div>
                </div>

                <a href="downloads/BCASyllabus.pdf" download="BCASyllabus.pdf"
                    class="inline-flex items-center justify-center gap-2 w-full px-5 py-3.5 bg-[#1a365d] hover:bg-[#152c4d] text-white font-bold text-sm rounded-xl transition-colors shadow-sm">
                    <i class="fas fa-file-pdf text-[#fbbf24] text-base"></i>
                    <span>Download Detailed Syllabus (PDF)</span>
                </a>
            </div>

            <!-- 4. Fee Structure -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm flex flex-col justify-between">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold font-display uppercase tracking-wide text-[#1a365d] mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <i class="fas fa-receipt text-[#fbbf24]"></i> Semester Fee Structure
                    </h2>
                    
                    <!-- Table -->
                    <div class="rounded-xl overflow-hidden border border-slate-200 mb-6">
                        <table class="w-full text-left text-xs sm:text-sm">
                            <thead class="bg-slate-100 border-b border-slate-200 text-slate-700">
                                <tr>
                                    <th class="px-4 py-2.5 font-bold">Semester</th>
                                    <th class="px-4 py-2.5 font-bold text-right">Fee (INR)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5">1<sup>st</sup> Semester</td>
                                    <td class="px-4 py-2.5 font-bold text-slate-900 text-right">Rs. 16,475</td>
                                </tr>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5">2<sup>nd</sup> Semester</td>
                                    <td class="px-4 py-2.5 font-bold text-slate-900 text-right">Rs. 13,000</td>
                                </tr>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5">3<sup>rd</sup> Semester</td>
                                    <td class="px-4 py-2.5 font-bold text-slate-900 text-right">Rs. 16,380</td>
                                </tr>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5">4<sup>th</sup> Semester</td>
                                    <td class="px-4 py-2.5 font-bold text-slate-900 text-right">Rs. 13,000</td>
                                </tr>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5">5<sup>th</sup> Semester</td>
                                    <td class="px-4 py-2.5 font-bold text-slate-900 text-right">Rs. 16,380</td>
                                </tr>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5">6<sup>th</sup> Semester</td>
                                    <td class="px-4 py-2.5 font-bold text-slate-900 text-right">Rs. 7,800</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <a href="downloads/FeeStructureCCSA.pdf" download="FeeStructureCCSA.pdf"
                    class="inline-flex items-center justify-center gap-2 w-full px-5 py-3.5 bg-slate-100 hover:bg-slate-200 text-[#1a365d] border border-slate-300 font-bold text-sm rounded-xl transition-colors shadow-sm">
                    <i class="fas fa-file-invoice text-[#1a365d] text-base"></i>
                    <span>Download Official Fee Structure (PDF)</span>
                </a>
            </div>
        </div>
    </div>
</section>

<?php
include 'templates/footer.php';
?>
