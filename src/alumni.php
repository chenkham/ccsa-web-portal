<?php
declare(strict_types=1);

/**
 * Alumni Network Page
 * Centre for Computer Science and Applications (CCSA), Dibrugarh University
 */

$pageTitle = 'Alumni Network - CCSA';
$currentPage = 'alumni';
include 'templates/header.php';
?>

<main class="min-h-screen">
    <!-- Alumni Cards Section -->
    <section class="py-16 bg-slate-50 relative overflow-hidden">
        <!-- 🎓 Alumni & Academics Doodle Background Pattern -->
        <div class="absolute inset-0 pointer-events-none opacity-[0.06] select-none">
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="doodle-alumni" width="260" height="260" patternUnits="userSpaceOnUse" patternTransform="rotate(-1)">
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
                            <!-- Trophy -->
                            <g transform="translate(145, 95) rotate(-3)">
                                <path d="M8 5V11C8 14 10.5 16.5 13.5 16.5C16.5 16.5 19 14 19 11V5H8Z" />
                                <path d="M5 5H8V9H5C3.5 9 2.5 7.8 2.5 6.5C2.5 5.2 3.5 5 5 5" />
                                <path d="M19 5H22C23.5 5 24.5 5.2 24.5 6.5C24.5 7.8 23.5 9 22 9H19" />
                                <path d="M13.5 16.5V22M9 22H18" />
                            </g>
                        </g>
                        <!-- Fillers -->
                        <text x="68" y="28" font-family="'Courier New',monospace" font-size="13" font-weight="900" fill="#1a365d">ALUMNI</text>
                        <text x="195" y="30" font-family="'Courier New',monospace" font-size="11" font-weight="bold" fill="#1a365d">CCSA</text>
                        <text x="12" y="72" font-family="'Courier New',monospace" font-size="14" font-weight="900" fill="#1a365d">∞</text>
                        <text x="68" y="230" font-family="'Courier New',monospace" font-size="11" font-weight="900" fill="#1a365d">BCA</text>
                        <text x="195" y="235" font-family="'Courier New',monospace" font-size="11" font-weight="bold" fill="#1a365d">MCA</text>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#doodle-alumni)"/>
            </svg>
        </div>

        <div class="container mx-auto px-4 lg:px-10 relative z-10">
            <!-- Header Section -->
            <div class="text-center mb-8 sm:mb-12">
                <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black font-display text-[#1a365d] uppercase tracking-wide">
                    Alumni Network
                </h1>
                <p class="text-xs sm:text-sm font-semibold uppercase tracking-wider text-slate-500 mt-2">
                    Celebrating Two Decades of CCSA Graduates Across Global Tech &amp; Academia
                </p>
                <div class="w-20 h-1 bg-[#fbbf24] mt-3 rounded-full mx-auto"></div>
            </div>

            <!-- Controls: Search Input & Programme Filter Tabs -->
            <div class="max-w-4xl mx-auto mb-10 space-y-4">
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <!-- Search Box -->
                    <div class="relative w-full sm:flex-1">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" id="alumniSearch" placeholder="Search alumni by name, organization, or location..." 
                            class="w-full pl-11 pr-4 py-3 bg-white rounded-xl border border-slate-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#1a365d] focus:border-transparent text-sm transition-all" />
                    </div>

                    <!-- Programme Filter Buttons -->
                    <div class="flex flex-wrap items-center justify-center gap-1.5 p-1 bg-slate-200/80 rounded-xl w-full sm:w-auto" id="programTabs" role="tablist">
                        <button type="button" data-filter="all" class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all bg-[#1a365d] text-white shadow-sm filter-tab active">
                            All
                        </button>
                        <button type="button" data-filter="MCA" class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all text-slate-700 hover:text-slate-900 filter-tab">
                            MCA
                        </button>
                        <button type="button" data-filter="BCA" class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all text-slate-700 hover:text-slate-900 filter-tab">
                            BCA
                        </button>
                        <button type="button" data-filter="PGDCA" class="px-3.5 py-2 rounded-lg text-xs font-bold transition-all text-slate-700 hover:text-slate-900 filter-tab">
                            PGDCA
                        </button>
                    </div>
                </div>

                <!-- Total Count Stats Indicator & Official Portal Button -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 font-semibold px-2">
                    <span id="alumniStats">Fetching alumni records...</span>
                    <a href="https://ccsaalumni.in/" target="_blank" rel="noopener noreferrer" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold border border-indigo-200 transition-colors">
                        <span>Official Alumni Portal (ccsaalumni.in)</span>
                        <i class="fas fa-external-link-alt text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Alumni Content Container -->
            <div id="alumni-container" class="space-y-8">
                <div class="text-center py-16">
                    <div class="inline-block w-10 h-10 border-4 border-[#1a365d] border-t-transparent rounded-full animate-spin mb-4"></div>
                    <p class="text-slate-600 font-semibold text-sm">Loading alumni records...</p>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById("alumni-container");
    const searchInput = document.getElementById("alumniSearch");
    const statsEl = document.getElementById("alumniStats");
    const filterTabs = document.querySelectorAll(".filter-tab");
    
    let allData = [];
    let currentFilter = 'all';
    let searchQuery = '';

    function renderAlumni() {
        container.innerHTML = "";
        let visibleCount = 0;

        if (!Array.isArray(allData) || allData.length === 0) {
            container.innerHTML = '<div class="text-center py-12 text-slate-500 italic">No alumni records available at this moment.</div>';
            statsEl.textContent = '0 alumni found';
            return;
        }

        // Group by Programme
        const grouped = {};
        allData.forEach(item => {
            const prog = item.program || 'Other';
            if (currentFilter !== 'all' && prog.toUpperCase() !== currentFilter.toUpperCase()) {
                return;
            }

            if (searchQuery) {
                const q = searchQuery.toLowerCase();
                const matchName = (item.name || '').toLowerCase().includes(q);
                const matchOrg = (item.organization || '').toLowerCase().includes(q);
                const matchLoc = (item.location || '').toLowerCase().includes(q);
                const matchDesig = (item.designation || '').toLowerCase().includes(q);
                const matchBatch = (item.batch || '').toLowerCase().includes(q);
                if (!matchName && !matchOrg && !matchLoc && !matchDesig && !matchBatch) {
                    return;
                }
            }

            if (!grouped[prog]) {
                grouped[prog] = [];
            }
            grouped[prog].push(item);
            visibleCount++;
        });

        const progKeys = Object.keys(grouped);
        if (progKeys.length === 0) {
            container.innerHTML = `
                <div class="text-center py-16 bg-white rounded-2xl border border-slate-200">
                    <i class="fas fa-user-slash text-3xl text-slate-400 mb-3"></i>
                    <p class="text-slate-600 font-bold text-base">No alumni records matched "${searchQuery}"</p>
                    <p class="text-slate-400 text-xs mt-1">Try searching for a different name, company, or batch year.</p>
                </div>
            `;
            statsEl.textContent = `Showing 0 alumni records`;
            return;
        }

        progKeys.forEach(progKey => {
            const list = grouped[progKey];
            const progContainer = document.createElement("div");
            progContainer.className = "space-y-6 bg-white/95 backdrop-blur-sm p-4 sm:p-7 rounded-2xl border border-slate-200/90 shadow-sm";

            const progHeader = document.createElement("div");
            progHeader.className = "flex flex-col sm:flex-row items-start sm:items-center justify-between pb-3 border-b-2 border-slate-200 gap-2";
            progHeader.innerHTML = `
                <div>
                    <h2 class="text-lg sm:text-2xl font-black font-display text-[#1a365d]">${progKey} Alumni Network</h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Centre for Computer Science and Applications, Dibrugarh University</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-800 text-xs font-bold self-start sm:self-auto">
                    ${list.length} Alumni
                </span>
            `;
            progContainer.appendChild(progHeader);

            const grid = document.createElement("div");
            grid.className = "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pt-2";

            list.forEach(alum => {
                const card = document.createElement("div");
                card.className = "p-4 rounded-xl border border-slate-100 bg-slate-50/60 hover:bg-blue-50/50 hover:border-blue-200 transition-all group flex flex-col justify-between";
                card.innerHTML = `
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-[#1a365d] text-white">
                                Batch ${alum.batch || 'Alumni'}
                            </span>
                            <span class="text-[11px] font-mono text-slate-400 font-bold">
                                <i class="fas fa-graduation-cap text-[#fbbf24]"></i> ${alum.program || 'CCSA'}
                            </span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800 group-hover:text-[#1a365d] transition-colors truncate mb-1">
                            ${alum.name || 'Alumnus'}
                        </h3>
                        <p class="text-xs text-slate-600 font-medium line-clamp-1 mb-1">
                            ${alum.designation || 'Software Professional'}
                        </p>
                        <p class="text-xs font-bold text-slate-800 flex items-center gap-1.5 mb-2">
                            <i class="fas fa-building text-slate-400 text-[10px]"></i>
                            <span>${alum.organization || 'Global IT Industry'}</span>
                        </p>
                    </div>
                    <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px] text-slate-500">
                        <span class="flex items-center gap-1"><i class="fas fa-map-marker-alt text-rose-500 text-[10px]"></i> ${alum.location || 'India'}</span>
                        <span class="font-semibold text-emerald-700"><i class="fas fa-check-circle text-[10px]"></i> Verified</span>
                    </div>
                `;
                grid.appendChild(card);
            });

            progContainer.appendChild(grid);
            container.appendChild(progContainer);
        });

        statsEl.textContent = `Showing ${visibleCount} alumni records`;
    }

    // Fetch from proxy
    fetch('proxy/alumni.php')
        .then(res => res.json())
        .then(data => {
            allData = Array.isArray(data) ? data : (data.data || []);
            renderAlumni();
        })
        .catch(err => {
            console.error('Error fetching alumni records:', err);
            // Fallback load
            fetch('cache/alumni_data.json')
                .then(r => r.json())
                .then(d => {
                    allData = d;
                    renderAlumni();
                })
                .catch(() => {
                    container.innerHTML = '<div class="text-center py-12 text-rose-500 font-semibold">Unable to connect to alumni service.</div>';
                    statsEl.textContent = 'Service unavailable';
                });
        });

    // Search event
    searchInput.addEventListener('input', (e) => {
        searchQuery = e.target.value.trim();
        renderAlumni();
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
            renderAlumni();
        });
    });
});
</script>

<?php
include 'templates/footer.php';
?>
