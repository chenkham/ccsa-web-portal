<?php
declare(strict_types=1);

$isSuperAdmin = (($userrole ?? '') === 'super_admin');

$adminUsers = [];
if (isset($pdo) && $pdo !== null) {
    try {
        $stmt = $pdo->prepare('SELECT id, name, email, role, status, createdAt FROM admin_users ORDER BY id ASC');
        $stmt->execute();
        $adminUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        error_log('Admin users DB fetch failed: ' . $e->getMessage());
    }
}

if (empty($adminUsers)) {
    $adminUsers = [
        ['id' => 1, 'name' => 'CCSA Super Admin', 'email' => 'admin@ccsdu.in', 'role' => 'super_admin', 'status' => 'active', 'createdAt' => '2026-01-01 00:00:00']
    ];
}
?>

<div class="card p-5 sm:p-7 bg-white rounded-2xl shadow-sm border border-slate-200/90">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-lg sm:text-xl font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-user-shield text-[#1a365d]"></i>
                <span>Admin Account Controls</span>
            </h2>
            <p class="text-xs text-slate-500 mt-1">Configure administrator accounts, credentials, and permission tiers</p>
        </div>
        <?php if ($isSuperAdmin): ?>
        <button onclick="document.getElementById('addAdminModal').classList.remove('hidden')" 
            class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl flex items-center gap-2 transition-all shadow hover:shadow-md">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            <span>Add Admin User</span>
        </button>
        <?php else: ?>
        <span class="text-xs text-slate-500 bg-slate-100 px-3 py-1.5 rounded-xl font-semibold border border-slate-200">
            <i class="fas fa-lock mr-1 text-slate-400"></i> Super Admin Only
        </span>
        <?php endif; ?>
    </div>

    <!-- Admin table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-slate-50 text-slate-600 text-xs uppercase border-b border-slate-200/80 font-bold">
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Email</th>
                    <th class="py-3 px-4">Role</th>
                    <th class="py-3 px-4">Created Date</th>
                    <th class="py-3 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($adminUsers as $user): ?>
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="py-3.5 px-4 font-bold text-slate-800"><?php echo htmlspecialchars($user['name']); ?></td>
                    <td class="py-3.5 px-4 text-xs font-mono text-indigo-600"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="py-3.5 px-4">
                        <span class="<?php echo $user['role'] === 'super_admin' ? 'bg-purple-100 text-purple-800 border-purple-200' : 'bg-blue-100 text-blue-800 border-blue-200'; ?> font-bold text-[11px] px-2.5 py-0.5 rounded-full border">
                            <?php echo htmlspecialchars($user['role']); ?>
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-xs text-slate-500 whitespace-nowrap">
                        <?php echo htmlspecialchars(substr($user['createdAt'] ?? date('Y-m-d'), 0, 10)); ?>
                    </td>
                    <td class="py-3.5 px-4 text-right">
                        <?php if ($isSuperAdmin && (int)$user['id'] !== 1 && (int)$user['id'] !== (int)($_SESSION['user_id'] ?? 0)): ?>
                        <form action="actions.php" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to remove this admin account?');">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="action" value="delete_admin">
                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                            <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-semibold px-2.5 py-1 rounded-lg hover:bg-red-50 transition-colors">
                                Delete
                            </button>
                        </form>
                        <?php elseif ((int)$user['id'] === 1): ?>
                        <span class="text-[11px] text-slate-400 font-medium italic">Primary Admin</span>
                        <?php else: ?>
                        <span class="text-[11px] text-slate-400 font-medium italic">Protected</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Admin Modal -->
<div id="addAdminModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative border border-slate-100">
        <div class="flex justify-between items-center mb-6 pb-3 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-user-shield text-indigo-600"></i>
                <span>Add Admin Account</span>
            </h3>
            <button onclick="document.getElementById('addAdminModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl">&times;</button>
        </div>

        <form action="actions.php" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="action" value="add_admin">

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Admin Full Name *</label>
                <input type="text" name="name" required placeholder="e.g. Dr. John Doe" 
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Email Address *</label>
                <input type="email" name="email" required placeholder="admin.new@ccsdu.in" 
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Password *</label>
                <input type="password" name="password" required placeholder="••••••••" 
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Role Tier</label>
                <select name="role" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="admin">Admin</option>
                    <option value="editor">Editor (Notices & Students only)</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('addAdminModal').classList.add('hidden')" 
                    class="px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">Cancel</button>
                <button type="submit" 
                    class="bg-[#1a365d] hover:bg-indigo-900 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow transition-colors">
                    Create Admin
                </button>
            </div>
        </form>
    </div>
</div>
