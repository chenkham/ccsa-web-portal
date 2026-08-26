<?php
declare(strict_types=1);

// Ensure /admin has a trailing slash so relative paths and form actions resolve properly
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$urlPath = parse_url($requestUri, PHP_URL_PATH) ?? '';
if (rtrim($urlPath, '/') === '/admin' && !str_ends_with($urlPath, '/')) {
    $queryString = parse_url($requestUri, PHP_URL_QUERY);
    $targetUrl = '/admin/' . ($queryString ? '?' . $queryString : '');
    header('Location: ' . $targetUrl, true, 301);
    exit;
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/security-validator.php';
require_once __DIR__ . '/check_session.php';

$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;
$expired = isset($_GET['expired']);

// Authentication check: show login form if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet');
    $csrf_token = SecurityValidator::generateCSRFToken();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
        <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">
        <title>Admin Login - CCSA Dibrugarh University</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            .login-card { animation: fadeIn 0.4s ease-out; }
            @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
        </style>
    </head>
    <body class="bg-gradient-to-br from-[#1a365d] via-[#1e293b] to-[#0f172a] min-h-screen flex items-center justify-center p-4">
        <div class="login-card max-w-md w-full bg-white/95 backdrop-blur-md rounded-3xl p-8 shadow-2xl border border-white/20">
            <div class="text-center mb-8">
                <div class="inline-flex p-3 rounded-2xl bg-indigo-50 border border-indigo-100 mb-3">
                    <img src="/faculty/du.png" alt="Dibrugarh University" class="w-16 h-16 object-contain" onerror="this.src='../faculty/du.png'">
                </div>
                <h1 class="text-2xl font-extrabold text-[#1a365d]">CCSA Admin Portal</h1>
                <p class="text-xs text-slate-500 mt-1">Centre for Computer Science and Applications</p>
            </div>

            <?php if ($error): ?>
                <div class="mb-5 p-3.5 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-xl flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-sm flex-shrink-0"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($expired): ?>
                <div class="mb-5 p-3.5 bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold rounded-xl flex items-center gap-2">
                    <i class="fas fa-clock text-sm flex-shrink-0"></i>
                    <span>Your session has expired. Please log in again.</span>
                </div>
            <?php endif; ?>

            <form method="POST" action="/admin/login.php" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Admin Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3.5 top-3.5 text-slate-400 text-sm"></i>
                        <input type="email" id="email" name="email" required placeholder="admin@ccsdu.in" 
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition-all">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3.5 top-3.5 text-slate-400 text-sm"></i>
                        <input type="password" id="password" name="password" required placeholder="••••••••" 
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition-all">
                    </div>
                </div>

                <button type="submit" 
                    class="w-full bg-[#1a365d] hover:bg-indigo-900 text-white font-bold py-3.5 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2 text-sm mt-2">
                    <span>Sign In to Dashboard</span>
                    <i class="fas fa-arrow-right text-xs"></i>
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <a href="../index.php" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold inline-flex items-center gap-1">
                    <i class="fas fa-chevron-left text-[10px]"></i>
                    <span>Back to Portal Homepage</span>
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

check_auth();

$username = htmlspecialchars($_SESSION['user_name'] ?? 'Administrator');
$userRole = (string)($_SESSION['user_role'] ?? 'super_admin');
$userrole = htmlspecialchars($userRole);
$useremail = htmlspecialchars($_SESSION['user_email'] ?? 'ccsduoffice@gmail.com');
$userEmail = (string)($_SESSION['user_email'] ?? 'ccsduoffice@gmail.com');
$isSuperAdmin = ($userRole === 'super_admin');
$csrf_token = SecurityValidator::generateCSRFToken();

// Role-based allowed tabs: Editor gets ONLY Notices and Inquiries
$allowedTabs = ['notifications', 'messages'];
if ($userRole === 'super_admin' || $userRole === 'admin') {
    $allowedTabs[] = 'logs';
}
if ($userRole === 'super_admin') {
    $allowedTabs[] = 'admin';
}

$currentTab = $_GET['tab'] ?? 'notifications';
if (!in_array($currentTab, $allowedTabs, true)) {
    $currentTab = 'notifications';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCSA Management Dashboard - Dibrugarh University</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">

    <!-- Top Navigation Header -->
    <header class="bg-[#1a365d] text-white sticky top-0 z-40 shadow-md">
        <div class="container mx-auto px-4 sm:px-6 py-3.5 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="../index.php" title="Go to Website">
                    <img src="../faculty/du.png" alt="DU Logo" class="h-10 w-10 object-contain p-1 bg-white rounded-lg">
                </a>
                <div>
                    <h1 class="text-base sm:text-lg font-bold leading-tight">CCSA Admin Suite</h1>
                    <p class="text-[11px] text-blue-200 hidden sm:block">Centre for Computer Science and Applications, Dibrugarh University</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Live Database Connection Status Badge & Sync Trigger -->
                <div class="flex items-center gap-2">
                    <?php if (isset($pdo) && $pdo !== null): ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30" title="Connected directly to Hostinger MySQL Database">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="hidden md:inline">MySQL</span> Connected
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30" title="Running in Local Cache Mirror">
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                            <span class="hidden md:inline">Local</span> Cache
                        </span>
                    <?php endif; ?>

                    <a href="sync_db.php?key=786004@Ccs" target="_blank" class="bg-indigo-600 hover:bg-indigo-500 text-white text-[11px] font-bold px-2.5 py-1 rounded-xl transition flex items-center gap-1 shadow border border-indigo-400/30 cursor-pointer" title="Auto-Sync all server cache accounts & notices into MySQL">
                        <i class="fas fa-sync-alt text-[10px]"></i>
                        <span class="hidden sm:inline">Sync DB</span>
                    </a>
                </div>

                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-white"><?php echo $username; ?></p>
                    <p class="text-[11px] text-blue-200"><?php echo $useremail; ?> (<?php echo $userrole; ?>)</p>
                </div>
                <button type="button" onclick="document.getElementById('changePasswordModal').classList.remove('hidden')" 
                    class="bg-indigo-700/80 hover:bg-indigo-600 text-white text-xs font-bold px-3 py-2 rounded-xl transition-all flex items-center gap-1.5 shadow border border-indigo-400/30 cursor-pointer">
                    <i class="fas fa-key text-[11px]"></i>
                    <span class="hidden sm:inline">Change Password</span>
                </button>
                <a href="logout.php" 
                    class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl transition-all flex items-center gap-1.5 shadow">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Change Password Modal for All Roles (super_admin, admin, editor) -->
    <div id="changePasswordModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200 relative animate-fadeIn">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100 mb-5">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-base font-black">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Change Account Password</h3>
                        <p class="text-xs text-slate-500"><?php echo $useremail; ?></p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('changePasswordModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 text-2xl font-light">&times;</button>
            </div>

            <form method="POST" action="actions.php" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="action" value="change_password">

                <div>
                    <label for="current_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required placeholder="••••••••" 
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition-all">
                </div>

                <div>
                    <label for="new_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">New Password</label>
                    <input type="password" id="new_password" name="new_password" required minlength="8" placeholder="At least 8 characters" 
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition-all">
                </div>

                <div>
                    <label for="confirm_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="8" placeholder="Repeat new password" 
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition-all">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('changePasswordModal').classList.add('hidden')" 
                        class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-5 py-2.5 bg-[#1a365d] hover:bg-indigo-900 text-white font-bold text-xs rounded-xl shadow transition-all flex items-center gap-1.5">
                        <i class="fas fa-check text-xs"></i>
                        <span>Update Password</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Workspace Container -->
    <div class="container mx-auto px-4 sm:px-6 py-8 flex-1">
        
        <!-- Toast Feedback -->
        <?php if ($success): ?>
            <div class="admin-toast mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold rounded-2xl flex items-center justify-between shadow-sm transition-all duration-500">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                    <span><?php echo htmlspecialchars($success); ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="admin-toast mb-6 p-4 bg-red-50 border border-red-200 text-red-800 text-sm font-semibold rounded-2xl flex items-center justify-between shadow-sm transition-all duration-500">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-red-600 text-base"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">&times;</button>
            </div>
        <?php endif; ?>

        <nav class="bg-white rounded-2xl p-2 shadow-sm border border-slate-200/90 mb-8 flex flex-wrap gap-2">
            <button onclick="switchTab('notifications')" id="tab-btn-notifications" 
                class="tab-btn flex-1 min-w-[110px] py-2.5 px-3 rounded-xl text-xs sm:text-sm font-bold transition-all text-center flex items-center justify-center gap-1.5 bg-[#1a365d] text-white shadow cursor-pointer">
                <i class="fas fa-bullhorn text-xs"></i>
                <span>Notices</span>
            </button>
            <button onclick="switchTab('messages')" id="tab-btn-messages" 
                class="tab-btn flex-1 min-w-[110px] py-2.5 px-3 rounded-xl text-xs sm:text-sm font-bold transition-all text-center flex items-center justify-center gap-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 cursor-pointer">
                <i class="fas fa-envelope-open-text text-xs"></i>
                <span>Inquiries</span>
            </button>
            <?php if (in_array('logs', $allowedTabs, true)): ?>
            <button onclick="switchTab('logs')" id="tab-btn-logs" 
                class="tab-btn flex-1 min-w-[110px] py-2.5 px-3 rounded-xl text-xs sm:text-sm font-bold transition-all text-center flex items-center justify-center gap-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 cursor-pointer">
                <i class="fas fa-shield-alt text-xs"></i>
                <span>Audit Logs</span>
            </button>
            <?php endif; ?>
            <?php if (in_array('admin', $allowedTabs, true)): ?>
            <button onclick="switchTab('admin')" id="tab-btn-admin" 
                class="tab-btn flex-1 min-w-[110px] py-2.5 px-3 rounded-xl text-xs sm:text-sm font-bold transition-all text-center flex items-center justify-center gap-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 cursor-pointer">
                <i class="fas fa-users-cog text-xs"></i>
                <span>Admins</span>
            </button>
            <?php endif; ?>
        </nav>

        <div id="tab-notifications" class="tab-content active">
            <?php include __DIR__ . '/sections/notifications.php'; ?>
        </div>

        <div id="tab-messages" class="tab-content">
            <?php include __DIR__ . '/sections/messages.php'; ?>
        </div>

        <?php if (in_array('logs', $allowedTabs, true)): ?>
        <div id="tab-logs" class="tab-content">
            <?php include __DIR__ . '/sections/logs.php'; ?>
        </div>
        <?php endif; ?>

        <?php if (in_array('admin', $allowedTabs, true)): ?>
        <div id="tab-admin" class="tab-content">
            <?php include __DIR__ . '/sections/admin_users.php'; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500">
        CCSA Portal Administrator Suite &bull; Dibrugarh University &bull; Version 2.0.0
    </footer>

    <!-- Interactive Tab Script -->
    <script>
    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(pane => pane.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-[#1a365d]', 'text-white', 'shadow');
            btn.classList.add('bg-slate-100', 'text-slate-700');
        });

        const targetPane = document.getElementById('tab-' + tabName);
        const targetBtn = document.getElementById('tab-btn-' + tabName);

        if (targetPane && targetBtn) {
            targetPane.classList.add('active');
            targetBtn.classList.remove('bg-slate-100', 'text-slate-700');
            targetBtn.classList.add('bg-[#1a365d]', 'text-white', 'shadow');
            sessionStorage.setItem('activeAdminTab', tabName);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab') || sessionStorage.getItem('activeAdminTab') || 'notifications';
        switchTab(tabParam);

        document.querySelectorAll('.admin-toast').forEach(toast => {
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-8px)';
                setTimeout(() => toast.remove(), 500);
            }, 3500);
        });
    });
    </script>
</body>
</html>