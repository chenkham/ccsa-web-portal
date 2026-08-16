/**
 * Homepage Interaction Module — CCSA
 *
 * Adds ONLY behaviour not already covered by animations.js.
 * animations.js already owns: .reveal, .stagger-grid, .counter,
 * #scroll-progress, .tilt-card, .magnetic-btn, #typewriter.
 *
 * This module owns:
 *   1. Hero background crossfade (opacity, not translate — no reflow)
 *   2. Pointer spotlight on the hero  (--mx / --my)
 *   3. Pointer wash on .spot-card     (--cx / --cy)
 *   4. Heritage timeline spine reveal (.is-visible)
 *   5. Scrollspy rail active state
 *   6. Notice count badge, fed by the DOM that notifications.js builds
 *
 * Every pointer handler is rAF-throttled and writes only custom
 * properties, so the browser stays on the compositor path.
 */
(() => {
  'use strict';

  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

  /* ─── 1. Hero background crossfade ───────────────────────────── */
  const initHeroSlides = () => {
    const slides = Array.from(document.querySelectorAll('.hero-media'));
    if (slides.length === 0) return;

    slides.forEach((s, i) => s.classList.toggle('is-active', i === 0));
    if (slides.length < 2) return;

    let index = 0;
    let timer = null;

    const advance = () => {
      slides[index].classList.remove('is-active');
      index = (index + 1) % slides.length;
      slides[index].classList.add('is-active');
    };

    const start = () => {
      if (timer) return;
      timer = window.setInterval(advance, 6500);
    };
    const stop = () => {
      window.clearInterval(timer);
      timer = null;
    };

    // Don't burn cycles animating a hero nobody is looking at.
    document.addEventListener('visibilitychange', () => {
      document.hidden ? stop() : start();
    });

    start();
  };

  /* ─── 2. Hero pointer spotlight ──────────────────────────────── */
  const initHeroSpotlight = () => {
    const hero = document.getElementById('hero');
    if (!hero || !finePointer || reduceMotion) return;

    let queued = false;
    let px = 0;
    let py = 0;

    const paint = () => {
      hero.style.setProperty('--mx', px + '%');
      hero.style.setProperty('--my', py + '%');
      queued = false;
    };

    hero.addEventListener('pointermove', (e) => {
      const rect = hero.getBoundingClientRect();
      px = ((e.clientX - rect.left) / rect.width) * 100;
      py = ((e.clientY - rect.top) / rect.height) * 100;
      if (!queued) {
        queued = true;
        requestAnimationFrame(paint);
      }
    }, { passive: true });
  };

  /* ─── 3. Spotlight cards ─────────────────────────────────────── */
  const initSpotCards = () => {
    const cards = document.querySelectorAll('.spot-card');
    if (cards.length === 0 || !finePointer || reduceMotion) return;

    cards.forEach((card) => {
      let queued = false;
      let cx = 50;
      let cy = 50;

      const paint = () => {
        card.style.setProperty('--cx', cx + '%');
        card.style.setProperty('--cy', cy + '%');
        queued = false;
      };

      card.addEventListener('pointermove', (e) => {
        const rect = card.getBoundingClientRect();
        cx = ((e.clientX - rect.left) / rect.width) * 100;
        cy = ((e.clientY - rect.top) / rect.height) * 100;

        // Aim the conic border gradient at the cursor so the highlight
        // appears to originate from the pointer.
        const angle = Math.atan2(cy - 50, cx - 50) * (180 / Math.PI);
        card.style.setProperty('--spin', (angle - 90) + 'deg');

        if (!queued) {
          queued = true;
          requestAnimationFrame(paint);
        }
      }, { passive: true });
    });
  };

  /* ─── 4. Timeline spine reveal ───────────────────────────────── */
  const initTimeline = () => {
    const timeline = document.querySelector('.timeline');
    if (!timeline) return;

    if (reduceMotion || !('IntersectionObserver' in window)) {
      timeline.classList.add('is-visible');
      return;
    }

    const io = new IntersectionObserver((entries, obs) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.25 });

    io.observe(timeline);
  };

  /* ─── 5. Scrollspy rail ──────────────────────────────────────── */
  const initRail = () => {
    const dots = Array.from(document.querySelectorAll('.rail__dot'));
    if (dots.length === 0 || !('IntersectionObserver' in window)) return;

    const sections = dots
      .map((dot) => document.querySelector(dot.getAttribute('href')))
      .filter(Boolean);
    if (sections.length === 0) return;

    const setActive = (id) => {
      dots.forEach((dot) => {
        const isActive = dot.getAttribute('href') === '#' + id;
        dot.classList.toggle('is-active', isActive);
        dot.setAttribute('aria-current', isActive ? 'true' : 'false');
      });
    };

    // -45% top margin makes the "active" band sit around the viewport
    // middle, which matches where a reader's attention actually is.
    const io = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) setActive(entry.target.id);
      });
    }, { rootMargin: '-45% 0px -45% 0px', threshold: 0 });

    sections.forEach((section) => io.observe(section));
  };

  /* ─── 6. Notice count badge ──────────────────────────────────── */
  const initNoticeCount = () => {
    const badge = document.getElementById('notice-count');
    const wrapper = document.getElementById('notice-wrapper');
    if (!badge || !wrapper || !('MutationObserver' in window)) return;

    const update = () => {
      // notifications.js renders one element per notice; the loading
      // skeletons carry .skeleton, so exclude them from the tally.
      const count = Array.from(wrapper.children).filter(
        (el) => !el.querySelector('.skeleton')
      ).length;

      // Skeletons gone => the fetch settled; release the busy state so
      // screen readers announce the list instead of a loading region.
      const loading = wrapper.children.length > 0 && count === 0;
      wrapper.setAttribute('aria-busy', loading ? 'true' : 'false');

      if (count > 0) {
        badge.textContent = count > 9 ? '9+' : String(count);
        badge.classList.remove('hidden');
      } else {
        badge.classList.add('hidden');
      }
    };

    new MutationObserver(update).observe(wrapper, { childList: true });
    update();
  };

  const init = () => {
    initHeroSlides();
    initHeroSpotlight();
    initSpotCards();
    initTimeline();
    initRail();
    initNoticeCount();
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
