<?php
declare(strict_types=1);

/**
 * Research Areas Page
 * Centre for Computer Science and Applications (CCSA), Dibrugarh University
 */

$pageTitle = 'Research Areas - Centre for Computer Science and Applications';
$currentPage = 'research';
include 'templates/header.php';
?>

<main class="min-h-screen bg-slate-50/70 py-12 sm:py-16 relative overflow-hidden">
    <!-- 🧠 AI & Computing Research SVG Doodle Pattern Background -->
    <div class="absolute inset-0 pointer-events-none opacity-[0.06] select-none">
        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="doodle-research-page" width="260" height="260" patternUnits="userSpaceOnUse" patternTransform="rotate(1)">
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
            <rect width="100%" height="100%" fill="url(#doodle-research-page)"/>
        </svg>
    </div>

    <div class="container mx-auto px-4 lg:px-10 relative z-10">
        <!-- Hero Header -->
        <div class="mb-10 text-center">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold font-display uppercase tracking-wide text-[#1a365d]">
                Research Areas
            </h1>
            <p class="text-xs sm:text-sm font-semibold uppercase tracking-wider text-slate-500 mt-2">
                Frontier Computing Research &amp; Scholarly Innovation at Dibrugarh University
            </p>
            <div class="w-20 h-1 bg-[#fbbf24] mt-3 rounded-full mx-auto"></div>
        </div>

        <!-- Research Stats Counter Strip -->
        <div class="bg-gradient-to-r from-[#1a365d] via-[#1e40af] to-[#2563eb] text-white rounded-2xl p-6 sm:p-8 mb-12 grid grid-cols-2 md:grid-cols-4 gap-6 shadow-md border border-blue-800/50">
            <div class="text-center">
                <h3 class="text-3xl sm:text-4xl font-black font-display text-[#fbbf24]">25+</h3>
                <p class="text-xs sm:text-sm font-medium text-blue-100 mt-1">Funded Projects</p>
            </div>
            <div class="text-center">
                <h3 class="text-3xl sm:text-4xl font-black font-display text-[#fbbf24]">100+</h3>
                <p class="text-xs sm:text-sm font-medium text-blue-100 mt-1">Indexed Publications</p>
            </div>
            <div class="text-center">
                <h3 class="text-3xl sm:text-4xl font-black font-display text-[#fbbf24]">15+</h3>
                <p class="text-xs sm:text-sm font-medium text-blue-100 mt-1">Research Partners</p>
            </div>
            <div class="text-center">
                <h3 class="text-3xl sm:text-4xl font-black font-display text-[#fbbf24]">30+</h3>
                <p class="text-xs sm:text-sm font-medium text-blue-100 mt-1">Doctoral Scholars</p>
            </div>
        </div>

        <!-- Research Areas Graphic Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 mb-14">
            <!-- 1. AI & ML -->
            <div class="bg-white rounded-2xl overflow-hidden border border-slate-200/90 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <!-- Graphic Header Banner -->
                    <div class="relative h-44 w-full overflow-hidden bg-slate-900">
                        <img src="https://images.unsplash.com/photo-1620712943543-bcc4688e7485?auto=format&fit=crop&w=600&q=60" 
                            alt="Artificial Intelligence & Machine Learning" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-85"
                            loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>
                        <div class="absolute bottom-3 left-4 right-4 flex items-center justify-between text-white">
                            <div class="flex items-center gap-2.5">
                                <span class="w-9 h-9 rounded-lg bg-amber-400 text-slate-950 flex items-center justify-center text-sm font-black shadow-md">
                                    <i class="fas fa-brain"></i>
                                </span>
                                <h3 class="text-lg font-bold font-display text-white tracking-wide leading-tight">
                                    AI &amp; Machine Learning
                                </h3>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6">
                        <p class="text-slate-600 text-xs sm:text-sm leading-relaxed mb-4">
                            Advanced research in artificial intelligence, deep learning architectures, cognitive computing, and neural systems.
                        </p>
                        <ul class="space-y-2.5 text-xs sm:text-sm text-slate-700">
                            <li class="flex items-center gap-2 font-medium">
                                <i class="fas fa-check-circle text-amber-500 text-xs flex-shrink-0"></i>
                                <span>Neural Networks &amp; Deep Learning</span>
                            </li>
                            <li class="flex items-center gap-2 font-medium">
                                <i class="fas fa-check-circle text-amber-500 text-xs flex-shrink-0"></i>
                                <span>Natural Language Processing (NLP)</span>
                            </li>
                            <li class="flex items-center gap-2 font-medium">
                                <i class="fas fa-check-circle text-amber-500 text-xs flex-shrink-0"></i>
                                <span>Computer Vision &amp; Image Processing</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 2. Data Science -->
            <div class="bg-white rounded-2xl overflow-hidden border border-slate-200/90 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <!-- Graphic Header Banner -->
                    <div class="relative h-44 w-full overflow-hidden bg-slate-900">
                        <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=600&q=60" 
                            alt="Data Science & Big Data" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-85"
                            loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>
                        <div class="absolute bottom-3 left-4 right-4 flex items-center justify-between text-white">
                            <div class="flex items-center gap-2.5">
                                <span class="w-9 h-9 rounded-lg bg-amber-400 text-slate-950 flex items-center justify-center text-sm font-black shadow-md">
                                    <i class="fas fa-database"></i>
                                </span>
                                <h3 class="text-lg font-bold font-display text-white tracking-wide leading-tight">
                                    Data Science &amp; Analytics
                                </h3>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6">
                        <p class="text-slate-600 text-xs sm:text-sm leading-relaxed mb-4">
                            Investigating big data mining algorithms, statistical modeling, data visualization, and predictive decision frameworks.
                        </p>
                        <ul class="space-y-2.5 text-xs sm:text-sm text-slate-700">
                            <li class="flex items-center gap-2 font-medium">
                                <i class="fas fa-check-circle text-amber-500 text-xs flex-shrink-0"></i>
                                <span>Big Data Mining &amp; Analytics</span>
                            </li>
                            <li class="flex items-center gap-2 font-medium">
                                <i class="fas fa-check-circle text-amber-500 text-xs flex-shrink-0"></i>
                                <span>Predictive Statistical Modeling</span>
                            </li>
                            <li class="flex items-center gap-2 font-medium">
                                <i class="fas fa-check-circle text-amber-500 text-xs flex-shrink-0"></i>
                                <span>Interactive Data Visualization</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 3. Cybersecurity -->
            <div class="bg-white rounded-2xl overflow-hidden border border-slate-200/90 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <!-- Graphic Header Banner -->
                    <div class="relative h-44 w-full overflow-hidden bg-slate-900">
                        <img src="https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=600&q=60" 
                            alt="Cybersecurity & Network Defense" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-85"
                            loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>
                        <div class="absolute bottom-3 left-4 right-4 flex items-center justify-between text-white">
                            <div class="flex items-center gap-2.5">
                                <span class="w-9 h-9 rounded-lg bg-amber-400 text-slate-950 flex items-center justify-center text-sm font-black shadow-md">
                                    <i class="fas fa-shield-alt"></i>
                                </span>
                                <h3 class="text-lg font-bold font-display text-white tracking-wide leading-tight">
                                    Cybersecurity &amp; Cryptography
                                </h3>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6">
                        <p class="text-slate-600 text-xs sm:text-sm leading-relaxed mb-4">
                            Exploring robust network security protocols, cryptographic primitives, threat detection models, and privacy preservation.
                        </p>
                        <ul class="space-y-2.5 text-xs sm:text-sm text-slate-700">
                            <li class="flex items-center gap-2 font-medium">
                                <i class="fas fa-check-circle text-amber-500 text-xs flex-shrink-0"></i>
                                <span>Network Security &amp; Firewalls</span>
                            </li>
                            <li class="flex items-center gap-2 font-medium">
                                <i class="fas fa-check-circle text-amber-500 text-xs flex-shrink-0"></i>
                                <span>Modern Applied Cryptography</span>
                            </li>
                            <li class="flex items-center gap-2 font-medium">
                                <i class="fas fa-check-circle text-amber-500 text-xs flex-shrink-0"></i>
                                <span>Intrusion &amp; Cyber Threat Detection</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 4. Cloud Computing -->
            <div class="bg-white rounded-2xl overflow-hidden border border-slate-200/90 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <!-- Graphic Header Banner -->
                    <div class="relative h-44 w-full overflow-hidden bg-slate-900">
                        <img src="https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=600&q=60" 
                            alt="Cloud Computing & Distributed Systems" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-85"
                            loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>
                        <div class="absolute bottom-3 left-4 right-4 flex items-center justify-between text-white">
                            <div class="flex items-center gap-2.5">
                                <span class="w-9 h-9 rounded-lg bg-amber-400 text-slate-950 flex items-center justify-center text-sm font-black shadow-md">
                                    <i class="fas fa-cloud"></i>
                                </span>
                                <h3 class="text-lg font-bold font-display text-white tracking-wide leading-tight">
                                    Cloud &amp; Distributed Systems
                                </h3>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6">
                        <p class="text-slate-600 text-xs sm:text-sm leading-relaxed mb-4">
                            Investigating elastic cloud architectures, edge-fog computing nodes, distributed consensus, and microservices reliability.
                        </p>
                        <ul class="space-y-2.5 text-xs sm:text-sm text-slate-700">
                            <li class="flex items-center gap-2 font-medium">
                                <i class="fas fa-check-circle text-amber-500 text-xs flex-shrink-0"></i>
                                <span>Scalable Cloud Architecture</span>
                            </li>
                            <li class="flex items-center gap-2 font-medium">
                                <i class="fas fa-check-circle text-amber-500 text-xs flex-shrink-0"></i>
                                <span>Distributed Systems &amp; Consensus</span>
                            </li>
                            <li class="flex items-center gap-2 font-medium">
                                <i class="fas fa-check-circle text-amber-500 text-xs flex-shrink-0"></i>
                                <span>Cloud Security &amp; Virtualization</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl overflow-hidden border border-slate-200/90 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="relative h-44 w-full overflow-hidden bg-slate-900">
                        <img src="https://images.unsplash.com/photo-1678911820864-e2c567c655d7?auto=format&fit=crop&w=600&q=60" 
                            alt="Generative AI & LLMs" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-85"
                            loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>
                        <div class="absolute bottom-3 left-4 right-4 flex items-center justify-between text-white">
                            <div class="flex items-center gap-2.5">
                                <span class="w-9 h-9 rounded-lg bg-amber-400 text-slate-950 flex items-center justify-center text-sm font-black shadow-md">
                                    <i class="fas fa-wand-magic-sparkles"></i>
                                </span>
                                <h3 class="text-lg font-bold font-display text-white tracking-wide leading-tight">
                                    Generative AI
                                </h3>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <p class="text-slate-600 text-xs sm:text-sm leading-relaxed mb-4">
                            Advancing foundational AI, prompt engineering, multimodal synthesis, Retrieval-Augmented Generation (RAG), and fine-tuning domain-specific LLMs.
                        </p>
                        <ul class="space-y-2.5 text-xs sm:text-sm text-slate-700">
                            <li class="flex items-center gap-2 font-medium">
                                <i class="fas fa-check-circle text-amber-500 text-xs flex-shrink-0"></i>
                                <span>Large Language Models (LLMs)</span>
                            </li>
                            <li class="flex items-center gap-2 font-medium">
                                <i class="fas fa-check-circle text-amber-500 text-xs flex-shrink-0"></i>
                                <span>Retrieval-Augmented Generation (RAG)</span>
                            </li>
                            <li class="flex items-center gap-2 font-medium">
                                <i class="fas fa-check-circle text-amber-500 text-xs flex-shrink-0"></i>
                                <span>Prompt Engineering &amp; Fine-Tuning</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl overflow-hidden border border-slate-200/90 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="relative h-44 w-full overflow-hidden bg-slate-900">
                        <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=600&q=60" 
                            alt="Software Engineering & Agile" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-85"
                            loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>
                        <div class="absolute bottom-3 left-4 right-4 flex items-center justify-between text-white">
                            <div class="flex items-center gap-2.5">
                                <span class="w-9 h-9 rounded-lg bg-amber-400 text-slate-950 flex items-center justify-center text-sm font-black shadow-md">
                                    <i class="fas fa-code"></i>
                                </span>
                                <h3 class="text-lg font-bold font-display text-white tracking-wide leading-tight">
                                    Software Engineering
                                </h3>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <p class="text-slate-600 text-xs sm:text-sm leading-relaxed mb-4">
                            Rigorous inquiry in automated software testing, software quality metrics, agile architectures, and formal verification.
                        </p>
                        <ul class="space-y-2.5 text-xs sm:text-sm text-slate-700">
                            <li class="flex items-center gap-2 font-medium">
                                <i class="fas fa-check-circle text-amber-500 text-xs flex-shrink-0"></i>
                                <span>Automated Software Testing</span>
                            </li>
                            <li class="flex items-center gap-2 font-medium">
                                <i class="fas fa-check-circle text-amber-500 text-xs flex-shrink-0"></i>
                                <span>Quality Assurance &amp; Verification</span>
                            </li>
                            <li class="flex items-center gap-2 font-medium">
                                <i class="fas fa-check-circle text-amber-500 text-xs flex-shrink-0"></i>
                                <span>Agile &amp; DevOps Engineering</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl sm:rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm">
            <h2 class="text-xl sm:text-2xl font-bold font-display text-[#1a365d] mb-3">
                Research Collaboration
            </h2>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed mb-6 max-w-4xl">
                We welcome collaboration opportunities with other institutions and industry partners. Our research facilities are equipped with state-of-the-art technology to support various research initiatives.
            </p>
            <a href="index.php#contact" class="inline-flex items-center gap-2 bg-[#1a365d] hover:bg-[#0f172a] text-white text-sm font-bold px-6 py-3 rounded-xl shadow transition-all duration-200">
                <span>Contact for Collaboration</span>
            </a>
        </div>
    </div>
</main>

<?php
include 'templates/footer.php';
?>
