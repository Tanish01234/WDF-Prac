
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', () => {
    initDashboard();
  });

  function initDashboard() {
    const isDashboard = document.getElementById('dashboardRoot');
    if (!isDashboard) return;

    renderDateAndGreeting();
    renderTodaySchedule();
    loadDashboardEvents();
    loadDashboardNotices();
  }

  // Set greeting and date
  function renderDateAndGreeting() {
    const greetingEl = document.getElementById('dashGreeting');
    const dateEl = document.getElementById('dashDate');

    const now = new Date();
    const hour = now.getHours();

    let greeting = 'Good morning';
    if (hour >= 12 && hour < 17) {
      greeting = 'Good afternoon';
    } else if (hour >= 17) {
      greeting = 'Good evening';
    }

    if (greetingEl) {
      greetingEl.textContent = `${greeting}, Tanis.`;
    }

    if (dateEl) {
      const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
      dateEl.textContent = now.toLocaleDateString('en-GB', options);
    }
  }

  // Sem 3 timetable data
  const officialSchedule = {
    Monday: [
      { start: '09:10', end: '10:10', time: '09:10 – 10:10', code: 'ITUE203', subject: 'Fundamentals of Web Development Frameworks', shortSub: 'WDF', location: 'Room 222', faculty: 'Prof. JV', type: 'lecture' },
      { start: '10:10', end: '11:10', time: '10:10 – 11:10', code: 'ITUC201', subject: 'Fundamentals of Computer Networks', shortSub: 'FCN', location: 'Room 222', faculty: 'Prof. RHP', type: 'lecture' },
      { start: '11:10', end: '12:10', time: '11:10 – 12:10', code: '', subject: 'Lunch Break', shortSub: 'Lunch Break', location: 'Campus Cafeteria', faculty: '', type: 'break' },
      {
        start: '12:10', end: '14:10', time: '12:10 – 02:10', code: 'ITUC201', subject: 'Computer Networks / WDF Practical Lab', shortSub: 'Lab Practical', location: 'Lab 218 / 303', faculty: '', type: 'practical',
        batches: [
          { batch: 'Batch A', desc: 'FCN(P) · Lab 218 · Prof. MH' },
          { batch: 'Batch B', desc: 'FCN(P) · Lab 218 · Prof. JP' },
          { batch: 'Batch C', desc: 'WDF(P) · Lab 303 · Prof. VKM' }
        ]
      },
      { start: '14:10', end: '14:20', time: '02:10 – 02:20', code: '', subject: 'Short Break', shortSub: 'Short Break', location: 'Campus', faculty: '', type: 'break' },
      {
        start: '14:20', end: '16:20', time: '02:20 – 04:20', code: 'CSUC201', subject: 'FDSA / OOP Practical Lab', shortSub: 'Lab Practical', location: 'Lab 208 / 204', faculty: '', type: 'practical',
        batches: [
          { batch: 'Batch A', desc: 'FDSA(P) · Lab 208 · Prof. ARP' },
          { batch: 'Batch B', desc: 'FDSA(P) · Lab 208 · Prof. ARP' },
          { batch: 'Batch C', desc: 'OOP(P) · Lab 204 · Prof. MPD' }
        ]
      }
    ],
    Tuesday: [
      { start: '09:10', end: '10:10', time: '09:10 – 10:10', code: 'ITUC201', subject: 'Fundamentals of Computer Networks', shortSub: 'FCN', location: 'Room 123', faculty: 'Prof. RHP', type: 'lecture' },
      { start: '10:10', end: '11:10', time: '10:10 – 11:10', code: 'CEUE203', subject: 'Object Oriented Programming', shortSub: 'OOP', location: 'Room 123', faculty: 'Prof. DAG', type: 'lecture' },
      { start: '11:10', end: '12:10', time: '11:10 – 12:10', code: '', subject: 'Lunch Break', shortSub: 'Lunch Break', location: 'Campus Cafeteria', faculty: '', type: 'break' },
      { start: '12:10', end: '13:10', time: '12:10 – 01:10', code: 'MSUD203', subject: 'Discrete Mathematics', shortSub: 'DM', location: 'Room 123', faculty: 'Prof. RVS', type: 'lecture' },
      { start: '13:10', end: '14:10', time: '01:10 – 02:10', code: 'COUNSEL', subject: 'Counselling / Mentoring Session', shortSub: 'Mentoring', location: 'Room 123', faculty: 'Prof. SYS', type: 'counsel' },
      { start: '14:10', end: '14:20', time: '02:10 – 02:20', code: '', subject: 'Short Break', shortSub: 'Short Break', location: 'Campus', faculty: '', type: 'break' },
      {
        start: '14:20', end: '16:20', time: '02:20 – 04:20', code: 'ITUE203', subject: 'Practical Lab Session', shortSub: 'Lab Practical', location: 'Lab 206 / 204 / 208', faculty: '', type: 'practical',
        batches: [
          { batch: 'Batch A (Tanis)', desc: 'WDF(P) · Room 206 · Prof. JP' },
          { batch: 'Batch B', desc: 'OOP(P) · Room 204 · Prof. MPD' },
          { batch: 'Batch C', desc: 'FDSA(P) · Room 208 · Prof. ARP' }
        ]
      }
    ],
    Wednesday: [
      { start: '09:10', end: '10:10', time: '09:10 – 10:10', code: 'MSUD203', subject: 'Discrete Mathematics', shortSub: 'DM', location: 'Room 124', faculty: 'Prof. RVS', type: 'lecture' },
      { start: '10:10', end: '11:10', time: '10:10 – 11:10', code: 'CSUC201', subject: 'Fundamentals of Data Structures & Algorithms', shortSub: 'FDSA', location: 'Room 124', faculty: 'Prof. PHP', type: 'lecture' },
      { start: '11:10', end: '12:10', time: '11:10 – 12:10', code: '', subject: 'Lunch Break', shortSub: 'Lunch Break', location: 'Campus Cafeteria', faculty: '', type: 'break' },
      { start: '12:10', end: '13:10', time: '12:10 – 01:10', code: 'ITUC201', subject: 'Fundamentals of Computer Networks', shortSub: 'FCN', location: 'Room 125', faculty: 'Prof. RHP', type: 'lecture' },
      { start: '13:10', end: '14:10', time: '01:10 – 02:10', code: 'COUNSEL', subject: 'Counselling / Mentoring Session', shortSub: 'Mentoring', location: 'Room 125', faculty: 'Prof. PC', type: 'counsel' },
      { start: '14:10', end: '14:20', time: '02:10 – 02:20', code: '', subject: 'Short Break', shortSub: 'Short Break', location: 'Campus', faculty: '', type: 'break' },
      {
        start: '14:20', end: '16:20', time: '02:20 – 04:20', code: 'ITUE203', subject: 'WDF & FCN Practical Lab', shortSub: 'Lab Practical', location: 'Lab 208 / 218', faculty: '', type: 'practical',
        batches: [
          { batch: 'Batch A', desc: 'WDF(P) · Lab 208 · Prof. SHP' },
          { batch: 'Batch B', desc: 'FCN(P) · Lab 218 · Prof. JP' },
          { batch: 'Batch C', desc: 'FCN(P) · Lab 218 · Prof. MH' }
        ]
      }
    ],
    Thursday: [
      { start: '09:10', end: '10:10', time: '09:10 – 10:10', code: 'CSUC201', subject: 'Fundamentals of Data Structures & Algorithms', shortSub: 'FDSA', location: 'Room 123', faculty: 'Prof. CUR', type: 'lecture' },
      { start: '10:10', end: '11:10', time: '10:10 – 11:10', code: 'CEUE203', subject: 'Object Oriented Programming', shortSub: 'OOP', location: 'Room 123', faculty: 'Prof. DAG', type: 'lecture' },
      { start: '11:10', end: '12:10', time: '11:10 – 12:10', code: '', subject: 'Lunch Break', shortSub: 'Lunch Break', location: 'Campus Cafeteria', faculty: '', type: 'break' },
      { start: '12:10', end: '14:10', time: '12:10 – 02:10', code: 'HSUV201', subject: 'Critical & Philosophical Inquiry', shortSub: 'CPI', location: 'Room 123', faculty: 'Prof. VHP', type: 'lecture' },
      { start: '14:10', end: '14:20', time: '02:10 – 02:20', code: '', subject: 'Short Break', shortSub: 'Short Break', location: 'Campus', faculty: '', type: 'break' },
      { start: '14:20', end: '15:20', time: '02:20 – 03:20', code: 'ITUE203', subject: 'Fundamentals of Web Development Frameworks', shortSub: 'WDF', location: 'Room 329', faculty: 'Prof. VKM', type: 'lecture' },
      { start: '15:20', end: '16:20', time: '03:20 – 04:20', code: 'MSUD203', subject: 'Discrete Mathematics', shortSub: 'DM', location: 'Room 123', faculty: 'Prof. RVS', type: 'lecture' }
    ],
    Friday: [
      { start: '09:10', end: '10:10', time: '09:10 – 10:10', code: 'ITUE203', subject: 'Fundamentals of Web Development Frameworks', shortSub: 'WDF', location: 'Room 123', faculty: 'Prof. JV', type: 'lecture' },
      { start: '10:10', end: '11:10', time: '10:10 – 11:10', code: 'MSUD203', subject: 'Discrete Mathematics', shortSub: 'DM', location: 'Room 124', faculty: 'Prof. RVS', type: 'lecture' },
      { start: '11:10', end: '12:10', time: '11:10 – 12:10', code: '', subject: 'Lunch Break', shortSub: 'Lunch Break', location: 'Campus Cafeteria', faculty: '', type: 'break' },
      {
        start: '12:10', end: '14:10', time: '12:10 – 02:10', code: 'CEUE203', subject: 'OOP / WDF / FDSA Practical Lab', shortSub: 'Lab Practical', location: 'Lab 204 / 206 / 208', faculty: '', type: 'practical',
        batches: [
          { batch: 'Batch A', desc: 'OOP(P) · Lab 204 · Prof. MPD' },
          { batch: 'Batch B', desc: 'WDF(P) · Lab 206 · Prof. JP' },
          { batch: 'Batch C', desc: 'FDSA(P) · Lab 208 · Prof. ARP' }
        ]
      },
      { start: '14:10', end: '14:20', time: '02:10 – 02:20', code: '', subject: 'Short Break', shortSub: 'Short Break', location: 'Campus', faculty: '', type: 'break' },
      { start: '14:20', end: '15:20', time: '02:20 – 03:20', code: 'CSUC201', subject: 'Fundamentals of Data Structures & Algorithms', shortSub: 'FDSA', location: 'Room 123', faculty: 'Prof. CUR', type: 'lecture' },
      { start: '15:20', end: '16:20', time: '03:20 – 04:20', code: 'ITUC201', subject: 'Fundamentals of Computer Networks', shortSub: 'FCN', location: 'Room 125', faculty: 'Prof. RHP', type: 'lecture' }
    ],
    Saturday: [
      { start: '09:10', end: '11:10', time: '09:10 – 11:10', code: 'SKILL', subject: 'Skill Enhancement & Project Practice', shortSub: 'Skill Lab', location: 'Innovation Lab', faculty: 'Faculty Mentors', type: 'practical' },
      { start: '11:10', end: '13:10', time: '11:10 – 01:10', code: 'REMEDIAL', subject: 'Remedial & Practical Doubt Clearing', shortSub: 'Remedial', location: 'Lab 208', faculty: 'Subject Faculties', type: 'lecture' }
    ]
  };

  // Convert time to minutes
  function toMinutes(timeStr) {
    if (!timeStr) return 0;
    const [h, m] = timeStr.split(':').map(Number);
    return h * 60 + m;
  }

  // Render today's classes
  function renderTodaySchedule() {
    const container = document.getElementById('dashScheduleList');
    if (!container) return;

    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const now = new Date();
    const todayName = days[now.getDay()];
    const currentMins = now.getHours() * 60 + now.getMinutes();

    // Default to Tuesday if weekend or not in schedule
    const activeDay = officialSchedule[todayName] ? todayName : 'Tuesday';
    const classes = officialSchedule[activeDay];

    const dayLabel = document.getElementById('dashScheduleDayName');
    if (dayLabel) {
      dayLabel.textContent = todayName === activeDay ? `Today (${activeDay})` : `${activeDay} (Official Schedule)`;
    }

    container.innerHTML = classes.map(item => {
      const startMins = toMinutes(item.start);
      const endMins = toMinutes(item.end);

      let statusClass = '';
      if (item.type === 'break') {
        statusClass = 'is-break';
      } else if (todayName === activeDay && currentMins >= endMins) {
        statusClass = 'is-past';
      } else if (todayName === activeDay && currentMins >= startMins && currentMins < endMins) {
        statusClass = 'is-current';
      }

      let badgeText = 'Lecture';
      if (item.type === 'practical') badgeText = 'Practical';
      else if (item.type === 'break') badgeText = 'Break';
      else if (item.type === 'counsel') badgeText = 'Mentoring';

      let batchesHtml = '';
      if (item.batches && item.batches.length > 0) {
        batchesHtml = `
          <div class="schedule-batch-breakdown">
            ${item.batches.map(b => `<span class="batch-tag"><strong>${b.batch}:</strong> ${b.desc}</span>`).join('')}
          </div>
        `;
      }

      const facultyLine = item.faculty ? ` · ${item.faculty}` : '';
      const subjectTitle = item.code && item.code !== 'COUNSEL' ? `${item.shortSub} — ${item.subject}` : item.subject;

      return `
        <div class="schedule-row ${statusClass}">
          <div class="schedule-time-col">${item.time}</div>
          <div class="schedule-details-col">
            <div class="schedule-subject-line">${subjectTitle}</div>
            <div class="schedule-sub-meta">${item.location}${facultyLine}</div>
            ${batchesHtml}
          </div>
          <div class="schedule-tag-col">
            <span class="class-type-badge ${item.type}">${badgeText}</span>
          </div>
        </div>
      `;
    }).join('');
  }

  // Load upcoming events
  async function loadDashboardEvents() {
    const container = document.getElementById('dashEventsPreview');
    if (!container) return;

    container.innerHTML = '<div class="text-sm text-muted" style="padding: 0.75rem 0;">Loading events...</div>';

    const prefix = window.location.pathname.includes('/pages/') ? '../data/' : 'data/';

    try {
      const res = await fetch(`${prefix}events.json`);
      if (!res.ok) throw new Error(`HTTP ${res.status}`);

      const events = await res.json();
      if (!events || events.length === 0) {
        container.innerHTML = '<div class="text-sm text-muted" style="padding: 0.75rem 0;">No upcoming events.</div>';
        return;
      }

      const previewEvents = events.slice(0, 3);
      container.innerHTML = previewEvents.map(ev => {
        let monthStr = 'OCT';
        let dayNum = '15';
        if (ev.date) {
          const parts = ev.date.split('-');
          if (parts.length === 3) {
            const d = new Date(ev.date);
            if (!isNaN(d.getTime())) {
              monthStr = d.toLocaleString('en-US', { month: 'short' }).toUpperCase();
              dayNum = d.getDate();
            }
          }
        }

        return `
          <div class="event-preview-item">
            <div class="event-date-box">
              <span class="event-date-month">${monthStr}</span>
              <span class="event-date-day">${dayNum}</span>
            </div>
            <div class="event-preview-info">
              <div class="event-preview-title" title="${ev.title}">${ev.title}</div>
              <div class="event-preview-meta">${ev.category} · ${ev.location}</div>
            </div>
            <a href="events.html" class="side-card-link">View</a>
          </div>
        `;
      }).join('');
    } catch (err) {
      console.error('Failed to load dashboard events:', err);
      container.innerHTML = `
        <div class="text-sm text-muted" style="padding: 0.75rem 0;">
          Unable to load events.
          <button type="button" id="retryEventsBtn" class="side-card-link" style="background: none; border: none; cursor: pointer; text-decoration: underline; margin-left: 0.4rem;">Retry</button>
        </div>
      `;
      const retryBtn = document.getElementById('retryEventsBtn');
      if (retryBtn) {
        retryBtn.addEventListener('click', loadDashboardEvents);
      }
    }
  }

  // Load notices
  async function loadDashboardNotices() {
    const container = document.getElementById('dashNoticesPreview');
    if (!container) return;

    container.innerHTML = '<div class="text-sm text-muted" style="padding: 0.75rem 0;">Loading notices...</div>';

    const prefix = window.location.pathname.includes('/pages/') ? '../data/' : 'data/';

    try {
      const res = await fetch(`${prefix}notices.json`);
      if (!res.ok) throw new Error(`HTTP ${res.status}`);

      const notices = await res.json();
      if (!notices || notices.length === 0) {
        container.innerHTML = '<div class="text-sm text-muted" style="padding: 0.75rem 0;">No notices published.</div>';
        return;
      }

      const previewNotices = notices.slice(0, 3);
      container.innerHTML = previewNotices.map(item => {
        let badgeClass = 'badge-muted';
        if (item.priority === 'Urgent') badgeClass = 'badge-danger';
        else if (item.priority === 'High') badgeClass = 'badge-warning';
        else if (item.priority === 'Important') badgeClass = 'badge-accent';

        return `
          <div class="notice-preview-item">
            <div class="notice-preview-top">
              <span class="badge ${badgeClass}" style="font-size: 0.65rem;">${item.priority}</span>
              <span class="text-xs text-muted font-mono">${item.date}</span>
            </div>
            <div class="notice-preview-title">${item.title}</div>
            <div class="notice-preview-dept">${item.department}</div>
          </div>
        `;
      }).join('');
    } catch (err) {
      console.error('Failed to load dashboard notices:', err);
      container.innerHTML = `
        <div class="text-sm text-muted" style="padding: 0.75rem 0;">
          Unable to load notices.
          <button type="button" id="retryNoticesBtn" class="side-card-link" style="background: none; border: none; cursor: pointer; text-decoration: underline; margin-left: 0.4rem;">Retry</button>
        </div>
      `;
      const retryBtn = document.getElementById('retryNoticesBtn');
      if (retryBtn) {
        retryBtn.addEventListener('click', loadDashboardNotices);
      }
    }
  }

  window.loadDashboardEvents = loadDashboardEvents;
  window.loadDashboardNotices = loadDashboardNotices;
})();
