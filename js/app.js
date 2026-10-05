
(function () {
  'use strict';

  // Toast notifications
  window.showToast = function (message, type = 'info', duration = 3500) {
    let container = document.querySelector('.toast-container');
    if (!container) {
      container = document.createElement('div');
      container.className = 'toast-container';
      container.setAttribute('aria-live', 'polite');
      container.setAttribute('aria-atomic', 'true');
      document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.setAttribute('role', 'status');

    let icon = '•';
    if (type === 'success') icon = '✓';
    if (type === 'error') icon = '✕';
    if (type === 'warning') icon = '!';

    toast.innerHTML = `
      <span class="toast-icon">${icon}</span>
      <span class="toast-message">${message}</span>
    `;

    container.appendChild(toast);

    const timeout = setTimeout(() => {
      dismissToast(toast);
    }, duration);

    toast.addEventListener('click', () => {
      clearTimeout(timeout);
      dismissToast(toast);
    });
  };

  function dismissToast(toast) {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(12px)';
    toast.style.transition = 'all 200ms ease';
    setTimeout(() => {
      if (toast.parentElement) {
        toast.parentElement.removeChild(toast);
      }
    }, 200);
  }

  // Modal helpers
  window.openModal = function (modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';

    const focusable = modal.querySelector('button, input, select, textarea, [tabindex="0"]');
    if (focusable) focusable.focus();
  };

  window.closeModal = function (modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('show');
    document.body.style.overflow = '';
  };

  // Dynamic greeting
  window.getGreeting = function () {
    const hour = new Date().getHours();
    if (hour < 12) return 'Good Morning';
    if (hour < 17) return 'Good Afternoon';
    return 'Good Evening';
  };

  // Accordions
  window.initAccordion = function (containerSelector = '.accordion') {
    const containers = document.querySelectorAll(containerSelector);
    containers.forEach(container => {
      const headers = container.querySelectorAll('.accordion-header');
      headers.forEach(header => {
        header.addEventListener('click', function () {
          const content = this.nextElementSibling;
          const isExpanded = this.getAttribute('aria-expanded') === 'true';

          const siblingHeaders = container.querySelectorAll('.accordion-header');
          const siblingContents = container.querySelectorAll('.accordion-content');

          siblingHeaders.forEach(h => {
            h.setAttribute('aria-expanded', 'false');
          });
          siblingContents.forEach(c => {
            c.classList.remove('open');
          });

          if (!isExpanded) {
            this.setAttribute('aria-expanded', 'true');
            if (content) content.classList.add('open');
          }
        });
      });
    });
  };

  // Global listeners
  document.addEventListener('DOMContentLoaded', () => {
    // Dynamic greeting
    const greetingEl = document.querySelector('.dynamic-greeting-text');
    if (greetingEl) {
      greetingEl.textContent = `${window.getGreeting()}, Tanis`;
    }

    // Modal close buttons
    document.querySelectorAll('.modal-close, [data-modal-dismiss]').forEach(btn => {
      btn.addEventListener('click', function () {
        const modal = this.closest('.modal-backdrop');
        if (modal) {
          modal.classList.remove('show');
          document.body.style.overflow = '';
        }
      });
    });

    // Close modal on backdrop click
    document.querySelectorAll('.modal-backdrop').forEach(modal => {
      modal.addEventListener('click', function (e) {
        if (e.target === this) {
          this.classList.remove('show');
          document.body.style.overflow = '';
        }
      });
    });

    // Notifications dropdown
    const notifBtn = document.querySelector('.notifications-btn');
    const notifDropdown = document.querySelector('.notification-dropdown');
    const markReadBtn = document.querySelector('.mark-all-read-btn');
    const notifCountBadge = document.querySelector('.notification-count');

    if (notifBtn && notifDropdown) {
      notifBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        notifDropdown.classList.toggle('show');
      });

      document.addEventListener('click', (e) => {
        if (!notifDropdown.contains(e.target) && e.target !== notifBtn) {
          notifDropdown.classList.remove('show');
        }
      });
    }

    if (markReadBtn) {
      markReadBtn.addEventListener('click', () => {
        const unreadItems = document.querySelectorAll('.notification-item.unread');
        unreadItems.forEach(item => item.classList.remove('unread'));
        if (notifCountBadge) {
          notifCountBadge.textContent = '0';
          notifCountBadge.style.display = 'none';
        }
        window.showToast('All notifications marked as read', 'success');
      });
    }

    window.initAccordion();
  });
})();
