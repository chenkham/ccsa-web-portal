<?php
declare(strict_types=1);

/**
 * Faculty Publications and Conferences Directory
 * Centre for Computer Science and Applications (CCSA), Dibrugarh University
 * Exact markup & card rendering logic from https://www.ccsdu.in/publication.html
 */

$pageTitle = 'Faculty Publications & Conferences - CCSA';
$currentPage = 'research';
include 'templates/header.php';
?>

<main class="min-h-screen bg-slate-50/70 py-12 sm:py-16 relative overflow-hidden">
    <!-- 🔬 Research & Academic SVG Doodle Pattern Background -->
    <div class="absolute inset-0 pointer-events-none opacity-[0.06] select-none">
        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="doodle-publications" width="260" height="260" patternUnits="userSpaceOnUse" patternTransform="rotate(-2)">
                    <g stroke="#1a365d" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <!-- Microscope -->
                        <g transform="translate(10, 8) rotate(4)">
                            <path d="M12 20A6 6 0 0 1 6 14V8" />
                            <rect x="10" y="2" width="6" height="11" transform="rotate(30 13 8)" />
                            <path d="M3 23H21M9 23V20H15V23" />
                            <circle cx="16" cy="14" r="2" />
                        </g>
                        <!-- Open Journal / Research Paper -->
                        <g transform="translate(140, 10) rotate(-3)">
                            <path d="M6 5V20C6 20 9 18 14 18C19 18 22 20 22 20V5C22 5 19 3 14 3C9 3 6 5 6 5Z" />
                            <path d="M14 3V18" />
                            <path d="M9 8H12M9 11H12M16 8H19M16 11H19" />
                        </g>
                        <!-- Atom / Molecule -->
                        <g transform="translate(75, 48) rotate(5)">
                            <ellipse cx="12" cy="12" rx="11" ry="4" transform="rotate(30 12 12)" />
                            <ellipse cx="12" cy="12" rx="11" ry="4" transform="rotate(90 12 12)" />
                            <ellipse cx="12" cy="12" rx="11" ry="4" transform="rotate(150 12 12)" />
                            <circle cx="12" cy="12" r="2" fill="#1a365d" />
                        </g>
                        <!-- Brain with Neural Path -->
                        <g transform="translate(205, 52) rotate(6)">
                            <path d="M14 6C10 6 7 9 7 13C7 17 10 20 14 20C18 20 21 17 21 13C21 9 18 6 14 6Z" stroke-dasharray="2 1.5" />
                            <circle cx="11" cy="12" r="1.8" /> <circle cx="17" cy="14" r="1.8" />
                        </g>
                        <!-- Lecture Podium -->
                        <g transform="translate(10, 95) rotate(4)">
                            <path d="M6 8H20L17 22H9L6 8Z" />
                            <path d="M4 8H22M9 2H17V8H9V2Z" />
                            <circle cx="13" cy="5" r="1.5" fill="#1a365d" />
                        </g>
                        <!-- Test Tube & Flask -->
                        <g transform="translate(145, 98) rotate(-6)">
                            <path d="M8 3V16C8 19 10.5 22 13.5 22C16.5 22 19 19 19 16V3" />
                            <path d="M6 3H21M8 11H19" />
                            <circle cx="13.5" cy="17" r="1.5" fill="#1a365d" />
                        </g>
                        <!-- Spectacles -->
                        <g transform="translate(70, 150) rotate(-7)">
                            <rect x="2" y="8" width="9" height="6" rx="2" />
                            <rect x="15" y="8" width="9" height="6" rx="2" />
                            <path d="M11 11C11 11 12.5 9.5 14 9.5C15.5 9.5 17 11 17 11" />
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
            <rect width="100%" height="100%" fill="url(#doodle-publications)"/>
        </svg>
    </div>

    <div class="container mx-auto px-4 lg:px-10 relative z-10">
        <!-- Header -->
        <div class="mb-10 text-center">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold font-display uppercase tracking-wide text-[#1a365d]">
                Faculty Publications &amp; Conferences
            </h1>
            <p class="text-xs sm:text-sm font-semibold uppercase tracking-wider text-slate-500 mt-2">
                Research Papers, Indexed Journals &amp; Conference Proceedings of CCSA Faculty
            </p>
            <div class="w-20 h-1 bg-[#fbbf24] mt-3 rounded-full mx-auto"></div>
        </div>

        <!-- Faculty Selector Grid with Authentic Photos -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-12">
            <!-- 1. Dr. Utpala Borgohain -->
            <div class="faculty-tab group cursor-pointer p-3.5 bg-white/95 hover:bg-white rounded-2xl shadow-sm hover:shadow-lg border border-slate-200/90 transition-all duration-300 flex items-center gap-3" data-staff-id="364">
                <div class="w-12 h-12 sm:w-14 sm:h-14 flex-shrink-0 rounded-full overflow-hidden border-2 border-slate-200 group-hover:border-[#fbbf24] shadow-sm bg-slate-100">
                    <img src="faculty/UtpolaMam.png" alt="Dr. Utpala Borgohain" class="w-full h-full object-cover object-top block" onerror="this.src='faculty/du.png'">
                </div>
                <div class="min-w-0 flex-1 text-left">
                    <h4 class="font-bold text-slate-800 text-xs sm:text-sm group-hover:text-[#1a365d] transition-colors truncate">Dr. Utpala Borgohain</h4>
                    <p class="text-[11px] text-slate-500 font-medium truncate">Assistant Professor</p>
                    <span class="inline-block text-[10px] text-indigo-700 font-semibold truncate">NLP &amp; Data Mining</span>
                </div>
            </div>

            <!-- 2. Dr. Rizwan Rehman -->
            <div class="faculty-tab group cursor-pointer p-3.5 bg-white/95 hover:bg-white rounded-2xl shadow-sm hover:shadow-lg border border-slate-200/90 transition-all duration-300 flex items-center gap-3" data-staff-id="365">
                <div class="w-12 h-12 sm:w-14 sm:h-14 flex-shrink-0 rounded-full overflow-hidden border-2 border-slate-200 group-hover:border-[#fbbf24] shadow-sm bg-slate-100">
                    <img src="faculty/Rizwan_SIr.png" alt="Dr. Rizwan Rehman" class="w-full h-full object-cover object-top block" onerror="this.src='faculty/du.png'">
                </div>
                <div class="min-w-0 flex-1 text-left">
                    <h4 class="font-bold text-slate-800 text-xs sm:text-sm group-hover:text-[#1a365d] transition-colors truncate">Dr. Rizwan Rehman</h4>
                    <p class="text-[11px] text-slate-500 font-medium truncate">Assistant Professor</p>
                    <span class="inline-block text-[10px] text-indigo-700 font-semibold truncate">Cloud Computing &amp; ML</span>
                </div>
            </div>

            <!-- 3. Dr. Toralima Bora -->
            <div class="faculty-tab group cursor-pointer p-3.5 bg-white/95 hover:bg-white rounded-2xl shadow-sm hover:shadow-lg border border-slate-200/90 transition-all duration-300 flex items-center gap-3" data-staff-id="398">
                <div class="w-12 h-12 sm:w-14 sm:h-14 flex-shrink-0 rounded-full overflow-hidden border-2 border-slate-200 group-hover:border-[#fbbf24] shadow-sm bg-slate-100">
                    <img src="faculty/ToraliMam.png" alt="Dr. Toralima Bora" class="w-full h-full object-cover object-top block" onerror="this.src='faculty/du.png'">
                </div>
                <div class="min-w-0 flex-1 text-left">
                    <h4 class="font-bold text-slate-800 text-xs sm:text-sm group-hover:text-[#1a365d] transition-colors truncate">Dr. Toralima Bora</h4>
                    <p class="text-[11px] text-slate-500 font-medium truncate">Assistant Professor</p>
                    <span class="inline-block text-[10px] text-indigo-700 font-semibold truncate">Information Systems</span>
                </div>
            </div>

            <!-- 4. Dr. Ujjal Saikia -->
            <div class="faculty-tab group cursor-pointer p-3.5 bg-white/95 hover:bg-white rounded-2xl shadow-sm hover:shadow-lg border border-slate-200/90 transition-all duration-300 flex items-center gap-3" data-staff-id="397">
                <div class="w-12 h-12 sm:w-14 sm:h-14 flex-shrink-0 rounded-full overflow-hidden border-2 border-slate-200 group-hover:border-[#fbbf24] shadow-sm bg-slate-100">
                    <img src="faculty/UjjalSir.png" alt="Dr. Ujjal Saikia" class="w-full h-full object-cover object-top block" onerror="this.src='faculty/du.png'">
                </div>
                <div class="min-w-0 flex-1 text-left">
                    <h4 class="font-bold text-slate-800 text-xs sm:text-sm group-hover:text-[#1a365d] transition-colors truncate">Dr. Ujjal Saikia</h4>
                    <p class="text-[11px] text-slate-500 font-medium truncate">Assistant Professor</p>
                    <span class="inline-block text-[10px] text-indigo-700 font-semibold truncate">Image Processing &amp; CV</span>
                </div>
            </div>

            <!-- 5. Ms. Kimasha Borah -->
            <div class="faculty-tab group cursor-pointer p-3.5 bg-white/95 hover:bg-white rounded-2xl shadow-sm hover:shadow-lg border border-slate-200/90 transition-all duration-300 flex items-center gap-3" data-staff-id="396">
                <div class="w-12 h-12 sm:w-14 sm:h-14 flex-shrink-0 rounded-full overflow-hidden border-2 border-slate-200 group-hover:border-[#fbbf24] shadow-sm bg-slate-100">
                    <img src="faculty/KimashaMam.png" alt="Ms. Kimasha Borah" class="w-full h-full object-cover object-top block" onerror="this.src='faculty/du.png'">
                </div>
                <div class="min-w-0 flex-1 text-left">
                    <h4 class="font-bold text-slate-800 text-xs sm:text-sm group-hover:text-[#1a365d] transition-colors truncate">Ms. Kimasha Borah</h4>
                    <p class="text-[11px] text-slate-500 font-medium truncate">Assistant Professor</p>
                    <span class="inline-block text-[10px] text-indigo-700 font-semibold truncate">Software Engineering</span>
                </div>
            </div>

            <!-- 6. Dr. Pranjal Kumar Bora -->
            <div class="faculty-tab group cursor-pointer p-3.5 bg-white/95 hover:bg-white rounded-2xl shadow-sm hover:shadow-lg border border-slate-200/90 transition-all duration-300 flex items-center gap-3" data-staff-id="393">
                <div class="w-12 h-12 sm:w-14 sm:h-14 flex-shrink-0 rounded-full overflow-hidden border-2 border-slate-200 group-hover:border-[#fbbf24] shadow-sm bg-slate-100">
                    <img src="faculty/PranjalSir.png" alt="Dr. Pranjal Kumar Bora" class="w-full h-full object-cover object-top block" onerror="this.src='faculty/du.png'">
                </div>
                <div class="min-w-0 flex-1 text-left">
                    <h4 class="font-bold text-slate-800 text-xs sm:text-sm group-hover:text-[#1a365d] transition-colors truncate">Dr. Pranjal Kumar Bora</h4>
                    <p class="text-[11px] text-slate-500 font-medium truncate">Assistant Professor</p>
                    <span class="inline-block text-[10px] text-indigo-700 font-semibold truncate">WSN &amp; IoT</span>
                </div>
            </div>

            <!-- 7. Mr. Ankumon Sarmah -->
            <div class="faculty-tab group cursor-pointer p-3.5 bg-white/95 hover:bg-white rounded-2xl shadow-sm hover:shadow-lg border border-slate-200/90 transition-all duration-300 flex items-center gap-3" data-staff-id="394">
                <div class="w-12 h-12 sm:w-14 sm:h-14 flex-shrink-0 rounded-full overflow-hidden border-2 border-slate-200 group-hover:border-[#fbbf24] shadow-sm bg-slate-100">
                    <img src="faculty/AnkumonSir.png" alt="Mr. Ankumon Sarmah" class="w-full h-full object-cover object-top block" onerror="this.src='faculty/du.png'">
                </div>
                <div class="min-w-0 flex-1 text-left">
                    <h4 class="font-bold text-slate-800 text-xs sm:text-sm group-hover:text-[#1a365d] transition-colors truncate">Mr. Ankumon Sarmah</h4>
                    <p class="text-[11px] text-slate-500 font-medium truncate">Assistant Professor</p>
                    <span class="inline-block text-[10px] text-indigo-700 font-semibold truncate">Pattern Recognition</span>
                </div>
            </div>

            <!-- 8. Ms. Kankana Dutta -->
            <div class="faculty-tab group cursor-pointer p-3.5 bg-white/95 hover:bg-white rounded-2xl shadow-sm hover:shadow-lg border border-slate-200/90 transition-all duration-300 flex items-center gap-3" data-staff-id="395">
                <div class="w-12 h-12 sm:w-14 sm:h-14 flex-shrink-0 rounded-full overflow-hidden border-2 border-slate-200 group-hover:border-[#fbbf24] shadow-sm bg-slate-100">
                    <img src="faculty/KanKanMam.png" alt="Ms. Kankana Dutta" class="w-full h-full object-cover object-top block" onerror="this.src='faculty/du.png'">
                </div>
                <div class="min-w-0 flex-1 text-left">
                    <h4 class="font-bold text-slate-800 text-xs sm:text-sm group-hover:text-[#1a365d] transition-colors truncate">Ms. Kankana Dutta</h4>
                    <p class="text-[11px] text-slate-500 font-medium truncate">Assistant Professor</p>
                    <span class="inline-block text-[10px] text-indigo-700 font-semibold truncate">Data Analytics</span>
                </div>
            </div>

            <!-- 9. Dr. Sumpi Saikia -->
            <div class="faculty-tab group cursor-pointer p-3.5 bg-white/95 hover:bg-white rounded-2xl shadow-sm hover:shadow-lg border border-slate-200/90 transition-all duration-300 flex items-center gap-3" data-staff-id="399">
                <div class="w-12 h-12 sm:w-14 sm:h-14 flex-shrink-0 rounded-full overflow-hidden border-2 border-slate-200 group-hover:border-[#fbbf24] shadow-sm bg-slate-100">
                    <img src="faculty/SumpiMam.png" alt="Dr. Sumpi Saikia" class="w-full h-full object-cover object-top block" onerror="this.src='faculty/du.png'">
                </div>
                <div class="min-w-0 flex-1 text-left">
                    <h4 class="font-bold text-slate-800 text-xs sm:text-sm group-hover:text-[#1a365d] transition-colors truncate">Dr. Sumpi Saikia</h4>
                    <p class="text-[11px] text-slate-500 font-medium truncate">Assistant Professor</p>
                    <span class="inline-block text-[10px] text-indigo-700 font-semibold truncate">Graph Algorithms</span>
                </div>
            </div>

            <!-- 10. Ms. Pinakshi Konwar -->
            <div class="faculty-tab group cursor-pointer p-3.5 bg-white/95 hover:bg-white rounded-2xl shadow-sm hover:shadow-lg border border-slate-200/90 transition-all duration-300 flex items-center gap-3" data-staff-id="4626">
                <div class="w-12 h-12 sm:w-14 sm:h-14 flex-shrink-0 rounded-full overflow-hidden border-2 border-slate-200 group-hover:border-[#fbbf24] shadow-sm bg-slate-100">
                    <img src="faculty/PinakshiMam.png" alt="Ms. Pinakshi Konwar" class="w-full h-full object-cover object-top block" onerror="this.src='faculty/du.png'">
                </div>
                <div class="min-w-0 flex-1 text-left">
                    <h4 class="font-bold text-slate-800 text-xs sm:text-sm group-hover:text-[#1a365d] transition-colors truncate">Ms. Pinakshi Konwar</h4>
                    <p class="text-[11px] text-slate-500 font-medium truncate">Assistant Professor</p>
                    <span class="inline-block text-[10px] text-indigo-700 font-semibold truncate">Database Systems</span>
                </div>
            </div>

            <!-- 11. Mr. Gunjan Malakar -->
            <div class="faculty-tab group cursor-pointer p-3.5 bg-white/95 hover:bg-white rounded-2xl shadow-sm hover:shadow-lg border border-slate-200/90 transition-all duration-300 flex items-center gap-3" data-staff-id="4629">
                <div class="w-12 h-12 sm:w-14 sm:h-14 flex-shrink-0 rounded-full overflow-hidden border-2 border-slate-200 group-hover:border-[#fbbf24] shadow-sm bg-slate-100">
                    <img src="faculty/Gunajn Sir.png" alt="Mr. Gunjan Malakar" class="w-full h-full object-cover object-top block" onerror="this.src='faculty/du.png'">
                </div>
                <div class="min-w-0 flex-1 text-left">
                    <h4 class="font-bold text-slate-800 text-xs sm:text-sm group-hover:text-[#1a365d] transition-colors truncate">Mr. Gunjan Malakar</h4>
                    <p class="text-[11px] text-slate-500 font-medium truncate">Assistant Professor</p>
                    <span class="inline-block text-[10px] text-indigo-700 font-semibold truncate">Computer Networks</span>
                </div>
            </div>
        </div>

        <!-- Publications Filter and Container -->
        <div class="mt-8">
            <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                <h3 class="text-xl sm:text-2xl text-[#1a365d] font-extrabold hidden" id="pub-head">Publications</h3>
                <div id="pub-filter-container" class="hidden w-full md:w-auto mt-4 md:mt-0">
                    <select id="pubYearFilter" class="block w-full md:w-48 px-4 py-2 bg-white border border-slate-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-slate-700 text-xs font-semibold">
                        <option value="all">All Years</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="publications-container">
                <div class="col-span-full text-center py-12 text-slate-500 italic bg-white/90 rounded-2xl shadow-sm border border-slate-200/80">
                    <i class="fas fa-book-reader text-3xl text-slate-400 mb-2 block"></i>
                    Please click on a faculty member above to load their research publications and conference proceedings.
                </div>
            </div>
        </div>

        <!-- Conferences Section -->
        <div id="conferences-section" class="mt-12 hidden">
            <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                <h3 class="text-xl sm:text-2xl text-[#1a365d] font-extrabold" id="conf-head">Conference Papers</h3>
                <div id="conf-filter-container" class="hidden w-full md:w-auto mt-4 md:mt-0">
                    <select id="confYearFilter" class="block w-full md:w-48 px-4 py-2 bg-white border border-slate-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-slate-700 text-xs font-semibold">
                        <option value="all">All Years</option>
                    </select>
                </div>
            </div>
            <div id="conferences-container" class="space-y-4"></div>
        </div>
    </div>
</main>

<script>
let currentFacultyPubs = [];
let currentFacultyConfs = [];

document.addEventListener('DOMContentLoaded', () => {
    // 1. Publication Filter Listener
    const pubYearFilter = document.getElementById('pubYearFilter');
    if (pubYearFilter) {
        pubYearFilter.addEventListener('change', function() {
            const selectedYear = this.value;
            let filteredPubs = currentFacultyPubs;
            if (selectedYear !== 'all') {
                filteredPubs = currentFacultyPubs.filter(p => (p.year || 0) == selectedYear);
            }
            renderPublications(filteredPubs);
        });
    }

    // 2. Conference Filter Listener
    const confYearFilter = document.getElementById('confYearFilter');
    if (confYearFilter) {
        confYearFilter.addEventListener('change', function() {
            const selectedYear = this.value;
            let filteredConfs = currentFacultyConfs;
            if (selectedYear !== 'all') {
                filteredConfs = currentFacultyConfs.filter(c => {
                    if (!c.conference_start_date) return false;
                    const d = new Date(c.conference_start_date);
                    return d.getFullYear() == selectedYear;
                });
            }
            renderConferences(filteredConfs);
        });
    }

    // 3. Faculty Tab Click Logic
    document.querySelectorAll('.faculty-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.faculty-tab').forEach(t => {
                t.classList.remove('ring-2', 'ring-[#fbbf24]', 'border-[#1a365d]', 'bg-blue-50/90', 'shadow-md');
                t.classList.add('bg-white/90');
                t.querySelector('h4')?.classList.remove('text-[#1a365d]');
            });
            this.classList.remove('bg-white/90');
            this.classList.add('ring-2', 'ring-[#fbbf24]', 'border-[#1a365d]', 'bg-blue-50/90', 'shadow-md');
            this.querySelector('h4')?.classList.add('text-[#1a365d]');
            
            const staffId = this.getAttribute('data-staff-id');
            loadPublications(staffId);
        });
    });
});

