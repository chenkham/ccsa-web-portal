<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/security-validator.php';
require_once __DIR__ . '/check_session.php';

$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;
$expired = isset($_GET['expired']);

// Authentication check: show login form if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $csrf_token = SecurityValidator::generateCSRFToken();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    <img src="../faculty/du.png" alt="Dibrugarh University" class="w-16 h-16 object-contain">
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

            <form method="POST" action="login.php" class="space-y-4">
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
$userrole = htmlspecialchars($_SESSION['user_role'] ?? 'super_admin');
$useremail = htmlspecialchars($_SESSION['user_email'] ?? 'admin@ccsdu.in');
$csrf_token = SecurityValidator::generateCSRFToken();

$currentTab = $_GET['tab'] ?? 'notifications';
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

            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-white"><?php echo $username; ?></p>
                    <p class="text-[11px] text-blue-200"><?php echo $useremail; ?> (<?php echo $userrole; ?>)</p>
                </div>
                <a href="logout.php" 
                    class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl transition-all flex items-center gap-1.5 shadow">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Workspace Container -->
    <div class="container mx-auto px-4 sm:px-6 py-8 flex-1">
        
        <!-- Toast Feedback -->
        <?php if ($success): ?>
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold rounded-2xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                    <span><?php echo htmlspecialchars($success); ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 text-sm font-semibold rounded-2xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-red-600 text-base"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">&times;</button>
            </div>
        <?php endif; ?>

        <nav class="bg-white rounded-2xl p-2 shadow-sm border border-slate-200/90 mb-8 flex flex-wrap gap-2">
            <button onclick="switchTab('notifications')" id="tab-btn-notifications" 
                class="tab-btn flex-1 min-w-[110px] py-2.5 px-3 rounded-xl text-xs sm:text-sm font-bold transition-all text-center flex items-center justify-center gap-1.5 bg-[#1a365d] text-white shadow">
                <i class="fas fa-bullhorn text-xs"></i>
                <span>Notices</span>
            </button>
            <button onclick="switchTab('messages')" id="tab-btn-messages" 
                class="tab-btn flex-1 min-w-[110px] py-2.5 px-3 rounded-xl text-xs sm:text-sm font-bold transition-all text-center flex items-center justify-center gap-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200">
                <i class="fas fa-envelope-open-text text-xs"></i>
                <span>Inquiries</span>
            </button>
            <button onclick="switchTab('logs')" id="tab-btn-logs" 
                class="tab-btn flex-1 min-w-[110px] py-2.5 px-3 rounded-xl text-xs sm:text-sm font-bold transition-all text-center flex items-center justify-center gap-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200">
                <i class="fas fa-shield-alt text-xs"></i>
                <span>Audit Logs</span>
            </button>
            <button onclick="switchTab('admin')" id="tab-btn-admin" 
                class="tab-btn flex-1 min-w-[110px] py-2.5 px-3 rounded-xl text-xs sm:text-sm font-bold transition-all text-center flex items-center justify-center gap-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200">
                <i class="fas fa-users-cog text-xs"></i>
                <span>Admins</span>
            </button>
        </nav>

        <div id="tab-notifications" class="tab-content active">
            <?php include __DIR__ . '/sections/notifications.php'; ?>
        </div>

        <div id="tab-messages" class="tab-content">
            <?php include __DIR__ . '/sections/messages.php'; ?>
        </div>

        <div id="tab-logs" class="tab-content">
            <?php include __DIR__ . '/sections/logs.php'; ?>
        </div>

        <div id="tab-admin" class="tab-content">
            <?php include __DIR__ . '/sections/admin_users.php'; ?>
        </div>
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
    });
    </script>
</body>
</html>