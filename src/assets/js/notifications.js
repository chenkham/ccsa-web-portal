/**
 * Notifications Fetcher Module
 * Populates: (1) Announcement Ticker Bar, (2) Notices & Announcements grid section
 */
(() => {
  'use strict';

  const NOTIFICATIONS_URL = 'endpoints/notifications.php';
  const MONTHS_SHORT = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

  /**
   * Build the scrolling ticker bar content
   */
  const populateTicker = (notices) => {
    const tickerTrack = document.getElementById('tickerTrack');
    if (!tickerTrack) return;

    // Filter notices to ONLY include active NEW announcements
    const now = new Date();
    const activeNewNotices = (notices || []).filter(n => {
      if (n.is_new !== undefined && (n.is_new === 0 || n.is_new === false || n.is_new === '0')) {
        return false;
      }
      if (n.new_until) {
        return new Date(n.new_until + 'T23:59:59') >= now;
      }
      if (n.is_new == 1) {
        return true;
      }
      return false;
    });

    if (activeNewNotices.length === 0) {
      const defaultItem = `<a href="notices.php" class="ticker-item text-slate-700 hover:text-[#1a365d] inline-flex items-center gap-1 sm:gap-1.5 transition-colors">
        <span class="ticker-dot"></span>
        <span class="text-[11px] sm:text-xs font-semibold leading-tight">Welcome to Centre for Computer Science and Applications (CCSA), Dibrugarh University</span>
      </a>`;
      tickerTrack.innerHTML = defaultItem + defaultItem + defaultItem;
      return;
    }

    // Build ticker items HTML (duplicate for seamless loop)
    const buildItems = () => activeNewNotices.map(n => {
      const title = n.title || 'Notification';
      const d = n.createdAt ? new Date(n.createdAt) : null;
      const dateStr = d ? `${d.getDate()} ${MONTHS_SHORT[d.getMonth()]}` : '';
      const isPinned = n.is_pinned == 1;

      return `<a href="notices.php" class="ticker-item text-slate-700 hover:text-[#1a365d] inline-flex items-center gap-1 sm:gap-1.5 transition-colors">
        <span class="ticker-dot"></span>
        ${isPinned ? '<i class="fas fa-thumbtack text-amber-500 text-[9px] sm:text-[10px] -rotate-45"></i>' : ''}
        <span class="bg-red-600 text-white text-[7px] sm:text-[8px] font-black uppercase px-1 sm:px-1.5 py-0.2 rounded shadow-xs animate-pulse shrink-0">NEW</span>
        <span class="text-[11px] sm:text-xs font-semibold leading-tight">${escapeHtml(title)}</span>
        ${dateStr ? `<span class="text-[9px] sm:text-[10px] font-medium text-slate-400">(${dateStr})</span>` : ''}
      </a>`;
    }).join('');

    // Duplicate content for seamless CSS animation loop
    tickerTrack.innerHTML = buildItems() + buildItems();
  };

  /**
   * Build the notice card grid (Simple Clean Style matching reference design)
   */
  const populateNoticeGrid = (notices) => {
    const wrapper = document.getElementById('notice-wrapper');
    const noNotices = document.getElementById('no-notices');
    if (!wrapper) return;

    wrapper.innerHTML = '';

    if (!notices || notices.length === 0) {
      if (noNotices) noNotices.classList.remove('hidden');
      return;
    }

    if (noNotices) noNotices.classList.add('hidden');

    // Populate all notices in the scrollable list
    const displayNotices = notices;

    displayNotices.forEach(notice => {
      const d = notice.createdAt ? new Date(notice.createdAt) : null;
      const day = d ? d.getDate() : '';
      const month = d ? (d.getMonth() + 1) : '';
      const year = d ? d.getFullYear() : '';
      const formattedDate = d ? `${day}/${month}/${year}` : '';

      const title = notice.title || 'Notification';
      const targetUrl = notice.file_path ? `admin/${notice.file_path}` : (notice.file_url || 'notices.php');
      const isExternal = !!(notice.file_path || notice.file_url);

      let category = notice.description || '';
      if (!category) {
        if (title.toLowerCase().includes('admission') || title.toLowerCase().includes('duat')) {
          category = 'Counseling cum Admission';
        } else if (title.toLowerCase().includes('scholarship')) {
          category = 'Scholarship';
        } else if (title.toLowerCase().includes('fee') || title.toLowerCase().includes('exam') || title.toLowerCase().includes('routine')) {
          category = 'Academic Notice';
        } else {
          category = 'Official Announcement';
        }
      }

      const item = document.createElement('div');
      item.className = 'py-4.5 border-b border-slate-100 last:border-b-0 group';

      let actionLink = '';
      if (notice.file_path) {
        actionLink = `
          <a href="admin/${notice.file_path}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-800 mt-2 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <span>View Attachment</span>
          </a>
        `;
      } else if (notice.file_url) {
        actionLink = `
          <a href="${notice.file_url}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-800 mt-2 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            <span>View Link</span>
          </a>
        `;
      } else {
        actionLink = `
          <a href="notices.php" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-800 mt-2 transition-colors">
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span>View Details</span>
          </a>
        `;
      }

      const isPinned = notice.is_pinned == 1;
      let isNewActive = false;
      if (notice.is_new !== undefined && (notice.is_new === 0 || notice.is_new === false || notice.is_new === '0')) {
        isNewActive = false;
      } else if (notice.new_until) {
        isNewActive = new Date(notice.new_until + 'T23:59:59') >= new Date();
      } else if (notice.is_new == 1) {
        isNewActive = true;
      }

      item.innerHTML = `
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-1 sm:gap-3">
          <div class="flex items-start gap-2 flex-1">
            ${isPinned ? '<i class="fas fa-thumbtack text-amber-500 text-xs shrink-0 mt-1 -rotate-45" title="Pinned Announcement"></i>' : ''}
            ${isNewActive ? '<span class="bg-red-600 text-white text-[9px] font-black uppercase px-1.5 py-0.5 rounded shadow-sm animate-pulse shrink-0 tracking-wider mt-0.5">NEW</span>' : ''}
            <a href="${targetUrl}" ${isExternal ? 'target="_blank" rel="noopener noreferrer"' : ''} class="text-xs sm:text-sm lg:text-base font-semibold text-slate-800 hover:text-indigo-600 transition-colors leading-snug">
              ${escapeHtml(title)}
            </a>
          </div>
          ${formattedDate ? `<span class="text-[10px] sm:text-xs text-slate-500 font-medium shrink-0 pt-0.5">${formattedDate}</span>` : ''}
        </div>
        ${category ? `<p class="text-[11px] sm:text-xs text-slate-500 font-normal mt-0.5 leading-relaxed">${escapeHtml(category)}</p>` : ''}
        <div class="mt-0.5">
          ${actionLink}
        </div>
      `;

      wrapper.appendChild(item);
    });
  };

  /**
   * Escape HTML to prevent XSS
   */
  const escapeHtml = (str) => {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
  };

  /**
   * Main init
   */
  const initNotifications = async () => {
    const wrapper = document.getElementById('notice-wrapper');
    if (!wrapper) return;

    try {
      const response = await fetch(NOTIFICATIONS_URL + '?t=' + Date.now());
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

      const result = await response.json();
      const notices = Array.isArray(result) ? result : (result.data || []);

      // Sort descending by createdAt
      const sortedNotices = notices.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));

      // Populate both the ticker and the grid
      populateTicker(sortedNotices);
      populateNoticeGrid(sortedNotices);

    } catch (error) {
      console.error('Error fetching notices:', error);
      const wrapper = document.getElementById('notice-wrapper');
      const noNotices = document.getElementById('no-notices');
      if (wrapper) wrapper.innerHTML = '';
      if (noNotices) {
        noNotices.innerHTML = '<i class="fas fa-exclamation-triangle text-2xl text-slate-300 mb-3 block"></i><p>Failed to load notices. Please try again later!</p>';
        noNotices.classList.remove('hidden');
      }

      // Show fallback ticker text
      const tickerTrack = document.getElementById('tickerTrack');
      if (tickerTrack) {
        tickerTrack.innerHTML = '<span class="text-xs font-medium text-white/50">Visit notices.php for the latest announcements</span>';
        tickerTrack.style.animation = 'none';
      }
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNotifications);
  } else {
    initNotifications();
  }
})();
