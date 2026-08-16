<?php
declare(strict_types=1);

/**
 * Announcements & Notices Archive Page
 * Centre for Computer Science and Applications (CCSA), Dibrugarh University
 */

$pageTitle = 'Announcements & Notifications - CCSA';
$currentPage = 'notices';
include 'templates/header.php';
?>

<section class="py-12 sm:py-16 px-4 bg-slate-50 min-h-screen relative overflow-hidden">
    <!-- 📢 Announcements & Notice Board Doodle Pattern Background -->
    <div class="absolute inset-0 pointer-events-none opacity-[0.06] select-none">
        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="doodle-notices-archive" width="260" height="260" patternUnits="userSpaceOnUse" patternTransform="rotate(1)">
                    <g stroke="#1a365d" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <!-- Megaphone -->
                        <g transform="translate(10, 8) rotate(-4)">
                            <path d="M8 16L3 13L3 8L8 5L19 2V19L8 16Z" />
                            <path d="M19 10L25 10M22 6L25 3M22 14L25 17" />
                            <path d="M8 16V22C8 22.8 7.3 23.5 6.5 23.5H5C4.2 23.5 3.5 22.8 3.5 22V14" />
                        </g>
                        <!-- Bell -->
                        <g transform="translate(140, 10) rotate(5)">
                            <path d="M14 2C14 2 8 4 8 11V15L5 18H23L20 15V11C20 4 14 2 14 2Z" />
                            <path d="M11 18V19C11 20.6 12.3 22 14 22C15.7 22 17 20.6 17 19V18" />
                            <path d="M14 1V2" />
                        </g>
                        <!-- Envelope -->
                        <g transform="translate(75, 48) rotate(-5)">
                            <rect x="2" y="5" width="22" height="15" rx="1.5" />
                            <path d="M2 7L13 14L24 7" />
                        </g>
                        <!-- Newspaper -->
                        <g transform="translate(205, 52) rotate(4)">
                            <rect x="3" y="3" width="18" height="22" rx="1.5" />
                            <path d="M6 7H18M6 11H18M6 15H14M16 15H18M6 19H11" />
                        </g>
                        <!-- Calendar -->
                        <g transform="translate(10, 95) rotate(-3)">
                            <rect x="2" y="5" width="20" height="17" rx="1.5" />
                            <path d="M6 2V7M18 2V7M2 10H22" />
                            <circle cx="7" cy="14" r="1" /> <circle cx="12" cy="14" r="1" /> <circle cx="17" cy="14" r="1" />
                        </g>
                        <!-- Bulletin Board Pin -->
                        <g transform="translate(145, 98) rotate(6)">
                            <path d="M7 3H17L15 9H18L13 17L13 23L11 23L11 17L6 9H9L7 3Z"/>
                        </g>
                        <!-- Clock -->
                        <g transform="translate(70, 150) rotate(-4)">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12,6 12,12 16,14"/>
                        </g>
                        <!-- Bookmark -->
                        <g transform="translate(195, 155) rotate(3)">
                            <path d="M5 3H17V21L11 17L5 21V3Z"/>
                        </g>
                        <!-- RSS Feed -->
                        <g transform="translate(12, 195) rotate(-2)">
                            <circle cx="5" cy="19" r="2" fill="#1a365d"/>
                            <path d="M3 12A12 12 0 0 1 15 24M3 6A18 18 0 0 1 21 24"/>
                        </g>
                        <!-- Loudspeaker -->
                        <g transform="translate(135, 200) rotate(5)">
                            <path d="M4 8H8L13 4V16L8 12H4V8Z"/>
                            <path d="M16 6A6 6 0 0 1 16 14"/>
                        </g>
                    </g>
                    <!-- Fillers -->
                    <text x="68" y="28" font-family="'Courier New',monospace" font-size="14" font-weight="900" fill="#1a365d">@</text>
                    <text x="195" y="30" font-family="'Courier New',monospace" font-size="15" font-weight="900" fill="#1a365d">!</text>
                    <text x="12" y="72" font-family="'Courier New',monospace" font-size="10" font-weight="bold" fill="#1a365d">NEW</text>
                    <text x="125" y="75" font-family="'Courier New',monospace" font-size="12" font-weight="bold" fill="#1a365d">#</text>
                    <text x="75" y="125" font-family="'Courier New',monospace" font-size="11" font-weight="bold" fill="#1a365d">PDF</text>
                    <text x="210" y="130" font-family="'Courier New',monospace" font-size="10" font-weight="900" fill="#1a365d">ALERT</text>
                    <text x="128" y="175" font-family="'Courier New',monospace" font-size="11" font-weight="bold" fill="#1a365d">2026</text>
                    <text x="15" y="172" font-family="'Courier New',monospace" font-size="12" font-weight="900" fill="#1a365d">DUAT</text>
                    <text x="68" y="230" font-family="'Courier New',monospace" font-size="11" font-weight="900" fill="#1a365d">EXAM</text>
                    <text x="195" y="235" font-family="'Courier New',monospace" font-size="11" font-weight="bold" fill="#1a365d">ADM</text>
                    <!-- Dots & Connectors -->
                    <circle cx="50" cy="50" r="1.3" fill="#1a365d"/><circle cx="115" cy="105" r="1.6" fill="#1a365d"/><circle cx="175" cy="85" r="1.3" fill="#1a365d"/>
                    <circle cx="58" cy="188" r="1.5" fill="#1a365d"/><circle cx="180" cy="198" r="1.3" fill="#1a365d"/><circle cx="245" cy="98" r="1.5" fill="#1a365d"/>
                    <path d="M50 50h10v8" stroke="#1a365d" stroke-width="1" stroke-dasharray="2 2" fill="none"/>
                    <path d="M175 85v10h10" stroke="#1a365d" stroke-width="1" stroke-dasharray="2 2" fill="none"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#doodle-notices-archive)"/>
        </svg>
    </div>

    <div class="container mx-auto max-w-5xl relative z-10">
        <!-- Breadcrumb -->
        <div class="mb-4">
            <a href="index.php" class="text-indigo-600 hover:text-indigo-800 text-xs sm:text-sm font-semibold inline-flex items-center gap-1.5 transition-colors">
                <i class="fas fa-arrow-left text-xs"></i> Back to Home
            </a>
        </div>

        <!-- Hero Banner -->
        <div class="bg-gradient-to-r from-[#1a365d] via-[#1e40af] to-[#2563eb] rounded-2xl sm:rounded-3xl p-6 sm:p-8 text-white shadow-lg mb-8">
            <span class="bg-amber-400 text-slate-900 text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider mb-2.5 inline-block">Notice Board</span>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold font-display tracking-tight mb-2">
                Announcements &amp; Official Notices
            </h1>
            <p class="text-blue-100 text-xs sm:text-sm max-w-2xl leading-relaxed">
                Timetables, admission schedules, examination guidelines, and official departmental announcements.
            </p>
        </div>

        <!-- Single Large Clean Announcement Card (Reference Design - Separated by single line) -->
        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200/90 p-5 sm:p-8">
            <!-- Header Bar -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <h2 class="text-lg sm:text-xl font-bold text-indigo-600 tracking-tight">All Official Announcements</h2>
                </div>
                <span id="noticeCountBadge" class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
                    Loading...
                </span>
            </div>

            <!-- Search Filter -->
            <div class="relative mb-6">
                <i class="fas fa-search absolute left-4 top-3.5 text-slate-400 text-xs sm:text-sm"></i>
                <input type="text" id="archiveSearch" placeholder="Search notices by title, keyword, category, or document name..." 
                    class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all shadow-inner">
            </div>

            <!-- Notice Archive List (Line-separated, NO isolated cards) -->
            <div id="noticeArchiveContainer" class="divide-y divide-slate-100">
                <!-- Skeletons -->
                <div class="py-4.5 space-y-2 animate-pulse">
                    <div class="flex justify-between items-center">
                        <div class="h-4 bg-slate-200 rounded w-3/4"></div>
                        <div class="h-3 bg-slate-100 rounded w-16"></div>
                    </div>
                    <div class="h-3 bg-slate-100 rounded w-1/3"></div>
                    <div class="h-3 bg-slate-100 rounded w-24"></div>
                </div>
                <div class="py-4.5 space-y-2 animate-pulse">
                    <div class="flex justify-between items-center">
                        <div class="h-4 bg-slate-200 rounded w-3/4"></div>
                        <div class="h-3 bg-slate-100 rounded w-16"></div>
                    </div>
                    <div class="h-3 bg-slate-100 rounded w-1/3"></div>
                    <div class="h-3 bg-slate-100 rounded w-24"></div>
                </div>
                <div class="py-4.5 space-y-2 animate-pulse">
                    <div class="flex justify-between items-center">
                        <div class="h-4 bg-slate-200 rounded w-3/4"></div>
                        <div class="h-3 bg-slate-100 rounded w-16"></div>
                    </div>
                    <div class="h-3 bg-slate-100 rounded w-1/3"></div>
                    <div class="h-3 bg-slate-100 rounded w-24"></div>
                </div>
            </div>

            <!-- No Results Message -->
            <div id="noResults" class="hidden text-center text-slate-400 text-sm py-12">
                <i class="fas fa-search text-2xl text-slate-300 mb-2 block"></i>
                <p>No notices matched your search query.</p>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const container = document.getElementById('noticeArchiveContainer');
    const searchInput = document.getElementById('archiveSearch');
    const countBadge = document.getElementById('noticeCountBadge');
    const noResults = document.getElementById('noResults');

    let allNotices = [];

    const escapeHtml = (str) => {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(str || ''));
        return div.innerHTML;
    };

    const renderNotices = (notices) => {
        container.innerHTML = '';

        if (!notices || notices.length === 0) {
            if (noResults) noResults.classList.remove('hidden');
            if (countBadge) countBadge.textContent = '0 Notices';
            return;
        }

        if (noResults) noResults.classList.add('hidden');
        if (countBadge) countBadge.textContent = `${notices.length} Notice${notices.length === 1 ? '' : 's'}`;

        notices.forEach(notice => {
            const d = notice.createdAt ? new Date(notice.createdAt) : null;
            const day = d ? d.getDate() : '';
            const month = d ? (d.getMonth() + 1) : '';
            const year = d ? d.getFullYear() : '';
            const formattedDate = d ? `${day}/${month}/${year}` : '';

            const title = notice.title || 'Notification';
            const targetUrl = notice.file_path ? `admin/${notice.file_path}` : (notice.file_url || '#');
            const isExternal = !!(notice.file_path || notice.file_url);

            let category = notice.description || '';
            if (!category) {
                if (title.toLowerCase().includes('admission') || title.toLowerCase().includes('duat')) {
                    category = 'Counseling cum Admission';
                } else if (title.toLowerCase().includes('scholarship')) {
                    category = 'Scholarship';
                } else if (title.toLowerCase().includes('fee') || title.toLowerCase().includes('exam') || title.toLowerCase().includes('routine')) {
                    category = 'Academic Notice';
                } else {
                    category = 'Official Announcement';
                }
            }

            const item = document.createElement('div');
            item.className = 'py-3.5 sm:py-4.5 border-b border-slate-100 last:border-b-0 group flex flex-col gap-1.5';

            let actionLink = '';
            if (notice.file_path) {
                actionLink = `
                    <a href="admin/${notice.file_path}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <span>View Attachment</span>
                    </a>
                `;
            } else if (notice.file_url) {
                actionLink = `
                    <a href="${notice.file_url}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        <span>View Link</span>
                    </a>
                `;
            } else {
                actionLink = `
                    <span class="inline-flex items-center gap-1 text-[11px] sm:text-xs text-slate-400">
                        <span>Notice Details</span>
                    </span>
                `;
            }

            item.innerHTML = `
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-1 sm:gap-3">
                    <a href="${targetUrl}" ${isExternal ? 'target="_blank" rel="noopener noreferrer"' : ''} class="text-xs sm:text-sm lg:text-base font-semibold text-slate-800 hover:text-indigo-600 transition-colors leading-snug flex-1">
                        ${escapeHtml(title)}
                    </a>
                    ${formattedDate ? `<span class="text-[10px] sm:text-xs text-slate-500 font-medium shrink-0 pt-0.5">${formattedDate}</span>` : ''}
                </div>
                ${category ? `<p class="text-[11px] sm:text-xs text-slate-500 font-normal leading-relaxed">${escapeHtml(category)}</p>` : ''}
                <div class="mt-0.5">
                    ${actionLink}
                </div>
            `;

            container.appendChild(item);
        });
    };

    // Fetch notices from endpoints
    try {
        const response = await fetch('endpoints/notifications.php');
        if (!response.ok) throw new Error('Failed to fetch');
        const data = await response.json();
        allNotices = Array.isArray(data) ? data : (data.data || []);
        allNotices.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));
        renderNotices(allNotices);
    } catch (err) {
        console.error('Error fetching archive notices:', err);
        if (container) {
            container.innerHTML = '<p class="text-center text-slate-400 py-8 text-sm">Failed to load notices. Please refresh the page.</p>';
        }
    }

    // Search filter
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase().trim();
            if (!query) {
                renderNotices(allNotices);
                return;
            }
            const filtered = allNotices.filter(n => {
                const title = (n.title || '').toLowerCase();
                const desc = (n.description || '').toLowerCase();
                return title.includes(query) || desc.includes(query);
            });
            renderNotices(filtered);
        });
    }
});
</script>

<?php
include 'templates/footer.php';
?>
