<?php
declare(strict_types=1);

/**
 * Universal Database Auto-Sync & Cache Migration Engine
 * Centre for Computer Science and Applications (CCSA), Dibrugarh University
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Session.php';

Session::start();

// Authentication: Super admin or direct verification key
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$authKey = $_GET['key'] ?? '';
$isValidKey = ($authKey === '786004@Ccs' || $authKey === 'DU_CCSA_SECURE_API_KEY_2026');

if (!$isLoggedIn && !$isValidKey) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Database Sync Authentication - CCSA</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-slate-900 min-h-screen flex items-center justify-center p-4 text-slate-100 font-sans">
        <div class="max-w-md w-full bg-slate-800 rounded-3xl p-8 shadow-2xl border border-slate-700 text-center">
            <div class="w-14 h-14 bg-indigo-500/20 text-indigo-400 rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                ⚡
            </div>
            <h2 class="text-xl font-bold text-white mb-2">CCSA Database Sync Tool</h2>
            <p class="text-xs text-slate-400 mb-6">Please log in to your admin panel first, or enter the database access key below to synchronize local cache with MySQL.</p>
            <form method="GET" action="sync_db.php" class="space-y-4">
                <input type="password" name="key" placeholder="Enter Database Access Key" required class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition shadow-lg cursor-pointer">
                    Authenticate &amp; Run Sync
                </button>
            </form>
            <div class="mt-6 pt-4 border-t border-slate-700/60">
                <a href="index.php" class="text-xs text-indigo-400 hover:underline">← Back to Admin Login</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$results = [
    'db_connected' => false,
    'db_name' => '',
    'users_synced' => [],
    'notices_synced' => 0,
    'logs_synced' => 0,
    'messages_synced' => 0,
    'errors' => []
];

try {
    $pdo = Database::getInstance();
    $results['db_connected'] = true;

    // Get current DB name
    $stmt = $pdo->query("SELECT DATABASE()");
    $results['db_name'] = (string)$stmt->fetchColumn();

    // 1. Ensure all modern tables exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin_users (
            id int(11) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            email varchar(150) NOT NULL,
            password varchar(255) NOT NULL,
            role enum('super_admin','admin','editor') NOT NULL DEFAULT 'admin',
            status enum('active','inactive') NOT NULL DEFAULT 'active',
            last_login datetime DEFAULT NULL,
            createdAt datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updatedAt datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notifications (
            id int(11) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            description text DEFAULT NULL,
            category varchar(50) NOT NULL DEFAULT 'General',
            creator_email varchar(150) DEFAULT 'admin@ccsdu.in',
            file_path varchar(255) DEFAULT NULL,
            file_url varchar(500) DEFAULT NULL,
            is_pinned tinyint(1) NOT NULL DEFAULT 0,
            is_new tinyint(1) NOT NULL DEFAULT 1,
            new_until date DEFAULT NULL,
            createdAt datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updatedAt datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_created (createdAt)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS audit_logs (
            id int(11) NOT NULL AUTO_INCREMENT,
            user_email varchar(150) NOT NULL,
            ip_address varchar(45) NOT NULL,
            action varchar(100) NOT NULL,
            details text DEFAULT NULL,
            user_agent varchar(255) DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_log_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS contact_messages (
            id int(11) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            email varchar(150) NOT NULL,
            message text NOT NULL,
            ip_address varchar(45) DEFAULT NULL,
            is_read tinyint(1) NOT NULL DEFAULT 0,
            createdAt datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 2. Discover all cache directories
    $candidateDirs = [
        __DIR__ . '/../cache',
        __DIR__ . '/../../cache',
        __DIR__ . '/cache',
        ($_SERVER['DOCUMENT_ROOT'] ?? '') . '/cache',
        ($_SERVER['DOCUMENT_ROOT'] ?? '') . '/src/cache',
        '/home/u332237642/public_html/cache',
        '/home/u332237642/public_html/src/cache'
    ];

    $foundCacheFiles = [];
    foreach ($candidateDirs as $dir) {
        if (!empty($dir) && is_dir($dir)) {
            $files = glob(rtrim($dir, '/') . '/*.json') ?: [];
            foreach ($files as $f) {
                $foundCacheFiles[basename($f)][] = $f;
            }
        }
    }

    // 3. Sync Admin Users
    $syncUserStmt = $pdo->prepare("
        INSERT INTO admin_users (name, email, password, role, status, createdAt) 
        VALUES (?, ?, ?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE password = VALUES(password), role = VALUES(role), status = VALUES(status), name = VALUES(name)
    ");

    // Ensure default Super Admin exists
    $syncUserStmt->execute([
        'CCSA Super Administrator',
        'ccsduoffice@gmail.com',
        '$2y$12$amJ/rdLNDOcdL3CpYXMv9OSSE1rCjCAE/.jUG6JURCjh/u0Wo8TBW',
        'super_admin',
        'active',
        date('Y-m-d H:i:s')
    ]);

    if (!empty($foundCacheFiles['admin_users.json'])) {
        foreach ($foundCacheFiles['admin_users.json'] as $fpath) {
            $data = json_decode((string)@file_get_contents($fpath), true);
            if (is_array($data)) {
                foreach ($data as $u) {
                    if (!empty($u['email']) && !empty($u['password'])) {
                        $syncUserStmt->execute([
                            $u['name'] ?? 'Administrator',
                            $u['email'],
                            $u['password'],
                            $u['role'] ?? 'admin',
                            $u['status'] ?? 'active',
                            $u['createdAt'] ?? date('Y-m-d H:i:s')
                        ]);
                        $results['users_synced'][$u['email']] = $u['name'] ?? $u['email'];
                    }
                }
            }
        }
    }

    // 4. Sync Notifications
    if (!empty($foundCacheFiles['local_notifications.json'])) {
        $checkNotice = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE title = ?");
        $insertNotice = $pdo->prepare("INSERT INTO notifications (title, description, category, creator_email, file_path, file_url, is_pinned, is_new, new_until, createdAt, updatedAt) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($foundCacheFiles['local_notifications.json'] as $fpath) {
            $notices = json_decode((string)@file_get_contents($fpath), true);
            if (is_array($notices)) {
                foreach ($notices as $n) {
                    if (!empty($n['title'])) {
                        $checkNotice->execute([$n['title']]);
                        if ((int)$checkNotice->fetchColumn() === 0) {
                            $insertNotice->execute([
                                $n['title'],
                                $n['description'] ?? '',
                                $n['category'] ?? 'General',
                                $n['creator_email'] ?? 'admin@ccsdu.in',
                                $n['file_path'] ?? null,
                                $n['file_url'] ?? null,
                                !empty($n['is_pinned']) ? 1 : 0,
                                !empty($n['is_new']) ? 1 : 0,
                                $n['new_until'] ?? null,
                                $n['createdAt'] ?? date('Y-m-d H:i:s'),
                                $n['updatedAt'] ?? date('Y-m-d H:i:s')
                            ]);
                            $results['notices_synced']++;
                        }
                    }
                }
            }
        }
    }

    // 5. Query final counts from MySQL
    $stmtUsers = $pdo->query("SELECT id, name, email, role, status, createdAt FROM admin_users ORDER BY id ASC");
    $allDbUsers = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

    $stmtNotices = $pdo->query("SELECT COUNT(*) FROM notifications");
    $totalDbNotices = (int)$stmtNotices->fetchColumn();

    $stmtLogs = $pdo->query("SELECT COUNT(*) FROM audit_logs");
    $totalDbLogs = (int)$stmtLogs->fetchColumn();

} catch (Throwable $e) {
    $results['errors'][] = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Synchronization Status - CCSA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen py-10 px-4 font-sans">
    <div class="max-w-3xl mx-auto space-y-6">
        
        <!-- Header -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>
                    <h1 class="text-xl sm:text-2xl font-black tracking-wide text-white">Database Auto-Sync Engine</h1>
                </div>
                <p class="text-xs text-slate-400">Centre for Computer Science and Applications, Dibrugarh University</p>
            </div>
            <a href="index.php" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow">
                Open Admin Suite →
            </a>
        </div>

        <!-- Connection Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-indigo-400">Database Connection Status</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-slate-950/80 p-4 rounded-2xl border border-slate-800">
                    <p class="text-[11px] text-slate-500 uppercase font-semibold">MySQL Status</p>
                    <p class="text-base font-bold <?php echo $results['db_connected'] ? 'text-emerald-400' : 'text-red-400'; ?> flex items-center gap-1.5 mt-1">
                        <i class="fas <?php echo $results['db_connected'] ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                        <span><?php echo $results['db_connected'] ? 'Connected' : 'Failed'; ?></span>
                    </p>
                </div>
                <div class="bg-slate-950/80 p-4 rounded-2xl border border-slate-800">
                    <p class="text-[11px] text-slate-500 uppercase font-semibold">Active Database</p>
                    <p class="text-base font-bold text-white mt-1"><?php echo htmlspecialchars($results['db_name'] ?: 'None'); ?></p>
                </div>
                <div class="bg-slate-950/80 p-4 rounded-2xl border border-slate-800">
                    <p class="text-[11px] text-slate-500 uppercase font-semibold">Total Notices in MySQL</p>
                    <p class="text-base font-bold text-[#fbbf24] mt-1"><?php echo $totalDbNotices ?? 0; ?></p>
                </div>
            </div>

            <?php if (!empty($results['errors'])): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-300 p-4 rounded-2xl text-xs space-y-1">
                <p class="font-bold">Errors encountered:</p>
                <?php foreach ($results['errors'] as $err): ?>
                    <p>• <?php echo htmlspecialchars($err); ?></p>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Synced Administrator Accounts Table -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <div class="flex justify-between items-center">
                <h2 class="text-sm font-bold uppercase tracking-wider text-indigo-400">
                    Admin Accounts in MySQL (<?php echo count($allDbUsers ?? []); ?>)
                </h2>
                <span class="text-[11px] text-emerald-400 font-semibold bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-0.5 rounded-full">
                    Synced &amp; Ready
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/80 text-slate-400 uppercase text-[10px] font-bold border-b border-slate-800">
                        <tr>
                            <th class="p-3">ID</th>
                            <th class="p-3">Name</th>
                            <th class="p-3">Email</th>
                            <th class="p-3">Role</th>
                            <th class="p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        <?php if (!empty($allDbUsers)): ?>
                            <?php foreach ($allDbUsers as $u): ?>
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="p-3 font-mono text-slate-500">#<?php echo $u['id']; ?></td>
                                <td class="p-3 font-bold text-white"><?php echo htmlspecialchars($u['name']); ?></td>
                                <td class="p-3 text-indigo-300 font-mono"><?php echo htmlspecialchars($u['email']); ?></td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?php echo $u['role'] === 'super_admin' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-blue-500/20 text-blue-300 border border-blue-500/30'; ?>">
                                        <?php echo htmlspecialchars($u['role']); ?>
                                    </span>
                                </td>
                                <td class="p-3">
                                    <span class="text-emerald-400 font-semibold">● Active</span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="p-4 text-center text-slate-500">No accounts loaded.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action Card -->
        <div class="bg-gradient-to-r from-indigo-900/40 via-slate-900 to-blue-900/40 border border-indigo-500/30 rounded-3xl p-6 text-center space-y-3">
            <h3 class="text-base font-bold text-white">Need to Re-Run Synchronization?</h3>
            <p class="text-xs text-slate-300 max-w-xl mx-auto">
                Any future accounts or notices added anywhere on the portal automatically sync directly into your MySQL database on every action.
            </p>
            <div class="pt-2 flex justify-center gap-3">
                <a href="sync_db.php?key=786004@Ccs" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition shadow cursor-pointer">
                    <i class="fas fa-sync-alt mr-1"></i> Re-Sync Database Now
                </a>
                <a href="index.php" class="bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition border border-slate-700">
                    Go to Admin Dashboard
                </a>
            </div>
        </div>

    </div>
</body>
</html>
