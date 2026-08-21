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

$localNoticeFile = __DIR__ . '/../../cache/local_notifications.json';
if (file_exists($localNoticeFile)) {
    $fallbackNotices = json_decode((string)file_get_contents($localNoticeFile), true) ?: [];
    if (!empty($fallbackNotices)) {
        $seenTitles = array_map(function($n) {
            return ($n['title'] ?? '') . '_' . ($n['createdAt'] ?? '');
        }, $adminNotices);
        foreach ($fallbackNotices as $fn) {
            $key = ($fn['title'] ?? '') . '_' . ($fn['createdAt'] ?? '');
            if (!in_array($key, $seenTitles, true)) {
                $adminNotices[] = $fn;
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
                    <?php foreach ($adminNotices as $notice): 
                        $isPinned = !empty($notice['is_pinned']);
                        $isNewActive = false;
                        $daysLeft = 0;
                        
                        $isNewFlag = isset($notice['is_new']) ? (int)$notice['is_new'] : null;
                        $newUntil = !empty($notice['new_until']) ? $notice['new_until'] : null;

                        if ($isNewFlag === 0) {
                            $isNewActive = false;
                        } elseif (!empty($newUntil)) {
                            $expiryTime = strtotime($newUntil . ' 23:59:59');
                            $diffSec = $expiryTime - time();
                            if ($diffSec > 0) {
                                $isNewActive = true;
                                $daysLeft = (int)ceil($diffSec / 86400);
                            }
                        } elseif ($isNewFlag === 1) {
                            $isNewActive = true;
                            if (!empty($notice['createdAt'])) {
                                $diffSec = (strtotime($notice['createdAt']) + (15 * 86400)) - time();
                                $daysLeft = max(1, (int)ceil($diffSec / 86400));
                            } else {
                                $daysLeft = 15;
                            }
                        }
                    ?>
                    <tr class="hover:bg-slate-50 transition-colors <?php echo $isPinned ? 'bg-amber-50/30' : ''; ?>">
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2">
                                <?php if ($isPinned): ?>
                                    <i class="fas fa-thumbtack text-amber-500 text-xs shrink-0 -rotate-45" title="Pinned Announcement"></i>
                                <?php endif; ?>
                                <?php if ($isNewActive): ?>
                                    <span class="bg-red-600 text-white text-[9px] font-black uppercase px-2 py-0.5 rounded-full shadow-sm shrink-0 tracking-wider inline-flex items-center gap-1.5" title="Badge valid for <?php echo $daysLeft; ?> more day<?php echo $daysLeft === 1 ? '' : 's'; ?>">
                                        <span class="animate-pulse">NEW</span>
                                        <span class="bg-red-800/80 px-1 py-0.2 rounded text-[8px] font-mono font-semibold"><?php echo $daysLeft === 1 ? '1d left' : "{$daysLeft}d left"; ?></span>
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
                            <button type="button" onclick='openEditNoticeModal(<?php echo json_encode($notice, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)' 
                                class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold px-2.5 py-1.5 rounded-lg hover:bg-indigo-50 transition-colors mr-1">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </button>
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

<!-- Publish Notice Modal -->
<div id="addNoticeModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative border border-slate-100 max-h-[90vh] overflow-y-auto">
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

            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/90 space-y-2.5">
                <label class="flex items-center gap-2 text-xs font-bold text-slate-800 cursor-pointer">
                    <input type="checkbox" name="show_new" id="showNewToggle" value="1" checked onchange="document.getElementById('newDateContainer').style.display = this.checked ? 'block' : 'none'" class="rounded text-red-600 focus:ring-red-500 w-4 h-4">
                    <span class="text-red-600 font-extrabold uppercase text-[10px] bg-red-100 px-1.5 py-0.5 rounded">NEW</span>
                    <span>Display 'NEW' Badge</span>
                </label>
                <div id="newDateContainer" class="pt-1">
                    <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Badge Valid Until (Exact Expiry Date):</label>
                    <input type="date" name="new_until" value="<?php echo date('Y-m-d', strtotime('+15 days')); ?>" min="<?php echo date('Y-m-d'); ?>"
                        class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-red-500">
                    <p class="text-[10px] text-slate-400 mt-1">The red NEW badge will automatically disappear from the website after this date.</p>
                </div>
            </div>

            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200/80">
                <label class="flex items-center gap-2 text-xs font-bold text-amber-900 cursor-pointer">
                    <input type="checkbox" name="is_pinned" value="1" class="rounded text-amber-600 focus:ring-amber-500 w-4 h-4">
                    <i class="fas fa-thumbtack text-amber-600 -rotate-45"></i>
                    <span>Pin to Top (Priority Announcement)</span>
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

<!-- Edit Notice Modal -->
<div id="editNoticeModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative border border-slate-100 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6 pb-3 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-edit text-indigo-600"></i>
                <span>Edit Announcement</span>
            </h3>
            <button onclick="document.getElementById('editNoticeModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl">&times;</button>
        </div>

        <form action="actions.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="action" value="edit_notification">
            <input type="hidden" name="id" id="editNoticeId">
            <input type="hidden" name="existing_file_path" id="editExistingFilePath">

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Notice Title *</label>
                <input type="text" name="title" id="editNoticeTitle" required 
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Description / Details</label>
                <textarea name="description" id="editNoticeDesc" rows="3" 
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Replace Document (.PDF, .DOCX)</label>
                <input type="file" name="notice_file" accept=".pdf,.doc,.docx,.jpg,.png" 
                    class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <p id="editCurrentFileDisplay" class="text-[11px] text-indigo-600 mt-1 font-medium"></p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Or External Link (Google Drive / URL)</label>
                <input type="url" name="file_url" id="editNoticeFileUrl" placeholder="https://drive.google.com/..." 
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/90 space-y-2.5">
                <label class="flex items-center gap-2 text-xs font-bold text-slate-800 cursor-pointer">
                    <input type="checkbox" name="show_new" id="editShowNewToggle" value="1" onchange="document.getElementById('editNewDateContainer').style.display = this.checked ? 'block' : 'none'" class="rounded text-red-600 focus:ring-red-500 w-4 h-4">
                    <span class="text-red-600 font-extrabold uppercase text-[10px] bg-red-100 px-1.5 py-0.5 rounded">NEW</span>
                    <span>Display 'NEW' Badge</span>
                </label>
                <div id="editNewDateContainer" class="pt-1">
                    <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Badge Valid Until (Exact Expiry Date):</label>
                    <input type="date" name="new_until" id="editNewUntilInput" min="<?php echo date('Y-m-d'); ?>"
                        class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-red-500">
                    <p class="text-[10px] text-slate-400 mt-1">Uncheck the box above or change the date to remove or extend the NEW badge.</p>
                </div>
            </div>

            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200/80">
                <label class="flex items-center gap-2 text-xs font-bold text-amber-900 cursor-pointer">
                    <input type="checkbox" name="is_pinned" id="editIsPinned" value="1" class="rounded text-amber-600 focus:ring-amber-500 w-4 h-4">
                    <i class="fas fa-thumbtack text-amber-600 -rotate-45"></i>
                    <span>Pin to Top (Priority Announcement)</span>
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('editNoticeModal').classList.add('hidden')" 
                    class="px-4 py-2.5 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                    class="px-5 py-2.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow hover:shadow-md transition-colors flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Save Changes</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditNoticeModal(notice) {
    document.getElementById('editNoticeId').value = notice.id || '';
    document.getElementById('editNoticeTitle').value = notice.title || '';
    document.getElementById('editNoticeDesc').value = notice.description || '';
    document.getElementById('editNoticeFileUrl').value = notice.file_url || '';
    document.getElementById('editExistingFilePath').value = notice.file_path || '';
    
    const fileDisplay = document.getElementById('editCurrentFileDisplay');
    if (notice.file_path) {
        fileDisplay.textContent = 'Current file: ' + notice.file_path;
    } else {
        fileDisplay.textContent = 'No current document attached';
    }

    document.getElementById('editIsPinned').checked = (notice.is_pinned == 1 || notice.is_pinned === true || notice.is_pinned === '1');

    // Strict boolean check for is_new
    let isNewChecked = false;
    if (notice.is_new !== undefined && notice.is_new !== null) {
        isNewChecked = (notice.is_new == 1 || notice.is_new === true || notice.is_new === '1');
    } else if (notice.new_until) {
        isNewChecked = (new Date(notice.new_until + 'T23:59:59') >= new Date());
    }
    
    document.getElementById('editShowNewToggle').checked = isNewChecked;
    
    const until = notice.new_until || '<?php echo date("Y-m-d", strtotime("+15 days")); ?>';
    document.getElementById('editNewUntilInput').value = until;
    document.getElementById('editNewDateContainer').style.display = isNewChecked ? 'block' : 'none';

    document.getElementById('editNoticeModal').classList.remove('hidden');
}

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
