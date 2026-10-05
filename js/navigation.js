
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', () => {
    initMobileNav();
    highlightActiveRoute();
  });

  function initMobileNav() {
    const menuBtn = document.querySelector('.mobile-menu-btn');
    const closeBtn = document.querySelector('.sidebar-close-btn');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    const publicNavToggle = document.querySelector('.public-nav-toggle');
    const publicNavLinks = document.querySelector('.public-nav-links');

    function openSidebar() {
      if (sidebar) sidebar.classList.add('open');
      if (overlay) overlay.classList.add('active');
      document.body.style.overflow = 'hidden';
      if (menuBtn) menuBtn.setAttribute('aria-expanded', 'true');
    }

    function closeSidebar() {
      if (sidebar) sidebar.classList.remove('open');
      if (overlay) overlay.classList.remove('active');
      document.body.style.overflow = '';
      if (menuBtn) menuBtn.setAttribute('aria-expanded', 'false');
    }

    if (menuBtn) {
      menuBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (sidebar && sidebar.classList.contains('open')) {
          closeSidebar();
        } else {
          openSidebar();
        }
      });
    }

    if (closeBtn) {
      closeBtn.addEventListener('click', closeSidebar);
    }

    if (overlay) {
      overlay.addEventListener('click', closeSidebar);
    }

    // Public navbar toggle
    if (publicNavToggle && publicNavLinks) {
      publicNavToggle.addEventListener('click', () => {
        publicNavLinks.classList.toggle('mobile-open');
        const isOpen = publicNavLinks.classList.contains('mobile-open');
        publicNavToggle.setAttribute('aria-expanded', isOpen);
      });
    }

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        closeSidebar();
        if (publicNavLinks) publicNavLinks.classList.remove('mobile-open');
      }
    });

    // Close mobile drawer when a nav link is clicked
    const navLinks = document.querySelectorAll('.nav-link, .public-nav-link');
    navLinks.forEach(link => {
      link.addEventListener('click', () => {
        if (window.innerWidth < 768) {
          closeSidebar();
          if (publicNavLinks) publicNavLinks.classList.remove('mobile-open');
        }
      });
    });
  }

  function highlightActiveRoute() {
    const currentPath = window.location.pathname;
    let filename = currentPath.substring(currentPath.lastIndexOf('/') + 1);
    if (!filename || filename === '') {
      filename = 'index.html';
    }

    const links = document.querySelectorAll('.nav-link, .public-nav-link');
    links.forEach(link => {
      const href = link.getAttribute('href');
      if (!href) return;
      const targetFilename = href.substring(href.lastIndexOf('/') + 1);

      if (targetFilename === filename) {
        link.classList.add('active');
        link.setAttribute('aria-current', 'page');
      } else {
        link.classList.remove('active');
        link.removeAttribute('aria-current');
      }
    });
  }
})();
