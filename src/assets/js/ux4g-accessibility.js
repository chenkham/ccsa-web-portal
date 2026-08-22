/**
 * UX4G & GIGW 3.0 Accessibility Module
 * Government of India Design System 3.0 Standards
 * - Font Size Resizing (A-, A, A+)
 * - Keyboard Navigation & Focus Ring Tracking
 * - Screen Reader Announcements
 */
(() => {
  'use strict';

  const STORAGE_KEY_FONT = 'ux4g_font_scale';

  // Font scale levels: [0.9 = A-, 1.0 = A, 1.15 = A+]
  const FONT_SCALES = [0.9, 1.0, 1.15];
  let currentScaleIndex = 1; // Default is 1.0 (Normal)

  // Ensure high-contrast is disabled and clean legacy storage
  try {
    localStorage.removeItem('ux4g_contrast_mode');
    document.documentElement.classList.remove('high-contrast');
    document.documentElement.removeAttribute('data-contrast');
  } catch (e) {}

  // ── DEVELOPER CONSOLE EASTER EGG ──────────────────────────────────────────
  try {
    console.log(
      "%c⚡ CCSA DIBRUGARH UNIVERSITY ⚡\n" +
      "%cHey curious developer! 👀 Looking for bugs or easter eggs?\n" +
      "If you find one, remember: it's not a bug, it's an undocumented feature! 😉\n" +
      "Interested in building high-performance software & AI? Explore our MCA, BCA & Ph.D. programmes: https://ccsdu.in/programs.php",
      "color: #0f172a; font-size: 15px; font-weight: 900; background: linear-gradient(90deg, #fbbf24, #f59e0b); padding: 5px 12px; border-radius: 6px;",
      "color: #4f46e5; font-size: 12px; font-weight: 600; line-height: 1.6; padding-top: 4px;"
    );
  } catch (e) {}

  /**
   * Apply font scaling to root html element
   */
  const applyFontScale = (index) => {
    currentScaleIndex = Math.max(0, Math.min(index, FONT_SCALES.length - 1));
    const scale = FONT_SCALES[currentScaleIndex];
    document.documentElement.style.fontSize = `${scale * 100}%`;
    localStorage.setItem(STORAGE_KEY_FONT, currentScaleIndex.toString());

    // Update active state on buttons
    const btnDecrease = document.getElementById('ux4g-font-decrease');
    const btnReset = document.getElementById('ux4g-font-reset');
    const btnIncrease = document.getElementById('ux4g-font-increase');

    if (btnDecrease) btnDecrease.classList.toggle('font-bold', currentScaleIndex === 0);
    if (btnReset) btnReset.classList.toggle('border-white/80', currentScaleIndex === 1);
    if (btnIncrease) btnIncrease.classList.toggle('font-bold', currentScaleIndex === 2);
  };

  /**
   * Announce message to Screen Readers
   */
  const announceToScreenReader = (message) => {
    let liveRegion = document.getElementById('ux4g-sr-announcer');
    if (!liveRegion) {
      liveRegion = document.createElement('div');
      liveRegion.id = 'ux4g-sr-announcer';
      liveRegion.setAttribute('aria-live', 'polite');
      liveRegion.setAttribute('aria-atomic', 'true');
      liveRegion.className = 'sr-only';
      document.body.appendChild(liveRegion);
    }
    liveRegion.textContent = '';
    setTimeout(() => {
      liveRegion.textContent = message;
    }, 50);
  };

  /**
   * Initialize UX4G Accessibility Controls
   */
  const init = () => {
    // 1. Restore Font Scaling from localStorage
    const savedFont = localStorage.getItem(STORAGE_KEY_FONT);
    if (savedFont !== null) {
      applyFontScale(parseInt(savedFont, 10) || 1);
    }

    // 2. Attach Click Event Listeners
    const btnDecrease = document.getElementById('ux4g-font-decrease');
    const btnReset = document.getElementById('ux4g-font-reset');
    const btnIncrease = document.getElementById('ux4g-font-increase');

    if (btnDecrease) {
      btnDecrease.addEventListener('click', (e) => {
        e.preventDefault();
        applyFontScale(currentScaleIndex - 1);
        announceToScreenReader('Font size decreased');
      });
    }

    if (btnReset) {
      btnReset.addEventListener('click', (e) => {
        e.preventDefault();
        applyFontScale(1);
        announceToScreenReader('Font size reset to normal');
      });
    }

    if (btnIncrease) {
      btnIncrease.addEventListener('click', (e) => {
        e.preventDefault();
        applyFontScale(currentScaleIndex + 1);
        announceToScreenReader('Font size increased');
      });
    }

    // 3. Keyboard Navigation Detection for UX4G Accessible Focus Outline
    window.addEventListener('keydown', (e) => {
      if (e.key === 'Tab') {
        document.body.classList.add('user-is-tabbing');
      }
    });

    window.addEventListener('mousedown', () => {
      document.body.classList.remove('user-is-tabbing');
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // Expose global helpers
  window.UX4G = {
    applyFontScale,
    announce: announceToScreenReader
  };
})();

