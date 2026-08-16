/**
 * Contact Form Module
 */
(() => {
  'use strict';

  /**
   * Displays a toast notification
   * @param {string} message Toast message
   * @param {string} type 'success' or 'error'
   */
  const showToast = (message, type = 'success') => {
    // Remove existing toast
    const existing = document.querySelector('.toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = `toast toast--${type}`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    // Trigger animation
    requestAnimationFrame(() => {
      toast.classList.add('show');
    });

    // Remove after 4s
    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 300);
    }, 4000);
  };

  const initContactForm = () => {
    const form = document.getElementById('contactForm');
    if (!form) return;

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    const showError = (input, message) => {
      const parent = input.closest('div');
      let errorDiv = parent ? parent.querySelector('.field-error') : null;
      if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'field-error text-xs text-red-400 mt-1';
        input.parentNode.appendChild(errorDiv);
      }
      errorDiv.textContent = message;
      errorDiv.classList.remove('hidden');
      input.classList.add('border-red-500');
    };

    const clearError = (input) => {
      const parent = input.closest('div');
      const errorDiv = parent ? parent.querySelector('.field-error') : null;
      if (errorDiv) {
        errorDiv.classList.add('hidden');
        errorDiv.textContent = '';
      }
      input.classList.remove('border-red-500');
    };

    // Real-time validation
    form.querySelectorAll('input, textarea').forEach(input => {
      input.addEventListener('input', () => {
        if (input.name === 'name') {
          if (input.value.trim().length < 2 || input.value.trim().length > 100) {
            showError(input, 'Name must be between 2 and 100 characters.');
          } else {
            clearError(input);
          }
        }
        if (input.name === 'email') {
          if (!emailRegex.test(input.value.trim())) {
            showError(input, 'Please enter a valid email address.');
          } else {
            clearError(input);
          }
        }
        if (input.name === 'message') {
          if (input.value.trim().length < 10 || input.value.trim().length > 2000) {
            showError(input, 'Message must be between 10 and 2000 characters.');
          } else {
            clearError(input);
          }
        }
      });
    });

    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      const nameInput = form.querySelector('[name="name"]');
      const emailInput = form.querySelector('[name="email"]');
      const messageInput = form.querySelector('[name="message"]');
      const csrfInput = form.querySelector('[name="csrf_token"]');
      const submitBtn = form.querySelector('#submitBtn') || form.querySelector('button[type="submit"]');

      let isValid = true;

      // Final Validation
      if (nameInput.value.trim().length < 2 || nameInput.value.trim().length > 100) {
        showError(nameInput, 'Name must be between 2 and 100 characters.');
        isValid = false;
      }
      if (!emailRegex.test(emailInput.value.trim())) {
        showError(emailInput, 'Please enter a valid email address.');
        isValid = false;
      }
      if (messageInput.value.trim().length < 10 || messageInput.value.trim().length > 2000) {
        showError(messageInput, 'Message must be between 10 and 2000 characters.');
        isValid = false;
      }

      if (!isValid) return;

      // Loading state
      const originalBtnContent = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = `
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-[#1a365d] inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Sending...</span>
      `;

      try {
        let recaptcha_token = '';
        if (typeof grecaptcha !== 'undefined' && typeof grecaptcha.execute === 'function') {
          try {
            recaptcha_token = await grecaptcha.execute('6LeEW94sAAAAAPFa_NXd8WemwqWn-SLlNjpnN0CH', { action: 'contact' });
          } catch (recaptchaErr) {
            console.warn('reCAPTCHA execution failed:', recaptchaErr);
          }
        }

        const payload = {
          name: nameInput.value.trim(),
          email: emailInput.value.trim(),
          message: messageInput.value.trim(),
          recaptcha_token: recaptcha_token,
          csrf_token: csrfInput ? csrfInput.value : ''
        };

        const response = await fetch('endpoints/contact.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (response.ok && result.success) {
          showToast('Message sent successfully! We will get back to you soon.', 'success');
          form.reset();
          form.querySelectorAll('.field-error').forEach(el => el.classList.add('hidden'));
        } else {
          throw new Error(result.error || 'Failed to send message.');
        }

      } catch (error) {
        console.error('Contact Form Error:', error);
        showToast(error.message || 'An error occurred. Please try again later.', 'error');
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnContent;
      }
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initContactForm);
  } else {
    initContactForm();
  }
})();
