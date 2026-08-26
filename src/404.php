<?php
declare(strict_types=1);

$pageTitle = '404 - Page Not Found | CCSA Dibrugarh University';
$metaDescription = 'The page you are looking for does not exist or has been moved. Centre for Computer Science and Applications, Dibrugarh University.';
require_once __DIR__ . '/templates/header.php';
?>

<main id="main-content" class="flex-1 flex items-center justify-center py-16 px-4 bg-gradient-to-b from-slate-50 to-indigo-50/40">
    <div class="max-w-xl w-full text-center space-y-6 bg-white p-8 sm:p-12 rounded-3xl shadow-xl border border-slate-200/80">
        <!-- Error Badge & Icon -->
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-amber-100/80 text-amber-600 text-3xl mb-2 shadow-inner">
            ☕
        </div>

        <div class="space-y-2">
            <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-700 font-black text-xs uppercase tracking-widest rounded-full border border-indigo-200/60">HTTP 404</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-[#1a365d] tracking-tight">Page Not Found</h1>
        </div>

        <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
            The page you are looking for does not exist or may have been moved. Please use the navigation links below to find department circulars, programmes, and admissions information.
        </p>

        <!-- Quick Helpful Links -->
        <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="index.php" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-[#1a365d] text-white text-xs sm:text-sm font-bold shadow-md hover:bg-indigo-900 transition-all">
                <i class="fas fa-home text-amber-400"></i>
                <span>Return to Homepage</span>
            </a>
            <a href="notices.php" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-slate-100 text-slate-700 text-xs sm:text-sm font-bold hover:bg-slate-200 transition-all">
                <i class="fas fa-bullhorn text-indigo-600"></i>
                <span>Latest Notices</span>
            </a>
        </div>

        <div class="pt-6 border-t border-slate-100 text-xs text-slate-400">
            Need urgent assistance? Reach out to <a href="mailto:ccsduoffice@gmail.com" class="text-indigo-600 font-semibold hover:underline">ccsduoffice@gmail.com</a>
        </div>
    </div>
</main>

<?php
include __DIR__ . '/templates/footer.php';
?>
