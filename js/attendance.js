
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', () => {
    initAttendance();
  });

  async function initAttendance() {
    const tableBody = document.getElementById('attendanceTableBody');
    const cardsGrid = document.getElementById('attendanceCardsGrid');
    if (!tableBody && !cardsGrid) return;

    const dataPath = window.location.pathname.includes('/pages/') ? '../data/attendance.json' : 'data/attendance.json';

    try {
      const res = await fetch(dataPath);
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = await res.json();

      updateGrossSummary(data);
      if (tableBody) renderTable(data, tableBody);
      if (cardsGrid) renderCards(data, cardsGrid);
    } catch (err) {
      console.error('Failed to load attendance data:', err);
    }
  }

  // Calculate lecture gross totals
  function updateGrossSummary(data) {
    let lectPresent = 0;
    let lectTotal = 0;

    data.forEach(item => {
      if (item.lecture) {
        lectPresent += item.lecture.present;
        lectTotal += item.lecture.total;
      }
    });

    const grossPct = lectTotal > 0 ? Math.round((lectPresent / lectTotal) * 100) : 0;

    const numberEl = document.getElementById('grossPctNumber');
    const subEl = document.getElementById('grossPctSub');

    if (numberEl) numberEl.textContent = `${grossPct}%`;
    if (subEl) subEl.textContent = `${lectPresent} / ${lectTotal} classes`;
  }

  // Render attendance table
  function renderTable(data, container) {
    container.innerHTML = data.map(item => {
      const lect = item.lecture;
      const lab = item.lab;

      const lectDisplay = lect ? `<strong>${lect.percentage}%</strong> <span class="att-num-pill">(${lect.present} / ${lect.total})</span>` : '—';
      const labDisplay = lab ? `<strong>${lab.percentage}%</strong> <span class="att-num-pill">(${lab.present} / ${lab.total})</span>` : '<span class="text-muted">—</span>';

      return `
        <tr>
          <td>
            <div class="att-course-cell">
              <span class="att-course-code">${item.code}</span>
              <strong style="color: var(--text-primary);">${item.shortName}</strong>
            </div>
          </td>
          <td>
            <div class="att-course-name">${item.name}</div>
          </td>
          <td>${lectDisplay}</td>
          <td>${labDisplay}</td>
        </tr>
      `;
    }).join('');
  }

  // Render course cards
  function renderCards(data, container) {
    container.innerHTML = data.map(item => {
      const lect = item.lecture;
      const lab = item.lab;

      let labBlock = `
        <div class="att-session-block">
          <div class="att-session-row">
            <span class="att-session-type">Lab Practical</span>
            <span class="text-muted">—</span>
          </div>
        </div>
      `;

      if (lab) {
        labBlock = `
          <div class="att-session-block">
            <div class="att-session-row">
              <span class="att-session-type">Lab Practical</span>
              <span class="font-mono text-xs">${lab.present} / ${lab.total}</span>
              <strong>${lab.percentage}%</strong>
            </div>
            <div class="att-bar">
              <div class="att-bar-fill neutral" style="width: ${lab.percentage}%;"></div>
            </div>
          </div>
        `;
      }

      return `
        <article class="att-subject-card">
          <div class="att-subject-head">
            <div>
              <span class="att-course-code">${item.code}</span>
              <h3 class="att-subject-title">${item.shortName}</h3>
              <p class="text-xs text-muted" style="margin: 0;">${item.name}</p>
            </div>
          </div>

          <div class="att-session-block">
            <div class="att-session-row">
              <span class="att-session-type">Theory Lecture</span>
              <span class="font-mono text-xs">${lect.present} / ${lect.total}</span>
              <strong>${lect.percentage}%</strong>
            </div>
            <div class="att-bar">
              <div class="att-bar-fill neutral" style="width: ${lect.percentage}%;"></div>
            </div>
          </div>

          ${labBlock}
        </article>
      `;
    }).join('');
  }
})();
