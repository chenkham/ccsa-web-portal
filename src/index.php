<?php
declare(strict_types=1);

/**
 * Main Portal Homepage — Centre for Computer Science and Applications (CCSA)
 * Dibrugarh University
 * 
 * Clean, professional, authentic design with smooth card-hover animations 
 * and extendable announcements.
 */

$pageTitle = 'CCSA - Centre for Computer Science and Applications, Dibrugarh University';
$currentPage = 'home';
include 'templates/header.php';
?>

    <!-- Hero Video Section -->
    <section class="relative bg-[#0f172a] text-white w-full overflow-hidden leading-none">
        <div class="w-full relative h-[380px] xs:h-[410px] sm:h-[460px] md:h-auto md:aspect-[1280/530] overflow-hidden flex items-center justify-center">
            <video 
                id="heroDepartmentVideo"
                autoplay 
                loop 
                muted 
                playsinline 
                preload="auto"
                poster="assets/videos/ccsa_poster.jpg"
                onloadedmetadata="this.playbackRate = 0.7;"
                onplay="this.playbackRate = 0.7;"
                class="h-full w-auto min-w-full md:w-full md:h-auto max-w-none md:max-w-full object-cover object-center block">
                <source src="assets/videos/ccsa_optimized.mp4" type="video/mp4">
            </video>

            <!-- Hero Headline & Outline Button (Oil India Style: Solid White, No Shadows, Clean Video View) -->
            <div class="absolute inset-0 flex items-center z-10 pointer-events-none">
                <div class="container mx-auto px-5 sm:px-8 lg:px-12 pointer-events-auto">
                    <div class="max-w-[320px] xs:max-w-[360px] sm:max-w-md md:max-w-lg lg:max-w-xl space-y-3 sm:space-y-4">
                        <h1 class="text-2xl xs:text-3xl sm:text-4xl md:text-5xl font-black uppercase text-white tracking-tight leading-tight">
                            Two Decades of<br>Computing Excellence
                        </h1>

                        <p class="text-xs sm:text-sm md:text-base font-normal text-white uppercase tracking-wider leading-relaxed">
                            Centre for Computer Science and Applications<br>Dibrugarh University
                        </p>

                        <div class="pt-2 sm:pt-3 flex items-center">
                            <a href="#programs" 
                                class="inline-flex items-center justify-center px-6 py-2.5 rounded-full border-2 border-white bg-transparent hover:bg-white hover:text-slate-950 text-white text-xs sm:text-sm font-bold transition-all duration-200">
                                <span>Academic Programmes</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ ANNOUNCEMENT TICKER BAR ═══ -->
    <div id="announcementTicker" class="bg-white border-y border-slate-200/90 shadow-xs relative z-20">
        <div class="container mx-auto px-2.5 sm:px-4 lg:px-10 flex items-center gap-1.5 sm:gap-3 py-1 sm:py-2">
            <!-- Live Badge -->
            <div class="flex items-center gap-1 shrink-0 bg-red-50 text-red-700 px-1.5 sm:px-2.5 py-0.5 rounded-full border border-red-200/80 shadow-xs">
                <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-red-600 animate-pulse shrink-0"></span>
                <span class="text-[9px] sm:text-xs font-black uppercase tracking-wider">NOTICES</span>
            </div>
            
            <div class="h-3 sm:h-4 w-px bg-slate-200 shrink-0"></div>
            
            <!-- Scrolling Headlines -->
            <div class="ticker-bar flex-1 overflow-hidden min-w-0">
                <div id="tickerTrack" class="ticker-track text-slate-700">
                    <span class="text-[11px] sm:text-xs font-medium text-slate-400">Loading announcements...</span>
                </div>
            </div>
            
            <!-- View All Link -->
            <a href="notices.php" class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-indigo-600 hover:text-indigo-800 transition-colors shrink-0 hidden md:inline-flex items-center gap-1 bg-indigo-50 hover:bg-indigo-100 px-2 py-0.5 sm:py-1 rounded-lg">
                <span>All Notices</span>
                <i class="fas fa-arrow-right text-[8px]"></i>
            </a>
        </div>
    </div>

    <!-- ═══ OVERVIEW & ANNOUNCEMENTS (Combined 2-Column Section) ═══ -->
    <section id="aboutus" class="py-12 sm:py-16 bg-[#fafbfd] relative overflow-hidden border-b border-slate-200/80">
        <!-- ╔═══════════════════════════════════════════════════════════════════╗
             ║  PREMIUM CS & AI DOODLE PATTERN BACKGROUND                      ║
             ║  25+ hand-drawn icons · 480×520 tile · organic rotations        ║
             ║  Covers: AI, ML, Data Science, Cybersecurity, Cloud, Coding     ║
             ╚═══════════════════════════════════════════════════════════════════╝ -->
        <div class="absolute inset-0 pointer-events-none opacity-[0.06] select-none">
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="ccsa-doodle" width="260" height="260" patternUnits="userSpaceOnUse" patternTransform="rotate(-2)">
                        <g stroke="#1a365d" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <!-- 🧠 AI Brain -->
                            <g transform="translate(8, 8) rotate(-4)">
                                <path d="M14 4C10 1 4 2 2 7C0 12 4 16 4 19C4 22 9 23 14 23"/>
                                <path d="M14 4C18 1 24 2 26 7C28 12 24 16 24 19C24 22 19 23 14 23"/>
                                <line x1="14" y1="4" x2="14" y2="23" stroke-dasharray="2 1.5"/>
                                <circle cx="7" cy="9" r="1.8" fill="#1a365d"/><circle cx="21" cy="9" r="1.8" fill="#1a365d"/>
                                <circle cx="8" cy="16" r="1.4" fill="#1a365d"/><circle cx="20" cy="16" r="1.4" fill="#1a365d"/>
                            </g>
                            <!-- 💻 Laptop / Terminal -->
                            <g transform="translate(140, 10) rotate(3)">
                                <rect x="4" y="3" width="24" height="15" rx="2"/>
                                <line x1="1" y1="18" x2="31" y2="18"/>
                                <path d="M7 8L11 11L7 14M14 14H19"/>
                            </g>
                            <!-- 🤖 Robot Head -->
                            <g transform="translate(75, 50) rotate(-3)">
                                <rect x="3" y="7" width="18" height="14" rx="3"/>
                                <circle cx="8" cy="12" r="1.8" fill="#1a365d"/><circle cx="16" cy="12" r="1.8" fill="#1a365d"/>
                                <line x1="8" y1="17" x2="16" y2="17"/>
                                <line x1="12" y1="2" x2="12" y2="7"/><circle cx="12" cy="1.5" r="1.2" fill="#1a365d"/>
                                <line x1="0" y1="13" x2="3" y2="13"/><line x1="21" y1="13" x2="24" y2="13"/>
                            </g>
                            <!-- 🗄️ Database Cylinder -->
                            <g transform="translate(205, 55) rotate(5)">
                                <ellipse cx="12" cy="5" rx="10" ry="3.5"/>
                                <path d="M2 5V12C2 14 6.5 15.5 12 15.5C17.5 15.5 22 14 22 12V5"/>
                                <path d="M2 12V19C2 21 6.5 22.5 12 22.5C17.5 22.5 22 21 22 19V12"/>
                            </g>
                            <!-- 🔌 Chip / Microprocessor -->
                            <g transform="translate(10, 95) rotate(4)">
                                <rect x="5" y="5" width="18" height="18" rx="2.5"/>
                                <rect x="9" y="9" width="10" height="10" rx="1" fill="#1a365d" fill-opacity="0.2"/>
                                <line x1="9" y1="1" x2="9" y2="5"/><line x1="14" y1="1" x2="14" y2="5"/><line x1="19" y1="1" x2="19" y2="5"/>
                                <line x1="9" y1="23" x2="9" y2="27"/><line x1="14" y1="23" x2="14" y2="27"/><line x1="19" y1="23" x2="19" y2="27"/>
                                <line x1="1" y1="9" x2="5" y2="9"/><line x1="1" y1="14" x2="5" y2="14"/><line x1="1" y1="19" x2="5" y2="19"/>
                                <line x1="23" y1="9" x2="27" y2="9"/><line x1="23" y1="14" x2="27" y2="14"/><line x1="23" y1="19" x2="27" y2="19"/>
                            </g>
                            <!-- ☁️ Cloud Computing -->
                            <g transform="translate(145, 100) rotate(-4)">
                                <path d="M7 16C4.5 16 2 14 2 11C2 8.5 4 6.5 6.5 6.2C7.5 3.5 10.5 1.5 14 1.5C18 1.5 21 4 22 7.5C23.8 8 25 9.5 25 11.5C25 13.8 23 16 20.5 16H7Z"/>
                                <path d="M11 18L13.5 15.5L16 18M13.5 15.5V22"/>
                            </g>
                            <!-- 🔒 Cybersecurity Shield -->
                            <g transform="translate(68, 150) rotate(2)">
                                <path d="M12 2L3 5.5V12C3 17 12 22 12 22C12 22 21 17 21 12V5.5L12 2Z"/>
                                <circle cx="12" cy="11" r="2" fill="#1a365d"/><path d="M12 13V16"/>
                            </g>
                            <!-- 🕸️ Neural Network -->
                            <g transform="translate(195, 155) rotate(-5)">
                                <circle cx="3" cy="5" r="2" fill="#1a365d"/><circle cx="3" cy="17" r="2" fill="#1a365d"/>
                                <circle cx="14" cy="11" r="2" fill="#1a365d"/><circle cx="25" cy="5" r="2.5" fill="#1a365d"/><circle cx="25" cy="17" r="2.5" fill="#1a365d"/>
                                <line x1="5" y1="5" x2="12" y2="11"/><line x1="5" y1="17" x2="12" y2="11"/>
                                <line x1="16" y1="11" x2="23" y2="5"/><line x1="16" y1="11" x2="23" y2="17"/>
                            </g>
                            <!-- ⚛️ Quantum Atom -->
                            <g transform="translate(12, 195) rotate(5)">
                                <ellipse cx="12" cy="12" rx="11" ry="4.5" transform="rotate(30 12 12)"/>
                                <ellipse cx="12" cy="12" rx="11" ry="4.5" transform="rotate(90 12 12)"/>
                                <ellipse cx="12" cy="12" rx="11" ry="4.5" transform="rotate(150 12 12)"/>
                                <circle cx="12" cy="12" r="2" fill="#1a365d"/>
                            </g>
                            <!-- 📶 WiFi Signal -->
                            <g transform="translate(135, 205) rotate(-2)">
                                <path d="M2 5A14 14 0 0 1 20 5M5 9A10 10 0 0 1 17 9M8 13A5 5 0 0 1 14 13"/>
                                <circle cx="11" cy="16" r="1.3" fill="#1a365d"/>
                            </g>
                        </g>
                        <!-- Rich Fillers & Typography -->
                        <text x="68" y="28" font-family="'Courier New',monospace" font-size="13" font-weight="900" fill="#1a365d">&lt;/&gt;</text>
                        <text x="195" y="32" font-family="'Courier New',monospace" font-size="15" font-weight="bold" fill="#1a365d">{ }</text>
                        <text x="14" y="72" font-family="'Courier New',monospace" font-size="9" font-weight="bold" fill="#1a365d" letter-spacing="1">10110</text>
                        <text x="120" y="74" font-family="'Courier New',monospace" font-size="11" font-weight="900" fill="#1a365d">AI</text>
                        <text x="75" y="125" font-family="'Courier New',monospace" font-size="11" font-weight="bold" fill="#1a365d">λ</text>
                        <text x="210" y="132" font-family="'Courier New',monospace" font-size="11" font-weight="900" fill="#1a365d">ML</text>
                        <text x="128" y="170" font-family="'Courier New',monospace" font-size="13" font-weight="bold" fill="#1a365d">∑</text>
                        <text x="15" y="168" font-family="'Courier New',monospace" font-size="10" font-weight="bold" fill="#1a365d">!=</text>
                        <text x="68" y="228" font-family="'Courier New',monospace" font-size="11" font-weight="900" fill="#1a365d">#py</text>
                        <text x="198" y="232" font-family="'Courier New',monospace" font-size="12" font-weight="bold" fill="#1a365d">f(x)</text>
                        <!-- Circuit connect dots -->
                        <circle cx="50" cy="50" r="1.3" fill="#1a365d"/><circle cx="115" cy="105" r="1.8" fill="#1a365d"/><circle cx="175" cy="85" r="1.3" fill="#1a365d"/>
                        <circle cx="58" cy="188" r="1.6" fill="#1a365d"/><circle cx="180" cy="198" r="1.4" fill="#1a365d"/><circle cx="245" cy="100" r="1.6" fill="#1a365d"/>
                        <path d="M50 50h12v8" stroke="#1a365d" stroke-width="1" stroke-dasharray="2 2" fill="none"/>
                        <path d="M175 85v12h10" stroke="#1a365d" stroke-width="1" stroke-dasharray="2 2" fill="none"/>
                        <path d="M58 188v-10h8" stroke="#1a365d" stroke-width="1" stroke-dasharray="2 2" fill="none"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#ccsa-doodle)"/>
            </svg>
        </div>

        <div class="container mx-auto px-4 lg:px-10 relative z-10">
            <!-- Unified 2-Column Horizontal Row: Overview (Left) + Announcements (Right) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-8 items-start">
                
                <!-- Left Column: Overview (lg:col-span-7) -->
                <div class="lg:col-span-7 bg-transparent rounded-2xl sm:rounded-3xl p-2 sm:p-4 lg:p-6 flex flex-col justify-between">
                    <div>
                        <div class="mb-5">
                            <span class="bg-[#fbbf24] text-slate-900 text-xs font-extrabold px-3.5 py-1 rounded-full uppercase tracking-wider mb-2 inline-block">About Centre</span>
                            <h2 class="text-xl sm:text-2xl lg:text-3xl font-extrabold font-display uppercase tracking-wide text-[#1a365d] mt-1">
                                Overview
                            </h2>
                            <p class="text-[11px] sm:text-xs font-semibold uppercase tracking-wider text-slate-500 mt-1">
                                Centre for Computer Science and Applications | Dibrugarh University
                            </p>
                            <div class="w-16 h-1 bg-[#fbbf24] mt-2.5 rounded-full"></div>
                        </div>

                        <div class="text-justify text-slate-700 text-xs sm:text-sm lg:text-[15px] leading-relaxed sm:leading-7 space-y-3.5 font-normal">
                            <p>
                                <span class="font-bold text-[#1a365d]">Centre for Computer Science and Applications</span> (formerly known as Centre for Computer Studies) is one of the premier institutes of North-East India imparting quality computer education. Dibrugarh University initiated its journey of imparting computer education by establishing a dedicated Computer Centre in 1976.
                            </p>
                            <p>
                                The Computer Centre was established with the prime objective of creating computer awareness among teachers, research scholars, and employees of the University. It introduced computer education through a comprehensive "Six-months Certificate Course on Computer Programming" and actively trained personnel from leading financial institutions, including State Bank of India, Dibrugarh University Branch, to catalyze digital transformation across the region.
                            </p>
                            <p>
                                In 2004, it was upgraded to the <span class="font-semibold text-slate-900">Centre for Computer Studies (CCS)</span> and launched the Post-Graduate Diploma in Computer Application (PGDCA). The BCA programme was introduced in July 2004, followed by MCA and BSc (IT) in 2007. In 2018, the centre was officially renamed the <span class="font-semibold text-[#1a365d]">Centre for Computer Science and Applications (CCSA)</span>, continuing its mission of academic excellence, cutting-edge computing research, and industry-aligned technical education.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Announcements (lg:col-span-5) -->
                <div id="notices" class="lg:col-span-5 bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200/90 p-5 sm:p-7 flex flex-col justify-between">
                    <div>
                        <!-- Header with Icon & View All Link -->
                        <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 mb-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <h2 class="text-base sm:text-lg font-bold text-indigo-600 tracking-tight">Announcements</h2>
                            </div>
                            <a href="notices.php" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1 group">
                                <span>View All</span>
                                <span class="w-4 h-4 bg-indigo-50 text-indigo-600 rounded flex items-center justify-center text-[10px] group-hover:translate-x-0.5 transition-transform">&gt;</span>
                            </a>
                        </div>

                        <!-- Scrollable Container for Notices -->
                        <div class="max-h-[380px] sm:max-h-[420px] overflow-y-auto pr-1.5 custom-scrollbar" id="notice-wrapper">
                            <!-- Skeletons while loading -->
                            <div class="space-y-3 py-2">
                                <div class="animate-pulse flex flex-col gap-2 py-3 border-b border-slate-100">
                                    <div class="h-4 bg-slate-200 rounded w-3/4"></div>
                                    <div class="h-3 bg-slate-100 rounded w-1/2"></div>
                                </div>
                                <div class="animate-pulse flex flex-col gap-2 py-3 border-b border-slate-100">
                                    <div class="h-4 bg-slate-200 rounded w-5/6"></div>
                                    <div class="h-3 bg-slate-100 rounded w-1/3"></div>
                                </div>
                                <div class="animate-pulse flex flex-col gap-2 py-3">
                                    <div class="h-4 bg-slate-200 rounded w-2/3"></div>
                                    <div class="h-3 bg-slate-100 rounded w-1/2"></div>
                                </div>
                            </div>
                        </div>

                        <div id="no-notices" class="hidden text-center py-10 text-slate-400 text-xs">
                            <i class="fas fa-inbox text-2xl text-slate-300 mb-2 block"></i>
                            No new announcements at this moment.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Programs Section (Structured, Clear & Informative) -->
    <section id="programs" class="py-14 sm:py-16 bg-slate-50 relative">
        <div class="absolute inset-0 pointer-events-none opacity-[0.08] select-none">
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="doodle-programs" width="260" height="260" patternUnits="userSpaceOnUse" patternTransform="rotate(-1)">
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
                <rect width="100%" height="100%" fill="url(#doodle-programs)"/>
            </svg>
        </div>
        <div class="container mx-auto px-4 lg:px-10 relative z-10">
            <!-- Section Header -->
            <div class="mb-8 sm:mb-12 text-center">
                <h2 class="text-xl sm:text-3xl lg:text-4xl font-extrabold font-display uppercase tracking-wide text-[#1a365d]">
                    Programmes Offered
                </h2>
                <p class="text-[11px] sm:text-sm font-semibold uppercase tracking-wider text-slate-500 mt-2">
                    Academic Degrees &amp; Research at Centre for Computer Science and Applications
                </p>
                <div class="w-20 h-1 bg-[#fbbf24] mt-3 rounded-full mx-auto"></div>
            </div>

            <!-- Program Cards Grid (Yellow Cards with Crisp White Typography & Solid White Buttons) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                <!-- BCA Card -->
                <div class="bg-[#fbbf24] text-white rounded-2xl p-6 sm:p-8 border border-amber-400 shadow-sm flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-md programme-card">
                    <div>
                        <!-- Header with Badge & Icon -->
                        <div class="flex items-center justify-between mb-5">
                            <span class="px-3.5 py-1 rounded-full bg-black/20 border border-white/30 text-white text-xs font-bold uppercase tracking-wider">
                                Undergraduate
                            </span>
                            <div class="w-11 h-11 rounded-xl bg-white/20 text-white flex items-center justify-center text-lg font-bold">
                                <i class="fas fa-laptop-code"></i>
                            </div>
                        </div>

                        <h3 class="text-xl sm:text-2xl font-black font-display text-white mb-2 leading-tight">
                            Bachelor of Computer Applications (BCA)
                        </h3>

                        <!-- Metadata Row -->
                        <div class="flex items-center gap-4 text-xs text-white font-bold mb-4 pb-3 border-b border-white/30">
                            <span class="flex items-center gap-1.5"><i class="fas fa-calendar-alt text-white"></i> 3 Years (6 Sem)</span>
                            <span class="flex items-center gap-1.5"><i class="fas fa-user-graduate text-white"></i> 60 Seats</span>
                        </div>

                        <p class="text-white text-xs sm:text-sm leading-relaxed mb-6 font-medium">
                            A foundational undergraduate programme focused on modern programming languages, database management, software development, web applications, and core computer science fundamentals.
                        </p>
                    </div>

                    <!-- Solid White Button -->
                    <a href="undergraduate.php"
                        class="inline-flex items-center justify-center w-full px-5 py-3 rounded-xl bg-white hover:bg-slate-100 text-slate-900 transition-all duration-200 text-xs sm:text-sm font-bold tracking-wide shadow-sm group">
                        <span class="text-slate-900 font-bold">Programme Details</span>
                        <i class="fas fa-arrow-right ml-2 text-xs text-slate-900 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>

                <!-- MCA Card -->
                <div class="bg-[#fbbf24] text-white rounded-2xl p-6 sm:p-8 border border-amber-400 shadow-sm flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-md programme-card">
                    <div>
                        <!-- Header with Badge & Icon -->
                        <div class="flex items-center justify-between mb-5">
                            <span class="px-3.5 py-1 rounded-full bg-black/20 border border-white/30 text-white text-xs font-bold uppercase tracking-wider">
                                Postgraduate | AICTE
                            </span>
                            <div class="w-11 h-11 rounded-xl bg-white/20 text-white flex items-center justify-center text-lg font-bold">
                                <i class="fas fa-laptop"></i>
                            </div>
                        </div>

                        <h3 class="text-xl sm:text-2xl font-black font-display text-white mb-2 leading-tight">
                            Master of Computer Applications (MCA)
                        </h3>

                        <!-- Metadata Row -->
                        <div class="flex items-center gap-4 text-xs text-white font-bold mb-4 pb-3 border-b border-white/30">
                            <span class="flex items-center gap-1.5"><i class="fas fa-calendar-alt text-white"></i> 2 Years (4 Sem)</span>
                            <span class="flex items-center gap-1.5"><i class="fas fa-check-double text-white"></i> AICTE Approved</span>
                        </div>

                        <p class="text-white text-xs sm:text-sm leading-relaxed mb-6 font-medium">
                            An AICTE-approved master's degree covering advanced computing architectures, artificial intelligence, cloud systems, machine learning, and full semester industry project internships.
                        </p>
                    </div>

                    <!-- Solid White Button -->
                    <a href="postgraduate.php"
                        class="inline-flex items-center justify-center w-full px-5 py-3 rounded-xl bg-white hover:bg-slate-100 text-slate-900 transition-all duration-200 text-xs sm:text-sm font-bold tracking-wide shadow-sm group">
                        <span class="text-slate-900 font-bold">Programme Details</span>
                        <i class="fas fa-arrow-right ml-2 text-xs text-slate-900 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>

                <!-- PGDCA Card -->
                <div class="bg-[#fbbf24] text-white rounded-2xl p-6 sm:p-8 border border-amber-400 shadow-sm flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-md programme-card">
                    <div>
                        <!-- Header with Badge & Icon -->
                        <div class="flex items-center justify-between mb-5">
                            <span class="px-3.5 py-1 rounded-full bg-black/20 border border-white/30 text-white text-xs font-bold uppercase tracking-wider">
                                Post Graduate Diploma
                            </span>
                            <div class="w-11 h-11 rounded-xl bg-white/20 text-white flex items-center justify-center text-lg font-bold">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                        </div>

                        <h3 class="text-xl sm:text-2xl font-black font-display text-white mb-2 leading-tight">
                            Post Graduate Diploma in Computer Applications (PGDCA)
                        </h3>

                        <!-- Metadata Row -->
                        <div class="flex items-center gap-4 text-xs text-white font-bold mb-4 pb-3 border-b border-white/30">
                            <span class="flex items-center gap-1.5"><i class="fas fa-calendar-alt text-white"></i> 1 Year (2 Sem)</span>
                            <span class="flex items-center gap-1.5"><i class="fas fa-certificate text-white"></i> Diploma</span>
                        </div>

                        <p class="text-white text-xs sm:text-sm leading-relaxed mb-6 font-medium">
                            A specialized 1-year professional postgraduate diploma programme tailored for graduates seeking mastery over essential programming tools, databases, and IT application workflows.
                        </p>
                    </div>

                    <!-- Solid White Button -->
                    <a href="postgraduate.php"
                        class="inline-flex items-center justify-center w-full px-5 py-3 rounded-xl bg-white hover:bg-slate-100 text-slate-900 transition-all duration-200 text-xs sm:text-sm font-bold tracking-wide shadow-sm group">
                        <span class="text-slate-900 font-bold">Programme Details</span>
                        <i class="fas fa-arrow-right ml-2 text-xs text-slate-900 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>

            <!-- Departmental Infrastructure Strip -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mt-10">
                <!-- 1. Expert Faculty -->
                <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-100 shadow-sm flex flex-col items-center justify-center text-center hover:shadow-md hover:-translate-y-1 transition-all duration-300 group">
                    <div class="text-amber-500 text-3xl sm:text-4xl mb-3.5 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4 class="font-bold font-display text-sm sm:text-base text-[#1a365d] tracking-tight">Expert Faculty</h4>
                </div>

                <!-- 2. Modern Computer Labs -->
                <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-100 shadow-sm flex flex-col items-center justify-center text-center hover:shadow-md hover:-translate-y-1 transition-all duration-300 group">
                    <div class="text-amber-500 text-3xl sm:text-4xl mb-3.5 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-flask"></i>
                    </div>
                    <h4 class="font-bold font-display text-sm sm:text-base text-[#1a365d] tracking-tight">Modern Computer Labs</h4>
                </div>

                <!-- 3. Internet (LAN and wifi) -->
                <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-100 shadow-sm flex flex-col items-center justify-center text-center hover:shadow-md hover:-translate-y-1 transition-all duration-300 group">
                    <div class="text-amber-500 text-3xl sm:text-4xl mb-3.5 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-wifi"></i>
                    </div>
                    <h4 class="font-bold font-display text-sm sm:text-base text-[#1a365d] tracking-tight">Internet (LAN and wifi)</h4>
                </div>

                <!-- 4. Library cum conference room -->
                <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-100 shadow-sm flex flex-col items-center justify-center text-center hover:shadow-md hover:-translate-y-1 transition-all duration-300 group">
                    <div class="text-amber-500 text-3xl sm:text-4xl mb-3.5 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h4 class="font-bold font-display text-sm sm:text-base text-[#1a365d] tracking-tight">Library cum conference room</h4>
                </div>
            </div>
        </div>
    </section>

    <!-- Chairperson Message Section (Full-Width Blue Band with Geometric Faceted Shadow Pattern) -->
    <section class="py-16 lg:py-20 bg-gradient-to-br from-[#0a1128] via-[#1a365d] to-[#1e40af] text-white relative overflow-hidden">
        <!-- Low-Poly Geometric Faceted Mesh & Shadow Shards (Like Reference Yellow Background Pattern) -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <svg class="w-full h-full object-cover min-w-[1000px] opacity-40" viewBox="0 0 1440 600" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Faceted Triangular Polygon Shards with Light and Shadow variations -->
                <polygon points="0,0 350,0 180,260" fill="rgba(255, 255, 255, 0.05)" />
                <polygon points="350,0 720,0 520,240" fill="rgba(0, 0, 0, 0.2)" />
                <polygon points="720,0 1100,0 920,280" fill="rgba(255, 255, 255, 0.08)" />
                <polygon points="1100,0 1440,0 1320,220" fill="rgba(0, 0, 0, 0.25)" />
                
                <!-- Mid-tier facets -->
                <polygon points="0,0 180,260 0,420" fill="rgba(0, 0, 0, 0.18)" />
                <polygon points="180,260 520,240 380,480" fill="rgba(255, 255, 255, 0.06)" />
                <polygon points="520,240 920,280 760,460" fill="rgba(0, 0, 0, 0.3)" />
                <polygon points="920,280 1320,220 1180,490" fill="rgba(255, 255, 255, 0.07)" />
                <polygon points="1320,220 1440,0 1440,380" fill="rgba(255, 255, 255, 0.04)" />
                <polygon points="1320,220 1440,380 1440,600" fill="rgba(0, 0, 0, 0.32)" />

                <!-- Bottom facets -->
                <polygon points="0,420 180,260 380,480" fill="rgba(0, 0, 0, 0.14)" />
                <polygon points="0,420 380,480 0,600" fill="rgba(255, 255, 255, 0.03)" />
                <polygon points="0,600 380,480 620,600" fill="rgba(0, 0, 0, 0.25)" />
                <polygon points="380,480 760,460 620,600" fill="rgba(255, 255, 255, 0.05)" />
                <polygon points="760,460 1180,490 980,600" fill="rgba(0, 0, 0, 0.3)" />
                <polygon points="620,600 760,460 980,600" fill="rgba(0, 0, 0, 0.15)" />
                <polygon points="980,600 1180,490 1440,600" fill="rgba(255, 255, 255, 0.06)" />

                <!-- Dynamic Shadow Creases & Angled Geometric Accent Lines -->
                <line x1="0" y1="0" x2="1440" y2="600" stroke="rgba(255, 255, 255, 0.1)" stroke-width="1.5" />
                <line x1="350" y1="0" x2="0" y2="420" stroke="rgba(0, 0, 0, 0.3)" stroke-width="2" />
                <line x1="720" y1="0" x2="380" y2="480" stroke="rgba(255, 255, 255, 0.12)" stroke-width="1.5" />
                <line x1="1100" y1="0" x2="760" y2="460" stroke="rgba(0, 0, 0, 0.35)" stroke-width="2" />
                <line x1="1440" y1="0" x2="980" y2="600" stroke="rgba(255, 255, 255, 0.12)" stroke-width="1.5" />
                <line x1="180" y1="260" x2="920" y2="280" stroke="rgba(0, 0, 0, 0.25)" stroke-width="1.5" />
                <line x1="380" y1="480" x2="1180" y2="490" stroke="rgba(255, 255, 255, 0.08)" stroke-width="1.5" />
            </svg>
        </div>
        <div class="absolute -right-24 -bottom-24 w-96 h-96 bg-blue-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="container mx-auto px-4 lg:px-12 relative z-10">
            <!-- Section Header -->
            <div class="text-center mb-8 sm:mb-10 lg:mb-14">
                <h2 class="text-xl sm:text-3xl lg:text-4xl font-extrabold uppercase tracking-wider text-white">
                    Message from The Chairperson
                </h2>
                <div class="w-20 h-1 bg-[#fbbf24] mx-auto mt-3 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                <!-- Chairperson Portrait Column with Circle Frame -->
                <div class="lg:col-span-5 flex justify-center items-center relative pt-2 lg:pt-0 order-1 lg:order-2 mb-4 lg:mb-0">
                    <!-- Ambient subtle backlight halo behind circle -->
                    <div class="absolute w-60 h-60 sm:w-76 sm:h-76 lg:w-92 lg:h-92 bg-gradient-to-tr from-[#fbbf24]/25 to-blue-500/25 rounded-full blur-2xl -z-10"></div>

                    <!-- Circular Frame with Depth & Glassmorphism -->
                    <div class="relative w-56 h-56 sm:w-72 sm:h-72 lg:w-88 lg:h-88 rounded-full p-2.5 sm:p-3 bg-gradient-to-b from-[#fbbf24]/40 via-white/15 to-blue-500/30 border-2 border-[#fbbf24]/60 shadow-[0_20px_45px_rgba(0,0,0,0.5),inset_0_2px_4px_rgba(255,255,255,0.25)] backdrop-blur-md flex items-end justify-center overflow-hidden group">
                        <!-- Background Canvas Disc -->
                        <div class="absolute inset-0 rounded-full bg-gradient-to-b from-slate-800/80 via-[#102a45] to-[#0b1329] -z-10"></div>
                        
                        <img src="faculty/Chairperson_cutout.png" 
                            alt="Prof. Paramananda Deka - Chairperson, CCSA" 
                            class="w-full h-full object-cover object-top transition-transform duration-500 group-hover:scale-105 drop-shadow-[0_10px_20px_rgba(0,0,0,0.4)]"
                            onerror="this.src='faculty/Chairperson.jpg'">
                    </div>
                </div>

                <!-- Expanded Writing Column (order-2 on mobile so text is below photo) -->
                <div class="lg:col-span-7 space-y-5 order-2 lg:order-1">
                    <div class="text-center lg:text-left">
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-[#fbbf24]">
                            Prof. Paramananda Deka
                        </h3>
                        <p class="text-xs sm:text-base text-blue-200 font-medium mt-1">
                            Professor, Department of Mathematics &amp; Chairperson, CCSA
                        </p>
                    </div>

                    <!-- Quote Symbol -->
                    <div class="text-[#fbbf24]/80 flex justify-center lg:justify-start">
                        <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="currentColor" viewBox="0 0 32 32">
                            <path d="M10 8c-3.3 0-6 2.7-6 6v10h10V14H6c0-2.2 1.8-4 4-4V8zm14 0c-3.3 0-6 2.7-6 6v10h10V14h-8c0-2.2 1.8-4 4-4V8z" />
                        </svg>
                    </div>

                    <!-- Expanded Authentic Message Content (Justified) -->
                    <div class="space-y-4 text-blue-50/90 text-sm sm:text-base leading-relaxed text-justify">
                        <p>
                            Welcome to the Centre for Computer Science and Applications at Dibrugarh University. As
                            we navigate through the digital age, our centre stands at the forefront of computer
                            science education and research in Northeast India.
                        </p>

                        <p>
                            Our mission is to nurture innovative minds and create technology leaders who can address
                            the challenges of tomorrow. We focus on providing a comprehensive education that blends
                            theoretical knowledge with practical applications.
                        </p>

                        <p>
                            With state-of-the-art facilities, dedicated faculty, and strong industry connections, we
                            prepare our students not just for careers, but for leadership in the ever-evolving field
                            of computer science.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ PLACEMENTS & RECRUITERS SECTION (Shadowy Design & Doodle Art) ═══ -->
    <section id="placements" class="py-16 sm:py-20 bg-slate-100/80 border-y border-slate-200/90 relative overflow-hidden shadow-[inset_0_15px_30px_-10px_rgba(15,23,42,0.07),inset_0_-15px_30px_-10px_rgba(15,23,42,0.07)]">
        <!-- 💼 Career & Industry SVG Doodle Background Pattern -->
        <div class="absolute inset-0 pointer-events-none opacity-[0.07] select-none">
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="doodle-placements" width="260" height="260" patternUnits="userSpaceOnUse" patternTransform="rotate(2)">
                        <g stroke="#1a365d" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <!-- Briefcase / Portfolio -->
                            <g transform="translate(10, 8) rotate(-3)">
                                <rect x="3" y="7" width="22" height="16" rx="2"/>
                                <path d="M9 7V4a1.5 1.5 0 0 1 1.5-1.5h5A1.5 1.5 0 0 1 17 4v3"/>
                                <line x1="3" y1="14" x2="25" y2="14"/>
                                <circle cx="14" cy="14" r="1.4" fill="#1a365d"/>
                            </g>
                            <!-- Handshake / Deal -->
                            <g transform="translate(140, 10) rotate(4)">
                                <path d="M4 12l4-4a2 2 0 0 1 3 0l3 3a2 2 0 0 0 3 0l4-4"/>
                                <path d="M8 16l3 3a2 2 0 0 0 3 0l3-3"/>
                                <rect x="1" y="9" width="5" height="8" rx="1"/>
                                <rect x="18" y="5" width="5" height="8" rx="1"/>
                            </g>
                            <!-- Skyscraper / Headquarters -->
                            <g transform="translate(75, 48) rotate(-2)">
                                <rect x="4" y="3" width="14" height="24" rx="1"/>
                                <rect x="18" y="9" width="9" height="18" rx="1"/>
                                <line x1="8" y1="7" x2="10" y2="7"/><line x1="12" y1="7" x2="14" y2="7"/>
                                <line x1="8" y1="11" x2="10" y2="11"/><line x1="12" y1="11" x2="14" y2="11"/>
                                <line x1="8" y1="15" x2="10" y2="15"/><line x1="12" y1="15" x2="14" y2="15"/>
                            </g>
                            <!-- Rocket Launch / Career -->
                            <g transform="translate(205, 52) rotate(22)">
                                <path d="M12 3c4 3 7 8 7 14l-4 3-3-4-4-3 3-4c5 0 8-6 8-6z"/>
                                <circle cx="13" cy="10" r="1.6" fill="#1a365d"/>
                                <path d="M8 16l-3 4 4-3"/>
                            </g>
                            <!-- Growth Bar Chart -->
                            <g transform="translate(10, 95) rotate(-4)">
                                <line x1="3" y1="22" x2="25" y2="22"/>
                                <line x1="3" y1="3" x2="3" y2="22"/>
                                <rect x="5" y="14" width="4" height="8" rx="0.5"/>
                                <rect x="11" y="10" width="4" height="12" rx="0.5"/>
                                <rect x="17" y="5" width="4" height="17" rx="0.5"/>
                                <polyline points="6,12 13,8 18,3"/>
                            </g>
                            <!-- Target / Bullseye -->
                            <!-- Dollar/Rupee Symbol -->
                            <g transform="translate(50, 320) rotate(-4)">
                                <circle cx="18" cy="18" r="14"/>
                                <path d="M12 11h12M12 16h10M12 11c4 0 6 5 0 8l8 7"/>
                            </g>
                            <!-- Tie / Professional -->
                            <g transform="translate(330, 310) rotate(-8)">
                                <polygon points="12,4 20,4 18,8 14,8"/>
                                <polygon points="14,8 18,8 21,26 16,32 11,26"/>
                            </g>
                        </g>
                        <!-- Fillers -->
                        <text x="130" y="60" font-family="'Courier New',monospace" font-size="16" font-weight="bold" fill="#1a365d">₹</text>
                        <text x="370" y="120" font-family="'Courier New',monospace" font-size="16" font-weight="bold" fill="#1a365d">100%</text>
                        <text x="20" y="250" font-family="'Courier New',monospace" font-size="18" font-weight="bold" fill="#1a365d">★</text>
                        <text x="260" y="360" font-family="'Courier New',monospace" font-size="14" font-weight="bold" fill="#1a365d">HIRED</text>
                        <text x="380" y="240" font-family="'Courier New',monospace" font-size="16" font-weight="bold" fill="#1a365d">↑</text>
                        <!-- Dots & Connectors -->
                        <circle cx="140" cy="140" r="2" fill="#1a365d"/><circle cx="280" cy="130" r="1.5" fill="#1a365d"/><circle cx="110" cy="270" r="1.8" fill="#1a365d"/><circle cx="370" cy="40" r="2" fill="#1a365d"/>
                        <path d="M100 80h25v25" stroke="#1a365d" stroke-width="1.3" stroke-dasharray="3 3" fill="none"/>
                        <path d="M340 100v30h-25" stroke="#1a365d" stroke-width="1.3" stroke-dasharray="3 3" fill="none"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#doodle-placements)"/>
            </svg>
        </div>

        <div class="container mx-auto px-4 lg:px-10 relative z-10">
            <!-- Section Header -->
            <div class="mb-8 sm:mb-12 text-center">
                <h2 class="text-xl sm:text-3xl lg:text-4xl font-extrabold font-display uppercase tracking-wide text-[#1a365d]">
                    Our Placements
                </h2>
                <p class="text-[11px] sm:text-sm font-semibold uppercase tracking-wider text-slate-500 mt-2">
                    Trusted by Leading Organizations &amp; Industry Partners
                </p>
                <div class="w-20 h-1 bg-[#fbbf24] mt-3 rounded-full mx-auto"></div>
            </div>

            <!-- Full-Color Direct Logo Stream (Uniform Shiny Structured Cards & Dynamic Fit) -->
            <div class="marquee-container py-4" style="mask-image: linear-gradient(to right, transparent 0%, black 5%, black 95%, transparent 100%); -webkit-mask-image: linear-gradient(to right, transparent 0%, black 5%, black 95%, transparent 100%);">
                <div class="marquee-track flex items-center gap-6 sm:gap-8 lg:gap-10">
                    <!-- State Bank of India -->
                    <div class="recruiter-shiny-card w-44 sm:w-52 lg:w-56 h-24 sm:h-28 flex-shrink-0 flex items-center justify-center p-3.5 sm:p-4 group" title="State Bank of India">
                        <div class="w-full h-full flex items-center justify-center relative z-10">
                            <img src="https://cdn.freelogovectors.net/wp-content/uploads/2023/08/sbi-logo-freelogovectors.net_.png" class="max-h-full max-w-[88%] w-auto h-auto object-contain transition-transform duration-300 group-hover:scale-105" alt="State Bank of India" loading="lazy">
                        </div>
                    </div>
                    <!-- Capgemini -->
                    <div class="recruiter-shiny-card w-44 sm:w-52 lg:w-56 h-24 sm:h-28 flex-shrink-0 flex items-center justify-center p-3.5 sm:p-4 group" title="Capgemini">
                        <div class="w-full h-full flex items-center justify-center relative z-10">
                            <img src="https://www.drupal.org/files/Capgemini_Logo_2COL_RGB.png" class="max-h-full max-w-[88%] w-auto h-auto object-contain transition-transform duration-300 group-hover:scale-105" alt="Capgemini" loading="lazy">
                        </div>
                    </div>
                    <!-- Tata Consultancy Services (TCS) -->
                    <div class="recruiter-shiny-card w-44 sm:w-52 lg:w-56 h-24 sm:h-28 flex-shrink-0 flex items-center justify-center p-3.5 sm:p-4 group" title="Tata Consultancy Services">
                        <div class="w-full h-full flex items-center justify-center relative z-10">
                            <img src="https://ibsintelligence.com/wp-content/uploads/2021/09/TCS.jpg" class="max-h-full max-w-[88%] w-auto h-auto object-contain transition-transform duration-300 group-hover:scale-105 rounded-md" alt="Tata Consultancy Services" loading="lazy">
                        </div>
                    </div>
                    <!-- Federal Bank -->
                    <div class="recruiter-shiny-card w-44 sm:w-52 lg:w-56 h-24 sm:h-28 flex-shrink-0 flex items-center justify-center p-3.5 sm:p-4 group" title="Federal Bank">
                        <div class="w-full h-full flex items-center justify-center relative z-10">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/8/86/Federal_Bank_Logo.jpg" class="max-h-full max-w-[88%] w-auto h-auto object-contain transition-transform duration-300 group-hover:scale-105 rounded-md" alt="Federal Bank" loading="lazy">
                        </div>
                    </div>
                    <!-- Wipro -->
                    <div class="recruiter-shiny-card w-44 sm:w-52 lg:w-56 h-24 sm:h-28 flex-shrink-0 flex items-center justify-center p-3.5 sm:p-4 group" title="Wipro">
                        <div class="w-full h-full flex items-center justify-center relative z-10">
                            <img src="https://s32519.pcdn.co/wp-content/uploads/2023/10/partner-wipro-512px.png" class="max-h-full max-w-[88%] w-auto h-auto object-contain transition-transform duration-300 group-hover:scale-105" alt="Wipro" loading="lazy">
                        </div>
                    </div>
                    <!-- Gratia Technology -->
                    <div class="recruiter-shiny-card w-44 sm:w-52 lg:w-56 h-24 sm:h-28 flex-shrink-0 flex items-center justify-center p-3.5 sm:p-4 group" title="Gratia Technology">
                        <div class="w-full h-full flex items-center justify-center relative z-10">
                            <img src="https://media.licdn.com/dms/image/v2/C560BAQHqBGJNYwYrQw/company-logo_200_200/company-logo_200_200/0/1670300070799/gratia_technology_private_limited_guwahati_logo?e=2147483647&v=beta&t=YosTHoHISNlfKqiJxpVP_kZdD76BOBskXRY5d0qrM8E" class="max-h-full max-w-[88%] w-auto h-auto object-contain transition-transform duration-300 group-hover:scale-105 rounded-lg" alt="Gratia Technology" loading="lazy">
                        </div>
                    </div>
                    <!-- IBM -->
                    <div class="recruiter-shiny-card w-44 sm:w-52 lg:w-56 h-24 sm:h-28 flex-shrink-0 flex items-center justify-center p-3.5 sm:p-4 group" title="IBM">
                        <div class="w-full h-full flex items-center justify-center relative z-10">
                            <img src="https://www.logo.wine/a/logo/IBM/IBM-Logo.wine.svg" class="max-h-full max-w-[88%] w-auto h-auto object-contain transition-transform duration-300 group-hover:scale-105" alt="IBM" loading="lazy">
                        </div>
                    </div>
                    <!-- Infosys -->
                    <div class="recruiter-shiny-card w-44 sm:w-52 lg:w-56 h-24 sm:h-28 flex-shrink-0 flex items-center justify-center p-3.5 sm:p-4 group" title="Infosys">
                        <div class="w-full h-full flex items-center justify-center relative z-10">
                            <img src="https://content.linkedin.com/content/dam/me/business/en-us/sales-solutions/resources/images/apac/images/infosys-logo.png.original.png" class="max-h-full max-w-[88%] w-auto h-auto object-contain transition-transform duration-300 group-hover:scale-105" alt="Infosys" loading="lazy">
                        </div>
                    </div>
                </div>

                <!-- Duplicate track for seamless infinite loop -->
                <div class="marquee-track flex items-center gap-6 sm:gap-8 lg:gap-10" aria-hidden="true">
                    <div class="recruiter-shiny-card w-44 sm:w-52 lg:w-56 h-24 sm:h-28 flex-shrink-0 flex items-center justify-center p-3.5 sm:p-4 group" title="State Bank of India">
                        <div class="w-full h-full flex items-center justify-center relative z-10">
                            <img src="https://cdn.freelogovectors.net/wp-content/uploads/2023/08/sbi-logo-freelogovectors.net_.png" class="max-h-full max-w-[88%] w-auto h-auto object-contain" alt="State Bank of India" loading="lazy">
                        </div>
                    </div>
                    <div class="recruiter-shiny-card w-44 sm:w-52 lg:w-56 h-24 sm:h-28 flex-shrink-0 flex items-center justify-center p-3.5 sm:p-4 group" title="Capgemini">
                        <div class="w-full h-full flex items-center justify-center relative z-10">
                            <img src="https://www.drupal.org/files/Capgemini_Logo_2COL_RGB.png" class="max-h-full max-w-[88%] w-auto h-auto object-contain" alt="Capgemini" loading="lazy">
                        </div>
                    </div>
                    <div class="recruiter-shiny-card w-44 sm:w-52 lg:w-56 h-24 sm:h-28 flex-shrink-0 flex items-center justify-center p-3.5 sm:p-4 group" title="Tata Consultancy Services">
                        <div class="w-full h-full flex items-center justify-center relative z-10">
                            <img src="https://ibsintelligence.com/wp-content/uploads/2021/09/TCS.jpg" class="max-h-full max-w-[88%] w-auto h-auto object-contain rounded-md" alt="Tata Consultancy Services" loading="lazy">
                        </div>
                    </div>
                    <div class="recruiter-shiny-card w-44 sm:w-52 lg:w-56 h-24 sm:h-28 flex-shrink-0 flex items-center justify-center p-3.5 sm:p-4 group" title="Federal Bank">
                        <div class="w-full h-full flex items-center justify-center relative z-10">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/8/86/Federal_Bank_Logo.jpg" class="max-h-full max-w-[88%] w-auto h-auto object-contain rounded-md" alt="Federal Bank" loading="lazy">
                        </div>
                    </div>
                    <div class="recruiter-shiny-card w-44 sm:w-52 lg:w-56 h-24 sm:h-28 flex-shrink-0 flex items-center justify-center p-3.5 sm:p-4 group" title="Wipro">
                        <div class="w-full h-full flex items-center justify-center relative z-10">
                            <img src="https://s32519.pcdn.co/wp-content/uploads/2023/10/partner-wipro-512px.png" class="max-h-full max-w-[88%] w-auto h-auto object-contain" alt="Wipro" loading="lazy">
                        </div>
                    </div>
                    <div class="recruiter-shiny-card w-44 sm:w-52 lg:w-56 h-24 sm:h-28 flex-shrink-0 flex items-center justify-center p-3.5 sm:p-4 group" title="Gratia Technology">
                        <div class="w-full h-full flex items-center justify-center relative z-10">
                            <img src="https://media.licdn.com/dms/image/v2/C560BAQHqBGJNYwYrQw/company-logo_200_200/company-logo_200_200/0/1670300070799/gratia_technology_private_limited_guwahati_logo?e=2147483647&v=beta&t=YosTHoHISNlfKqiJxpVP_kZdD76BOBskXRY5d0qrM8E" class="max-h-full max-w-[88%] w-auto h-auto object-contain rounded-lg" alt="Gratia Technology" loading="lazy">
                        </div>
                    </div>
                    <div class="recruiter-shiny-card w-44 sm:w-52 lg:w-56 h-24 sm:h-28 flex-shrink-0 flex items-center justify-center p-3.5 sm:p-4 group" title="IBM">
                        <div class="w-full h-full flex items-center justify-center relative z-10">
                            <img src="https://www.logo.wine/a/logo/IBM/IBM-Logo.wine.svg" class="max-h-full max-w-[88%] w-auto h-auto object-contain" alt="IBM" loading="lazy">
                        </div>
                    </div>
                    <div class="recruiter-shiny-card w-44 sm:w-52 lg:w-56 h-24 sm:h-28 flex-shrink-0 flex items-center justify-center p-3.5 sm:p-4 group" title="Infosys">
                        <div class="w-full h-full flex items-center justify-center relative z-10">
                            <img src="https://content.linkedin.com/content/dam/me/business/en-us/sales-solutions/resources/images/apac/images/infosys-logo.png.original.png" class="max-h-full max-w-[88%] w-auto h-auto object-contain" alt="Infosys" loading="lazy">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Achievements Section (Structured, Clear & Impressive) -->
    <section id="achievements" class="py-14 sm:py-16 bg-slate-50/70 border-t border-slate-200/70 relative">
        <div class="absolute inset-0 pointer-events-none opacity-[0.08] select-none">
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="doodle-achievements" width="260" height="260" patternUnits="userSpaceOnUse" patternTransform="rotate(1)">
                        <g stroke="#1a365d" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <!-- Medal -->
                            <g transform="translate(10, 8) rotate(-5)">
                                <circle cx="14" cy="16" r="5.5" />
                                <path d="M10 12L7 4L11 4L14 11M18 12L21 4L17 4L14 11" />
                                <circle cx="14" cy="16" r="2.5" fill="#1a365d" fill-opacity="0.2" />
                            </g>
                            <!-- Trophy Cup -->
                            <g transform="translate(140, 10) rotate(4)">
                                <path d="M8 4H19V8C19 11 16.5 13.5 13.5 13.5C10.5 13.5 8 11 8 8V4Z" />
                                <path d="M8 5H5C3.5 5 2.5 6.2 2.5 7.5C2.5 8.8 3.5 10 5 10H8M19 5H22C23.5 5 24.5 6.2 24.5 7.5C24.5 8.8 23.5 10 22 10H19" />
                                <path d="M13.5 13.5V19M9 19H18" />
                            </g>
                            <!-- Star -->
                            <g transform="translate(75, 48) rotate(-3)">
                                <path d="M12 2L15 8.5L22 9.5L17 14.5L18.5 21.5L12 18L5.5 21.5L7 14.5L2 9.5L9 8.5L12 2Z" />
                            </g>
                            <!-- Laurel Wreath -->
                            <g transform="translate(205, 52) rotate(5)">
                                <path d="M8 18C5 14 5 9 8 5C9 7.5 12 9 12 9C12 9 9 10.5 9 13C9 15.5 10 17 10 17" />
                                <path d="M18 18C21 14 21 9 18 5C17 7.5 14 9 14 9C14 9 17 10.5 17 13C17 15.5 16 17 16 17" />
                                <circle cx="13" cy="19" r="1.3" fill="#1a365d" />
                            </g>
                            <!-- Certificate -->
                            <g transform="translate(10, 95) rotate(-4)">
                                <rect x="4" y="5" width="19" height="14" rx="1.5" />
                                <circle cx="19" cy="15" r="2.8" />
                                <path d="M17 17L16 21L19 19.5L22 21L21 17" />
                                <path d="M7 8H14M7 11H15M7 14H12" />
                            </g>
                            <!-- Crown -->
                            <g transform="translate(145, 98) rotate(4)">
                                <path d="M4 16L5 7L9 11L13 5L17 11L21 7L22 16H4Z" />
                                <path d="M4 19H22" />
                                <circle cx="5" cy="5.5" r="1" fill="#1a365d" /> <circle cx="13" cy="4" r="1" fill="#1a365d" /> <circle cx="21" cy="5.5" r="1" fill="#1a365d" />
                            </g>
                            <!-- Thumbs up -->
                            <g transform="translate(70, 150) rotate(-5)">
                                <path d="M9 9V17H15C16.5 17 17.5 16 17.5 14.5V12L15 9H9Z" />
                                <path d="M9 9C9 7 12 6.5 12 4C12 2.5 10.5 2.5 10.5 4V9" />
                                <rect x="5" y="9" width="4" height="8" rx="0.5" />
                            </g>
                            <!-- Podium -->
                            <g transform="translate(195, 155) rotate(2)">
                                <path d="M9 10H17V21H9V10Z" />
                                <path d="M4 14H9V21H4V14Z" />
                                <path d="M17 16H22V21H17V16Z" />
                                <text x="12" y="16" font-family="sans-serif" font-size="6" fill="#1a365d" stroke="none">1</text>
                            </g>
                            <!-- Winner Ribbon -->
                            <g transform="translate(12, 195) rotate(-3)">
                                <circle cx="12" cy="8" r="6"/>
                                <path d="M8 13L6 21L12 18L18 21L16 13"/>
                                <text x="10" y="11" font-family="sans-serif" font-size="7" font-weight="bold" fill="#1a365d" stroke="none">★</text>
                            </g>
                            <!-- Sparkle / Diamond -->
                            <g transform="translate(135, 200) rotate(5)">
                                <path d="M12 2L15 8L21 11L15 14L12 20L9 14L3 11L9 8Z"/>
                            </g>
                        </g>
                        <!-- Fillers -->
                        <text x="68" y="28" font-family="'Courier New',monospace" font-size="13" font-weight="900" fill="#1a365d">#1</text>
                        <text x="195" y="30" font-family="'Courier New',monospace" font-size="15" font-weight="900" fill="#1a365d">★</text>
                        <text x="12" y="72" font-family="'Courier New',monospace" font-size="11" font-weight="bold" fill="#1a365d">100%</text>
                        <text x="125" y="75" font-family="'Courier New',monospace" font-size="13" font-weight="900" fill="#1a365d">🏆</text>
                        <text x="75" y="125" font-family="'Courier New',monospace" font-size="13" font-weight="bold" fill="#1a365d">RANK 1</text>
                        <text x="210" y="130" font-family="'Courier New',monospace" font-size="14" font-weight="900" fill="#1a365d">⬆</text>
                        <text x="128" y="175" font-family="'Courier New',monospace" font-size="12" font-weight="bold" fill="#1a365d">GOLD</text>
                        <text x="15" y="172" font-family="'Courier New',monospace" font-size="11" font-weight="bold" fill="#1a365d">TOP</text>
                        <text x="68" y="230" font-family="'Courier New',monospace" font-size="11" font-weight="900" fill="#1a365d">A++</text>
                        <text x="195" y="235" font-family="'Courier New',monospace" font-size="11" font-weight="bold" fill="#1a365d">NAAC</text>
                        <!-- Dots & Connectors -->
                        <circle cx="50" cy="50" r="1.3" fill="#1a365d"/><circle cx="115" cy="105" r="1.6" fill="#1a365d"/><circle cx="175" cy="85" r="1.3" fill="#1a365d"/>
                        <circle cx="58" cy="188" r="1.5" fill="#1a365d"/><circle cx="180" cy="198" r="1.3" fill="#1a365d"/><circle cx="245" cy="98" r="1.5" fill="#1a365d"/>
                        <path d="M50 50h10v8" stroke="#1a365d" stroke-width="1" stroke-dasharray="2 2" fill="none"/>
                        <path d="M175 85v10h10" stroke="#1a365d" stroke-width="1" stroke-dasharray="2 2" fill="none"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#doodle-achievements)"/>
            </svg>
        </div>
        <div class="container mx-auto px-4 lg:px-10 relative z-10">
            <!-- Section Header -->
            <div class="mb-8 sm:mb-10 text-center">
                <h2 class="text-xl sm:text-3xl lg:text-4xl font-extrabold font-display uppercase tracking-wide text-[#1a365d]">
                    Our Achievements
                </h2>
                <p class="text-[11px] sm:text-sm font-semibold uppercase tracking-wider text-slate-500 mt-2">
                    Excellence in Research, Academic Rigor &amp; Industry Impact
                </p>
                <div class="w-20 h-1 bg-[#fbbf24] mt-3 rounded-full mx-auto"></div>
            </div>

            <!-- Key Metric Counter Band (Shiny Dark Grey Cards) -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mb-10">
                <!-- 1976 Legacy -->
                <div class="relative overflow-hidden bg-gradient-to-b from-[#333842] via-[#21242b] to-[#15171c] p-5 sm:p-6 rounded-2xl border border-slate-500/40 shadow-[0_10px_25px_-5px_rgba(0,0,0,0.45),inset_0_1px_1px_rgba(255,255,255,0.25)] text-center hover:scale-[1.02] hover:border-slate-400/60 hover:shadow-[0_15px_30px_-5px_rgba(0,0,0,0.6),inset_0_1px_2px_rgba(255,255,255,0.35)] transition-all duration-300 group">
                    <div class="absolute inset-x-0 top-0 h-1/2 bg-gradient-to-b from-white/[0.09] to-transparent pointer-events-none rounded-t-2xl"></div>
                    <p class="text-3xl sm:text-4xl font-black font-display text-[#fbbf24] drop-shadow-[0_2px_10px_rgba(251,191,36,0.35)] relative z-10">1976</p>
                    <p class="text-xs sm:text-sm font-semibold text-slate-200 mt-1.5 tracking-wide relative z-10">Legacy of CS Education</p>
                </div>

                <!-- 50+ Research Publications -->
                <div class="relative overflow-hidden bg-gradient-to-b from-[#333842] via-[#21242b] to-[#15171c] p-5 sm:p-6 rounded-2xl border border-slate-500/40 shadow-[0_10px_25px_-5px_rgba(0,0,0,0.45),inset_0_1px_1px_rgba(255,255,255,0.25)] text-center hover:scale-[1.02] hover:border-slate-400/60 hover:shadow-[0_15px_30px_-5px_rgba(0,0,0,0.6),inset_0_1px_2px_rgba(255,255,255,0.35)] transition-all duration-300 group">
                    <div class="absolute inset-x-0 top-0 h-1/2 bg-gradient-to-b from-white/[0.09] to-transparent pointer-events-none rounded-t-2xl"></div>
                    <p class="text-3xl sm:text-4xl font-black font-display text-[#38bdf8] drop-shadow-[0_2px_10px_rgba(56,189,248,0.35)] relative z-10">50+</p>
                    <p class="text-xs sm:text-sm font-semibold text-slate-200 mt-1.5 tracking-wide relative z-10">Research Publications</p>
                </div>

                <!-- 5+ University Gold Medalists -->
                <div class="relative overflow-hidden bg-gradient-to-b from-[#333842] via-[#21242b] to-[#15171c] p-5 sm:p-6 rounded-2xl border border-slate-500/40 shadow-[0_10px_25px_-5px_rgba(0,0,0,0.45),inset_0_1px_1px_rgba(255,255,255,0.25)] text-center hover:scale-[1.02] hover:border-slate-400/60 hover:shadow-[0_15px_30px_-5px_rgba(0,0,0,0.6),inset_0_1px_2px_rgba(255,255,255,0.35)] transition-all duration-300 group">
                    <div class="absolute inset-x-0 top-0 h-1/2 bg-gradient-to-b from-white/[0.09] to-transparent pointer-events-none rounded-t-2xl"></div>
                    <p class="text-3xl sm:text-4xl font-black font-display text-[#fbbf24] drop-shadow-[0_2px_10px_rgba(251,191,36,0.35)] relative z-10">5+</p>
                    <p class="text-xs sm:text-sm font-semibold text-slate-200 mt-1.5 tracking-wide relative z-10">University Gold Medalists</p>
                </div>

                <!-- 1000+ Alumni Network -->
                <div class="relative overflow-hidden bg-gradient-to-b from-[#333842] via-[#21242b] to-[#15171c] p-5 sm:p-6 rounded-2xl border border-slate-500/40 shadow-[0_10px_25px_-5px_rgba(0,0,0,0.45),inset_0_1px_1px_rgba(255,255,255,0.25)] text-center hover:scale-[1.02] hover:border-slate-400/60 hover:shadow-[0_15px_30px_-5px_rgba(0,0,0,0.6),inset_0_1px_2px_rgba(255,255,255,0.35)] transition-all duration-300 group">
                    <div class="absolute inset-x-0 top-0 h-1/2 bg-gradient-to-b from-white/[0.09] to-transparent pointer-events-none rounded-t-2xl"></div>
                    <p class="text-3xl sm:text-4xl font-black font-display text-[#34d399] drop-shadow-[0_2px_10px_rgba(52,211,153,0.35)] relative z-10">1000+</p>
                    <p class="text-xs sm:text-sm font-semibold text-slate-200 mt-1.5 tracking-wide relative z-10">Alumni Network</p>
                </div>
            </div>

            <!-- 3 Structured Pillar Cards -->
            <div class="grid md:grid-cols-3 gap-6 lg:gap-8">
                <!-- Research Excellence -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/90 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#1a365d] flex items-center justify-center text-xl">
                                <i class="fas fa-flask"></i>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full">
                                Research
                            </span>
                        </div>

                        <h3 class="text-lg font-bold font-display text-[#1a365d] mb-3">
                            Research &amp; Publications
                        </h3>

                        <ul class="text-slate-600 text-xs sm:text-sm space-y-2.5">
                            <li class="flex items-start gap-2.5">
                                <i class="fas fa-check-circle text-emerald-600 mt-0.5 flex-shrink-0"></i>
                                <span>Over 50+ research papers published in prestigious IEEE, Springer &amp; ACM indexed journals.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fas fa-check-circle text-emerald-600 mt-0.5 flex-shrink-0"></i>
                                <span>Active research grants in Machine Learning, NLP, Generative AI, and Cybersecurity.</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Academic Excellence -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/90 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full">
                                Academic
                            </span>
                        </div>

                        <h3 class="text-lg font-bold font-display text-[#1a365d] mb-3">
                            Academic Rigor &amp; Medals
                        </h3>

                        <ul class="text-slate-600 text-xs sm:text-sm space-y-2.5">
                            <li class="flex items-start gap-2.5">
                                <i class="fas fa-check-circle text-emerald-600 mt-0.5 flex-shrink-0"></i>
                                <span>Consistent university rank holders with multiple Gold Medalists in BCA &amp; MCA programmes.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fas fa-check-circle text-emerald-600 mt-0.5 flex-shrink-0"></i>
                                <span>Curriculum aligned with National Education Policy (NEP) and industry-relevant competencies.</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Industry Linkages -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/90 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wider text-purple-700 bg-purple-50 px-2.5 py-1 rounded-full">
                                Placement
                            </span>
                        </div>

                        <h3 class="text-lg font-bold font-display text-[#1a365d] mb-3">
                            Industry Linkages &amp; Placements
                        </h3>

                        <ul class="text-slate-600 text-xs sm:text-sm space-y-2.5">
                            <li class="flex items-start gap-2.5">
                                <i class="fas fa-check-circle text-emerald-600 mt-0.5 flex-shrink-0"></i>
                                <span>Active campus recruitment drives by TCS, Wipro, Capgemini, State Bank of India, and tech firms.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fas fa-check-circle text-emerald-600 mt-0.5 flex-shrink-0"></i>
                                <span>Frequent national coding competitions, hackathons, and technical symposia for students.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section (Google Material Dark Grey Style) -->
    <section id="contact" class="py-14 sm:py-16 bg-[#202124] border-t border-[#3c4043] text-[#e8eaed] relative">
        <div class="container px-4 lg:px-10 mx-auto relative z-10">
            <!-- Section Header -->
            <div class="mb-8 sm:mb-10 text-center">
                <h2 class="text-xl sm:text-3xl lg:text-4xl font-extrabold font-display tracking-tight text-[#e8eaed]">
                    Contact Us
                </h2>
                <p class="text-[11px] sm:text-sm font-semibold uppercase tracking-wider text-[#9aa0a6] mt-2">
                    Connect with the Centre for Computer Science and Applications, Dibrugarh University
                </p>
                <div class="w-20 h-1 bg-[#fbbf24] mt-3 rounded-full mx-auto"></div>
            </div>

            <div class="grid md:grid-cols-2 gap-8 items-start">
                <!-- Left Column: Contact Cards & Interactive Map -->
                <div class="bg-[#2d2f31] p-6 sm:p-8 rounded-2xl border border-[#3c4043] shadow-md space-y-6">
                    <div class="space-y-5">
                        <!-- Address -->
                        <div class="flex items-start gap-4 group">
                            <div class="w-11 h-11 rounded-full bg-[#1e2838] text-[#8ab4f8] border border-[#3c4043] flex items-center justify-center text-base flex-shrink-0 shadow-inner">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-xs uppercase tracking-wider text-[#9aa0a6] mb-1">Campus Address</h3>
                                <p class="text-sm font-semibold text-[#e8eaed] leading-snug">
                                    Centre for Computer Science and Applications (CCSA)
                                </p>
                                <p class="text-xs text-[#9aa0a6] mt-0.5 leading-relaxed">
                                    Dibrugarh University, NH-37, Rajabheta, Dibrugarh - 786004, Assam, India
                                </p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="flex items-start gap-4 group">
                            <div class="w-11 h-11 rounded-full bg-[#372b15] text-[#fdd663] border border-[#3c4043] flex items-center justify-center text-base flex-shrink-0 shadow-inner">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-xs uppercase tracking-wider text-[#9aa0a6] mb-1">Official Email</h3>
                                <a href="mailto:ccsduoffice@gmail.com" class="text-sm font-semibold text-[#8ab4f8] hover:underline">
                                    ccsduoffice@gmail.com
                                </a>
                                <p class="text-xs text-[#9aa0a6] mt-0.5">Office Hours: Mon – Fri, 9:30 AM – 5:00 PM</p>
                            </div>
                        </div>
                    </div>

                    <!-- Map -->
                    <div class="rounded-xl overflow-hidden border border-[#3c4043] shadow-inner h-52 sm:h-60">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1770.3297589595813!2d94.89320993864749!3d27.448719944082946!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3740a2b2ddddd7f1%3A0x89121528a05cb44a!2sCentre%20for%20Computer%20Science%20and%20Applications!5e0!3m2!1sen!2sin!4v1749018895880!5m2!1sen!2sin"
                            class="w-full h-full border-0" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>

                <!-- Right Column: Contact Form -->
                <div class="bg-[#2d2f31] p-6 sm:p-8 rounded-2xl border border-[#3c4043] shadow-md">
                    <h3 class="text-lg font-bold font-display text-[#e8eaed] mb-1">Send a Message</h3>
                    <p class="text-xs text-[#9aa0a6] mb-6">Have an inquiry regarding admissions, syllabus, or research? Drop us a note.</p>

                    <form id="contactForm" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?php echo Security::escape($csrfToken); ?>">
                        <div>
                            <label class="block mb-1.5 text-[#e8eaed] font-semibold text-xs" for="contact-name">Your Full Name</label>
                            <input type="text" id="contact-name" name="name"
                                class="w-full px-4 py-3 rounded-xl bg-[#202124] border border-[#3c4043] text-[#e8eaed] placeholder-[#9aa0a6] text-sm focus:bg-[#1a1a1c] focus:border-[#8ab4f8] focus:ring-2 focus:ring-[#8ab4f8]/20 focus:outline-none transition-all"
                                placeholder="Your Name" required>
                            <div class="field-error text-red-400 text-xs mt-1 hidden"></div>
                        </div>

                        <div>
                            <label class="block mb-1.5 text-[#e8eaed] font-semibold text-xs" for="contact-email">Email Address</label>
                            <input type="email" id="contact-email" name="email"
                                class="w-full px-4 py-3 rounded-xl bg-[#202124] border border-[#3c4043] text-[#e8eaed] placeholder-[#9aa0a6] text-sm focus:bg-[#1a1a1c] focus:border-[#8ab4f8] focus:ring-2 focus:ring-[#8ab4f8]/20 focus:outline-none transition-all"
                                placeholder="e.g. you@example.com" required>
                            <div class="field-error text-red-400 text-xs mt-1 hidden"></div>
                        </div>

                        <div>
                            <label class="block mb-1.5 text-[#e8eaed] font-semibold text-xs" for="contact-message">Your Message</label>
                            <textarea id="contact-message" name="message"
                                class="w-full px-4 py-3 rounded-xl bg-[#202124] border border-[#3c4043] text-[#e8eaed] placeholder-[#9aa0a6] text-sm focus:bg-[#1a1a1c] focus:border-[#8ab4f8] focus:ring-2 focus:ring-[#8ab4f8]/20 focus:outline-none transition-all"
                                rows="4" placeholder="Write your message here..." required></textarea>
                            <div class="field-error text-red-400 text-xs mt-1 hidden"></div>
                        </div>

                        <button type="submit" id="submitBtn"
                            class="w-full bg-[#8ab4f8] hover:bg-[#a8c7fa] text-[#202124] py-3.5 rounded-xl font-extrabold transition-colors shadow-md flex items-center justify-center gap-2 text-sm cursor-pointer">
                            <span>Send Message</span>
                            <i class="fas fa-paper-plane text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const vid = document.getElementById('heroDepartmentVideo');
        if (vid) {
            vid.playbackRate = 0.7;
            vid.addEventListener('play', () => { vid.playbackRate = 0.7; });
            vid.addEventListener('canplay', () => { vid.playbackRate = 0.7; });
            vid.addEventListener('seeked', () => { vid.playbackRate = 0.7; });
        }
    });
    </script>

<?php
$extraScripts = '<script src="assets/js/notifications.js?v=' . time() . '"></script><script src="assets/js/contact.js?v=' . time() . '"></script>';
include 'templates/footer.php';
?>
