/**
 * Core Application JavaScript for CCSA Website
 */
(() => {
  'use strict';

  /**
   * Navigation Module
   */
  /**
   * Navigation Module - Touch support for hybrid/tablet devices
   */
  const initNavigation = () => {
    const navItems = document.querySelectorAll('.nav-item');
    
    // On touch devices, allow first tap to open dropdown and second tap to navigate
    navItems.forEach(item => {
      const dropdown = item.querySelector('.dropdown-menu');
      if (!dropdown) return;

      item.addEventListener('touchstart', (e) => {
        if (window.innerWidth >= 1024) {
          const isExpanded = dropdown.style.visibility === 'visible';
          if (!isExpanded) {
            e.preventDefault();
            dropdown.style.visibility = 'visible';
            dropdown.style.opacity = '1';
            dropdown.style.pointerEvents = 'auto';
          }
        }
      }, { passive: false });
    });
  };

  /**
   * Mobile Menu Module
   */
  const initMobileMenu = () => {
    const mobileMenuButton = document.getElementById('mobile-menu-button') || document.getElementById('mobileMenuButton');
    const mobileMenu = document.getElementById('mobile-menu') || document.getElementById('mobileMenu');
    
    if (!mobileMenuButton || !mobileMenu) return;

    mobileMenuButton.addEventListener('click', (e) => {
      e.stopPropagation();
      const isHidden = mobileMenu.classList.contains('hidden');
      if (isHidden) {
        mobileMenu.classList.remove('hidden');
        mobileMenuButton.classList.add('is-active');
        mobileMenuButton.setAttribute('aria-expanded', 'true');
      } else {
        mobileMenu.classList.add('hidden');
        mobileMenuButton.classList.remove('is-active');
        mobileMenuButton.setAttribute('aria-expanded', 'false');
      }
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
      if (!mobileMenu.contains(e.target) && !mobileMenuButton.contains(e.target) && !mobileMenu.classList.contains('hidden')) {
        mobileMenu.classList.add('hidden');
        mobileMenuButton.classList.remove('is-active');
        mobileMenuButton.setAttribute('aria-expanded', 'false');
      }
    });

    // Close menu when clicking a direct link (e.g. anchor links)
    const mobileLinks = mobileMenu.querySelectorAll('a:not(.has-submenu)');
    mobileLinks.forEach(link => {
      link.addEventListener('click', () => {
        if (!mobileMenu.classList.contains('hidden')) {
          mobileMenu.classList.add('hidden');
          mobileMenuButton.classList.remove('is-active');
          mobileMenuButton.setAttribute('aria-expanded', 'false');
        }
      });
    });

    // Handle mobile top-level dropdown buttons
    const mobileDropdownBtns = mobileMenu.querySelectorAll('.mobile-dropdown-btn');
    mobileDropdownBtns.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const submenu = btn.nextElementSibling;
        const chevron = btn.querySelector('.mobile-chevron');
        if (submenu) {
          const isClosed = submenu.classList.contains('hidden');
          submenu.classList.toggle('hidden');
          if (chevron) {
            chevron.style.transform = isClosed ? 'rotate(180deg)' : 'rotate(0)';
          }
        }
      });
    });

    // Handle mobile nested dropdown buttons
    const mobileNestedBtns = mobileMenu.querySelectorAll('.mobile-nested-btn');
    mobileNestedBtns.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const submenu = btn.nextElementSibling;
        const chevron = btn.querySelector('.mobile-nested-chevron');
        if (submenu) {
          const isClosed = submenu.classList.contains('hidden');
          submenu.classList.toggle('hidden');
          if (chevron) {
            chevron.style.transform = isClosed ? 'rotate(180deg)' : 'rotate(0)';
          }
        }
      });
    });
  };

  /**
   * Hero Slider Module
   */
  const initHeroSlider = () => {
    const sliderContainer = document.querySelector('.hero-slider');
    if (!sliderContainer) return;
    
    const slides = sliderContainer.querySelectorAll('.slide');
    if (slides.length <= 1) return;

    let currentIndex = 0;
    let autoPlayInterval;
    let isHovered = false;
    let touchStartX = 0;
    let touchEndX = 0;

    // Create dots
    const dotsContainer = document.createElement('div');
    dotsContainer.className = 'absolute bottom-4 left-0 right-0 flex justify-center gap-2 z-20';
    
    slides.forEach((_, i) => {
      const dot = document.createElement('button');
      dot.className = `w-3 h-3 rounded-full transition-colors ${i === 0 ? 'bg-white' : 'bg-white/50'}`;
      dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
      dot.addEventListener('click', () => goToSlide(i));
      dotsContainer.appendChild(dot);
    });
    
    sliderContainer.appendChild(dotsContainer);
    const dots = dotsContainer.querySelectorAll('button');

    const updateSlider = () => {
      slides.forEach((slide, i) => {
        slide.style.transform = `translateX(${100 * (i - currentIndex)}%)`;
      });
      
      dots.forEach((dot, i) => {
        if (i === currentIndex) {
          dot.classList.remove('bg-white/50');
          dot.classList.add('bg-white');
        } else {
          dot.classList.remove('bg-white');
          dot.classList.add('bg-white/50');
        }
      });
    };

    const goToSlide = (index) => {
      currentIndex = index;
      if (currentIndex < 0) currentIndex = slides.length - 1;
      if (currentIndex >= slides.length) currentIndex = 0;
      updateSlider();
    };

    const nextSlide = () => goToSlide(currentIndex + 1);
    const prevSlide = () => goToSlide(currentIndex - 1);

    const startAutoPlay = () => {
      if (!isHovered) {
        clearInterval(autoPlayInterval);
        autoPlayInterval = setInterval(nextSlide, 5000);
      }
    };

    // Initialize positions
    slides.forEach((slide, i) => {
      slide.style.position = 'absolute';
      slide.style.top = '0';
      slide.style.left = '0';
      slide.style.width = '100%';
      slide.style.height = '100%';
      slide.style.transition = 'transform 0.5s ease-in-out';
    });
    
    updateSlider();
    startAutoPlay();

    // Pause on hover
    sliderContainer.addEventListener('mouseenter', () => {
      isHovered = true;
      clearInterval(autoPlayInterval);
    });
    
    sliderContainer.addEventListener('mouseleave', () => {
      isHovered = false;
      startAutoPlay();
    });

    // Touch/swipe support
    sliderContainer.addEventListener('touchstart', (e) => {
      touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    sliderContainer.addEventListener('touchend', (e) => {
      touchEndX = e.changedTouches[0].screenX;
      handleSwipe();
    }, { passive: true });

    const handleSwipe = () => {
      if (touchEndX < touchStartX - 50) nextSlide();
      if (touchEndX > touchStartX + 50) prevSlide();
    };
  };

  /**
   * Download Handler
   */
  const initDownloads = () => {
    const downloadItems = document.querySelectorAll('.download-item');
    
    downloadItems.forEach(item => {
      item.addEventListener('click', async (e) => {
        e.preventDefault();
        
        const url = item.getAttribute('href') || item.dataset.url;
        if (!url) return;

        // Visual feedback
        item.style.pointerEvents = 'none';
        item.style.opacity = '0.7';
        
        let progressBar = item.querySelector('.download-progress');
        if (!progressBar) {
          progressBar = document.createElement('div');
          progressBar.className = 'download-progress';
          item.appendChild(progressBar);
        }

        try {
          const response = await fetch(url);
          
          if (!response.ok) throw new Error('Network response was not ok');
          
          const contentLength = response.headers.get('content-length');
          if (!contentLength) {
            // Fallback if no content-length
            const blob = await response.blob();
            triggerDownload(blob, url.split('/').pop() || 'download');
            return;
          }

          const total = parseInt(contentLength, 10);
          let loaded = 0;

          const reader = response.body.getReader();
          const chunks = [];

          while(true) {
            const {done, value} = await reader.read();
            if (done) break;
            
            chunks.push(value);
            loaded += value.length;
            
            const progress = (loaded / total) * 100;
            progressBar.style.width = `${progress}%`;
          }

          const blob = new Blob(chunks);
          triggerDownload(blob, url.split('/').pop() || 'download');
          
        } catch (error) {
          console.error('Download failed:', error);
          alert('Failed to download file. Please try again.');
        } finally {
          item.style.pointerEvents = 'auto';
          item.style.opacity = '1';
          setTimeout(() => {
            if (progressBar) progressBar.style.width = '0%';
          }, 1000);
        }
      });
    });

    const triggerDownload = (blob, filename) => {
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.style.display = 'none';
      a.href = url;
      a.download = filename;
      document.body.appendChild(a);
      a.click();
      window.URL.revokeObjectURL(url);
      document.body.removeChild(a);
    };
  };

  /**
   * Back-to-Top Button
   */
  const initBackToTop = () => {
    const btn = document.createElement('button');
    btn.innerHTML = `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>`;
    btn.className = 'fixed bottom-8 right-8 z-50 p-3 bg-blue-600 text-white rounded-full shadow-lg opacity-0 transition-opacity duration-300 pointer-events-none hover:bg-blue-700';
    btn.setAttribute('aria-label', 'Back to top');
    document.body.appendChild(btn);

    window.addEventListener('scroll', () => {
      if (window.scrollY > 300) {
        btn.classList.remove('opacity-0', 'pointer-events-none');
        btn.classList.add('opacity-100', 'pointer-events-auto');
      } else {
        btn.classList.remove('opacity-100', 'pointer-events-auto');
        btn.classList.add('opacity-0', 'pointer-events-none');
      }
    });

    btn.addEventListener('click', () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  };

  /**
   * Smart Header Auto-Hide on Scroll Down / Reveal on Scroll Up (Desktop lg viewports only)
   */
  const initSmartHeader = () => {
    const header = document.getElementById('smartHeader');
    if (!header) return;

    let lastScrollY = window.scrollY;
    let ticking = false;

    window.addEventListener('scroll', () => {
      // Mobile screens (< 1024px): navbar stays at top of page, no sticky floating behavior
      if (window.innerWidth < 1024) {
        if (header.style.transform !== '') {
          header.style.transform = '';
        }
        return;
      }

      if (!ticking) {
        window.requestAnimationFrame(() => {
          if (window.innerWidth < 1024) {
            header.style.transform = '';
            ticking = false;
            return;
          }

          const currentScrollY = window.scrollY;

          // When scrolled past header threshold on desktop
          if (currentScrollY > 120) {
            if (currentScrollY > lastScrollY && currentScrollY - lastScrollY > 8) {
              // Scrolling down -> Smoothly slide up out of view
              header.style.transform = 'translateY(-100%)';
            } else if (currentScrollY < lastScrollY && lastScrollY - currentScrollY > 8) {
              // Scrolling up -> Smoothly slide down into view
              header.style.transform = 'translateY(0)';
            }
          } else {
            // At the top of the page -> Always visible
            header.style.transform = 'translateY(0)';
          }

          lastScrollY = Math.max(0, currentScrollY);
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });

    window.addEventListener('resize', () => {
      if (window.innerWidth < 1024) {
        header.style.transform = '';
      }
    }, { passive: true });
  };

  // Initialize all modules
  document.addEventListener('DOMContentLoaded', () => {
    initNavigation();
    initMobileMenu();
    initHeroSlider();
    initDownloads();
    initBackToTop();
    initSmartHeader();
  });

})();


