<?php
declare(strict_types=1);

/**
 * Present Students Directory Page
 * Centre for Computer Science and Applications (CCSA), Dibrugarh University
 * Exact dynamic semester & student rendering from https://www.ccsdu.in/Present_Stu.html
 */

$pageTitle = 'Present Students - CCSA';
$currentPage = 'students';
include 'templates/header.php';
?>

<main class="min-h-screen">
    <!-- Student Cards Section -->
    <section class="py-16 bg-slate-50 relative overflow-hidden">
        <!-- 🎓 Student & Academics Doodle Background Pattern -->
        <div class="absolute inset-0 pointer-events-none opacity-[0.06] select-none">
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="doodle-students" width="260" height="260" patternUnits="userSpaceOnUse" patternTransform="rotate(-1)">
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
                        <text x="68" y="28" font-family="'Courier New',monospace" font-size="13" font-weight="900" fill="#1a365d">A+</text>
                        <text x="195" y="30" font-family="'Courier New',monospace" font-size="11" font-weight="bold" fill="#1a365d">GPA</text>
                        <text x="12" y="72" font-family="'Courier New',monospace" font-size="14" font-weight="900" fill="#1a365d">∞</text>
                        <text x="125" y="75" font-family="'Courier New',monospace" font-size="12" font-weight="bold" fill="#1a365d">π</text>
                        <text x="75" y="125" font-family="'Courier New',monospace" font-size="11" font-weight="bold" fill="#1a365d">∫dx</text>
                        <text x="210" y="130" font-family="'Courier New',monospace" font-size="12" font-weight="900" fill="#1a365d">100%</text>
                        <text x="128" y="175" font-family="'Courier New',monospace" font-size="12" font-weight="bold" fill="#1a365d">√x</text>
                        <text x="15" y="172" font-family="'Courier New',monospace" font-size="14" font-weight="900" fill="#1a365d">+</text>
                        <text x="68" y="230" font-family="'Courier New',monospace" font-size="11" font-weight="900" fill="#1a365d">BCA</text>
                        <text x="195" y="235" font-family="'Courier New',monospace" font-size="11" font-weight="bold" fill="#1a365d">MCA</text>
                        <!-- Dots & Connectors -->
                        <circle cx="50" cy="50" r="1.3" fill="#1a365d"/><circle cx="115" cy="105" r="1.6" fill="#1a365d"/><circle cx="175" cy="85" r="1.3" fill="#1a365d"/>
                        <circle cx="58" cy="188" r="1.5" fill="#1a365d"/><circle cx="180" cy="198" r="1.3" fill="#1a365d"/><circle cx="245" cy="98" r="1.5" fill="#1a365d"/>
                        <path d="M50 50h10v8" stroke="#1a365d" stroke-width="1" stroke-dasharray="2 2" fill="none"/>
                        <path d="M175 85v10h10" stroke="#1a365d" stroke-width="1" stroke-dasharray="2 2" fill="none"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#doodle-students)"/>
            </svg>
        </div>

        <div class="container mx-auto px-4 lg:px-10 relative z-10">
            <!-- Header Section -->
            <div class="text-center mb-8 sm:mb-12">
                <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black font-display text-[#1a365d] uppercase tracking-wide">
                    Present Students Directory
                </h1>
                <p class="text-xs sm:text-base text-slate-500 font-medium mt-2 max-w-2xl mx-auto">
                    Live student roster enrolled across undergraduate, postgraduate, and diploma programmes at CCSA, Dibrugarh University.
                </p>
                <div class="w-20 h-1 bg-[#fbbf24] mt-3 rounded-full mx-auto"></div>
            </div>

            <!-- Controls: Search Input & Programme Filter Tabs -->
            <div class="max-w-4xl mx-auto mb-10 space-y-4">
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <!-- Search Box -->
                    <div class="relative w-full sm:flex-1">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" id="studentSearch" placeholder="Search student by name..." 
                            class="w-full pl-11 pr-4 py-3 bg-white rounded-xl border border-slate-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#1a365d] focus:border-transparent text-sm transition-all" />
                    </div>

                    <!-- Programme Filter Buttons -->
                    <div class="flex flex-wrap items-center justify-center gap-1.5 p-1 bg-slate-200/80 rounded-xl w-full sm:w-auto" id="programTabs" role="tablist">
                        <button type="button" data-filter="all" class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all bg-[#1a365d] text-white shadow-sm filter-tab active">
                            All
                        </button>
                        <button type="button" data-filter="BCA" class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all text-slate-700 hover:text-slate-900 filter-tab">
                            BCA
                        </button>
                        <button type="button" data-filter="MCA" class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all text-slate-700 hover:text-slate-900 filter-tab">
                            MCA
                        </button>
                        <button type="button" data-filter="PGDCA" class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all text-slate-700 hover:text-slate-900 filter-tab">
                            PGDCA
                        </button>
                    </div>
                </div>

                <!-- Total Count Stats Indicator -->
                <div class="flex items-center justify-between text-xs text-slate-500 font-semibold px-2">
                    <span id="studentStats">Fetching student records...</span>
                </div>
            </div>

            <!-- Students Content Container -->
            <div id="students-container" class="space-y-12">
                <div class="text-center py-16">
                    <div class="inline-block w-10 h-10 border-4 border-[#1a365d] border-t-transparent rounded-full animate-spin mb-4"></div>
                    <p class="text-slate-600 font-semibold text-sm">Loading student directory from Dibrugarh University...</p>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById("students-container");
    const searchInput = document.getElementById("studentSearch");
    const statsEl = document.getElementById("studentStats");
    const filterTabs = document.querySelectorAll(".filter-tab");
    
    let allData = [];
    let currentFilter = 'all';
    let searchQuery = '';

    function renderStudents() {
        container.innerHTML = "";
        let totalCount = 0;
        let visibleCount = 0;

        if (!Array.isArray(allData) || allData.length === 0) {
            container.innerHTML = '<div class="text-center py-12 text-slate-500 italic">No student records available at this moment.</div>';
            statsEl.textContent = '0 students found';
            return;
        }

        allData.forEach(dept => {
            if (!dept.programmes) return;
            dept.programmes.forEach(prog => {
                const progName = prog.programme || '';
                
                // Programme Tab Filter
                if (currentFilter !== 'all' && !progName.toUpperCase().includes(currentFilter.toUpperCase())) {
                    return;
                }

                const matchedSemesters = [];

                (prog.semesters || []).forEach(sem => {
                    const filteredStudents = (sem.students || []).filter(student => {
                        totalCount++;
                        if (!student.name) return false;
                        if (!searchQuery) return true;
                        return student.name.toLowerCase().includes(searchQuery.toLowerCase());
                    });

                    if (filteredStudents.length > 0) {
                        matchedSemesters.push({
                            semester: sem.semester,
                            students: filteredStudents
                        });
                        visibleCount += filteredStudents.length;
                    }
                });

                if (matchedSemesters.length === 0) return;

                const programmeContainer = document.createElement("div");
                programmeContainer.className = "space-y-6 bg-white/95 backdrop-blur-sm p-4 sm:p-7 rounded-2xl border border-slate-200/90 shadow-sm";

                const programmeHeader = document.createElement("div");
                programmeHeader.className = "flex flex-col sm:flex-row items-start sm:items-center justify-between pb-3 border-b-2 border-slate-200 gap-2";
                programmeHeader.innerHTML = `
                    <div>
                        <h2 class="text-lg sm:text-2xl font-black font-display text-[#1a365d]">${progName}</h2>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">Centre for Computer Science and Applications</p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-800 text-xs font-bold self-start sm:self-auto">
                        ${matchedSemesters.reduce((acc, s) => acc + s.students.length, 0)} Students
                    </span>
                `;
                programmeContainer.appendChild(programmeHeader);

                matchedSemesters.forEach(sem => {
                    const semesterContainer = document.createElement("div");
                    semesterContainer.className = "space-y-3 pt-2";

                    const semesterTitle = document.createElement("div");
                    semesterTitle.className = "flex items-center justify-between bg-slate-50 px-3.5 py-2 rounded-lg border border-slate-200/70";
                    semesterTitle.innerHTML = `
                        <h3 class="text-xs sm:text-sm font-extrabold text-[#1a365d] flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#fbbf24] flex-shrink-0"></span>
                            ${(sem.semester || '').replace("Year/", "")}
                        </h3>
                        <span class="text-[11px] font-semibold text-slate-500">${sem.students.length} Enrolled</span>
                    `;
                    semesterContainer.appendChild(semesterTitle);

                    // Clean, simple line-separated list (Mobile Friendly)
                    const studentList = document.createElement("div");
                    studentList.className = "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6";

                    sem.students.forEach((student, idx) => {
                        const studentRow = document.createElement("div");
                        studentRow.className = "py-2.5 px-3 border-b border-slate-100 flex items-center justify-between hover:bg-blue-50/40 transition-colors group";

                        studentRow.innerHTML = `
                            <div class="flex items-center gap-3 min-w-0 pr-2">
                                <span class="text-[11px] font-mono font-bold text-slate-400 w-6 flex-shrink-0">${idx + 1}.</span>
                                <span class="text-xs sm:text-sm font-semibold text-slate-800 group-hover:text-[#1a365d] transition-colors truncate" title="${student.name}">${student.name}</span>
                            </div>
                            <span class="text-[10px] text-slate-400 font-medium flex-shrink-0 uppercase">${(sem.semester || '').replace("Year/", "").replace("Semester", "Sem")}</span>
                        `;

                        studentList.appendChild(studentRow);
                    });

                    semesterContainer.appendChild(studentList);
                    programmeContainer.appendChild(semesterContainer);
                });

                container.appendChild(programmeContainer);
            });
        });



        if (visibleCount === 0) {
            container.innerHTML = `
                <div class="text-center py-16 bg-white rounded-2xl border border-slate-200">
                    <i class="fas fa-user-slash text-3xl text-slate-400 mb-3"></i>
                    <p class="text-slate-600 font-bold text-base">No students matched "${searchQuery}"</p>
                    <p class="text-slate-400 text-xs mt-1">Try clearing your search or switching programme filters.</p>
                </div>
            `;
        }

        statsEl.textContent = `Showing ${visibleCount} student records`;
    }

    // Fetch from the updated proxy
    fetch('proxy/students.php')
        .then(response => response.json())
        .then(data => {
            allData = data;
            renderStudents();
        })
        .catch(error => {
            console.error('Error loading student data:', error);
            container.innerHTML = '<div class="text-center py-12 text-rose-500 font-semibold">Unable to connect to student directory service. Please try again.</div>';
            statsEl.textContent = 'Service unavailable';
        });

    // Search event
    searchInput.addEventListener('input', (e) => {
        searchQuery = e.target.value.trim();
        renderStudents();
    });

    // Filter tab events
    filterTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            filterTabs.forEach(t => {
                t.classList.remove('bg-[#1a365d]', 'text-white', 'shadow-sm');
                t.classList.add('text-slate-700');
            });
            tab.classList.add('bg-[#1a365d]', 'text-white', 'shadow-sm');
            tab.classList.remove('text-slate-700');
            currentFilter = tab.dataset.filter;
            renderStudents();
        });
    });
});
</script>

<?php
include 'templates/footer.php';
?>
