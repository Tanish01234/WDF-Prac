
(function () {
  'use strict';

  // State
  let allEvents = [];
  let filteredEvents = [];
  let currentPage = 1;
  const RECORDS_PER_PAGE = 6;
  let currentSearchQuery = '';
  let currentCategory = 'all';
  let currentSort = 'date-asc';

  document.addEventListener('DOMContentLoaded', () => {
    initEventsEngine();
  });

  function initEventsEngine() {
    const container = document.getElementById('eventsContainer');
    if (!container) return;

    // Detect correct data path (from root or pages/)
    const dataPath = window.location.pathname.includes('/pages/') ? '../data/events.json' : 'data/events.json';

    setupEventListeners();
    fetchEvents(dataPath);
  }

  // Fetch events
  async function fetchEvents(url) {
    const container = document.getElementById('eventsContainer');
    const loadingEl = document.getElementById('eventsLoading');
    const errorEl = document.getElementById('eventsError');
    const emptyEl = document.getElementById('eventsEmpty');

    if (loadingEl) loadingEl.style.display = 'flex';
    if (errorEl) errorEl.style.display = 'none';
    if (emptyEl) emptyEl.style.display = 'none';
    if (container) container.innerHTML = '';

    try {
      const response = await fetch(url);
      if (!response.ok) {
        throw new Error(`HTTP Error ${response.status}: Unable to load events data`);
      }

      allEvents = await response.json();
      filteredEvents = [...allEvents];

      if (loadingEl) loadingEl.style.display = 'none';

      // Update total events count badge
      const countBadge = document.getElementById('totalEventsCount');
      if (countBadge) countBadge.textContent = `${allEvents.length} Events`;

      processAndRender();
    } catch (err) {
      console.error('Fetch error:', err);
      if (loadingEl) loadingEl.style.display = 'none';
      if (errorEl) {
        errorEl.style.display = 'block';
        const msg = errorEl.querySelector('.error-message');
        if (msg) msg.textContent = err.message || 'Could not load event data from server.';
      }
    }
  }

  // Filter and sort
  function processAndRender() {
    let result = [...allEvents];

    // Filter by category
    if (currentCategory !== 'all') {
      result = result.filter(ev => ev.category.toLowerCase() === currentCategory.toLowerCase());
    }

    // Filter by search query
    if (currentSearchQuery.trim()) {
      const q = currentSearchQuery.trim().toLowerCase();
      result = result.filter(ev =>
        ev.title.toLowerCase().includes(q) ||
        ev.description.toLowerCase().includes(q) ||
        ev.location.toLowerCase().includes(q) ||
        ev.category.toLowerCase().includes(q) ||
        (ev.organizer && ev.organizer.toLowerCase().includes(q))
      );
    }

    // Sort
    result.sort((a, b) => {
      if (currentSort === 'date-asc') {
        return new Date(a.date) - new Date(b.date);
      } else if (currentSort === 'date-desc') {
        return new Date(b.date) - new Date(a.date);
      } else if (currentSort === 'title-asc') {
        return a.title.localeCompare(b.title);
      } else if (currentSort === 'title-desc') {
        return b.title.localeCompare(a.title);
      }
      return 0;
    });

    filteredEvents = result;

    // Reset to page 1 if current page is out of bounds
    const totalPages = Math.ceil(filteredEvents.length / RECORDS_PER_PAGE) || 1;
    if (currentPage > totalPages) {
      currentPage = 1;
    }

    renderCards();
    renderPagination();
  }

  // Render cards
  function renderCards() {
    const container = document.getElementById('eventsContainer');
    const emptyEl = document.getElementById('eventsEmpty');
    const resultCountEl = document.getElementById('filterResultCount');

    if (!container) return;

    if (resultCountEl) {
      resultCountEl.textContent = `Showing ${filteredEvents.length} of ${allEvents.length} events`;
    }

    if (filteredEvents.length === 0) {
      container.innerHTML = '';
      if (emptyEl) emptyEl.style.display = 'flex';
      return;
    }

    if (emptyEl) emptyEl.style.display = 'none';

    // Slice for current page
    const startIndex = (currentPage - 1) * RECORDS_PER_PAGE;
    const paginatedItems = filteredEvents.slice(startIndex, startIndex + RECORDS_PER_PAGE);

    const cardsHtml = paginatedItems.map(ev => {
      const badgeClass = getCategoryBadgeClass(ev.category);
      const formattedDate = formatDate(ev.date);

      return `
        <article class="card card-hover event-card" data-id="${ev.id}">
          <div class="event-card-top">
            <span class="badge ${badgeClass}">${ev.category}</span>
            <span class="badge ${ev.status === 'Open' ? 'badge-success' : 'badge-muted'}">${ev.status}</span>
          </div>
          <h3 class="event-title">${escapeHtml(ev.title)}</h3>
          <p class="event-desc">${escapeHtml(ev.description)}</p>
          <div class="event-meta-list">
            <div class="event-meta-item">
              <span class="meta-icon">📅</span>
              <span>${formattedDate}</span>
            </div>
            <div class="event-meta-item">
              <span class="meta-icon">⏰</span>
              <span>${ev.time}</span>
            </div>
            <div class="event-meta-item">
              <span class="meta-icon">📍</span>
              <span>${escapeHtml(ev.location)}</span>
            </div>
            ${ev.seats ? `
            <div class="event-meta-item">
              <span class="meta-icon">👥</span>
              <span>${ev.seats} Seats Available</span>
            </div>` : ''}
          </div>
          <div class="card-footer" style="padding-top: 1rem; margin-top: auto;">
            <span class="text-xs text-muted">By ${escapeHtml(ev.organizer || 'DEPSTAR')}</span>
            <button type="button" class="btn btn-sm btn-primary event-details-btn" data-id="${ev.id}">
              Details & Register →
            </button>
          </div>
        </article>
      `;
    }).join('');

    container.innerHTML = cardsHtml;

    // Attach click listeners to Details buttons
    container.querySelectorAll('.event-details-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const eventId = parseInt(e.currentTarget.getAttribute('data-id'), 10);
        showEventModal(eventId);
      });
    });
  }

  // Pagination
  function renderPagination() {
    const paginationContainer = document.getElementById('eventsPagination');
    if (!paginationContainer) return;

    const totalPages = Math.ceil(filteredEvents.length / RECORDS_PER_PAGE);

    if (totalPages <= 1) {
      paginationContainer.innerHTML = '';
      return;
    }

    let buttonsHtml = `
      <button type="button" class="page-btn prev-btn" ${currentPage === 1 ? 'disabled' : ''} aria-label="Previous Page">
        ‹ Previous
      </button>
    `;

    for (let i = 1; i <= totalPages; i++) {
      buttonsHtml += `
        <button type="button" class="page-btn ${i === currentPage ? 'active' : ''}" data-page="${i}" aria-label="Page ${i}">
          ${i}
        </button>
      `;
    }

    buttonsHtml += `
      <button type="button" class="page-btn next-btn" ${currentPage === totalPages ? 'disabled' : ''} aria-label="Next Page">
        Next ›
      </button>
    `;

    paginationContainer.innerHTML = buttonsHtml;

    // Bind Pagination Events
    paginationContainer.querySelector('.prev-btn')?.addEventListener('click', () => {
      if (currentPage > 1) {
        currentPage--;
        renderCards();
        renderPagination();
        scrollToEventsTop();
      }
    });

    paginationContainer.querySelector('.next-btn')?.addEventListener('click', () => {
      if (currentPage < totalPages) {
        currentPage++;
        renderCards();
        renderPagination();
        scrollToEventsTop();
      }
    });

    paginationContainer.querySelectorAll('.page-btn[data-page]').forEach(btn => {
      btn.addEventListener('click', (e) => {
        currentPage = parseInt(e.currentTarget.getAttribute('data-page'), 10);
        renderCards();
        renderPagination();
        scrollToEventsTop();
      });
    });
  }

  function scrollToEventsTop() {
    const el = document.getElementById('eventsHeaderSection');
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  // Event details modal
  function showEventModal(id) {
    const ev = allEvents.find(item => item.id === id);
    if (!ev) return;

    const modalBody = document.getElementById('eventModalBody');
    if (!modalBody) return;

    const badgeClass = getCategoryBadgeClass(ev.category);

    modalBody.innerHTML = `
      <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        <div>
          <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
            <span class="badge ${badgeClass}">${ev.category}</span>
            <span class="badge ${ev.status === 'Open' ? 'badge-success' : 'badge-muted'}">${ev.status}</span>
          </div>
          <h2 style="font-size: 1.45rem; line-height: 1.3;">${escapeHtml(ev.title)}</h2>
        </div>
        <p style="font-size: 0.95rem; color: var(--text-secondary); line-height: 1.6;">${escapeHtml(ev.description)}</p>
        <div style="background: var(--bg-elevated); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1.25rem; display: flex; flex-direction: column; gap: 0.75rem;">
          <div class="event-meta-item">
            <span class="meta-icon">📅</span>
            <span><strong>Date:</strong> ${formatDate(ev.date)}</span>
          </div>
          <div class="event-meta-item">
            <span class="meta-icon">⏰</span>
            <span><strong>Time:</strong> ${ev.time}</span>
          </div>
          <div class="event-meta-item">
            <span class="meta-icon">📍</span>
            <span><strong>Venue:</strong> ${escapeHtml(ev.location)}</span>
          </div>
          <div class="event-meta-item">
            <span class="meta-icon">🏛️</span>
            <span><strong>Organized By:</strong> ${escapeHtml(ev.organizer || 'CHARUSAT DEPSTAR')}</span>
          </div>
          <div class="event-meta-item">
            <span class="meta-icon">👥</span>
            <span><strong>Capacity:</strong> ${ev.seats || 'Open'} attendees</span>
          </div>
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem;">
          <button type="button" class="btn btn-secondary modal-close" data-modal-dismiss>Close</button>
          <button type="button" class="btn btn-primary" id="registerConfirmBtn" ${ev.status !== 'Open' ? 'disabled' : ''}>
            ${ev.status === 'Open' ? 'Confirm Registration (Free)' : 'Registration Closed'}
          </button>
        </div>
      </div>
    `;

    if (window.openModal) {
      window.openModal('eventModal');
    }

    // Modal close bindings
    modalBody.querySelector('[data-modal-dismiss]')?.addEventListener('click', () => {
      if (window.closeModal) window.closeModal('eventModal');
    });

    modalBody.querySelector('#registerConfirmBtn')?.addEventListener('click', () => {
      if (window.showToast) {
        window.showToast(`Successfully registered for "${ev.title}"! E-pass sent to student email.`, 'success', 5000);
      }
      if (window.closeModal) window.closeModal('eventModal');
    });
  }

  // Event listeners
  function setupEventListeners() {
    const searchInput = document.getElementById('eventSearchInput');
    const sortSelect = document.getElementById('eventSortSelect');
    const categoryChips = document.querySelectorAll('.category-chip');
    const retryBtn = document.getElementById('eventsRetryBtn');

    if (searchInput) {
      searchInput.addEventListener('input', (e) => {
        currentSearchQuery = e.target.value;
        currentPage = 1;
        processAndRender();
      });
    }

    if (sortSelect) {
      sortSelect.addEventListener('change', (e) => {
        currentSort = e.target.value;
        processAndRender();
      });
    }

    categoryChips.forEach(chip => {
      chip.addEventListener('click', (e) => {
        categoryChips.forEach(c => c.classList.remove('active'));
        e.currentTarget.classList.add('active');
        currentCategory = e.currentTarget.getAttribute('data-category') || 'all';
        currentPage = 1;
        processAndRender();
      });
    });

    if (retryBtn) {
      retryBtn.addEventListener('click', () => {
        const dataPath = window.location.pathname.includes('/pages/') ? '../data/events.json' : 'data/events.json';
        fetchEvents(dataPath);
      });
    }
  }

  // Helpers
  function getCategoryBadgeClass(category) {
    const map = {
      'Hackathon': 'badge-accent',
      'Workshop': 'badge-info',
      'Seminar': 'badge-warning',
      'Academic': 'badge-accent',
      'Sports': 'badge-success',
      'Cultural': 'badge-warning',
      'Placement': 'badge-info'
    };
    return map[category] || 'badge-muted';
  }

  function formatDate(isoStr) {
    if (!isoStr) return '';
    const d = new Date(isoStr);
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
  }

  function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>'"]/g, tag => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      "'": '&#39;',
      '"': '&quot;'
    }[tag] || tag));
  }
})();