function loadPublications(staffId) {
    const pubContainer = document.getElementById('publications-container');
    const confContainer = document.getElementById('conferences-container');
    const pubHead = document.getElementById('pub-head');

    document.getElementById('pub-filter-container').classList.add('hidden');
    document.getElementById('conf-filter-container').classList.add('hidden');
    document.getElementById('conferences-section').classList.add('hidden');
    pubHead.classList.add('hidden');

    pubContainer.innerHTML = '<div class="col-span-full text-center py-12 text-indigo-600 font-semibold animate-pulse">Loading publications...</div>';
    confContainer.innerHTML = '';

    const oldScript = document.getElementById('faculty-data-script');
    if (oldScript) oldScript.remove();

    const script = document.createElement('script');
    script.id = 'faculty-data-script';
    script.src = `proxy.php?staff_id=${staffId}`;

    script.onload = function () {
        currentFacultyPubs = (window.publicationsData && window.publicationsData.data) ? window.publicationsData.data : [];
        currentFacultyConfs = (window.conferenceData && window.conferenceData.data) ? window.conferenceData.data.filter(conf => conf.staff_id == staffId) : [];

        renderPublications(currentFacultyPubs);

        if (currentFacultyPubs.length > 0) {
            pubHead.innerHTML = `Publications (${currentFacultyPubs.length})`;
            pubHead.classList.remove('hidden');
            populateFilter(currentFacultyPubs, 'pubYearFilter', 'year');
            document.getElementById('pub-filter-container').classList.remove('hidden');
        } else {
            pubHead.innerHTML = 'Publications (0)';
            pubHead.classList.remove('hidden');
            pubContainer.innerHTML = '<div class="col-span-full text-center text-gray-500 italic py-8">No publications found.</div>';
        }

        if (currentFacultyConfs.length > 0) {
            renderConferences(currentFacultyConfs);
            populateFilter(currentFacultyConfs, 'confYearFilter', 'date');
            document.getElementById('conf-filter-container').classList.remove('hidden');
        }

        delete window.publicationsData;
        delete window.conferenceData;
    };

    script.onerror = function () {
        pubContainer.innerHTML = '<div class="col-span-full text-center text-red-500 py-8">Error loading publications data.</div>';
    };

    document.body.appendChild(script);
}

