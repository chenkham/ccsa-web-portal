<?php
declare(strict_types=1);

$adminMessages = [];
if (isset($pdo) && $pdo !== null) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM contact_messages ORDER BY createdAt DESC LIMIT 100');
        $stmt->execute();
        $adminMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        error_log('Admin messages DB fetch failed: ' . $e->getMessage());
    }
}

$fallbackFile = __DIR__ . '/../../cache/messages_fallback.json';
if (file_exists($fallbackFile)) {
    $fallbackMessages = json_decode((string)file_get_contents($fallbackFile), true) ?: [];
    if (!empty($fallbackMessages)) {
        $seenEmailsAndTimes = array_map(function($m) {
            return ($m['email'] ?? '') . '_' . ($m['createdAt'] ?? '');
        }, $adminMessages);
        foreach ($fallbackMessages as $fm) {
            $key = ($fm['email'] ?? '') . '_' . ($fm['createdAt'] ?? '');
            if (!in_array($key, $seenEmailsAndTimes, true)) {
                $adminMessages[] = $fm;
            }
        }
    }
}
?>

<div class="card p-5 sm:p-7 bg-white rounded-2xl shadow-sm border border-slate-200/90">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-lg sm:text-xl font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-envelope-open-text text-[#1a365d]"></i>
                <span>Public Inquiries &amp; Contact Messages</span>
            </h2>
            <p class="text-xs text-slate-500 mt-1">Inquiries submitted via the portal Contact Us form on the homepage</p>
        </div>
        <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-3 py-1.5 rounded-xl border border-indigo-100">
            Total Messages: <?php echo count($adminMessages); ?>
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-slate-50 text-slate-600 text-xs uppercase border-b border-slate-200/80 font-bold">
                    <th class="py-3 px-4">Sender &amp; Email</th>
                    <th class="py-3 px-4">Message Body</th>
                    <th class="py-3 px-4">Date &amp; IP</th>
                    <th class="py-3 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($adminMessages)): ?>
                <tr>
                    <td colspan="4" class="py-10 text-center text-slate-400 italic">
                        <i class="fas fa-inbox text-3xl mb-2 text-slate-300 block"></i>
                        No contact inquiries received yet.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($adminMessages as $msg): ?>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-4">
                            <p class="font-bold text-slate-800 text-sm"><?php echo htmlspecialchars($msg['name']); ?></p>
                            <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" class="text-xs text-indigo-600 hover:underline font-mono">
                                <?php echo htmlspecialchars($msg['email']); ?>
                            </a>
                        </td>
                        <td class="py-3.5 px-4 text-xs text-slate-700 max-w-md">
                            <p class="line-clamp-3 leading-relaxed"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                        </td>
                        <td class="py-3.5 px-4 text-xs text-slate-500 whitespace-nowrap">
                            <p class="font-semibold text-slate-700"><?php echo htmlspecialchars(substr($msg['createdAt'], 0, 16)); ?></p>
                            <p class="text-[10px] text-slate-400 font-mono"><?php echo htmlspecialchars($msg['ip_address'] ?? '127.0.0.1'); ?></p>
                        </td>
                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                            <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>?subject=<?php echo urlencode('Re: CCSA Dibrugarh University Inquiry'); ?>" 
                                class="text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold px-3 py-1.5 rounded-lg transition-colors mr-2 inline-flex items-center gap-1.5">
                                <i class="fas fa-reply text-[11px]"></i>
                                <span>Reply</span>
                            </a>
                            <form action="actions.php" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <input type="hidden" name="action" value="delete_message">
                                <input type="hidden" name="id" value="<?php echo $msg['id']; ?>">
                                <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-semibold px-2.5 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                    <i class="fas fa-trash-alt mr-1"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
