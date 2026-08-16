<?php
declare(strict_types=1);

$adminNotices = [];
if (isset($pdo) && $pdo !== null) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM notifications ORDER BY is_pinned DESC, createdAt DESC LIMIT 100');
        $stmt->execute();
        $adminNotices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        error_log('Admin notices DB fetch failed: ' . $e->getMessage());
    }
}

if (empty($adminNotices)) {
    $docsDir = __DIR__ . '/../uploads/notification_docs';
    if (is_dir($docsDir)) {
        $files = scandir($docsDir);
        $idx = 1;
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && $file !== '.htaccess' && pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
                $cleanTitle = preg_replace('/^[a-f0-9]+_/', '', pathinfo($file, PATHINFO_FILENAME));
                $cleanTitle = str_replace('_', ' ', $cleanTitle);
                $fileTime = date('Y-m-d H:i:s', filemtime($docsDir . '/' . $file));
                $adminNotices[] = [
                    'id' => $idx++,
                    'title' => $cleanTitle,
                    'description' => 'Official circular document published on portal.',
                    'file_path' => 'uploads/notification_docs/' . $file,
                    'file_url' => '',
                    'is_pinned' => 0,
                    'createdAt' => $fileTime
                ];
            }
        }
    }
}
?>

<div class="card p-5 sm:p-7 bg-white rounded-2xl shadow-sm border border-slate-200/90">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-lg sm:text-xl font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-bullhorn text-[#1a365d]"></i>
                <span>Notice Board &amp; Announcements</span>
            </h2>
            <p class="text-xs text-slate-500 mt-1">Publish new circulars, examination notices, and upload PDF attachments</p>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64">
                <i class="fas fa-search absolute left-3 top-3 text-slate-400 text-xs"></i>
                <input type="text" id="searchNoticeInput" onkeyup="filterNoticesTable()" placeholder="Search notices..." 
                    class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button onclick="document.getElementById('addNoticeModal').classList.remove('hidden')" 
                class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl flex items-center gap-1.5 transition-all shadow hover:shadow-md whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Publish Notice</span>
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm" id="noticesTable">
            <thead>
                <tr class="bg-slate-50 text-slate-600 text-xs uppercase border-b border-slate-200/80 font-bold">
                    <th class="py-3 px-4">Title &amp; Status</th>
                    <th class="py-3 px-4">Attached Document</th>
                    <th class="py-3 px-4">Date</th>
                    <th class="py-3 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($adminNotices)): ?>
                <tr>
                    <td colspan="4" class="py-8 text-center text-slate-400 italic">No notifications published yet.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($adminNotices as $notice): ?>
                    <tr class="hover:bg-slate-50 transition-colors <?php echo !empty($notice['is_pinned']) ? 'bg-amber-50/40' : ''; ?>">
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2">
                                <?php if (!empty($notice['is_pinned'])): ?>
                                <span class="bg-amber-100 text-amber-800 text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md border border-amber-200 flex items-center gap-1">
                                    <i class="fas fa-thumbtack text-[9px]"></i> Pinned
                                </span>
                                <?php endif; ?>
                                <p class="font-bold text-slate-800 text-sm"><?php echo htmlspecialchars($notice['title']); ?></p>
                            </div>
                            <?php if (!empty($notice['description'])): ?>
                            <p class="text-xs text-slate-500 mt-0.5 line-clamp-1"><?php echo htmlspecialchars($notice['description']); ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4 text-xs whitespace-nowrap">
                            <?php if (!empty($notice['file_path'])): ?>
                                <a href="<?php echo htmlspecialchars($notice['file_path']); ?>" target="_blank" 
                                    class="inline-flex items-center gap-1.5 text-indigo-600 hover:text-indigo-800 font-semibold bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1 rounded-lg transition-colors">
                                    <i class="fas fa-file-pdf text-red-500"></i>
                                    <span>View Document</span>
                                </a>
                            <?php elseif (!empty($notice['file_url'])): ?>
                                <a href="<?php echo htmlspecialchars($notice['file_url']); ?>" target="_blank" 
                                    class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-800 font-semibold bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-lg transition-colors">
                                    <i class="fas fa-external-link-alt"></i>
                                    <span>External URL</span>
                                </a>
                            <?php else: ?>
                                <span class="text-slate-400 text-[11px]">No file attached</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4 text-xs text-slate-500 whitespace-nowrap">
                            <?php echo htmlspecialchars(substr($notice['createdAt'], 0, 10)); ?>
                        </td>
                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                            <form action="actions.php" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this notification?');">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <input type="hidden" name="action" value="delete_notification">
                                <input type="hidden" name="id" value="<?php echo $notice['id']; ?>">
                                <input type="hidden" name="file_path" value="<?php echo htmlspecialchars($notice['file_path'] ?? ''); ?>">
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

<div id="addNoticeModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative border border-slate-100">
        <div class="flex justify-between items-center mb-6 pb-3 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-bullhorn text-indigo-600"></i>
                <span>Publish New Notification</span>
            </h3>
            <button onclick="document.getElementById('addNoticeModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl">&times;</button>
        </div>

        <form action="actions.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="action" value="add_notification">

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Notice Title *</label>
                <input type="text" name="title" required placeholder="e.g. DUAT 2026 Examination Notice" 
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Description / Details</label>
                <textarea name="description" rows="3" placeholder="Brief summary of the notice..." 
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Upload Document (.PDF, .DOCX)</label>
                <input type="file" name="notice_file" accept=".pdf,.doc,.docx,.jpg,.png" 
                    class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Or External Link (Google Drive / URL)</label>
                <input type="url" name="file_url" placeholder="https://drive.google.com/..." 
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200/80">
                <label class="flex items-center gap-2 text-xs font-bold text-amber-900 cursor-pointer">
                    <input type="checkbox" name="is_pinned" value="1" class="rounded text-amber-600 focus:ring-amber-500 w-4 h-4">
                    <span>Pin to Top (Mark as Urgent / Featured Announcement)</span>
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('addNoticeModal').classList.add('hidden')" 
                    class="px-4 py-2.5 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                    class="px-5 py-2.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow hover:shadow-md transition-colors flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>Publish Announcement</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function filterNoticesTable() {
    const input = document.getElementById('searchNoticeInput');
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('#noticesTable tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
}
</script>