function populateFilter(data, elementId, type) {
    const select = document.getElementById(elementId);
    select.innerHTML = '<option value="all">All Years</option>';
    const years = new Set();

    data.forEach(item => {
        let year;
        if (type === 'year') {
            year = item.year;
        } else if (type === 'date' && item.conference_start_date) {
            year = new Date(item.conference_start_date).getFullYear();
        }
        if (year) years.add(year);
    });

    const sortedYears = Array.from(years).sort((a, b) => b - a);
    sortedYears.forEach(y => {
        const opt = document.createElement('option');
        opt.value = y;
        opt.textContent = y;
        select.appendChild(opt);
    });

    select.value = 'all';
}

function renderPublications(publications) {
    const container = document.getElementById('publications-container');
    container.innerHTML = '';

    if (publications.length === 0) {
        container.innerHTML = '<div class="col-span-full text-center text-gray-500 italic py-8">No publications found for this selection.</div>';
        return;
    }

    publications.sort((a, b) => (b.year || 0) - (a.year || 0));

    publications.forEach(pub => {
        container.appendChild(createPublicationCard(pub));
    });
}

function renderConferences(conferences) {
    const container = document.getElementById('conferences-container');
    const section = document.getElementById('conferences-section');
    const head = document.getElementById('conf-head');

    container.innerHTML = '';

    if (conferences.length === 0) {
        const isFiltering = document.getElementById('confYearFilter').value !== 'all';
        if (isFiltering) {
            container.innerHTML = '<div class="text-center text-gray-500 italic py-4">No conference papers found for this year.</div>';
            section.classList.remove('hidden');
        } else {
            section.classList.add('hidden');
        }
        return;
    }

    section.classList.remove('hidden');
    head.innerHTML = `Conference Papers (${conferences.length})`;

    conferences.sort((a, b) => new Date(b.conference_start_date) - new Date(a.conference_start_date));

    conferences.forEach(conf => {
        container.appendChild(createConferenceCard(conf));
    });
}

