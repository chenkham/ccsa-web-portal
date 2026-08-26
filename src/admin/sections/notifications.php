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
                            <form action="/admin/actions.php" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this notification?');">
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

        <form action="/admin/actions.php" method="POST" enctype="multipart/form-data" class="space-y-4">
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

            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/90 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-xs font-bold text-slate-800 cursor-pointer select-none">
                        <input type="checkbox" name="show_new" id="showNewToggle" value="1" checked onchange="document.getElementById('newDateContainer').style.display = this.checked ? 'block' : 'none'" class="rounded text-red-600 focus:ring-red-500 w-4 h-4 cursor-pointer">
                        <span class="text-red-600 font-extrabold uppercase text-[10px] bg-red-100 px-1.5 py-0.5 rounded">NEW</span>
                        <span>Display 'NEW' Badge</span>
                    </label>
                </div>

                <div id="newDateContainer" class="pt-1 space-y-2.5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide">
                            Badge Expiry Date:
                        </label>
                        <span id="addDatePreview" class="text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200/80 px-2.5 py-0.5 rounded-lg inline-flex items-center gap-1.5 w-fit">
                            <i class="far fa-calendar-check text-indigo-500"></i>
                            <span id="addDatePreviewText">Expires in 15 days</span>
                        </span>
                    </div>

                    <!-- Quick Duration Presets -->
                    <div>
                        <span class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5">Quick Presets:</span>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" onclick="setNewBadgeDuration(7, 'add')" class="add-preset-btn px-2.5 py-1 text-[11px] font-bold rounded-lg border border-slate-200 bg-white hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-300 transition-all cursor-pointer shadow-xs">
                                +7 Days
                            </button>
                            <button type="button" onclick="setNewBadgeDuration(15, 'add')" class="add-preset-btn px-2.5 py-1 text-[11px] font-bold rounded-lg border border-indigo-500 bg-indigo-50 text-indigo-700 transition-all cursor-pointer shadow-xs">
                                +15 Days (Default)
                            </button>
                            <button type="button" onclick="setNewBadgeDuration(30, 'add')" class="add-preset-btn px-2.5 py-1 text-[11px] font-bold rounded-lg border border-slate-200 bg-white hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-300 transition-all cursor-pointer shadow-xs">
                                +30 Days (1 Mo)
                            </button>
                            <button type="button" onclick="setNewBadgeDuration(60, 'add')" class="add-preset-btn px-2.5 py-1 text-[11px] font-bold rounded-lg border border-slate-200 bg-white hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-300 transition-all cursor-pointer shadow-xs">
                                +60 Days (2 Mo)
                            </button>
                            <button type="button" onclick="setNewBadgeDuration(90, 'add')" class="add-preset-btn px-2.5 py-1 text-[11px] font-bold rounded-lg border border-slate-200 bg-white hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-300 transition-all cursor-pointer shadow-xs">
                                +90 Days (3 Mo)
                            </button>
                        </div>
                    </div>

                    <!-- Direct Day / Month / Year Dropdowns -->
                    <div>
                        <span class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Select Exact Date:</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-[10px] text-slate-400 font-semibold mb-0.5">Day</label>
                                <select id="addDaySelect" onchange="syncDateFromDropdowns('add')" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                                    <?php for ($d = 1; $d <= 31; $d++): ?>
                                        <option value="<?php echo sprintf('%02d', $d); ?>"><?php echo $d; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-400 font-semibold mb-0.5">Month</label>
                                <select id="addMonthSelect" onchange="syncDateFromDropdowns('add')" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                                    <?php 
                                    $months = ['01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December'];
                                    foreach ($months as $num => $name): ?>
                                        <option value="<?php echo $num; ?>"><?php echo $name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-400 font-semibold mb-0.5">Year</label>
                                <select id="addYearSelect" onchange="syncDateFromDropdowns('add')" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                                    <?php 
                                    $currentYear = (int)date('Y');
                                    for ($y = $currentYear; $y <= $currentYear + 5; $y++): ?>
                                        <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden ISO Date Input submitted with form -->
                    <input type="hidden" name="new_until" id="addNewUntilInput" value="<?php echo date('Y-m-d', strtotime('+15 days')); ?>">
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

        <form action="/admin/actions.php" method="POST" enctype="multipart/form-data" class="space-y-4">
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

            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/90 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-xs font-bold text-slate-800 cursor-pointer select-none">
                        <input type="checkbox" name="show_new" id="editShowNewToggle" value="1" onchange="document.getElementById('editNewDateContainer').style.display = this.checked ? 'block' : 'none'" class="rounded text-red-600 focus:ring-red-500 w-4 h-4 cursor-pointer">
                        <span class="text-red-600 font-extrabold uppercase text-[10px] bg-red-100 px-1.5 py-0.5 rounded">NEW</span>
                        <span>Display 'NEW' Badge</span>
                    </label>
                </div>

                <div id="editNewDateContainer" class="pt-1 space-y-2.5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wide">
                            Badge Expiry Date:
                        </label>
                        <span id="editDatePreview" class="text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200/80 px-2.5 py-0.5 rounded-lg inline-flex items-center gap-1.5 w-fit">
                            <i class="far fa-calendar-check text-indigo-500"></i>
                            <span id="editDatePreviewText">Expires in 15 days</span>
                        </span>
                    </div>

                    <!-- Quick Duration Presets -->
                    <div>
                        <span class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5">Quick Presets:</span>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" onclick="setNewBadgeDuration(7, 'edit')" class="edit-preset-btn px-2.5 py-1 text-[11px] font-bold rounded-lg border border-slate-200 bg-white hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-300 transition-all cursor-pointer shadow-xs">
                                +7 Days
                            </button>
                            <button type="button" onclick="setNewBadgeDuration(15, 'edit')" class="edit-preset-btn px-2.5 py-1 text-[11px] font-bold rounded-lg border border-indigo-500 bg-indigo-50 text-indigo-700 transition-all cursor-pointer shadow-xs">
                                +15 Days
                            </button>
                            <button type="button" onclick="setNewBadgeDuration(30, 'edit')" class="edit-preset-btn px-2.5 py-1 text-[11px] font-bold rounded-lg border border-slate-200 bg-white hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-300 transition-all cursor-pointer shadow-xs">
                                +30 Days (1 Mo)
                            </button>
                            <button type="button" onclick="setNewBadgeDuration(60, 'edit')" class="edit-preset-btn px-2.5 py-1 text-[11px] font-bold rounded-lg border border-slate-200 bg-white hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-300 transition-all cursor-pointer shadow-xs">
                                +60 Days (2 Mo)
                            </button>
                            <button type="button" onclick="setNewBadgeDuration(90, 'edit')" class="edit-preset-btn px-2.5 py-1 text-[11px] font-bold rounded-lg border border-slate-200 bg-white hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-300 transition-all cursor-pointer shadow-xs">
                                +90 Days (3 Mo)
                            </button>
                        </div>
                    </div>

                    <!-- Direct Day / Month / Year Dropdowns -->
                    <div>
                        <span class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Select Exact Date:</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-[10px] text-slate-400 font-semibold mb-0.5">Day</label>
                                <select id="editDaySelect" onchange="syncDateFromDropdowns('edit')" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                                    <?php for ($d = 1; $d <= 31; $d++): ?>
                                        <option value="<?php echo sprintf('%02d', $d); ?>"><?php echo $d; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-400 font-semibold mb-0.5">Month</label>
                                <select id="editMonthSelect" onchange="syncDateFromDropdowns('edit')" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                                    <?php 
                                    $months = ['01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December'];
                                    foreach ($months as $num => $name): ?>
                                        <option value="<?php echo $num; ?>"><?php echo $name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-400 font-semibold mb-0.5">Year</label>
                                <select id="editYearSelect" onchange="syncDateFromDropdowns('edit')" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                                    <?php 
                                    $currentYear = (int)date('Y');
                                    for ($y = $currentYear; $y <= $currentYear + 5; $y++): ?>
                                        <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden ISO Date Input submitted with form -->
                    <input type="hidden" name="new_until" id="editNewUntilInput" value="<?php echo date('Y-m-d', strtotime('+15 days')); ?>">
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
const MONTH_NAMES = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

