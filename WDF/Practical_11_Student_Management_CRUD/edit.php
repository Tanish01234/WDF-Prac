<?php
// Practical 11 — Edit Student Record
require_once __DIR__ . '/../Practical_08_MySQL_Schema_Connectivity/db.php';
$mysqli = getMysqliConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$error = null;

// Fetch existing
$stmt = $mysqli->prepare('SELECT * FROM students WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    header('Location: index.php?msg=' . urlencode('Student not found.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rollNo = strtoupper(trim($_POST['roll_no'] ?? ''));
    $name   = trim($_POST['name'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $batch  = trim($_POST['batch'] ?? 'Batch A');
    $course = trim($_POST['course'] ?? 'B.Tech IT');

    if (empty($rollNo) || empty($name) || empty($email) || empty($mobile)) {
        $error = 'All fields are mandatory.';
    } else {
        $upd = $mysqli->prepare('UPDATE students SET roll_no = ?, name = ?, email = ?, mobile = ?, course = ?, batch = ? WHERE id = ?');
        $upd->bind_param('ssssssi', $rollNo, $name, $email, $mobile, $course, $batch, $id);
        $upd->execute();

        header('Location: index.php?msg=' . urlencode("Student {$rollNo} updated successfully."));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Practical 11: Edit Student Record</title>
  <link rel="stylesheet" href="../../css/global.css">
  <link rel="stylesheet" href="../../css/components.css">
  <style>
    body { background: var(--bg-primary); color: var(--text-primary); padding: 2.5rem 1.5rem; display: flex; justify-content: center; }
    .card { background: var(--bg-card); border: 1px solid var(--border-medium); border-radius: var(--radius-lg); padding: 2.5rem; max-width: 600px; width: 100%; }
  </style>
</head>
<body>
  <div class="card">
    <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Edit Student Record</h2>
    <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 1.5rem;">Updating Record #<?= $id ?> using MySQLi Prepared Statement</p>

    <?php if ($error): ?>
      <div class="alert alert-danger" style="margin-bottom: 1.25rem; font-size: 0.85rem;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="edit.php?id=<?= $id ?>">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Roll Number</label>
          <input type="text" name="roll_no" class="form-input" value="<?= htmlspecialchars($student['roll_no']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Full Name</label>
          <input type="text" name="name" class="form-input" value="<?= htmlspecialchars($student['name']) ?>" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-input" value="<?= htmlspecialchars($student['email']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Mobile</label>
          <input type="tel" name="mobile" class="form-input" value="<?= htmlspecialchars($student['mobile']) ?>" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Lab Batch</label>
          <select name="batch" class="form-select">
            <option value="Batch A" <?= $student['batch'] === 'Batch A' ? 'selected' : '' ?>>Batch A</option>
            <option value="Batch B" <?= $student['batch'] === 'Batch B' ? 'selected' : '' ?>>Batch B</option>
            <option value="Batch C" <?= $student['batch'] === 'Batch C' ? 'selected' : '' ?>>Batch C</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Program</label>
          <select name="course" class="form-select">
            <option value="B.Tech IT" <?= $student['course'] === 'B.Tech IT' ? 'selected' : '' ?>>B.Tech IT</option>
            <option value="B.Tech CSE" <?= $student['course'] === 'B.Tech CSE' ? 'selected' : '' ?>>B.Tech CSE</option>
            <option value="B.Tech CE" <?= $student['course'] === 'B.Tech CE' ? 'selected' : '' ?>>B.Tech CE</option>
          </select>
        </div>
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
        <a href="index.php" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</body>
</html>
