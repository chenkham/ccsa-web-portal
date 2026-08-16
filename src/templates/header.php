<?php
declare(strict_types=1);

/**
 * Shared header template for all public CCSA pages.
 * 
 * Expected variables before including:
 *   $pageTitle  (string) — Page <title> content, defaults to 'CCSA'
 *   $currentPage (string) — Active page identifier for nav highlighting
 *   $extraHead  (string) — Optional additional <head> content
 */

require_once __DIR__ . '/../includes/Session.php';
require_once __DIR__ . '/../includes/Security.php';
require_once __DIR__ . '/nav-data.php';

Session::start();
$csrfToken = Security::generateCsrfToken();

$pageTitle = $pageTitle ?? 'CCSA - Centre for Computer Science and Applications';
$currentPage = $currentPage ?? '';
$extraHead = $extraHead ?? '';

/**
 * Renders a single download item with download + view actions.
 */
function renderDownloadItem(array $item, string $viewIcon): string {
    $label = Security::escape($item['label']);
    $href = $item['href'];
    $download = $item['download'] ?? '';
    $view = $item['view'] ?? '';
    $icon = $item['icon'] ?? 'fas fa-file-pdf';

    $html = '<div class="flex items-center justify-between px-4 py-2 hover:bg-slate-100 transition-colors dropdown-item">';
    $html .= '<a href="' . Security::escape($href) . '"';
    if ($download) {
        $html .= ' download="' . Security::escape($download) . '"';
    }
    $html .= ' class="text-[#1a365d] flex items-center gap-2"><i class="' . Security::escape($icon) . ' text-red-600"></i> <span>' . $label . '</span></a>';

    if ($view) {
        $html .= ' <a href="' . Security::escape($view) . '" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-indigo-600 ml-2" title="View Document">' . $viewIcon . '</a>';
    }
    $html .= '</div>';
    return $html;
}

/**
 * Renders a mobile download item with download + view actions.
 */
