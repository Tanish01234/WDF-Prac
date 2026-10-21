<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Practical 07: PHP Form Processing with CSV/JSON Storage</title>
  <link rel="stylesheet" href="../../css/global.css">
  <link rel="stylesheet" href="../../css/components.css">
  <style>
    body { background: var(--bg-primary); color: var(--text-primary); padding: 2.5rem 1.5rem; }
    .container { max-width: 800px; margin: 0 auto; }
    .form-card { background: var(--bg-card); border: 1px solid var(--border-medium); border-radius: var(--radius-lg); padding: 2.5rem; box-shadow: var(--shadow-lg); }
    .storage-preview { background: var(--bg-elevated); border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); padding: 1rem; font-family: monospace; font-size: 0.8rem; overflow-x: auto; margin-top: 1rem; }
  </style>
</head>
<body>

  <div class="container">
    <header style="margin-bottom: 2rem;">
      <span class="badge badge-accent">Practical 07 Demonstration</span>
      <h1 style="font-size: 1.85rem; font-weight: 700; margin-top: 0.4rem;">PHP Server-Side Form Processing & File Storage</h1>
      <p style="color: var(--text-secondary); font-size: 0.9rem;">
        Server-side validation, sanitization, concurrency locks (flock), and dual storage (CSV & JSON).
      </p>
    </header>

    <div class="form-card">
      <div id="statusBanner" class="alert" style="display: none; margin-bottom: 1.5rem;"></div>

      <form id="p7Form" onsubmit="submitForm(event)">
        <div class="form-group">
          <label class="form-label" for="studName">Full Name <span class="required">*</span></label>
          <input type="text" id="studName" name="name" class="form-input" placeholder="e.g. Tanis Bedia" required>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="studEmail">University Email <span class="required">*</span></label>
            <input type="email" id="studEmail" name="email" class="form-input" placeholder="25dit006@charusat.edu.in" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="studMobile">Mobile Number <span class="required">*</span></label>
            <input type="tel" id="studMobile" name="mobile" class="form-input" placeholder="9876543210" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="studCourse">Program <span class="required">*</span></label>
            <select id="studCourse" name="course" class="form-select" required>
              <option value="B.Tech IT" selected>B.Tech Information Technology</option>
              <option value="B.Tech CSE">B.Tech Computer Science & Eng</option>
              <option value="B.Tech CE">B.Tech Computer Engineering</option>
              <option value="B.Tech EC">B.Tech Electronics & Comm</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="storageFormat">Target Storage Destination <span class="required">*</span></label>
            <select id="storageFormat" name="format" class="form-select">
              <option value="csv" selected>CSV File (storage/registrations.csv)</option>
              <option value="json">JSON File (storage/feedback.json)</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="studComment">Academic Note / Feedback</label>
          <input type="text" id="studComment" name="comment" class="form-input" placeholder="e.g. Enrolled in Semester 3 B.Tech IT">
        </div>

        <div style="background: var(--bg-elevated); padding: 0.85rem; border-radius: 6px; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.5rem;">
          🔒 <strong>Security Implementation:</strong> Input is sanitized via <code>strip_tags()</code> and <code>filter_var()</code> on the server. Direct web downloading of <code>storage/</code> is blocked via <code>.htaccess</code>. File locking (<code>LOCK_EX</code>) prevents write collisions.
        </div>

        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" style="width: 100%;">
          Submit to PHP Server Processor →
        </button>
      </form>
    </div>

    <!-- Live Preview of Stored Records -->
    <div style="margin-top: 2rem;">
      <h3 style="font-size: 1.15rem; margin-bottom: 0.5rem;">Recent Server Storage (CSV & JSON)</h3>
      <div class="storage-preview">
        <strong>Last 5 registrations in storage/registrations.csv:</strong>
        <pre><?php
          $csv = __DIR__ . '/storage/registrations.csv';
          if (file_exists($csv)) {
            $lines = file($csv);
            $last = array_slice($lines, -5);
            echo htmlspecialchars(implode('', $last));
          } else {
            echo "No registrations recorded yet.";
          }
        ?></pre>
      </div>
    </div>
  </div>

  <script>
    async function submitForm(e) {
      e.preventDefault();
      const form = document.getElementById('p7Form');
      const banner = document.getElementById('statusBanner');
      const btn = document.getElementById('submitBtn');

      btn.disabled = true;
      btn.innerText = 'Processing on Server...';
      banner.style.display = 'none';

      const payload = {
        name: document.getElementById('studName').value.trim(),
        email: document.getElementById('studEmail').value.trim(),
        mobile: document.getElementById('studMobile').value.trim(),
        course: document.getElementById('studCourse').value,
        format: document.getElementById('storageFormat').value,
        comment: document.getElementById('studComment').value.trim()
      };

      try {
        const res = await fetch('process.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();

        btn.disabled = false;
        btn.innerText = 'Submit to PHP Server Processor →';

        if (data.success) {
          banner.className = 'alert alert-success';
          banner.innerHTML = `<strong>Success!</strong> ${data.message} <br><span class="text-xs">Recorded at ${data.timestamp}</span>`;
          banner.style.display = 'block';
          form.reset();
          setTimeout(() => location.reload(), 1500);
        } else {
          banner.className = 'alert alert-danger';
          banner.innerHTML = `<strong>Validation Error:</strong> ${data.message}`;
          banner.style.display = 'block';
        }
      } catch (err) {
        btn.disabled = false;
        btn.innerText = 'Submit to PHP Server Processor →';
        banner.className = 'alert alert-danger';
        banner.innerText = 'Server communication error: ' + err.message;
        banner.style.display = 'block';
      }
    }
  </script>
</body>
</html>
