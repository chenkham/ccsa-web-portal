<?php
declare(strict_types=1);

$extraScripts = $extraScripts ?? '';
?>

    </main>

    <footer class="bg-[#0b1329] text-white pt-12 pb-8 border-t border-slate-800/80 relative overflow-hidden" role="contentinfo">
        <div class="absolute -top-24 left-1/4 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 right-1/4 w-96 h-96 bg-amber-500/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="container mx-auto px-4 lg:px-10 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 lg:gap-12 pb-8">
                <div class="lg:col-span-4">
                    <div class="mb-4">
                        <h4 class="text-sm sm:text-base font-extrabold uppercase tracking-wider text-white">Quick Links</h4>
                    </div>
                    <ul class="space-y-2.5 text-sm text-slate-300">
                        <li><a href="index.php#aboutus" class="hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-chevron-right text-[10px] text-white"></i> About Us</a></li>
                        <li><a href="index.php#programs" class="hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-chevron-right text-[10px] text-white"></i> Programs</a></li>
                        <li><a href="faculty.php" class="hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-chevron-right text-[10px] text-white"></i> Faculty Directory</a></li>
                        <li><a href="notices.php" class="hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-chevron-right text-[10px] text-white"></i> Announcements</a></li>
                        <li><a href="publication.php" class="hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-chevron-right text-[10px] text-white"></i> Research</a></li>
                        <li><a href="index.php#contact" class="hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-chevron-right text-[10px] text-white"></i> Contact</a></li>
                    </ul>
                </div>

                <div class="lg:col-span-4">
                    <div class="mb-4">
                        <h4 class="text-sm sm:text-base font-extrabold uppercase tracking-wider text-white">Important Links</h4>
                    </div>
                    <ul class="space-y-2.5 text-sm text-slate-300">
                        <li><a href="https://erp.dibru.work/dibru/student/login" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-external-link-alt text-[10px] text-white"></i> Student Portal</a></li>
                        <li><a href="https://dibru.ac.in/library" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-external-link-alt text-[10px] text-white"></i> Library</a></li>
                        <li><a href="https://dibru.ac.in/" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-external-link-alt text-[10px] text-white"></i> Dibrugarh University</a></li>
                        <li><a href="https://ccsaalumni.in/" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-external-link-alt text-[10px] text-white"></i> Alumni</a></li>
                        <li><button type="button" onclick="openHelplineModal()" class="hover:text-[#fbbf24] transition-colors flex items-center gap-2 text-left cursor-pointer"><i class="fas fa-shield-alt text-[10px] text-amber-400"></i> Anti-Ragging Helpline</button></li>
                        <li><a href="https://placeccsa.wordpress.com/" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-briefcase text-[10px] text-white"></i> Placement Cell</a></li>
                    </ul>
                </div>

                <div class="lg:col-span-4">
                    <div class="mb-4">
                        <h4 class="text-sm sm:text-base font-extrabold uppercase tracking-wider text-white">Live Weather</h4>
                    </div>

                    <div class="bg-[#121b33] border border-slate-700/70 rounded-2xl p-4 weather-widget shadow-lg">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                            <div class="flex items-center gap-2">
                                <span id="current-icon" class="text-2xl w-7 text-center">☀️</span>
                                <div>
                                    <span id="current-temp" class="text-xl font-extrabold text-white font-mono">--°C</span>
                                    <p class="text-[10px] text-slate-400 font-medium">Dibrugarh, Assam</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span id="current-time" class="text-xs font-mono font-bold text-white block">--:--</span>
                                <span class="text-[9px] text-white uppercase tracking-wider font-bold">Live</span>
                            </div>
                        </div>

                        <div class="pt-3">
                            <p class="text-[10px] uppercase font-bold tracking-wider text-slate-400 mb-2">Hourly Forecast</p>
                            <div id="hourly-forecast" class="grid grid-cols-4 gap-1.5">
                                <div class="w-full h-14 rounded-lg skeleton bg-slate-800/80 animate-pulse"></div>
                                <div class="w-full h-14 rounded-lg skeleton bg-slate-800/80 animate-pulse"></div>
                                <div class="w-full h-14 rounded-lg skeleton bg-slate-800/80 animate-pulse"></div>
                                <div class="w-full h-14 rounded-lg skeleton bg-slate-800/80 animate-pulse"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-start gap-4 pt-4 pb-4 border-t border-slate-800/80">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 mr-1">Social:</span>
                <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Official Facebook page"
                   class="w-9 h-9 rounded-lg bg-slate-800/90 hover:bg-[#1877f2] border border-slate-700/80 flex items-center justify-center text-slate-300 hover:text-white text-sm transition-all hover:-translate-y-0.5 shadow-sm">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" aria-label="Official LinkedIn page"
                   class="w-9 h-9 rounded-lg bg-slate-800/90 hover:bg-[#0a66c2] border border-slate-700/80 flex items-center justify-center text-slate-300 hover:text-white text-sm transition-all hover:-translate-y-0.5 shadow-sm">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Official Instagram handle"
                   class="w-9 h-9 rounded-lg bg-slate-800/90 hover:bg-[#e4405f] border border-slate-700/80 flex items-center justify-center text-slate-300 hover:text-white text-sm transition-all hover:-translate-y-0.5 shadow-sm">
                    <i class="fab fa-instagram"></i>
                </a>
            </div>

            <div class="border-t border-slate-800/90 pt-6 mt-2 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left text-xs text-slate-400">
                <p>
                    &copy; <?php echo date('Y'); ?> <strong class="text-slate-200">Centre for Computer Science and Applications</strong>, Dibrugarh University. All rights reserved.
                </p>
                <p class="text-slate-400">
                    Powered by <a href="https://dssdu.in" target="_blank" rel="noopener noreferrer" class="text-white hover:text-slate-200 font-semibold underline underline-offset-2">Digital Solution Cell, Dibrugarh University</a>
                </p>
            </div>
        </div>

        <button id="back-to-top" 
            class="hidden fixed bottom-8 right-8 z-40 p-3 bg-yellow-500 text-slate-900 rounded-full shadow-lg transition-opacity duration-300 hover:bg-yellow-600 cursor-pointer"
            title="Back to top">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
            </svg>
        </button>
    </footer>

    <div id="helplineModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 relative animate-fadeIn">
            <div class="flex justify-between items-center pb-4 border-b border-slate-100 mb-5">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg font-black">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Anti-Ragging &amp; Grievance Helpline</h3>
                        <p class="text-xs text-slate-500">Dibrugarh University Statutory Support</p>
                    </div>
                </div>
                <button onclick="closeHelplineModal()" class="text-slate-400 hover:text-slate-700 text-2xl font-light">&times;</button>
            </div>

            <div class="space-y-4 text-xs sm:text-sm">
                <div class="bg-red-50 p-4 rounded-2xl border border-red-200/80">
                    <p class="font-bold text-red-900 flex items-center gap-2 mb-1">
                        <i class="fas fa-phone-alt text-red-600"></i> National 24x7 Anti-Ragging Toll-Free:
                    </p>
                    <a href="tel:18001805522" class="text-base font-extrabold text-red-700 hover:underline font-mono">1800-180-5522</a>
                    <p class="text-[11px] text-red-800 mt-1">Email: <a href="mailto:helpline@antiragging.in" class="underline">helpline@antiragging.in</a></p>
                </div>

                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-2">
                    <div>
                        <span class="font-bold text-slate-800 block">Dibrugarh University Proctor Office:</span>
                        <span class="text-slate-600">+91-373-2370231 / proctor@dibru.ac.in</span>
                    </div>
                    <div>
                        <span class="font-bold text-slate-800 block">CCSA Student Grievance Cell:</span>
                        <span class="text-slate-600">ccsduoffice@gmail.com</span>
                    </div>
                </div>

                <p class="text-[11px] text-slate-500 leading-relaxed">
                    Dibrugarh University maintains a strict zero-tolerance policy against any form of ragging or harassment on campus and hostels.
                </p>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 flex justify-end">
                <button onclick="closeHelplineModal()" class="px-5 py-2.5 bg-[#1a365d] text-white text-xs font-bold rounded-xl hover:bg-[#0f172a] transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <div id="spotlightModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-start justify-center p-4 pt-16 sm:pt-24">
        <div class="bg-white rounded-2xl max-w-xl w-full shadow-2xl border border-slate-200 overflow-hidden relative animate-fadeIn">
            <div class="p-4 border-b border-slate-100 flex items-center gap-3">
                <i class="fas fa-search text-slate-400 text-sm"></i>
                <input type="text" id="spotlightInput" placeholder="Type a faculty name, course, notice, or topic..." 
                    class="w-full text-sm font-medium bg-transparent focus:outline-none placeholder:text-slate-400">
                <button onclick="closeSpotlightSearch()" class="text-slate-400 hover:text-slate-700 text-xl leading-none">&times;</button>
            </div>

            <div id="spotlightResults" class="max-h-96 overflow-y-auto p-3">
            </div>
        </div>
    </div>

    <script>
    const portalPages = [
        { title: "Dr. Tazid Ali (Chairperson)", url: "faculty.php", category: "Faculty", desc: "Chairperson & Professor, Centre for Computer Science and Applications" },
        { title: "Dr. Utpala Borgohain", url: "faculty.php", category: "Faculty", desc: "Assistant Professor, Data Mining, Machine Learning & NLP" },
        { title: "Dr. Rizwan Rehman", url: "faculty.php", category: "Faculty", desc: "Assistant Professor, Deep Learning, NLP, Cloud Computing & Programming" },
        { title: "Dr. Ujjal Saikia", url: "faculty.php", category: "Faculty", desc: "Assistant Professor, Computer Vision, Database Systems & Pattern Recognition" },
        { title: "Dr. Pranjal Kumar Bora", url: "faculty.php", category: "Faculty", desc: "Assistant Professor, Machine Learning, Graph Theory & Wireless Networks" },
        { title: "Dr. Kimasha Borah", url: "faculty.php", category: "Faculty", desc: "Assistant Professor, Complex Networks, Social Network Analysis & Web Tech" },
        { title: "Dr. Toralima Bora", url: "faculty.php", category: "Faculty", desc: "Assistant Professor, Fuzzy Mathematics, Numerical Methods & Cryptography" },
        { title: "Mr. Ankumon Sarmah", url: "faculty.php", category: "Faculty", desc: "Assistant Professor, NLP, Speech Processing & Machine Learning" },
        { title: "Ms. Kankana Dutta", url: "faculty.php", category: "Faculty", desc: "Assistant Professor, Machine Learning & Speech Processing" },
        { title: "Dr. Sumpi Saikia", url: "faculty.php", category: "Faculty", desc: "Assistant Professor, Machine Learning & Image Processing" },
        { title: "Ms. Pinakshi Konwar", url: "faculty.php", category: "Faculty", desc: "Assistant Professor, Machine Learning & Computational Biology" },
        { title: "Bachelor of Computer Applications (BCA)", url: "undergraduate.php", category: "Academic Programmes", desc: "3-year undergraduate degree, 6 semesters, merit-based admission" },
        { title: "Master of Computer Applications (MCA)", url: "postgraduate.php", category: "Academic Programmes", desc: "2-year postgraduate program approved by AICTE, 4 semesters" },
        { title: "Post Graduate Diploma in Computer Applications (PGDCA)", url: "pgdca.php", category: "Academic Programmes", desc: "1-year specialized postgraduate diploma program" },
        { title: "Ph.D. in Computer Science", url: "phd.php", category: "Doctoral Research", desc: "Pre-registration coursework and doctoral research" },
        { title: "Research Areas & Innovation", url: "research.php", category: "Research", desc: "AI, Machine Learning, Generative AI, Cloud, Cybersecurity, Software Engineering" },
        { title: "Generative AI & LLMs Research", url: "research.php", category: "Research", desc: "Large Language Models, Prompt Engineering, RAG architectures" },
        { title: "Research Collaborations & Industry Partners", url: "research.php", category: "Research", desc: "Academic tie-ups, research facilities, contact for collaboration" },
        { title: "Research Publications & Papers", url: "publication.php", category: "Research", desc: "Indexed IEEE, Springer, ACM journals and conference papers" },
        { title: "Present Enrolled Students Roster", url: "Present_Stu.php", category: "Students", desc: "Directory of current students across BCA, MCA, PGDCA, and Ph.D." },
        { title: "Announcements & Notice Board", url: "notices.php", category: "Notice Board", desc: "Official circulars, DUAT admission notices, timetables, and exam schedules" },
        { title: "DUAT 2026 Examination Notice", url: "notices.php", category: "Notice Board", desc: "Dibrugarh University Admission Test schedule and application procedure" },
        { title: "Contact CCSA Office & Location", url: "index.php#contact", category: "Contact", desc: "Office email ccsduoffice@gmail.com, inquiries, and campus location" },
        { title: "Student ERP Portal Login", url: "https://erp.dibru.work/dibru/student/login", category: "Quick Links", desc: "Dibrugarh University student ERP portal login" },
        { title: "Alumni Association Platform", url: "https://ccsaalumni.in/", category: "Quick Links", desc: "CCSA Alumni network and community platform" },
        { title: "Placement Cell & Career Highlights", url: "https://placeccsa.wordpress.com/", category: "Quick Links", desc: "Student recruitment records and company placement drives" },
        { title: "Dibrugarh University Central Library", url: "https://dibru.ac.in/library", category: "Quick Links", desc: "Lakshminath Bezbaroa Central Library resources and journals" },
        { title: "Anti-Ragging & Grievance Helpline", url: "javascript:openHelplineModal()", category: "Helpline", desc: "24x7 National Toll-Free Helpline 1800-180-5522 and DU Proctor contact" }
    ];

    function getRecentSearches() {
        try {
            return JSON.parse(localStorage.getItem('ccsa_recent_searches') || '[]');
        } catch (e) {
            return [];
        }
    }

    function saveRecentSearch(item) {
        try {
            let recents = getRecentSearches();
            recents = recents.filter(r => r.url !== item.url && r.title !== item.title);
            recents.unshift(item);
            if (recents.length > 5) recents = recents.slice(0, 5);
            localStorage.setItem('ccsa_recent_searches', JSON.stringify(recents));
        } catch (e) {}
    }

    function clearRecentSearches() {
        try {
            localStorage.removeItem('ccsa_recent_searches');
            filterSpotlight('');
        } catch (e) {}
    }

    function openSpotlightSearch() {
        const modal = document.getElementById('spotlightModal');
        const input = document.getElementById('spotlightInput');
        if (modal) {
            modal.classList.remove('hidden');
            input.value = '';
            filterSpotlight('');
            setTimeout(() => input.focus(), 50);
        }
    }

    function closeSpotlightSearch() {
        const modal = document.getElementById('spotlightModal');
        if (modal) modal.classList.add('hidden');
    }

    function openHelplineModal() {
        const modal = document.getElementById('helplineModal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeHelplineModal() {
        const modal = document.getElementById('helplineModal');
        if (modal) modal.classList.add('hidden');
    }

    function selectSearchResult(url, title, category, desc) {
        saveRecentSearch({ title, url, category, desc });
        if (url.startsWith('javascript:')) {
            closeSpotlightSearch();
            eval(url.replace('javascript:', ''));
        } else {
            window.location.href = url;
        }
    }

    function filterSpotlight(query) {
        const resultsContainer = document.getElementById('spotlightResults');
        if (!resultsContainer) return;

        const q = query.toLowerCase().trim();

        if (q === '') {
            const recents = getRecentSearches();
            if (recents.length > 0) {
                resultsContainer.innerHTML = `
                    <div class="flex items-center justify-between px-2 py-1 mb-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                            <i class="fas fa-history text-[10px]"></i> Recent Searches
                        </span>
                        <button onclick="clearRecentSearches()" class="text-[11px] text-indigo-600 hover:text-indigo-800 font-semibold cursor-pointer">
                            Clear
                        </button>
                    </div>
                    <div class="space-y-1">
                        ${recents.map(p => `
                            <a href="javascript:void(0)" onclick="selectSearchResult('${p.url}', '${p.title.replace(/'/g, "\\'")}', '${p.category.replace(/'/g, "\\'")}', '${(p.desc || '').replace(/'/g, "\\'")}')" class="block p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">${p.title}</span>
                                    <span class="text-[10px] font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">${p.category}</span>
                                </div>
                                ${p.desc ? `<p class="text-[11px] text-slate-500 mt-0.5 line-clamp-1">${p.desc}</p>` : ''}
                            </a>
                        `).join('')}
                    </div>
                `;
            } else {
                resultsContainer.innerHTML = `
                    <div class="p-8 text-center text-slate-400">
                        <i class="fas fa-search text-slate-300 text-2xl mb-2 block"></i>
                        <p class="text-xs">Type a faculty name, course, notice, or topic to search...</p>
                    </div>
                `;
            }
            return;
        }

        const filtered = portalPages.filter(p => 
            p.title.toLowerCase().includes(q) || 
            p.desc.toLowerCase().includes(q) || 
            p.category.toLowerCase().includes(q)
        );

        if (filtered.length === 0) {
            resultsContainer.innerHTML = `
                <div class="p-8 text-center text-slate-400">
                    <i class="fas fa-search-minus text-slate-300 text-2xl mb-2 block"></i>
                    <p class="text-xs">No matching results found for "${query}".</p>
                </div>
            `;
            return;
        }

        resultsContainer.innerHTML = `
            <div class="space-y-1">
                ${filtered.map(p => `
                    <a href="javascript:void(0)" onclick="selectSearchResult('${p.url}', '${p.title.replace(/'/g, "\\'")}', '${p.category.replace(/'/g, "\\'")}', '${(p.desc || '').replace(/'/g, "\\'")}')" class="block p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">${p.title}</span>
                            <span class="text-[10px] font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">${p.category}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-1">${p.desc}</p>
                    </a>
                `).join('')}
            </div>
        `;
    }

    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            const modal = document.getElementById('spotlightModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeSpotlightSearch();
            } else {
                openSpotlightSearch();
            }
        }
        if (e.key === 'Escape') {
            closeSpotlightSearch();
            closeHelplineModal();
        }
    });

    document.getElementById('spotlightInput')?.addEventListener('input', (e) => {
        filterSpotlight(e.target.value);
    });
    </script>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="assets/js/weather.js"></script>
    <?php echo $extraScripts; ?>

</body>
</html>
