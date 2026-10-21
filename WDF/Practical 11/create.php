<?php
// Practical 11 — Create Student Record
require_once __DIR__ . '/../Practical 8/db.php';
$mysqli = getMysqliConnection();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rollNo = strtoupper(trim($_POST['roll_no'] ?? ''));
    $name   = trim($_POST['name'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $batch  = trim($_POST['batch'] ?? 'Batch A');
    $course = trim($_POST['course'] ?? 'B.Tech IT');
    $gender = trim($_POST['gender'] ?? 'Male');

    if (empty($rollNo) || empty($name) || empty($email) || empty($mobile)) {
        $error = 'Roll Number, Full Name, Email, and Mobile are required.';
    } else {
        // Check duplicate roll_no
        $chk = $mysqli->prepare('SELECT id FROM students WHERE roll_no = ?');
        $chk->bind_param('s', $rollNo);
        $chk->execute();
        if ($chk->get_result()->fetch_assoc()) {
            $error = "Student Roll Number {$rollNo} already exists.";
        } else {
            $ins = $mysqli->prepare('INSERT INTO students (roll_no, name, email, mobile, course, semester, year, gender, division, batch) VALUES (?, ?, ?, ?, ?, 3, 2, ?, "3IT-A", ?)');
            $ins->bind_param('sssssss', $rollNo, $name, $email, $mobile, $course, $gender, $batch);
            $ins->execute();

            header('Location: index.php?msg=' . urlencode("Student {$rollNo} created successfully."));
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Practical 11: Add New Student Record</title>
  <link rel="stylesheet" href="../../css/global.css">
  <link rel="stylesheet" href="../../css/components.css">
  <style>
    body { background: var(--bg-primary); color: var(--text-primary); padding: 2.5rem 1.5rem; display: flex; justify-content: center; }
    .card { background: var(--bg-card); border: 1px solid var(--border-medium); border-radius: var(--radius-lg); padding: 2.5rem; max-width: 600px; width: 100%; }
  </style>
</head>
<body>
  <div class="card">
    <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Add New Student Record</h2>
    <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 1.5rem;">Inserting via MySQLi Prepared Statement into `students` table</p>

    <?php if ($error): ?>
      <div class="alert alert-danger" style="margin-bottom: 1.25rem; font-size: 0.85rem;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="create.php">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Roll Number <span class="required">*</span></label>
          <input type="text" name="roll_no" class="form-input" placeholder="e.g. 25DIT065" required>
        </div>
        <div class="form-group">
          <label class="form-label">Full Name <span class="required">*</span></label>
          <input type="text" name="name" class="form-input" placeholder="e.g. Yash Patel" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Email <span class="required">*</span></label>
          <input type="email" name="email" class="form-input" placeholder="student@charusat.edu.in" required>
        </div>
        <div class="form-group">
          <label class="form-label">Mobile <span class="required">*</span></label>
          <input type="tel" name="mobile" class="form-input" placeholder="9876543210" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Lab Batch</label>
          <select name="batch" class="form-select">
            <option value="Batch A">Batch A</option>
            <option value="Batch B">Batch B</option>
            <option value="Batch C">Batch C</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Program</label>
          <select name="course" class="form-select">
            <option value="B.Tech IT">B.Tech IT</option>
            <option value="B.Tech CSE">B.Tech CSE</option>
            <option value="B.Tech CE">B.Tech CE</option>
          </select>
        </div>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
        <a href="index.php" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Create Record</button>
      </div>
    </form>
  </div>
</body>
</html>