function createPublicationCard(publication) {
    const card = document.createElement('div');
    card.className = 'publication-card bg-white rounded-xl shadow-sm p-4 sm:p-6 flex flex-col transition hover:shadow-md border border-slate-200/80';

    const title = document.createElement('h4');
    title.className = 'text-xs sm:text-sm lg:text-base font-semibold text-[#1a365d] mb-1.5 leading-snug';
    title.textContent = publication.title || 'Untitled Publication';

    const authors = document.createElement('p');
    authors.className = 'text-slate-600 mb-1.5 text-[11px] sm:text-xs leading-relaxed';
    authors.textContent = `Authors: ${publication.author_names || 'N/A'}`;

    const journalInfo = document.createElement('p');
    journalInfo.className = 'text-[11px] sm:text-xs text-slate-500 mb-3';
    journalInfo.textContent = `Published in ${publication.journal || publication.journal_name || 'N/A'}, ${publication.year || 'N/A'}`;

    const spacer = document.createElement('div');
    spacer.className = 'flex-grow';

    const link = publication.url || publication.file_path;

    card.appendChild(title);
    card.appendChild(authors);
    card.appendChild(journalInfo);
    card.appendChild(spacer);

    if (link) {
        const btnDiv = document.createElement('div');
        btnDiv.className = 'mt-2';
        const btn = document.createElement('a');
        btn.className = 'inline-block bg-blue-600 hover:bg-blue-700 text-white text-[11px] sm:text-xs font-semibold py-1.5 px-3 rounded-lg transition-colors';
        btn.textContent = publication.file_url ? 'View PDF' : 'Read More';
        btn.href = link;
        btn.target = '_blank';
        btnDiv.appendChild(btn);
        card.appendChild(btnDiv);
    }
    return card;
}