function formatISODate(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
}

function updateDatePreview(mode, date) {
    const previewTextEl = document.getElementById(`${mode}DatePreviewText`);
    if (!previewTextEl) return;

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const target = new Date(date);
    target.setHours(0, 0, 0, 0);

    const diffDays = Math.round((target - today) / (1000 * 60 * 60 * 24));
    const dayStr = target.getDate();
    const monthStr = MONTH_NAMES[target.getMonth()];
    const yearStr = target.getFullYear();

    let text = `${dayStr} ${monthStr} ${yearStr}`;
    if (diffDays > 0) {
        text += ` (in ${diffDays} days)`;
    } else if (diffDays === 0) {
        text += ` (Expires today)`;
    } else {
        text += ` (Expired)`;
    }

    previewTextEl.textContent = text;
}

function updateDateDropdowns(mode, isoDateStr) {
    if (!isoDateStr) return;
    const parts = isoDateStr.split('-');
    if (parts.length !== 3) return;

    const year = parts[0];
    const month = parts[1];
    const day = parts[2];

    const daySelect = document.getElementById(`${mode}DaySelect`);
    const monthSelect = document.getElementById(`${mode}MonthSelect`);
    const yearSelect = document.getElementById(`${mode}YearSelect`);
    const hiddenInput = document.getElementById(`${mode}NewUntilInput`);

    if (daySelect) daySelect.value = day;
    if (monthSelect) monthSelect.value = month;
    if (yearSelect) yearSelect.value = year;
    if (hiddenInput) hiddenInput.value = isoDateStr;

    const dateObj = new Date(parseInt(year, 10), parseInt(month, 10) - 1, parseInt(day, 10));
    updateDatePreview(mode, dateObj);
}

