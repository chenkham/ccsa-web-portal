(function() {
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  document.addEventListener('DOMContentLoaded', () => {
    // 1. Scroll-Triggered Reveals
    const revealElements = document.querySelectorAll('.reveal');
    if (revealElements.length > 0) {
      if (prefersReducedMotion) {
        revealElements.forEach(el => el.classList.add('is-revealed'));
      } else {
        const revealObserver = new IntersectionObserver((entries, observer) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.classList.add('is-revealed');
              observer.unobserve(entry.target);
            }
          });
        }, { threshold: 0.15, rootMargin: '0px 0px -60px 0px' });
        
        revealElements.forEach(el => revealObserver.observe(el));
      }
    }

    // 2. Staggered Grid Animation
    const staggerGrids = document.querySelectorAll('.stagger-grid');
    if (staggerGrids.length > 0) {
      if (prefersReducedMotion) {
        document.querySelectorAll('.stagger-card').forEach(card => card.classList.add('is-revealed'));
      } else {
        const staggerObserver = new IntersectionObserver((entries, observer) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              const grid = entry.target;
              const delay = parseInt(grid.getAttribute('data-stagger') || '80', 10);
              const cards = grid.querySelectorAll('.stagger-card');
              
              cards.forEach((card, index) => {
                setTimeout(() => {
                  card.classList.add('is-revealed');
                }, index * delay);
              });
              
              observer.unobserve(grid);
            }
          });
        }, { threshold: 0.15, rootMargin: '0px 0px -60px 0px' });
        
        staggerGrids.forEach(grid => staggerObserver.observe(grid));
      }
    }

    // 3. Animated Number Counters
    const counters = document.querySelectorAll('.counter');
    if (counters.length > 0) {
      if (prefersReducedMotion) {
        counters.forEach(counter => {
          const target = parseFloat(counter.getAttribute('data-target') || '0');
          const prefix = counter.getAttribute('data-prefix') || '';
          const suffix = counter.getAttribute('data-suffix') || '';
          const decimals = parseInt(counter.getAttribute('data-decimals') || '0', 10);
          counter.textContent = prefix + target.toLocaleString(undefined, { minimumFractionDigits: decimals, maximumFractionDigits: decimals }) + suffix;
        });
      } else {
        const easeOutExpo = t => t === 1 ? 1 : 1 - Math.pow(2, -10 * t);
        
        const counterObserver = new IntersectionObserver((entries, observer) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              const counter = entry.target;
              const target = parseFloat(counter.getAttribute('data-target') || '0');
              const prefix = counter.getAttribute('data-prefix') || '';
              const suffix = counter.getAttribute('data-suffix') || '';
              const duration = parseInt(counter.getAttribute('data-duration') || '2000', 10);
              const decimals = parseInt(counter.getAttribute('data-decimals') || '0', 10);
              
              let startTime = null;
              
              const updateCounter = (currentTime) => {
                if (!startTime) startTime = currentTime;
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const easedProgress = easeOutExpo(progress);
                
                const currentValue = easedProgress * target;
                counter.textContent = prefix + currentValue.toLocaleString(undefined, { minimumFractionDigits: decimals, maximumFractionDigits: decimals }) + suffix;
                
                if (progress < 1) {
                  requestAnimationFrame(updateCounter);
                } else {
                  counter.textContent = prefix + target.toLocaleString(undefined, { minimumFractionDigits: decimals, maximumFractionDigits: decimals }) + suffix;
                }
              };
              
              requestAnimationFrame(updateCounter);
              observer.unobserve(counter);
            }
          });
        }, { threshold: 0.15 });
        
        counters.forEach(counter => counterObserver.observe(counter));
      }
    }

    // 4. Scroll Progress Bar
    const progressBar = document.getElementById('scroll-progress');
    if (progressBar) {
      let ticking = false;
      window.addEventListener('scroll', () => {
        if (!ticking) {
          window.requestAnimationFrame(() => {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            let scrolled = 0;
            if (height > 0) {
              scrolled = winScroll / height;
            }
            progressBar.style.transform = `scaleX(${scrolled})`;
            ticking = false;
          });
          ticking = true;
        }
      }, { passive: true });
    }

    // 5. 3D Tilt Cards
    const tiltCards = document.querySelectorAll('.tilt-card');
    if (tiltCards.length > 0 && !window.matchMedia('(hover: none)').matches && !prefersReducedMotion) {
      tiltCards.forEach(card => {
        card.addEventListener('mousemove', e => {
          const rect = card.getBoundingClientRect();
          const x = e.clientX - rect.left;
          const y = e.clientY - rect.top;
          
          const centerX = rect.width / 2;
          const centerY = rect.height / 2;
          
          const rotateX = ((y - centerY) / centerY) * -10;
          const rotateY = ((x - centerX) / centerX) * 10;
          
          card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
          
          const glare = card.querySelector('.tilt-glare');
          if (glare) {
            glare.style.opacity = '1';
            glare.style.background = `radial-gradient(circle at ${x}px ${y}px, rgba(255,255,255,0.2) 0%, transparent 50%)`;
          }
        });
        
        card.addEventListener('mouseleave', () => {
          card.style.transform = `perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)`;
          const glare = card.querySelector('.tilt-glare');
          if (glare) {
            glare.style.opacity = '0';
          }
        });
      });
    }

    // 6. Magnetic Buttons
    const magneticBtns = document.querySelectorAll('.magnetic-btn');
    if (magneticBtns.length > 0 && !window.matchMedia('(pointer: coarse)').matches && !prefersReducedMotion) {
      magneticBtns.forEach(btn => {
        const inner = btn.querySelector('.magnetic-inner');
        
        btn.addEventListener('mousemove', e => {
          const rect = btn.getBoundingClientRect();
          const x = e.clientX - rect.left - rect.width / 2;
          const y = e.clientY - rect.top - rect.height / 2;
          
          btn.style.transform = `translate3d(${x * 0.25}px, ${y * 0.25}px, 0)`;
          if (inner) {
            inner.style.transform = `translate3d(${x * 0.12}px, ${y * 0.12}px, 0)`;
          }
        });
        
        btn.addEventListener('mouseleave', () => {
          btn.style.transform = `translate3d(0, 0, 0)`;
          if (inner) {
            inner.style.transform = `translate3d(0, 0, 0)`;
          }
        });
      });
    }

    // 7. Typewriter Effect
    const typewriter = document.getElementById('typewriter');
    if (typewriter && !prefersReducedMotion) {
      const words = ['Computer Applications', 'Artificial Intelligence', 'Data Science', 'Cyber Security', 'Cloud Computing'];
      let wordIndex = 0;
      let charIndex = 0;
      let isDeleting = false;
      
      typewriter.classList.add('animate-blink-cursor');
      
      const type = () => {
        const currentWord = words[wordIndex];
        
        if (isDeleting) {
          typewriter.textContent = currentWord.substring(0, charIndex - 1);
          charIndex--;
        } else {
          typewriter.textContent = currentWord.substring(0, charIndex + 1);
          charIndex++;
        }
        
        let typeSpeed = isDeleting ? 45 : 90;
        
        if (!isDeleting && charIndex === currentWord.length) {
          typeSpeed = 2200;
          isDeleting = true;
        } else if (isDeleting && charIndex === 0) {
          isDeleting = false;
          wordIndex = (wordIndex + 1) % words.length;
          typeSpeed = 400;
        }
        
        setTimeout(type, typeSpeed);
      };
      
      setTimeout(type, 1000);
    } else if (typewriter && prefersReducedMotion) {
      typewriter.textContent = 'Computer Applications';
    }
  });
})();
