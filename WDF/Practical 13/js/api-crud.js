// Practical 13 — Asynchronous RESTful JSON API CRUD Client
let currentPage = 1;

document.addEventListener('DOMContentLoaded', () => {
  fetchStudents(1);

  document.getElementById('apiSearchInput').addEventListener('input', () => {
    fetchStudents(1);
  });
  document.getElementById('apiBatchFilter').addEventListener('change', () => {
    fetchStudents(1);
  });
});

async function fetchStudents(page = 1) {
  currentPage = page;
  const search = document.getElementById('apiSearchInput').value.trim();
  const batch = document.getElementById('apiBatchFilter').value;

  const url = new URL('api/students.php', window.location.href);
  url.searchParams.set('page', page);
  url.searchParams.set('limit', 8);
  if (search) url.searchParams.set('search', search);
  if (batch !== 'all') url.searchParams.set('batch', batch);

  logApiRequest('GET', url.toString());

  try {
    const res = await fetch(url);
    const json = await res.json();
    logApiResponse(res.status, json);

    renderTable(json.data);
    renderPagination(json.pagination);
  } catch (err) {
    logApiResponse('ERR', { error: err.message });
  }
}

function renderTable(students) {
  const tbody = document.getElementById('apiTableBody');
  if (!students || students.length === 0) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">No records found.</td></tr>';
    return;
  }

  tbody.innerHTML = students.map(s => `
    <tr>
      <td><strong style="color: var(--accent-primary); font-family: monospace;">${escapeHtml(s.roll_no)}</strong></td>
      <td><strong>${escapeHtml(s.name)}</strong></td>
      <td>${escapeHtml(s.email)}</td>
      <td><span class="badge badge-accent">${escapeHtml(s.batch)}</span></td>
      <td>${escapeHtml(s.course)}</td>
      <td style="text-align: right; white-space: nowrap;">
        <button type="button" class="btn btn-secondary btn-sm" onclick="editStudent(${s.id})">Edit (PUT)</button>
        <button type="button" class="btn btn-ghost btn-sm" style="color: #ef4444;" onclick="deleteStudent(${s.id}, '${escapeHtml(s.roll_no)}')">Delete</button>
      </td>
    </tr>
  `).join('');
}

function renderPagination(p) {
  const container = document.getElementById('apiPagination');
  if (!p) return;

  let html = `<span style="font-size: 0.8rem; color: var(--text-muted);">Showing page ${p.page} of ${p.total_pages || 1} (${p.total} total)</span> <div style="display: flex; gap: 0.35rem;">`;
  for (let i = 1; i <= (p.total_pages || 1); i++) {
    html += `<button type="button" class="btn btn-sm ${i === p.page ? 'btn-primary' : 'btn-secondary'}" onclick="fetchStudents(${i})">${i}</button>`;
  }
  html += '</div>';
  container.innerHTML = html;
}

async function addStudent(e) {
  e.preventDefault();
  const payload = {
    roll_no: document.getElementById('addRoll').value.trim(),
    name: document.getElementById('addName').value.trim(),
    email: document.getElementById('addEmail').value.trim(),
    mobile: document.getElementById('addMobile').value.trim(),
    batch: document.getElementById('addBatch').value,
    course: 'B.Tech IT'
  };

  logApiRequest('POST', 'api/students.php', payload);

  try {
    const res = await fetch('api/students.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const json = await res.json();
    logApiResponse(res.status, json);

    if (json.success) {
      document.getElementById('addStudentForm').reset();
      fetchStudents(1);
    } else {
      alert(json.message);
    }
  } catch (err) {
    logApiResponse('ERR', { error: err.message });
  }
}

async function editStudent(id) {
  const newMobile = prompt('Enter new mobile number for student #' + id + ':');
  if (!newMobile) return;

  const payload = { id, mobile: newMobile.trim() };
  logApiRequest('PUT', 'api/students.php', payload);

  try {
    const res = await fetch('api/students.php', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const json = await res.json();
    logApiResponse(res.status, json);
    fetchStudents(currentPage);
  } catch (err) {
    logApiResponse('ERR', { error: err.message });
  }
}

async function deleteStudent(id, rollNo) {
  if (!confirm(`Delete student ${rollNo}?`)) return;

  logApiRequest('DELETE', 'api/students.php', { id });

  try {
    const res = await fetch('api/students.php', {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id })
    });
    const json = await res.json();
    logApiResponse(res.status, json);
    fetchStudents(currentPage);
  } catch (err) {
    logApiResponse('ERR', { error: err.message });
  }
}

function logApiRequest(method, url, payload) {
  const el = document.getElementById('apiReqLog');
  el.textContent = `[REQUEST] ${method} ${url}\nPayload: ${payload ? JSON.stringify(payload) : 'None'}`;
}

function logApiResponse(status, json) {
  const el = document.getElementById('apiResLog');
  const codeBadge = document.getElementById('httpStatusBadge');
  codeBadge.textContent = `HTTP ${status}`;
  codeBadge.className = status >= 200 && status < 300 ? 'badge badge-success' : 'badge badge-accent';
  el.textContent = JSON.stringify(json, null, 2);
}

function escapeHtml(text) {
  if (!text) return '';
  return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
