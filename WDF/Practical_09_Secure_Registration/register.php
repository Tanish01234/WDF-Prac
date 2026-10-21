<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Practical 09: Secure User Registration with MySQL & Bcrypt</title>
  <link rel="stylesheet" href="../../css/global.css">
  <link rel="stylesheet" href="../../css/components.css">
  <style>
    body { background: var(--bg-primary); color: var(--text-primary); padding: 2.5rem 1.5rem; display: flex; justify-content: center; }
    .card { background: var(--bg-card); border: 1px solid var(--border-medium); border-radius: var(--radius-lg); padding: 2.5rem; max-width: 640px; width: 100%; box-shadow: var(--shadow-lg); }
  </style>
</head>
<body>

  <div class="card">
    <div style="text-align: center; margin-bottom: 2rem;">
      <span class="badge badge-accent" style="margin-bottom: 0.5rem;">Practical 09 Implementation</span>
      <h1 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 0.35rem;">Secure Student Registration</h1>
      <p style="color: var(--text-secondary); font-size: 0.9rem;">
        MySQL Prepared Statement Insert, Duplicate Prevention & Bcrypt Password Hashing
      </p>
    </div>

    <div id="statusAlert" class="alert" style="display: none; margin-bottom: 1.5rem;"></div>

    <form id="regForm" onsubmit="submitRegistration(event)">
      <div class="form-group">
        <label class="form-label" for="rName">Full Name <span class="required">*</span></label>
        <input type="text" id="rName" class="form-input" placeholder="e.g. Priyansh Shah" required>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="rEmail">University Email <span class="required">*</span></label>
          <input type="email" id="rEmail" class="form-input" placeholder="student@charusat.edu.in" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="rMobile">Mobile Number <span class="required">*</span></label>
          <input type="tel" id="rMobile" class="form-input" placeholder="9876543210" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="rCourse">Program</label>
          <select id="rCourse" class="form-select">
            <option value="B.Tech IT" selected>B.Tech IT</option>
            <option value="B.Tech CSE">B.Tech CSE</option>
            <option value="B.Tech CE">B.Tech CE</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="rBatch">Lab Batch</label>
          <select id="rBatch" class="form-select">
            <option value="Batch A">Batch A (001–020)</option>
            <option value="Batch B">Batch B (021–040)</option>
            <option value="Batch C">Batch C (041–060)</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="rPass">Password <span class="required">*</span></label>
        <input type="password" id="rPass" class="form-input" placeholder="Min 8 chars with Upper, Lower, Digit, Symbol" required>
      </div>

      <div class="form-group">
        <label class="form-label" for="rConfirm">Confirm Password <span class="required">*</span></label>
        <input type="password" id="rConfirm" class="form-input" placeholder="Repeat password" required>
      </div>

      <div style="background: var(--bg-elevated); padding: 0.85rem; border-radius: 6px; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.5rem;">
        🛡️ <strong>Practical 09 Security Guarantee:</strong> Submissions are checked against existing users/students in MySQL. Passwords are encrypted via <code>password_hash(..., PASSWORD_BCRYPT)</code>.
      </div>

      <button type="submit" class="btn btn-primary btn-lg" id="regBtn" style="width: 100%;">
        Register & Encrypt in MySQL →
      </button>

      <div style="margin-top: 1.25rem; text-align: center;">
        <a href="verify-db.php" class="text-xs" style="color: var(--accent-primary); font-weight: 600;">
          🔍 Open Database Password Hashing Inspector
        </a>
      </div>
    </form>
  </div>

  <script>
    async function submitRegistration(e) {
      e.preventDefault();
      const alertBox = document.getElementById('statusAlert');
      const btn = document.getElementById('regBtn');

      btn.disabled = true;
      btn.innerText = 'Encrypting & Storing in MySQL...';
      alertBox.style.display = 'none';

      const payload = {
        name: document.getElementById('rName').value.trim(),
        email: document.getElementById('rEmail').value.trim(),
        mobile: document.getElementById('rMobile').value.trim(),
        course: document.getElementById('rCourse').value,
        batch: document.getElementById('rBatch').value,
        password: document.getElementById('rPass').value,
        confirmPassword: document.getElementById('rConfirm').value
      };

      try {
        const res = await fetch('process-registration.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();

        btn.disabled = false;
        btn.innerText = 'Register & Encrypt in MySQL →';

        if (data.success) {
          alertBox.className = 'alert alert-success';
          alertBox.innerHTML = `<strong>Success!</strong> ${data.message} <br><a href="verify-db.php" style="color: #fff; text-decoration: underline; font-weight: 700;">Click to view encrypted hash in database</a>`;
          alertBox.style.display = 'block';
          document.getElementById('regForm').reset();
        } else {
          alertBox.className = 'alert alert-danger';
          alertBox.innerHTML = `<strong>Registration Rejected:</strong> ${data.message}`;
          alertBox.style.display = 'block';
        }
      } catch (err) {
        btn.disabled = false;
        btn.innerText = 'Register & Encrypt in MySQL →';
        alertBox.className = 'alert alert-danger';
        alertBox.innerText = 'Server error: ' + err.message;
        alertBox.style.display = 'block';
      }
    }
  </script>
</body>
</html>
