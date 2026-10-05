
(function () {
  'use strict';

  let allNotices = [];
  let filteredNotices = [];
  let currentCategory = 'all';
  let currentSearch = '';

  document.addEventListener('DOMContentLoaded', () => {
    initNotices();
  });

  async function initNotices() {
    const container = document.getElementById('noticesContainer');
    if (!container) return;

    const dataPath = window.location.pathname.includes('/pages/') ? '../data/notices.json' : 'data/notices.json';
    const searchInput = document.getElementById('noticeSearchInput');
    const categoryChips = document.querySelectorAll('.notice-category-chip');

    if (searchInput) {
      searchInput.addEventListener('input', (e) => {
        currentSearch = e.target.value.toLowerCase();
        applyFilters();
      });
    }

    categoryChips.forEach(chip => {
      chip.addEventListener('click', (e) => {
        categoryChips.forEach(c => c.classList.remove('active'));
        e.currentTarget.classList.add('active');
        currentCategory = e.currentTarget.getAttribute('data-category') || 'all';
        applyFilters();
      });
    });

    try {
      const response = await fetch(dataPath);
      if (!response.ok) throw new Error('Failed to load notices data');
      allNotices = await response.json();
      filteredNotices = [...allNotices];
      renderNotices();
    } catch (err) {
      console.error(err);
      container.innerHTML = `
        <div class="alert alert-danger" style="grid-column: 1 / -1;">
          <span>Unable to load campus notices feed. Please check connectivity or refresh.</span>
        </div>
      `;
    }
  }

  function applyFilters() {
    filteredNotices = allNotices.filter(notice => {
      const matchesCat = (currentCategory === 'all' || notice.category.toLowerCase() === currentCategory.toLowerCase() || notice.priority.toLowerCase() === currentCategory.toLowerCase());
      const matchesSearch = !currentSearch ||
        notice.title.toLowerCase().includes(currentSearch) ||
        notice.description.toLowerCase().includes(currentSearch) ||
        notice.department.toLowerCase().includes(currentSearch);
      return matchesCat && matchesSearch;
    });
    renderNotices();
  }

  function renderNotices() {
    const container = document.getElementById('noticesContainer');
    if (!container) return;

    if (filteredNotices.length === 0) {
      container.innerHTML = `
        <div class="empty-state" style="grid-column: 1 / -1;">
          <div class="empty-state-icon">📢</div>
          <h3>No notices found</h3>
          <p>No notices match your current search or category filter.</p>
        </div>
      `;
      return;
    }

    container.innerHTML = filteredNotices.map(notice => {
      let priorityBadge = 'badge-muted';
      if (notice.priority === 'Urgent') priorityBadge = 'badge-danger';
      else if (notice.priority === 'Important') priorityBadge = 'badge-warning';
      else priorityBadge = 'badge-info';

      return `
        <article class="card card-hover notice-card" style="display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
              <span class="badge ${priorityBadge}">${notice.priority}</span>
              <span class="text-xs text-muted font-mono">${notice.date}</span>
            </div>
            <h3 style="font-size: 1.15rem; margin-bottom: 0.5rem; line-height: 1.35;">${notice.title}</h3>
            <p style="font-size: 0.9rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1rem;">${notice.description}</p>
          </div>
          <div class="card-footer" style="padding-top: 0.75rem; margin-top: auto; display: flex; align-items: center; justify-content: space-between;">
            <span class="text-xs text-muted">🏛️ ${notice.department}</span>
            <button type="button" class="btn btn-sm btn-ghost view-notice-btn" data-id="${notice.id}">Read Notice →</button>
          </div>
        </article>
      `;
    }).join('');

    container.querySelectorAll('.view-notice-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const id = parseInt(e.currentTarget.getAttribute('data-id'), 10);
        const notice = allNotices.find(n => n.id === id);
        if (!notice) return;

        const modalBody = document.getElementById('noticeModalBody');
        if (modalBody) {
          modalBody.innerHTML = `
            <div style="display: flex; flex-direction: column; gap: 1rem;">
              <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span class="badge ${notice.priority === 'Urgent' ? 'badge-danger' : 'badge-warning'}">${notice.priority}</span>
                <span class="badge badge-accent">${notice.category}</span>
                <span class="text-xs text-muted" style="margin-left: auto;">${notice.date}</span>
              </div>
              <h2 style="font-size: 1.35rem;">${notice.title}</h2>
              <div style="background: var(--bg-elevated); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle); font-size: 0.85rem; color: var(--text-secondary);">
                <strong>Issued by:</strong> ${notice.department} • CHARUSAT Campus
              </div>
              <p style="font-size: 0.95rem; line-height: 1.7; color: var(--text-primary);">${notice.description}</p>
              <div style="display: flex; justify-content: flex-end; margin-top: 0.5rem;">
                <button type="button" class="btn btn-secondary" onclick="window.closeModal('noticeModal')">Close</button>
              </div>
            </div>
          `;
          if (window.openModal) window.openModal('noticeModal');
        }
      });
    });
  }
})();