function syncDateFromDropdowns(mode) {
    const daySelect = document.getElementById(`${mode}DaySelect`);
    const monthSelect = document.getElementById(`${mode}MonthSelect`);
    const yearSelect = document.getElementById(`${mode}YearSelect`);
    const hiddenInput = document.getElementById(`${mode}NewUntilInput`);

    if (!daySelect || !monthSelect || !yearSelect || !hiddenInput) return;

    const day = daySelect.value;
    const month = monthSelect.value;
    const year = yearSelect.value;

    const isoStr = `${year}-${month}-${day}`;
    hiddenInput.value = isoStr;

    // Reset active preset button styling
    document.querySelectorAll(`.${mode}-preset-btn`).forEach(btn => {
        btn.classList.remove('border-indigo-500', 'bg-indigo-50', 'text-indigo-700');
        btn.classList.add('border-slate-200', 'bg-white', 'text-slate-700');
    });

    const dateObj = new Date(parseInt(year, 10), parseInt(month, 10) - 1, parseInt(day, 10));
    updateDatePreview(mode, dateObj);
}

function setNewBadgeDuration(days, mode) {
    const targetDate = new Date();
    targetDate.setDate(targetDate.getDate() + days);

    const isoStr = formatISODate(targetDate);
    updateDateDropdowns(mode, isoStr);

    // Update active preset button highlight
    document.querySelectorAll(`.${mode}-preset-btn`).forEach(btn => {
        if (btn.textContent.includes(`+${days}`)) {
            btn.classList.add('border-indigo-500', 'bg-indigo-50', 'text-indigo-700');
            btn.classList.remove('border-slate-200', 'bg-white');
        } else {
            btn.classList.remove('border-indigo-500', 'bg-indigo-50', 'text-indigo-700');
            btn.classList.add('border-slate-200', 'bg-white');
        }
    });
}

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

    let isNewChecked = false;
    if (notice.is_new !== undefined && notice.is_new !== null) {
        isNewChecked = (notice.is_new == 1 || notice.is_new === true || notice.is_new === '1');
    } else if (notice.new_until) {
        isNewChecked = (new Date(notice.new_until + 'T23:59:59') >= new Date());
    }
    
    document.getElementById('editShowNewToggle').checked = isNewChecked;
    
    const until = notice.new_until || '<?php echo date("Y-m-d", strtotime("+15 days")); ?>';
    updateDateDropdowns('edit', until);
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

// Initialize Add Notice date defaults on page load
document.addEventListener('DOMContentLoaded', () => {
    const defaultAddUntil = '<?php echo date("Y-m-d", strtotime("+15 days")); ?>';
    updateDateDropdowns('add', defaultAddUntil);
});
</script>
