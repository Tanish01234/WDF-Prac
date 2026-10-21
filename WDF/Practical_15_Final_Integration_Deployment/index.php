<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Practical 15: Final Integration, Testing & Local Deployment</title>
  <link rel="stylesheet" href="../../css/global.css">
  <link rel="stylesheet" href="../../css/components.css">
  <style>
    body { background: var(--bg-primary); color: var(--text-primary); padding: 2.5rem 1.5rem; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
    .container { max-width: 1140px; margin: 0 auto; }
    .card { background: var(--bg-card); border: 1px solid var(--border-medium); border-radius: var(--radius-lg); padding: 2rem; margin-bottom: 2rem; box-shadow: var(--shadow-lg); }
    .grid-practicals { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1rem; margin-top: 1rem; }
    .p-card { background: var(--bg-elevated); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; transition: border-color 0.2s, transform 0.2s; }
    .p-card:hover { border-color: var(--accent-primary); transform: translateY(-2px); }
    .test-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; margin-top: 1rem; }
    .test-table th, .test-table td { padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-subtle); text-align: left; }
    .test-table th { background: var(--bg-elevated); color: var(--text-secondary); text-transform: uppercase; font-size: 0.75rem; }
  </style>
</head>
<body>

  <div class="container">
    <header style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2.5rem; border-bottom: 1px solid var(--border-medium); padding-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
      <div>
        <span class="badge badge-accent">CHARUSAT DEPSTAR ITUE203 • Semester 3</span>
        <h1 style="font-size: 2.1rem; font-weight: 800; margin-top: 0.35rem; letter-spacing: -0.02em;">
          Practical 15: Master Integration, Automated Testing & Deployment
        </h1>
        <p style="color: var(--text-secondary); font-size: 1rem; margin-top: 0.25rem;">
          Synora Academic Student Portal — Practicals 01 through 15 Unified Verification Console
        </p>
      </div>
      <div>
        <a href="../../index.html" class="btn btn-primary">Launch Master Student Portal →</a>
      </div>
    </header>

    <!-- Automated Test Suite Runner -->
    <section class="card">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
          <h2 style="font-size: 1.35rem; font-weight: 700;">Automated 15-Practical End-to-End Test Suite</h2>
          <p style="font-size: 0.85rem; color: var(--text-secondary);">Direct programmatic verification of files, database tables, sessions & APIs</p>
        </div>
        <div style="display: flex; gap: 0.75rem; align-items: center;">
          <span id="overallRateBadge" class="badge badge-success" style="font-size: 0.9rem; padding: 0.4rem 0.85rem;">Running Tests...</span>
          <button type="button" class="btn btn-secondary btn-sm" onclick="runTests()">🔄 Re-Run Suite</button>
        </div>
      </div>

      <div style="overflow-x: auto;">
        <table class="test-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Practical</th>
              <th>Test Target & Specification</th>
              <th>Status</th>
              <th>Execution Output</th>
            </tr>
          </thead>
          <tbody id="testTableBody">
            <tr><td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">Executing automated tests...</td></tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Individual Practical Directories Launchpad -->
    <section class="card">
      <h2 style="font-size: 1.35rem; font-weight: 700; margin-bottom: 0.5rem;">Independent Practical Demonstrations (P1 – P15)</h2>
      <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.5rem;">
        Each practical folder is self-contained with its own HTML, CSS, JavaScript, PHP, JSON, or SQL implementations.
      </p>

      <div class="grid-practicals">
        <!-- P1 -->
        <div class="p-card">
          <div>
            <span class="badge badge-accent text-xs">P01</span>
            <h4 style="font-size: 1.05rem; margin: 0.4rem 0;">Project Initiation & Sitemap</h4>
            <p style="font-size: 0.8rem; color: var(--text-secondary);">Requirements, 15-page sitemap, responsive wireframes, Git setup, and viva notes.</p>
          </div>
          <div style="margin-top: 1rem;"><a href="../Practical_01_Project_Initiation/index.html" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">Launch P1 Demo →</a></div>
        </div>

        <!-- P2 -->
        <div class="p-card">
          <div>
            <span class="badge badge-accent text-xs">P02</span>
            <h4 style="font-size: 1.05rem; margin: 0.4rem 0;">Semantic HTML5 Structure</h4>
            <p style="font-size: 0.8rem; color: var(--text-secondary);">13 linked semantic HTML5 pages, skip-to-content, and WCAG accessibility.</p>
          </div>
          <div style="margin-top: 1rem;"><a href="../Practical_02_Semantic_HTML5/index.html" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">Launch P2 Demo →</a></div>
        </div>

        <!-- P3 -->
        <div class="p-card">
          <div>
            <span class="badge badge-accent text-xs">P03</span>
            <h4 style="font-size: 1.05rem; margin: 0.4rem 0;">Responsive UI CSS Grid & Flexbox</h4>
            <p style="font-size: 0.8rem; color: var(--text-secondary);">Mobile-first layouts tested across 375px, 768px, and 1440px viewports.</p>
          </div>
          <div style="margin-top: 1rem;"><a href="../Practical_03_Responsive_UI_CSS/index.html" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">Launch P3 Demo →</a></div>
        </div>

        <!-- P4 -->
        <div class="p-card">
          <div>
            <span class="badge badge-accent text-xs">P04</span>
            <h4 style="font-size: 1.05rem; margin: 0.4rem 0;">JavaScript DOM & Events</h4>
            <p style="font-size: 0.8rem; color: var(--text-secondary);">Collapsible FAQ, image slider, modal dialog, and theme switcher with localStorage.</p>
          </div>
          <div style="margin-top: 1rem;"><a href="../Practical_04_JavaScript_DOM_Events/index.html" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">Launch P4 Demo →</a></div>
        </div>

        <!-- P5 -->
        <div class="p-card">
          <div>
            <span class="badge badge-accent text-xs">P05</span>
            <h4 style="font-size: 1.05rem; margin: 0.4rem 0;">Registration Form Regex Validation</h4>
            <p style="font-size: 0.8rem; color: var(--text-secondary);">Real-time input constraints, password strength meter, and error indicators.</p>
          </div>
          <div style="margin-top: 1rem;"><a href="../Practical_05_Registration_Validation/index.html" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">Launch P5 Demo →</a></div>
        </div>

        <!-- P6 -->
        <div class="p-card">
          <div>
            <span class="badge badge-accent text-xs">P06</span>
            <h4 style="font-size: 1.05rem; margin: 0.4rem 0;">JSON Fetch, Search & Filter</h4>
            <p style="font-size: 0.8rem; color: var(--text-secondary);">Dynamic rendering of events, students, and FAQs JSON datasets (16 items each).</p>
          </div>
          <div style="margin-top: 1rem;"><a href="../Practical_06_JSON_Fetch_Search_Filter/index.html" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">Launch P6 Demo →</a></div>
        </div>

        <!-- P7 -->
        <div class="p-card">
          <div>
            <span class="badge badge-accent text-xs">P07</span>
            <h4 style="font-size: 1.05rem; margin: 0.4rem 0;">PHP Form Processing & CSV/JSON</h4>
            <p style="font-size: 0.8rem; color: var(--text-secondary);">Server-side validation, sanitization, and concurrent write file locking (flock).</p>
          </div>
          <div style="margin-top: 1rem;"><a href="../Practical_07_PHP_Form_CSV_JSON/index.php" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">Launch P7 Demo →</a></div>
        </div>

        <!-- P8 -->
        <div class="p-card">
          <div>
            <span class="badge badge-accent text-xs">P08</span>
            <h4 style="font-size: 1.05rem; margin: 0.4rem 0;">MySQL Schema & Connectivity</h4>
            <p style="font-size: 0.8rem; color: var(--text-secondary);">Normalized 3NF relational schema, seed data, and dual PDO/MySQLi connection tests.</p>
          </div>
          <div style="margin-top: 1rem;"><a href="../Practical_08_MySQL_Schema_Connectivity/connection-test.php" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">Launch P8 Demo →</a></div>
        </div>

        <!-- P9 -->
        <div class="p-card">
          <div>
            <span class="badge badge-accent text-xs">P09</span>
            <h4 style="font-size: 1.05rem; margin: 0.4rem 0;">Secure Registration & Password Hash</h4>
            <p style="font-size: 0.8rem; color: var(--text-secondary);">MySQL prepared statements, duplicate checks, and Bcrypt password hashing.</p>
          </div>
          <div style="margin-top: 1rem;"><a href="../Practical_09_Secure_Registration/register.php" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">Launch P9 Demo →</a></div>
        </div>

        <!-- P10 -->
        <div class="p-card">
          <div>
            <span class="badge badge-accent text-xs">P10</span>
            <h4 style="font-size: 1.05rem; margin: 0.4rem 0;">Login, Sessions & RBAC</h4>
            <p style="font-size: 0.8rem; color: var(--text-secondary);">password_verify(), session fixation defense, student/admin roles, and 30-min timeout.</p>
          </div>
          <div style="margin-top: 1rem;"><a href="../Practical_10_Login_Sessions_RBAC/login.php" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">Launch P10 Demo →</a></div>
        </div>

        <!-- P11 -->
        <div class="p-card">
          <div>
            <span class="badge badge-accent text-xs">P11</span>
            <h4 style="font-size: 1.05rem; margin: 0.4rem 0;">Student Management CRUD</h4>
            <p style="font-size: 0.8rem; color: var(--text-secondary);">MySQLi prepared statements, search, batch filter, and 22+ seeded student records.</p>
          </div>
          <div style="margin-top: 1rem;"><a href="../Practical_11_Student_Management_CRUD/index.php" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">Launch P11 Demo →</a></div>
        </div>

        <!-- P12 -->
        <div class="p-card">
          <div>
            <span class="badge badge-accent text-xs">P12</span>
            <h4 style="font-size: 1.05rem; margin: 0.4rem 0;">Event Management & Poster Upload</h4>
            <p style="font-size: 0.8rem; color: var(--text-secondary);">Server-side MIME validation (finfo), 2MB limit, unique hashes, and .htaccess lock.</p>
          </div>
          <div style="margin-top: 1rem;"><a href="../Practical_12_Event_Management_Upload/index.php" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">Launch P12 Demo →</a></div>
        </div>

        <!-- P13 -->
        <div class="p-card">
          <div>
            <span class="badge badge-accent text-xs">P13</span>
            <h4 style="font-size: 1.05rem; margin: 0.4rem 0;">PHP JSON API & AJAX CRUD</h4>
            <p style="font-size: 0.8rem; color: var(--text-secondary);">RESTful endpoints (GET/POST/PUT/DELETE) and async DOM updates without reload.</p>
          </div>
          <div style="margin-top: 1rem;"><a href="../Practical_13_PHP_JSON_API_AJAX_CRUD/index.html" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">Launch P13 Demo →</a></div>
        </div>

        <!-- P14 -->
        <div class="p-card">
          <div>
            <span class="badge badge-accent text-xs">P14</span>
            <h4 style="font-size: 1.05rem; margin: 0.4rem 0;">Admin Dashboard & Audit Log</h4>
            <p style="font-size: 0.8rem; color: var(--text-secondary);">Real SQL aggregate metrics and non-repudiation security audit log stream.</p>
          </div>
          <div style="margin-top: 1rem;"><a href="../Practical_14_Admin_Dashboard_RBAC_Audit/admin-dashboard.php" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">Launch P14 Demo →</a></div>
        </div>

        <!-- P15 -->
        <div class="p-card" style="border: 1px solid var(--accent-primary);">
          <div>
            <span class="badge badge-success text-xs">P15</span>
            <h4 style="font-size: 1.05rem; margin: 0.4rem 0;">Master Student Portal</h4>
            <p style="font-size: 0.8rem; color: var(--text-secondary);">Full end-to-end integration combining all 14 preceding practicals.</p>
          </div>
          <div style="margin-top: 1rem;"><a href="../../index.html" class="btn btn-primary btn-sm" style="width: 100%; justify-content: center;">Launch Synora Portal →</a></div>
        </div>
      </div>
    </section>

    <!-- Viva Questions & Answers Panel -->
    <section class="card">
      <h2 style="font-size: 1.35rem; font-weight: 700; margin-bottom: 0.5rem;">Academic Viva Preparation Guide</h2>
      <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.25rem;">Concise answers reflecting the actual implementation in this project</p>

      <div style="display: flex; flex-direction: column; gap: 0.85rem; font-size: 0.875rem;">
        <details style="background: var(--bg-elevated); padding: 0.85rem 1.25rem; border-radius: 6px; cursor: pointer;">
          <summary style="font-weight: 700; color: var(--text-primary);">Q: Why are prepared statements essential for SQL execution?</summary>
          <p style="margin-top: 0.5rem; color: var(--text-secondary);">Prepared statements separate the query structure from user data. The database compiles the SQL template first, treating parameters as literal scalar data, eliminating SQL injection even if strings contain quotes or malicious payloads.</p>
        </details>

        <details style="background: var(--bg-elevated); padding: 0.85rem 1.25rem; border-radius: 6px; cursor: pointer;">
          <summary style="font-weight: 700; color: var(--text-primary);">Q: Why use password_hash() instead of MD5 or SHA256?</summary>
          <p style="margin-top: 0.5rem; color: var(--text-secondary);">MD5 and SHA256 are general-purpose hash functions designed to be fast, making them vulnerable to GPU brute-force and rainbow table attacks. <code>password_hash()</code> uses Bcrypt ($2y$) which is intentionally slow and automatically includes a unique cryptographic salt per password.</p>
        </details>

        <details style="background: var(--bg-elevated); padding: 0.85rem 1.25rem; border-radius: 6px; cursor: pointer;">
          <summary style="font-weight: 700; color: var(--text-primary);">Q: What is the purpose of session_regenerate_id(true)?</summary>
          <p style="margin-top: 0.5rem; color: var(--text-secondary);">It prevents Session Fixation attacks. If an attacker tricks a user into using a known session ID before logging in, regenerating the session ID upon authentication gives the user a brand-new token and deletes the old one.</p>
        </details>

        <details style="background: var(--bg-elevated); padding: 0.85rem 1.25rem; border-radius: 6px; cursor: pointer;">
          <summary style="font-weight: 700; color: var(--text-primary);">Q: How is file upload security enforced on the server?</summary>
          <p style="margin-top: 0.5rem; color: var(--text-secondary);">1. Server inspects binary magic bytes using <code>finfo(FILEINFO_MIME_TYPE)</code> (not filename extensions). 2. File size is capped to 2MB. 3. Randomized unique filenames are generated. 4. Apache <code>.htaccess</code> disables script execution in the upload directory.</p>
        </details>
      </div>
    </section>
  </div>

  <script>
    async function runTests() {
      const tbody = document.getElementById('testTableBody');
      const badge = document.getElementById('overallRateBadge');
      badge.textContent = 'Running Tests...';
      badge.className = 'badge badge-accent';

      try {
        const res = await fetch('test-runner.php');
        const data = await res.json();

        badge.textContent = `${data.passed_count} / ${data.total_count} Passed (${data.rate})`;
        badge.className = data.success ? 'badge badge-success' : 'badge badge-accent';

        tbody.innerHTML = data.tests.map(t => `
          <tr>
            <td><strong class="font-mono text-xs">${escapeHtml(t.id)}</strong></td>
            <td><span class="badge badge-accent text-xs">${escapeHtml(t.practical)}</span></td>
            <td><strong>${escapeHtml(t.name)}</strong></td>
            <td>
              <span class="badge ${t.status === 'PASS' ? 'badge-success' : 'badge-danger'} text-xs">
                ${t.status}
              </span>
            </td>
            <td class="text-xs text-muted font-mono">${escapeHtml(t.details || 'Verified successfully')}</td>
          </tr>
        `).join('');
      } catch (err) {
        badge.textContent = 'Execution Error';
        badge.className = 'badge badge-danger';
        tbody.innerHTML = `<tr><td colspan="5" style="color: #ef4444; padding: 2rem; text-align: center;">Error running test suite: ${escapeHtml(err.message)}</td></tr>`;
      }
    }

    function escapeHtml(t) {
      if (!t) return '';
      return String(t).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    document.addEventListener('DOMContentLoaded', runTests);
  </script>
</body>
</html>
