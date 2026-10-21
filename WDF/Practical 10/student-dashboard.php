<?php
// Practical 10 — Protected Student Dashboard Area
require_once __DIR__ . '/auth.php';
requireLogin();

$user = $_SESSION['p10_user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Practical 10: Student Dashboard Area</title>
  <link rel="stylesheet" href="../../css/global.css">
  <link rel="stylesheet" href="../../css/components.css">
  <style>
    body { background: var(--bg-primary); color: var(--text-primary); padding: 2.5rem 1.5rem; }
    .card { background: var(--bg-card); border: 1px solid var(--border-medium); border-radius: var(--radius-lg); padding: 2rem; max-width: 720px; margin: 0 auto; box-shadow: var(--shadow-lg); }
  </style>
</head>
<body>
  <div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
      <span class="badge badge-success">✓ Student Session Active</span>
      <span class="text-xs text-muted">Idle Timeout: 30 Mins</span>
    </div>

    <h1 style="font-size: 1.75rem; margin-bottom: 0.5rem;">Welcome, <?= htmlspecialchars($user['full_name']) ?>!</h1>
    <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
      You have successfully authenticated via PHP Sessions (<code>$_SESSION</code>) with <code>session_regenerate_id(true)</code> protection.
    </p>

    <div style="background: var(--bg-elevated); padding: 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.85rem; line-height: 1.8;">
      <div><strong>Student ID / Roll No:</strong> <?= htmlspecialchars($user['roll_no']) ?></div>
      <div><strong>Email Address:</strong> <?= htmlspecialchars($user['email']) ?></div>
      <div><strong>User Role:</strong> <span class="badge badge-blue"><?= htmlspecialchars($user['role']) ?></span></div>
      <div><strong>Session Key:</strong> <code><?= session_id() ?></code></div>
    </div>

    <div style="display: flex; gap: 0.75rem;">
      <a href="admin-dashboard.php" class="btn btn-secondary">Attempt Access to Admin Area →</a>
      <a href="logout.php" class="btn btn-primary">Sign Out (Destroy Session)</a>
    </div>
  </div>
</body>
</html>