function createConferenceCard(conference) {
    const card = document.createElement('div');
    card.className = 'conference-card bg-white rounded-xl shadow-sm p-4 sm:p-6 flex flex-col border-l-4 border-blue-900 border border-slate-200/80';

    const title = document.createElement('h4');
    title.className = 'text-xs sm:text-sm lg:text-base font-semibold text-[#1a365d] mb-1.5 leading-snug';
    title.textContent = conference.title || 'Untitled Conference Paper';

    const details = document.createElement('div');
    details.className = 'text-[11px] sm:text-xs text-slate-600 space-y-1';

    details.innerHTML = `
        <p><span class="font-medium text-slate-700">Authors:</span> ${conference.author_names || 'N/A'}</p>
        <p><span class="font-medium text-slate-700">Conference:</span> ${conference.conference_name || 'N/A'}</p>
        <p><span class="font-medium text-slate-700">Organizer:</span> ${conference.organizer || 'N/A'}</p>
    `;

    if (conference.conference_start_date) {
        const d = new Date(conference.conference_start_date);
        const dateStr = `${d.getDate()}/${d.getMonth()+1}/${d.getFullYear()}`;
        details.innerHTML += `<p class="text-gray-500 mt-2"><i class="far fa-calendar-alt mr-1"></i> ${dateStr}</p>`;
    }

    card.appendChild(title);
    card.appendChild(details);

    if (conference.url) {
        const btn = document.createElement('a');
        btn.href = conference.url;
        btn.target = '_blank';
        btn.className = 'mt-4 inline-block text-blue-600 hover:text-blue-800 font-semibold text-xs';
        btn.textContent = 'View Details →';
        card.appendChild(btn);
    }

    return card;
}
</script>

<?php
include 'templates/footer.php';
?>
