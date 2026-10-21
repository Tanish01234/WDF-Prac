// Practical 06 — Dynamic JSON Fetching, Search, Filter, Sort & Pagination
let currentDataset = 'events';
let rawData = [];
let filteredData = [];
let currentPage = 1;
const PAGE_SIZE = 6;

document.addEventListener('DOMContentLoaded', () => {
  loadDataset('events');

  // Search input with debounce
  let searchTimer;
  document.getElementById('searchInput').addEventListener('input', (e) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
      applyFilters();
    }, 250);
  });

  // Sort and filter changes
  document.getElementById('categoryFilter').addEventListener('change', applyFilters);
  document.getElementById('sortSelect').addEventListener('change', applyFilters);
});

async function loadDataset(datasetName) {
  currentDataset = datasetName;
  currentPage = 1;

  // Update tabs
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.target === datasetName);
  });

  const statusText = document.getElementById('loadingStatus');
  statusText.style.display = 'block';
  statusText.textContent = `Fetching data/${datasetName}.json via Fetch API...`;

  try {
    const res = await fetch(`data/${datasetName}.json`);
    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
    rawData = await res.json();
    statusText.style.display = 'none';

    populateCategoryOptions();
    applyFilters();
  } catch (err) {
    statusText.style.display = 'block';
    statusText.style.color = '#EF4444';
    statusText.textContent = `Error fetching ${datasetName}.json: ${err.message}. (Ensure page is served over HTTP/HTTPS, not file://).`;
  }
}

function populateCategoryOptions() {
  const catSelect = document.getElementById('categoryFilter');
  catSelect.innerHTML = '<option value="all">All Categories / Groups</option>';

  const categories = new Set();
  rawData.forEach(item => {
    if (item.category) categories.add(item.category);
    else if (item.batch) categories.add(item.batch);
  });

  categories.forEach(cat => {
    const opt = document.createElement('option');
    opt.value = cat;
    opt.textContent = cat;
    catSelect.appendChild(opt);
  });
}

function applyFilters() {
  const search = document.getElementById('searchInput').value.trim().toLowerCase();
  const selectedCat = document.getElementById('categoryFilter').value;
  const sortMode = document.getElementById('sortSelect').value;

  filteredData = rawData.filter(item => {
    // Category match
    const itemCat = item.category || item.batch || '';
    if (selectedCat !== 'all' && itemCat !== selectedCat) return false;

    // Search query match
    if (search) {
      const matchTitle = (item.title || item.name || item.question || '').toLowerCase().includes(search);
      const matchDesc = (item.description || item.email || item.answer || item.roll_no || '').toLowerCase().includes(search);
      return matchTitle || matchDesc;
    }
    return true;
  });

  // Sorting
  filteredData.sort((a, b) => {
    const titleA = (a.title || a.name || a.question || '').toLowerCase();
    const titleB = (b.title || b.name || b.question || '').toLowerCase();

    if (sortMode === 'title-asc') return titleA.localeCompare(titleB);
    if (sortMode === 'title-desc') return titleB.localeCompare(titleA);
    if (sortMode === 'date-asc' && a.event_date && b.event_date) return a.event_date.localeCompare(b.event_date);
    if (sortMode === 'date-desc' && a.event_date && b.event_date) return b.event_date.localeCompare(a.event_date);
    return 0;
  });

  currentPage = 1;
  renderView();
}

function renderView() {
  const container = document.getElementById('dataContainer');
  const countEl = document.getElementById('recordCountText');

  countEl.textContent = `Showing ${filteredData.length} of ${rawData.length} records`;

  if (filteredData.length === 0) {
    container.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--text-muted);">No records matched your search/filter criteria.</div>';
    document.getElementById('paginationControls').innerHTML = '';
    return;
  }

  // Slice for pagination
  const start = (currentPage - 1) * PAGE_SIZE;
  const pageSlice = filteredData.slice(start, start + PAGE_SIZE);

  container.innerHTML = pageSlice.map(item => {
    if (currentDataset === 'events') {
      return `
        <div class="data-card">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
            <span class="badge badge-blue">${escapeHtml(item.category)}</span>
            <span style="font-size: 0.75rem; color: var(--text-muted);">${escapeHtml(item.event_date)}</span>
          </div>
          <h3 style="font-size: 1.05rem; margin-bottom: 0.5rem;">${escapeHtml(item.title)}</h3>
          <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1rem; flex: 1;">${escapeHtml(item.description)}</p>
          <div style="font-size: 0.8rem; color: var(--text-muted);">📍 ${escapeHtml(item.location || 'DEPSTAR Lab')} • 👥 ${item.capacity || 50} Max</div>
        </div>
      `;
    } else if (currentDataset === 'students') {
      return `
        <div class="data-card">
          <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
            <span class="badge badge-green">${escapeHtml(item.batch || 'Batch A')}</span>
            <span style="font-family: monospace; font-size: 0.8rem; color: var(--accent-primary); font-weight: 700;">${escapeHtml(item.roll_no)}</span>
          </div>
          <h3 style="font-size: 1.1rem; margin-bottom: 0.25rem;">${escapeHtml(item.name)}</h3>
          <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">${escapeHtml(item.email)}</div>
          <div style="font-size: 0.8rem; color: var(--text-muted);">Program: ${escapeHtml(item.course || 'B.Tech IT')} • Sem ${item.semester || 3}</div>
        </div>
      `;
    } else {
      return `
        <div class="data-card">
          <span class="badge badge-blue" style="align-self: flex-start; margin-bottom: 0.5rem;">${escapeHtml(item.category || 'FAQ')}</span>
          <h3 style="font-size: 1rem; margin-bottom: 0.5rem;">${escapeHtml(item.question)}</h3>
          <p style="font-size: 0.85rem; color: var(--text-secondary); flex: 1;">${escapeHtml(item.answer)}</p>
        </div>
      `;
    }
  }).join('');

  renderPagination();
}

function renderPagination() {
  const totalPages = Math.ceil(filteredData.length / PAGE_SIZE) || 1;
  const container = document.getElementById('paginationControls');

  let html = `<span style="font-size: 0.85rem; color: var(--text-muted); align-self: center;">Page ${currentPage} of ${totalPages}</span> <div style="display: flex; gap: 0.35rem;">`;

  if (currentPage > 1) {
    html += `<button type="button" class="page-btn" onclick="goToPage(${currentPage - 1})">← Prev</button>`;
  }

  for (let i = 1; i <= totalPages; i++) {
    html += `<button type="button" class="page-btn ${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
  }

  if (currentPage < totalPages) {
    html += `<button type="button" class="page-btn" onclick="goToPage(${currentPage + 1})">Next →</button>`;
  }

  html += '</div>';
  container.innerHTML = html;
}

function goToPage(page) {
  currentPage = page;
  renderView();
}

function escapeHtml(text) {
  if (!text) return '';
  return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