function renderMobileDownloadItem(array $item, string $viewIcon): string {
    $label = Security::escape($item['label']);
    $href = $item['href'];
    $download = $item['download'] ?? '';
    $view = $item['view'] ?? '';

    $html = '<div class="flex justify-between items-center py-1">';
    $html .= '<a href="' . Security::escape($href) . '"';
    if ($download) {
        $html .= ' download="' . Security::escape($download) . '"';
    }
    $html .= ' class="block py-1 text-slate-300 hover:text-[#fbbf24] text-sm">' . $label . '</a>';

    if ($view) {
        $html .= ' <a href="' . Security::escape($view) . '" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-[#fbbf24] ml-2">' . $viewIcon . '</a>';
    }
    $html .= '</div>';
    return $html;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="faculty/du.png" />
    <title><?php echo Security::escape($pageTitle); ?></title>

    <meta name="description" content="<?php echo Security::escape($metaDescription ?? 'Centre for Computer Science and Applications (CCSA), Dibrugarh University. Premier institute offering BCA, MCA, PGDCA, and Ph.D. in Computer Science, Generative AI, Cloud Computing, and Cybersecurity.'); ?>">
    <meta name="keywords" content="CCSA Dibrugarh University, BCA Dibrugarh, MCA Assam, PGDCA, Computer Science Dibrugarh, DUAT 2026, Ph.D. Computer Science Assam, AICTE Approved MCA Dibrugarh, Data Science, Generative AI">
    <meta name="author" content="Centre for Computer Science and Applications, Dibrugarh University">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <link rel="canonical" href="https://ccsdu.in/<?php echo Security::escape(basename($_SERVER['PHP_SELF'] ?? 'index.php')); ?>">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="CCSA Dibrugarh University">
    <meta property="og:title" content="<?php echo Security::escape($pageTitle); ?>">
    <meta property="og:description" content="<?php echo Security::escape($metaDescription ?? 'Centre for Computer Science and Applications (CCSA), Dibrugarh University. Premier institute offering BCA, MCA, PGDCA, and Ph.D. in Computer Science.'); ?>">
    <meta property="og:image" content="https://ccsdu.in/faculty/du.png">
    <meta property="og:url" content="https://ccsdu.in/<?php echo Security::escape(basename($_SERVER['PHP_SELF'] ?? 'index.php')); ?>">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo Security::escape($pageTitle); ?>">
    <meta name="twitter:description" content="<?php echo Security::escape($metaDescription ?? 'Centre for Computer Science and Applications (CCSA), Dibrugarh University.'); ?>">
    <meta name="twitter:image" content="https://ccsdu.in/faculty/du.png">

    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "EducationalOrganization",
      "name": "Centre for Computer Science and Applications, Dibrugarh University",
      "alternateName": "CCSA Dibrugarh University",
      "url": "https://ccsdu.in/",
      "logo": "https://ccsdu.in/faculty/du.png",
      "sameAs": [
        "https://dibru.ac.in",
        "https://ccsaalumni.in/",
        "https://placeccsa.wordpress.com/"
      ],
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Dibrugarh University Campus, Rajabheta",
        "addressLocality": "Dibrugarh",
        "addressRegion": "Assam",
        "postalCode": "786004",
        "addressCountry": "IN"
      },
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+91-373-2370231",
        "contactType": "Academic Inquiries and Admissions",
        "email": "ccsduoffice@gmail.com"
      }
    }
    </script>

    <script>
        (function () {
            try {
                var fontScale = localStorage.getItem('ux4g_font_scale');
                if (fontScale !== null) {
                    var scales = [0.9, 1.0, 1.15];
                    var idx = parseInt(fontScale, 10);
                    if (idx >= 0 && idx < scales.length) {
                        document.documentElement.style.fontSize = (scales[idx] * 100) + '%';
                    }
                }
            } catch (e) {}
        })();
    </script>

    
    <!-- Google Fonts: Inter (Body) + Outfit & Plus Jakarta Sans (Headings & Display) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Icons & Animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              primary: '#1a365d',
              accent: '#fbbf24',
            },
            fontFamily: {
              sans: ['Inter', 'system-ui', 'sans-serif'],
              display: ['Outfit', 'Plus Jakarta Sans', 'sans-serif'],
              heading: ['Plus Jakarta Sans', 'Outfit', 'sans-serif'],
            }
          }
        }
      }
    </script>
    
    <!-- Swiper & Google reCAPTCHA -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js?render=6LeEW94sAAAAAPFa_NXd8WemwqWn-SLlNjpnN0CH"></script>
    
    <!-- Custom CSS (Special effects, keyframes, scrollbar) -->
    <link rel="stylesheet" href="assets/css/custom.css?v=<?php echo time(); ?>">
    <!-- UX4G Accessibility Module -->
    <script src="assets/js/ux4g-accessibility.js" defer></script>

    <!-- Logo Badge + Shine Effect (inline for reliability) -->
    <style>
        @keyframes radialGlow {
            0% { opacity: 0.1; transform: scale(0.95); }
            50% { opacity: 0.4; transform: scale(1.05); }
            100% { opacity: 0.1; transform: scale(0.95); }
        }
        @keyframes shine {
            from { transform: translateX(-50%) rotate(35deg); }
            to { transform: translateX(250%) rotate(35deg); }
        }
        .badge-container {
            position: relative;
            overflow: hidden;
            border-radius: 50%;
            background: linear-gradient(145deg, #2a4a7f 0%, #1a365d 100%);
            padding: clamp(2px, 1vw, 4px);
            width: clamp(48px, 20vw, 64px);
            height: clamp(48px, 20vw, 64px);
            margin: auto;
        }
        .badge-container::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to right,
                transparent 0%,
                rgba(255, 223, 0, 0.1) 30%,
                rgba(255, 215, 0, 0.4) 50%,
                rgba(255, 223, 0, 0.1) 70%,
                transparent 100%
            );
            animation: shine 3s infinite;
        }
        .badge-inner {
            position: relative;
            z-index: 1;
            border-radius: 50%;
            overflow: hidden;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .glow-effect {
            position: relative;
            display: inline-block;
        }
        .glow-effect::before {
            content: '';
            position: absolute;
            inset: clamp(-10px, -3vw, -20px);
            background: radial-gradient(circle,
                rgba(255, 223, 0, 0.4) 0%,
                rgba(255, 215, 0, 0) 70%
            );
            animation: radialGlow 3s ease-in-out infinite;
        }
        .badge-inner img {
            width: 90%;
            height: auto;
            max-width: 100px;
            object-fit: cover;
            border-radius: 50%;
        }
        @media screen and (max-width: 768px) {
            .badge-container {
                width: clamp(40px, 25vw, 56px);
                height: clamp(40px, 25vw, 56px);
            }
            .glow-effect::before {
                inset: clamp(-8px, -2vw, -12px);
            }
            .badge-inner img {
                max-width: 80px;
            }
        }
    </style>

    <?php echo $extraHead; ?>
</head>

<body class="font-sans antialiased text-slate-800 bg-white min-h-screen flex flex-col">

    <!-- ═══ UX4G & GIGW 3.0 ACCESSIBILITY: SKIP TO MAIN CONTENT ═══ -->
    <a href="#main-content" 
        class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[100] focus:px-4 focus:py-2 focus:bg-[#1a365d] focus:text-[#fbbf24] focus:rounded-xl focus:font-extrabold focus:shadow-2xl focus:outline-none focus:ring-4 focus:ring-[#fbbf24] transition-all text-xs">
        Skip to main content
    </a>

    <!-- Smart Header Wrapper (Static at top on mobile; Smart Auto-Hide/Reveal on desktop lg screens) -->
    <div id="smartHeader" class="relative lg:sticky lg:top-0 z-50 lg:transition-transform lg:duration-300 lg:ease-in-out">
        
        <!-- ═══ SINGLE UNIFIED GREEN TOP BAR (ACCESSIBILITY, GOVT AFFILIATION & QUICK LINKS) ═══ -->
        <header class="bg-teal-800 text-white text-xs relative overflow-hidden border-b border-teal-900/40">
            <!-- Faceted Accent Geometric Overlay -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-20">
                <svg class="w-full h-full object-cover min-w-[1000px]" viewBox="0 0 1440 50" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                    <polygon points="0,0 420,0 210,50" fill="rgba(255,255,255,0.06)" />
                    <polygon points="420,0 880,0 660,50" fill="rgba(0,0,0,0.22)" />
                    <polygon points="880,0 1440,0 1200,50" fill="rgba(255,255,255,0.08)" />
                    <line x1="0" y1="0" x2="1440" y2="50" stroke="rgba(255,255,255,0.08)" stroke-width="1" />
                </svg>
            </div>

            <div class="container mx-auto px-4 lg:px-10 relative z-10">
                <div class="flex justify-between items-center py-2 gap-2 flex-wrap sm:flex-nowrap">
                    <!-- Left: Email, Affiliation & University identity -->
                    <div class="flex items-center gap-2 sm:gap-4 font-medium">
                        <a href="mailto:ccsduoffice@gmail.com" class="flex items-center hover:text-[#fbbf24] transition-colors">
                            <svg class="inline mr-1.5 w-3.5 h-3.5 text-amber-300" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z" />
                            </svg>
                            <span class="hidden md:inline">ccsduoffice@gmail.com</span>
                            <span class="md:hidden">CCSA Office</span>
                        </a>
                        <span class="text-teal-600 hidden sm:inline">|</span>
                        <span class="font-semibold text-teal-100 hidden sm:inline">
                            Approved by AICTE
                        </span>
                        <span class="text-teal-600 hidden lg:inline">|</span>
                        <span class="text-slate-200 hidden lg:inline">
                            Dibrugarh University, Assam
                        </span>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-3.5 font-medium ml-auto">
                        <div class="hidden sm:flex items-center gap-3 text-xs">
                            <button type="button" onclick="openHelplineModal()" class="hover:text-[#fbbf24] transition-colors flex items-center gap-1 cursor-pointer">
                                <i class="fas fa-shield-alt text-amber-300 text-xs"></i>
                                <span>Anti-Ragging Helpline</span>
                            </button>
                            <a href="https://ccsaalumni.in/" target="_blank" rel="noopener noreferrer" class="hover:text-[#fbbf24] transition-colors">Alumni</a>
                            <a href="https://placeccsa.wordpress.com/" target="_blank" rel="noopener noreferrer" class="hover:text-[#fbbf24] transition-colors">Placement Cell</a>
                        </div>

                        <button type="button" onclick="openSpotlightSearch()" 
                            class="flex items-center gap-1.5 bg-teal-900/80 hover:bg-teal-700/80 text-white text-xs font-semibold px-2.5 py-1 rounded-lg border border-teal-700/80 transition-all cursor-pointer shadow-sm" 
                            title="Search">
                            <i class="fas fa-search text-white text-xs"></i>
                            <span>Search</span>
                        </button>

                        <span class="h-3.5 w-px bg-white/20 hidden sm:block" aria-hidden="true"></span>

                        <div class="flex items-center gap-0.5 bg-teal-900/60 rounded-lg p-0.5 border border-teal-700/60" role="group" aria-label="Text Size Controls">
                            <button type="button" id="ux4g-font-decrease" title="Decrease font size" aria-label="Decrease text size" 
                                class="px-1.5 py-0.5 text-[10px] font-medium text-slate-200 hover:text-white hover:bg-teal-700/50 rounded transition-colors cursor-pointer">
                                A−
                            </button>
                            <button type="button" id="ux4g-font-reset" title="Reset font size" aria-label="Reset text size to default" 
                                class="px-1.5 py-0.5 text-[10px] font-bold text-amber-300 hover:text-white hover:bg-teal-700/50 rounded transition-colors cursor-pointer">
                                A
                            </button>
                            <button type="button" id="ux4g-font-increase" title="Increase font size" aria-label="Increase text size" 
                                class="px-1.5 py-0.5 text-[10px] font-medium text-slate-200 hover:text-white hover:bg-teal-700/50 rounded transition-colors cursor-pointer">
                                A+
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Navigation Bar with Faceted Geometric Shadow Pattern -->
        <nav class="bg-[#1a365d] text-white shadow-md relative border-b border-blue-900/40">
            <!-- Low-Poly Geometric Faceted Mesh & Shadow Shards -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <svg class="w-full h-full object-cover min-w-[1000px] opacity-35" viewBox="0 0 1440 90" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                    <polygon points="0,0 350,0 180,90" fill="rgba(255, 255, 255, 0.05)" />
                    <polygon points="350,0 720,0 520,90" fill="rgba(0, 0, 0, 0.22)" />
                    <polygon points="720,0 1100,0 920,90" fill="rgba(255, 255, 255, 0.08)" />
                    <polygon points="1100,0 1440,0 1320,90" fill="rgba(0, 0, 0, 0.28)" />
                    <polygon points="180,90 520,90 350,0" fill="rgba(0, 0, 0, 0.15)" />
                    <polygon points="520,90 920,90 720,0" fill="rgba(255, 255, 255, 0.06)" />
                    <polygon points="920,90 1320,90 1100,0" fill="rgba(0, 0, 0, 0.2)" />
                    <line x1="0" y1="0" x2="1440" y2="90" stroke="rgba(255, 255, 255, 0.1)" stroke-width="1" />
                    <line x1="350" y1="0" x2="180" y2="90" stroke="rgba(0, 0, 0, 0.25)" stroke-width="1.5" />
                    <line x1="720" y1="0" x2="520" y2="90" stroke="rgba(255, 255, 255, 0.1)" stroke-width="1" />
                    <line x1="1100" y1="0" x2="920" y2="90" stroke="rgba(0, 0, 0, 0.3)" stroke-width="1.5" />
                </svg>
            </div>

            <div class="container mx-auto px-4 lg:px-8 relative z-10">
                <div class="flex justify-between items-center py-3 sm:py-3.5 lg:py-4">
                    <!-- Branding -->
                    <div class="flex items-center space-x-3 sm:space-x-3.5 min-w-0 pr-2">
                        <a href="index.php" class="flex items-center space-x-3 sm:space-x-3.5 group min-w-0">
                            <div class="relative flex-shrink-0 glow-effect">
                                <div class="badge-container">
                                    <div class="badge-inner">
                                        <img src="faculty/du.png" alt="Dibrugarh University Logo" class="h-16 w-16 object-cover">
                                    </div>
                                </div>
                            </div>

                            <div id="text-animation-container" class="min-w-0">
                                <h1 class="text-xs sm:text-sm lg:text-[15px] xl:text-base font-bold font-display text-white leading-tight group-hover:text-amber-300 transition-colors">
                                    Centre for Computer Science And Applications, Dibrugarh University
                                </h1>
                                <p class="text-[10px] sm:text-xs text-blue-200 font-medium mt-0.5 leading-tight">
                                    পৰিকলন বিজ্ঞান আৰু প্ৰয়োগ কেন্দ্ৰ, ডিব্ৰুগড় বিশ্ববিদ্যালয়
                                </p>
                            </div>
                        </a>
                    </div>

                    <!-- Desktop Navigation (Original Standard Size) -->
                    <div class="hidden lg:flex items-center space-x-5 xl:space-x-6 text-sm font-medium flex-shrink-0">
<?php foreach ($navItems as $item): ?>
    <?php if (empty($item['children'])): ?>
                        <div class="nav-item relative">
                            <a href="<?php echo Security::escape($item['href']); ?>" class="hover:text-[#fbbf24] py-2 transition-colors flex items-center"><?php echo Security::escape($item['label']); ?></a>
                        </div>
    <?php else: ?>
                        <div class="nav-item relative group">
                            <a href="<?php echo Security::escape($item['href']); ?>" class="hover:text-[#fbbf24] py-2 transition-colors flex items-center gap-1">
                                <span><?php echo Security::escape($item['label']); ?></span>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-200 group-hover:rotate-180"></i>
                            </a>
                            <div class="dropdown-menu shadow-2xl rounded-xl py-2 min-w-[220px]">
        <?php foreach ($item['children'] as $child): ?>
            <?php if (!empty($child['children'])): ?>
                                <!-- Nested dropdown: <?php echo Security::escape($child['label']); ?> -->
                                <div class="nested-dropdown relative group/nested">
                                    <a href="#" class="dropdown-item has-submenu flex items-center justify-between px-4 py-2.5 text-[#1a365d] hover:bg-slate-100 transition-colors text-sm">
                                        <span class="flex items-center gap-2 font-medium">
                                            <i class="<?php echo Security::escape($child['icon'] ?? 'fas fa-folder'); ?> text-indigo-600"></i>
                                            <?php echo Security::escape($child['label']); ?>
                                        </span>
                                        <i class="fas fa-chevron-right text-xs text-slate-400"></i>
                                    </a>
                                    <div class="dropdown-menu nested-menu shadow-2xl rounded-xl py-2 min-w-[220px]">
                <?php foreach ($child['children'] as $subItem): ?>
                                        <?php echo renderDownloadItem($subItem, $viewIcon); ?>
                <?php endforeach; ?>
                                    </div>
                                </div>
            <?php elseif (!empty($child['download']) || !empty($child['view'])): ?>
                                <?php echo renderDownloadItem($child, $viewIcon); ?>
            <?php else: ?>
                                <a href="<?php echo Security::escape($child['href']); ?>" class="dropdown-item block px-4 py-2.5 text-[#1a365d] hover:bg-slate-100 transition-colors text-sm font-medium"><?php echo Security::escape($child['label']); ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
                            </div>
                        </div>
    <?php endif; ?>
<?php endforeach; ?>
                    </div>

                    <!-- Mobile Menu Button (Simple 2-Line) -->
                    <div class="lg:hidden flex-shrink-0">
                        <button id="mobile-menu-button" class="p-2 text-white hover:text-[#fbbf24] focus:outline-none transition-colors flex flex-col justify-center items-end gap-1.5 w-9 h-9 cursor-pointer" aria-label="Toggle Navigation Menu" aria-expanded="false">
                            <span class="hamburger-line w-6 h-0.5 bg-white group-hover:bg-[#fbbf24] rounded-full transition-all duration-300 transform origin-center"></span>
                            <span class="hamburger-line w-4 h-0.5 bg-white group-hover:bg-[#fbbf24] rounded-full transition-all duration-300 transform origin-center"></span>
                        </button>
                    </div>
                </div>

                <!-- Simple Clean Mobile Navigation Menu -->
                <div id="mobile-menu" class="lg:hidden hidden pt-2 pb-4 border-t border-blue-900/60 space-y-1">
<?php foreach ($navItems as $item): ?>
    <?php if (empty($item['children'])): ?>
                    <a href="<?php echo Security::escape($item['href']); ?>" class="block py-2 px-2 text-slate-200 hover:text-[#fbbf24] text-sm font-medium transition-colors"><?php echo Security::escape($item['label']); ?></a>
    <?php else: ?>
                    <div class="mobile-dropdown py-0.5">
                        <button type="button" class="mobile-dropdown-btn w-full text-left py-2 px-2 text-slate-200 hover:text-[#fbbf24] flex justify-between items-center text-sm font-medium transition-colors">
                            <span><?php echo Security::escape($item['label']); ?></span>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200 mobile-chevron"></i>
                        </button>
                        <div class="mobile-submenu hidden pl-3 space-y-1 mt-1 border-l border-blue-800">
        <?php foreach ($item['children'] as $child): ?>
            <?php if (!empty($child['children'])): ?>
                            <div class="mobile-nested-dropdown py-0.5">
                                <button type="button" class="mobile-nested-btn w-full text-left py-1.5 px-2 text-slate-300 hover:text-[#fbbf24] flex justify-between items-center text-xs font-medium">
                                    <span><?php echo Security::escape($child['label']); ?></span>
                                    <i class="fas fa-chevron-down text-xs mobile-nested-chevron"></i>
                                </button>
                                <div class="mobile-nested-submenu hidden pl-3 space-y-1 mt-1 border-l border-blue-700">
                    <?php foreach ($child['children'] as $subItem): ?>
                                    <?php echo renderMobileDownloadItem($subItem, $viewIcon); ?>
                    <?php endforeach; ?>
                                </div>
                            </div>
            <?php elseif (!empty($child['download']) || !empty($child['view'])): ?>
                            <?php echo renderMobileDownloadItem($child, $viewIcon); ?>
            <?php else: ?>
                            <a href="<?php echo Security::escape($child['href']); ?>" class="block py-1.5 px-2 text-slate-300 hover:text-[#fbbf24] text-xs font-normal transition-colors"><?php echo Security::escape($child['label']); ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
                        </div>
                    </div>
    <?php endif; ?>
<?php endforeach; ?>
                </div>
            </div>
        </nav>
    </div>

    <!-- ═══ MAIN CONTENT LANDMARK (GIGW 3.0 & UX4G ACCESSIBILITY) ═══ -->
    <main id="main-content" role="main" class="flex-1">
