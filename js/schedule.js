
(function () {
  'use strict';

  // Master timetable data
  const TIMETABLE_DATA = {
    institution: "CHARUSAT University — DEPSTAR",
    department: "Department of Information Technology",
    program: "B.Tech IT — 3rd Semester (Odd Term 2026–27)",
    effectiveDate: "06/07/2026",
    days: ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
    timeSlots: [
      { id: "slot1", start: "09:10", end: "10:10", label: "09:10 AM – 10:10 AM" },
      { id: "slot2", start: "10:10", end: "11:10", label: "10:10 AM – 11:10 AM" },
      { id: "lunch", start: "11:10", end: "12:10", label: "11:10 AM – 12:10 PM", isBreak: true, breakName: "Lunch Break 🍴" },
      { id: "slot3", start: "12:10", end: "13:10", label: "12:10 PM – 01:10 PM" },
      { id: "slot4", start: "13:10", end: "14:10", label: "01:10 PM – 02:10 PM" },
      { id: "short", start: "14:10", end: "14:20", label: "02:10 PM – 02:20 PM", isBreak: true, breakName: "Short Break ☕" },
      { id: "slot5", start: "14:20", end: "15:20", label: "02:20 PM – 03:20 PM" },
      { id: "slot6", start: "15:20", end: "16:20", label: "03:20 PM – 04:20 PM" }
    ],
    facultyDirectory: {
      "JV": "Prof. Jignesh Vania",
      "RHP": "Prof. Ritesh Patel",
      "RVS": "Prof. R. V. Savaliya",
      "CUR": "Prof. Chintan Raval",
      "DAG": "Prof. Dharmendra Gohel",
      "PHP": "Prof. Parth Patel",
      "MH": "Prof. M. H. Vaghela",
      "JP": "Prof. Jignesh Patel",
      "VKM": "Prof. V. K. Modi",
      "ARP": "Prof. Amit Patel",
      "SHP": "Prof. Shital Patel",
      "MPD": "Prof. M. P. Dave",
      "VHP": "Prof. V. H. Panchal",
      "SYS": "Prof. S. Y. Shah",
      "PC": "Prof. P. Chauhan"
    },
    courseDirectory: {
      "ITUC201": { code: "ITUC201", shortName: "FCN", fullName: "Fundamentals of Computer Networks", credits: 4 },
      "CSUC201": { code: "CSUC201", shortName: "FDSA", fullName: "Fundamentals of Data Structures & Algorithms", credits: 4 },
      "ITUE203": { code: "ITUE203", shortName: "WDF", fullName: "Web Development Frameworks", credits: 4 },
      "CEUE203": { code: "CEUE203", shortName: "OOP", fullName: "Object Oriented Programming", credits: 4 },
      "MSUD203": { code: "MSUD203", shortName: "DM", fullName: "Discrete Mathematics", credits: 4 },
      "HSUV201": { code: "HSUV201", shortName: "CPI", fullName: "Creativity, Problem Solving & Innovation", credits: 2 },
      "COUNSEL": { code: "COUNSEL", shortName: "Counselling", fullName: "Student Mentorship & Guidance Session", credits: 0 },
      "SKILL": { code: "SKILL", shortName: "Skill Lab", fullName: "Skill Enhancement & Project Work", credits: 2 }
    }
  };

  // Expose master data globally
  window.SYNORA_TIMETABLE = TIMETABLE_DATA;

  document.addEventListener('DOMContentLoaded', () => {
    initTimetableControls();
    checkCurrentClass();
    setInterval(checkCurrentClass, 30000); // Check every 30s
  });

  // Timetable controls and filters
  function initTimetableControls() {
    const searchBox = document.getElementById('timetableSearch');
    const dayFilter = document.getElementById('dayFilter');
    const subjectFilter = document.getElementById('subjectFilter');
    const todayBtn = document.getElementById('todayBtn');
    const resetBtn = document.getElementById('resetBtn');
    const printBtn = document.getElementById('printBtn');
    const classCells = document.querySelectorAll('.class-cell');

    function applyFilters() {
      const query = (searchBox ? searchBox.value.trim().toLowerCase() : '');
      const selectedDay = (dayFilter ? dayFilter.value : 'all');
      const selectedSubject = (subjectFilter ? subjectFilter.value : 'all');

      classCells.forEach(cell => {
        const day = cell.getAttribute('data-day') || '';
        const subject = cell.getAttribute('data-subject') || '';
        const code = cell.getAttribute('data-code') || '';
        const room = cell.getAttribute('data-room') || '';
        const faculty = cell.getAttribute('data-faculty') || '';
        const text = cell.textContent.toLowerCase();

        let matchesSearch = true;
        if (query) {
          matchesSearch = text.includes(query) ||
                          subject.toLowerCase().includes(query) ||
                          code.toLowerCase().includes(query) ||
                          room.toLowerCase().includes(query) ||
                          faculty.toLowerCase().includes(query);
        }

        let matchesDay = (selectedDay === 'all' || day.toLowerCase() === selectedDay.toLowerCase());
        let matchesSubject = (selectedSubject === 'all' || subject.toLowerCase().includes(selectedSubject.toLowerCase()) || code.toLowerCase() === selectedSubject.toLowerCase());

        if (matchesSearch && matchesDay && matchesSubject) {
          cell.style.opacity = '1';
          cell.style.transform = 'scale(1)';
          cell.classList.remove('dimmed');
        } else {
          cell.style.opacity = '0.25';
          cell.classList.add('dimmed');
        }
      });

      // Filter day column headers if day filter applied
      const dayHeaders = document.querySelectorAll('th[data-day]');
      dayHeaders.forEach(th => {
        const thDay = th.getAttribute('data-day');
        if (selectedDay === 'all' || thDay.toLowerCase() === selectedDay.toLowerCase()) {
          th.style.opacity = '1';
        } else {
          th.style.opacity = '0.35';
        }
      });
    }

    if (searchBox) {
      searchBox.addEventListener('input', applyFilters);
    }

    if (dayFilter) {
      dayFilter.addEventListener('change', applyFilters);
    }

    if (subjectFilter) {
      subjectFilter.addEventListener('change', applyFilters);
    }

    if (todayBtn) {
      todayBtn.addEventListener('click', () => {
        const todayName = getTodayName();
        if (dayFilter) {
          dayFilter.value = todayName;
          applyFilters();
        }
        highlightTodayColumn(todayName);
        if (window.showToast) {
          window.showToast(`Showing schedule for ${todayName}`, 'info');
        }
      });
    }

    if (resetBtn) {
      resetBtn.addEventListener('click', () => {
        if (searchBox) searchBox.value = '';
        if (dayFilter) dayFilter.value = 'all';
        if (subjectFilter) subjectFilter.value = 'all';
        applyFilters();
        document.querySelectorAll('.class-cell, th[data-day]').forEach(el => {
          el.style.opacity = '1';
          el.classList.remove('dimmed');
        });
        if (window.showToast) {
          window.showToast('Schedule filters reset', 'info');
        }
      });
    }

    if (printBtn) {
      printBtn.addEventListener('click', () => {
        window.print();
      });
    }

    // Cell click modal handler
    classCells.forEach(cell => {
      cell.addEventListener('click', function () {
        const subject = this.getAttribute('data-subject') || 'N/A';
        const code = this.getAttribute('data-code') || 'N/A';
        const room = this.getAttribute('data-room') || 'N/A';
        const faculty = this.getAttribute('data-faculty') || 'N/A';
        const day = this.getAttribute('data-day') || 'N/A';
        const isPractical = this.classList.contains('practical');
        const row = this.closest('tr');
        const time = row ? row.querySelector('.time-slot')?.textContent.trim() : 'N/A';

        const facultyFull = TIMETABLE_DATA.facultyDirectory[faculty] || faculty;
        const courseInfo = TIMETABLE_DATA.courseDirectory[code] || { fullName: subject, credits: 4 };

        const modalBody = document.getElementById('classModalDetails');
        if (modalBody) {
          modalBody.innerHTML = `
            <div style="display: flex; flex-direction: column; gap: 1rem;">
              <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.75rem;">
                <div>
                  <h3 style="margin-bottom: 0.25rem;">${courseInfo.fullName}</h3>
                  <span class="badge ${isPractical ? 'badge-warning' : 'badge-accent'}">${isPractical ? 'Practical Lab Session' : 'Theory Lecture'}</span>
                </div>
                <span class="badge badge-info font-mono" style="font-size: 0.9rem;">${code}</span>
              </div>
              <div class="grid grid-2" style="gap: 1rem;">
                <div style="background: var(--bg-elevated); padding: 0.85rem; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                  <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Day & Time</span>
                  <p style="font-weight: 700; color: var(--text-primary); margin-top: 0.2rem;">${day} | ${time}</p>
                </div>
                <div style="background: var(--bg-elevated); padding: 0.85rem; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                  <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Classroom / Lab</span>
                  <p style="font-weight: 700; color: var(--text-primary); margin-top: 0.2rem;">Room ${room}</p>
                </div>
                <div style="background: var(--bg-elevated); padding: 0.85rem; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                  <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Faculty</span>
                  <p style="font-weight: 700; color: var(--text-primary); margin-top: 0.2rem;">${facultyFull} (${faculty})</p>
                </div>
                <div style="background: var(--bg-elevated); padding: 0.85rem; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                  <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Academic Credits</span>
                  <p style="font-weight: 700; color: var(--text-primary); margin-top: 0.2rem;">${courseInfo.credits} Credits</p>
                </div>
              </div>
              ${isPractical ? `
                <div style="background: var(--warning-soft); border: 1px solid rgba(242, 184, 75, 0.3); border-radius: var(--radius-sm); padding: 0.75rem; font-size: 0.85rem; color: var(--warning);">
                  <strong>Lab Batch Allocation Note:</strong> Batch A, B, and C rotate through allocated rooms 204, 206, 208, 218, and 303 as scheduled.
                </div>
              ` : ''}
            </div>
          `;
          if (window.openModal) {
            window.openModal('classModal');
          }
        }
      });
    });

    // Initial highlight for today
    highlightTodayColumn(getTodayName());
  }

  function getTodayName() {
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    return days[new Date().getDay()];
  }

  function highlightTodayColumn(todayName) {
    const dayHeaders = document.querySelectorAll('th[data-day]');
    dayHeaders.forEach(th => {
      if (th.getAttribute('data-day') === todayName) {
        th.style.borderBottom = '3px solid var(--accent-primary)';
        th.style.color = 'var(--accent-primary)';
      }
    });
  }

  // Live current class detection
  function checkCurrentClass() {
    const currentClassBanner = document.getElementById('currentClassBanner');
    const currentClassText = document.getElementById('currentClassText');
    if (!currentClassText) return;

    const now = new Date();
    const today = getTodayName();

    if (today === 'Sunday') {
      currentClassText.textContent = "No academic classes scheduled on Sunday.";
      return;
    }

    const currentMinutes = now.getHours() * 60 + now.getMinutes();
    let found = false;

    // Check cells matching today
    const cells = document.querySelectorAll(`.class-cell[data-day="${today}"]`);
    cells.forEach(cell => {
      cell.classList.remove('current-class-highlight');
      const row = cell.closest('tr');
      const timeCell = row ? row.querySelector('.time-slot') : null;
      if (!timeCell) return;

      const timeText = timeCell.textContent.trim();
      const parts = timeText.split('-');
      if (parts.length !== 2) return;

      const startMin = parseTimeToMinutes(parts[0]);
      const endMin = parseTimeToMinutes(parts[1]);

      if (currentMinutes >= startMin && currentMinutes < endMin) {
        cell.classList.add('current-class-highlight');
        const subject = cell.getAttribute('data-subject');
        const room = cell.getAttribute('data-room');
        const faculty = cell.getAttribute('data-faculty');
        currentClassText.innerHTML = `<strong>Ongoing:</strong> ${subject} in Room ${room} with ${faculty} (${timeText})`;
        found = true;
      }
    });

    if (!found) {
      if (currentMinutes < parseTimeToMinutes("09:10")) {
        currentClassText.textContent = `Classes start at 09:10 AM today (${today}). First session: WDF / FCN.`;
      } else if (currentMinutes >= parseTimeToMinutes("16:20")) {
        currentClassText.textContent = `All regular academic sessions completed for today (${today}).`;
      } else if (currentMinutes >= parseTimeToMinutes("11:10") && currentMinutes < parseTimeToMinutes("12:10")) {
        currentClassText.textContent = "Currently Lunch Break (11:10 AM – 12:10 PM 🍴)";
      } else if (currentMinutes >= parseTimeToMinutes("14:10") && currentMinutes < parseTimeToMinutes("14:20")) {
        currentClassText.textContent = "Currently Short Break (02:10 PM – 02:20 PM ☕)";
      } else {
        currentClassText.textContent = `Break or transition period between academic blocks.`;
      }
    }
  }

  function parseTimeToMinutes(str) {
    const s = str.trim();
    const [h, m] = s.split(':').map(Number);
    // Academic day runs 09:10 to 16:20. Times 1:10 to 4:20 are PM.
    let hours = h;
    if (hours >= 1 && hours <= 6) {
      hours += 12;
    }
    return hours * 60 + m;
  }
})();
