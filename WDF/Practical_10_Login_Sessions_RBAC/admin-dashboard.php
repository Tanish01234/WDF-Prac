<?php
// Practical 10 — Protected Administrator Area (Enforcing Admin Role)
require_once __DIR__ . '/auth.php';
requireAdmin();

$user = $_SESSION['p10_user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Practical 10: Admin Dashboard Area</title>
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
      <span class="badge badge-accent">🛡️ Administrator Privilege Confirmed</span>
      <span class="text-xs text-muted">RBAC Guard Passed</span>
    </div>

    <h1 style="font-size: 1.75rem; margin-bottom: 0.5rem;">Administrator Control Console</h1>
    <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
      Protected administrative route. Non-admin users are strictly intercepted and redirected by <code>requireAdmin()</code>.
    </p>

    <div style="background: var(--bg-elevated); padding: 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.85rem; line-height: 1.8;">
      <div><strong>Administrator Name:</strong> <?= htmlspecialchars($user['full_name']) ?></div>
      <div><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></div>
      <div><strong>Assigned RBAC Role:</strong> <span class="badge badge-accent"><?= htmlspecialchars($user['role']) ?></span></div>
    </div>

    <div style="display: flex; gap: 0.75rem;">
      <a href="student-dashboard.php" class="btn btn-secondary">← View Student Area</a>
      <a href="logout.php" class="btn btn-primary">Sign Out (Destroy Session)</a>
    </div>
  </div>
</body>
</html>
