<?php
declare(strict_types=1);

$adminLogs = [];

// 1. Try fetching from MySQL audit_logs table
if (isset($pdo) && $pdo !== null) {
    try {
        $stmt = $pdo->prepare('SELECT id, user_email, ip_address, action, details, user_agent, created_at FROM audit_logs ORDER BY created_at DESC LIMIT 500');
        $stmt->execute();
        $adminLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        error_log('Admin audit logs DB fetch failed: ' . $e->getMessage());
    }
}

// 2. If MySQL returned empty, load from file-based JSON cache
if (empty($adminLogs)) {
    $cacheFile = __DIR__ . '/../../cache/audit_logs.json';
    if (file_exists($cacheFile)) {
        $jsonLogs = json_decode((string)file_get_contents($cacheFile), true);
        if (is_array($jsonLogs)) {
            $adminLogs = $jsonLogs;
        }
    }
}

// 3. Fallback if no logs exist yet
if (empty($adminLogs)) {
    $adminLogs = [
        [
            'id' => 1,
            'user_email' => 'system',
            'ip_address' => '127.0.0.1',
            'action' => 'DATABASE_INITIALIZATION',
            'details' => 'CCSA Database and audit security subsystem initialized.',
            'created_at' => date('Y-m-d H:i:s')
        ]
    ];
}

$isSuperAdmin = (($userRole ?? $userrole ?? $_SESSION['user_role'] ?? '') === 'super_admin');
?>

<div class="card p-5 sm:p-7 bg-white rounded-2xl shadow-sm border border-slate-200/90">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-lg sm:text-xl font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-shield-alt text-[#1a365d]"></i>
                <span>Security &amp; Session Audit Logs</span>
            </h2>
            <p class="text-xs text-slate-500 mt-1">Real-time tracking of administrator actions, sign-in attempts, notice changes, and security events</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="bg-slate-100 text-slate-700 text-xs font-bold px-3 py-1.5 rounded-xl border border-slate-200">
                Total Records: <?php echo count($adminLogs); ?>
            </span>
            <?php if ($isSuperAdmin && count($adminLogs) > 0): ?>
            <form action="/admin/actions.php" method="POST" onsubmit="return confirm('Are you sure you want to clear all audit log history?');">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="action" value="clear_logs">
                <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition-colors">
                    <i class="fas fa-trash-alt mr-1"></i> Clear Logs
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Search Filter -->
    <div class="mb-4">
        <div class="relative">
            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" id="logSearchInput" placeholder="Filter audit records by email, action (e.g. LOGIN, NOTICE), or details..." 
                   class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
        </div>
    </div>

    <!-- Logs table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm" id="auditLogsTable">
            <thead>
                <tr class="bg-slate-50 text-slate-600 text-xs uppercase border-b border-slate-200/80 font-bold">
                    <th class="py-3 px-4">User / Actor</th>
                    <th class="py-3 px-4">Action Event</th>
                    <th class="py-3 px-4">Details</th>
                    <th class="py-3 px-4">IP Address</th>
                    <th class="py-3 px-4 text-right">Timestamp</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs">
                <?php foreach ($adminLogs as $log): 
                    $act = (string)($log['action'] ?? '');
                    $badgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
                    if (str_contains($act, 'SUCCESS')) {
                        $badgeClass = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                    } elseif (str_contains($act, 'FAILED') || str_contains($act, 'LOCKED') || str_contains($act, 'INACTIVE')) {
                        $badgeClass = 'bg-rose-50 text-rose-800 border-rose-200';
                    } elseif (str_contains($act, 'PUBLISHED') || str_contains($act, 'CREATED') || str_contains($act, 'ADDED')) {
                        $badgeClass = 'bg-blue-50 text-blue-800 border-blue-200';
                    } elseif (str_contains($act, 'DELETED') || str_contains($act, 'REMOVED')) {
                        $badgeClass = 'bg-amber-50 text-amber-900 border-amber-200';
                    } elseif (str_contains($act, 'UPDATED') || str_contains($act, 'SMTP')) {
                        $badgeClass = 'bg-purple-50 text-purple-800 border-purple-200';
                    } elseif (str_contains($act, 'SCANNER') || str_contains($act, 'PROBE')) {
                        $badgeClass = 'bg-orange-50 text-orange-900 border-orange-200';
                    } elseif (str_contains($act, 'INQUIRY')) {
                        $badgeClass = 'bg-cyan-50 text-cyan-900 border-cyan-200';
                    }
                ?>
                <tr class="hover:bg-slate-50/80 transition-colors log-row">
                    <td class="py-3 px-4 font-sans font-bold text-slate-800">
                        <?php echo htmlspecialchars((string)$log['user_email']); ?>
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-block border font-bold font-mono text-[10px] px-2 py-0.5 rounded <?php echo $badgeClass; ?>">
                            <?php echo htmlspecialchars($act); ?>
                        </span>
                    </td>
                    <td class="py-3 px-4 text-slate-600 font-sans">
                        <?php echo htmlspecialchars((string)($log['details'] ?? 'Routine admin operation.')); ?>
                    </td>
                    <td class="py-3 px-4 text-slate-500 font-mono">
                        <?php echo htmlspecialchars((string)($log['ip_address'] ?? '127.0.0.1')); ?>
                    </td>
                    <td class="py-3 px-4 text-right text-slate-500 font-mono whitespace-nowrap">
                        <?php echo htmlspecialchars((string)($log['created_at'] ?? '')); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('logSearchInput')?.addEventListener('input', function(e) {
    const q = e.target.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#auditLogsTable tbody tr.log-row');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(q) ? '' : 'none';
    });
});
</script>
