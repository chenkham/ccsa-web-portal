/**
 * Notifications Fetcher Module
 * Populates: (1) Announcement Ticker Bar, (2) Notices & Announcements grid section
 * 
 * @author Chenkham Chowlu
 * @copyright (c) 2024-2026 Chenkham Chowlu
 * @signature Q2hlbmtoYW0gQ2hvd2x1
 */
(() => {
  'use strict';

  const NOTIFICATIONS_URL = 'endpoints/notifications.php';
  const MONTHS_SHORT = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

  const parseSafeDate = (dateStr) => {
    if (!dateStr) return null;
    const str = String(dateStr).trim();
    // Try ISO format
    let d = new Date(str.replace(' ', 'T'));
    if (!isNaN(d.getTime())) return d;
    // Fallback to slash format
    d = new Date(str.replace(/-/g, '/'));
    return isNaN(d.getTime()) ? null : d;
  };

  const isNoticeNewActive = (notice) => {
    if (!notice) return false;
    // Explicitly disabled
    if (notice.is_new !== undefined && (notice.is_new === 0 || notice.is_new === false || notice.is_new === '0')) {
      return false;
    }
    // Check expiry date
    if (notice.new_until) {
      const untilStr = String(notice.new_until).split('T')[0] + 'T23:59:59';
      const untilDate = parseSafeDate(untilStr);
      if (untilDate) {
        return untilDate.getTime() >= Date.now();
      }
    }
    return (notice.is_new == 1 || notice.is_new === true || notice.is_new === '1');
  };

  /**
   * Build the scrolling ticker bar content (Single item without duplicate when count is 0 or 1)
   */
  const populateTicker = (notices) => {
    const tickerTrack = document.getElementById('tickerTrack');
    if (!tickerTrack) return;

    // Filter notices to ONLY include active NEW announcements
    const activeNewNotices = (notices || []).filter(n => isNoticeNewActive(n));

    // Case 0: No active new announcements -> Show exactly once, static
    if (activeNewNotices.length === 0) {
      tickerTrack.style.animation = 'none';
      tickerTrack.innerHTML = `<a href="notices.php" class="ticker-item text-slate-600 hover:text-red-600 inline-flex items-center gap-2 transition-colors">
        <span class="ticker-dot bg-slate-400"></span>
        <span class="text-[11px] sm:text-xs font-semibold leading-tight">No new announcement right now</span>
      </a>`;
      return;
    }

    const renderItemHtml = (n) => {
      const title = n.title || 'Notification';
      const d = parseSafeDate(n.createdAt);
      const dateStr = d ? `${d.getDate()} ${MONTHS_SHORT[d.getMonth()]}` : '';
      const isPinned = n.is_pinned == 1 || n.is_pinned === true || n.is_pinned === '1';

      return `<a href="notices.php" class="ticker-item text-slate-800 hover:text-red-600 inline-flex items-center gap-2 transition-colors">
        <span class="ticker-dot bg-slate-400"></span>
        ${isPinned ? '<i class="fas fa-thumbtack text-amber-500 text-[9px] sm:text-[10px] -rotate-45"></i>' : ''}
        <span class="bg-red-600 text-white text-[8px] sm:text-[9px] font-black uppercase px-1.5 py-0.5 rounded shadow-xs shrink-0">NEW</span>
        <span class="text-[11px] sm:text-xs font-semibold leading-tight">${escapeHtml(title)}</span>
        ${dateStr ? `<span class="text-[9px] sm:text-[10px] font-medium text-slate-400">(${dateStr})</span>` : ''}
      </a>`;
    };

    // Case 1: Exactly 1 announcement -> Show exactly once, static without duplicates
    if (activeNewNotices.length === 1) {
      tickerTrack.style.animation = 'none';
      tickerTrack.innerHTML = renderItemHtml(activeNewNotices[0]);
      return;
    }

    // Case 2: Multiple announcements -> Animate scroll with clean duplicated set for infinite marquee
    tickerTrack.style.animation = '';
    const itemsListHtml = activeNewNotices.map(renderItemHtml).join('');
    tickerTrack.innerHTML = itemsListHtml + itemsListHtml;
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
      const d = parseSafeDate(notice.createdAt);
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

      const isPinned = notice.is_pinned == 1 || notice.is_pinned === true || notice.is_pinned === '1';
      const isNewActive = isNoticeNewActive(notice);

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
   * Main init with Stale-While-Revalidate caching for instant rendering
   */
  const initNotifications = async () => {
    // 1. Instant render from local storage cache if available
    try {
      const cached = localStorage.getItem('ccsa_cached_notices');
      if (cached) {
        const cachedNotices = JSON.parse(cached);
        if (Array.isArray(cachedNotices) && cachedNotices.length > 0) {
          populateTicker(cachedNotices);
          populateNoticeGrid(cachedNotices);
        }
      }
    } catch (e) {}

    // 2. Fetch fresh data in the background (cache-busted for real-time updates)
    try {
      const response = await fetch(NOTIFICATIONS_URL + '?t=' + Date.now());
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

      const result = await response.json();
      const notices = Array.isArray(result) ? result : (result.data || []);

      const sortedNotices = notices.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));

      // Update UI with fresh data
      populateTicker(sortedNotices);
      populateNoticeGrid(sortedNotices);

      // Save to cache for next visit
      try {
        localStorage.setItem('ccsa_cached_notices', JSON.stringify(sortedNotices));
      } catch (e) {}

    } catch (error) {
      console.warn('Notice fetch notice:', error);
      const wrapper = document.getElementById('notice-wrapper');
      const noNotices = document.getElementById('no-notices');

      // Only show error if no cached data was rendered
      if (wrapper && wrapper.children.length === 0) {
        wrapper.innerHTML = '';
        if (noNotices) {
          noNotices.innerHTML = '<i class="fas fa-info-circle text-2xl text-slate-300 mb-3 block"></i><p>Visit notices page for recent announcements.</p>';
          noNotices.classList.remove('hidden');
        }

        const tickerTrack = document.getElementById('tickerTrack');
        if (tickerTrack) {
          tickerTrack.innerHTML = '<span class="text-xs font-medium text-slate-500">Visit notices.php for recent announcements</span>';
          tickerTrack.style.animation = 'none';
        }
      }
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNotifications);
  } else {
    initNotifications();
  }
})();
