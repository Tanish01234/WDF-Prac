
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

  // Session & Authentication state synchronizer (Practicals 10 & 14)
  async function syncAuthSession() {
    const isPagesDir = window.location.pathname.includes('/pages/');
    const sessionUrl = isPagesDir ? '../backend/auth/session.php' : 'backend/auth/session.php';
    const logoutUrl  = isPagesDir ? '../backend/auth/logout.php' : 'backend/auth/logout.php';
    const loginUrl   = isPagesDir ? 'login.html' : 'pages/login.html';
    const adminDashUrl = isPagesDir ? 'admin-dashboard.html' : 'pages/admin-dashboard.html';

    try {
      const res = await fetch(sessionUrl);
      if (!res.ok) return;
      const data = await res.json();

      if (data && data.logged_in && data.user) {
        const user = data.user;

        // Update name and initials in UI
        const nameEls = document.querySelectorAll('.student-name, #userDisplayName');
        nameEls.forEach(el => { el.textContent = user.full_name || user.name || user.email; });

        const metaEls = document.querySelectorAll('.student-meta, #userDisplayMeta');
        metaEls.forEach(el => {
          if (user.role === 'admin') {
            el.textContent = 'Administrator • IT Dept';
          } else {
            el.textContent = `${user.roll_no || 'Student'} · ${user.division || '3IT-A'}`;
          }
        });

        // Greeting update
        const greeting = document.getElementById('dashGreeting');
        if (greeting && user.full_name) {
          const firstName = user.full_name.split(' ')[0];
          greeting.textContent = `Good day, ${firstName}.`;
        }

        // Avatar initials
        const avatarEls = document.querySelectorAll('.student-avatar');
        avatarEls.forEach(av => {
          if (user.role === 'admin') {
            av.textContent = 'AD';
          } else if (user.full_name) {
            const parts = user.full_name.trim().split(' ');
            const initials = parts.length > 1 ? (parts[0][0] + parts[parts.length - 1][0]).toUpperCase() : parts[0].substring(0, 2).toUpperCase();
            av.textContent = initials;
          }
        });

        // If admin, add Admin Console shortcut in sidebar if not present
        if (user.role === 'admin') {
          const navGroups = document.querySelectorAll('.nav-group');
          if (navGroups.length > 0 && !document.getElementById('navAdminLink')) {
            const adminItem = document.createElement('div');
            adminItem.className = 'nav-item';
            adminItem.id = 'navAdminLink';
            adminItem.innerHTML = `
              <a href="${adminDashUrl}" class="nav-link" style="color: #ef4444; font-weight: 600;">
                <span class="nav-icon">🛡️</span>
                <span>Admin Console</span>
              </a>
            `;
            navGroups[0].appendChild(adminItem);
          }
        }

        // Add Sign Out link in sidebar footer if not already present
        const sidebarFooters = document.querySelectorAll('.sidebar-footer');
        sidebarFooters.forEach(sf => {
          if (!sf.querySelector('.logout-btn-link')) {
            const logoutDiv = document.createElement('div');
            logoutDiv.className = 'logout-btn-link';
            logoutDiv.style.marginTop = '0.75rem';
            logoutDiv.style.paddingTop = '0.5rem';
            logoutDiv.style.borderTop = '1px solid var(--border-subtle)';
            logoutDiv.innerHTML = `
              <a href="${logoutUrl}" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center; font-size: 0.8rem;">
                Sign Out
              </a>
            `;
            sf.appendChild(logoutDiv);
          }
        });
      }
    } catch (e) {
      // Quiet fail if offline or not on PHP server
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    syncAuthSession();
  });
})();

