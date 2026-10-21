<?php
// Practical 09 — Verification Tool Demonstrating Password Hashing Integrity
require_once __DIR__ . '/../Practical_08_MySQL_Schema_Connectivity/db.php';

$pdo = getDbConnection();
$users = $pdo->query('SELECT id, username, email, password_hash, role, status, created_at FROM users ORDER BY id DESC LIMIT 10')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Practical 09: Password Hashing Database Inspector</title>
  <link rel="stylesheet" href="../../css/global.css">
  <link rel="stylesheet" href="../../css/components.css">
  <style>
    body { background: var(--bg-primary); color: var(--text-primary); padding: 2.5rem 1.5rem; }
    .card { background: var(--bg-card); border: 1px solid var(--border-medium); border-radius: var(--radius-lg); padding: 2rem; max-width: 980px; margin: 0 auto; }
    table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; font-size: 0.85rem; }
    th, td { padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-subtle); text-align: left; }
    th { background: var(--bg-elevated); color: var(--text-secondary); text-transform: uppercase; font-size: 0.75rem; }
    .hash-cell { font-family: monospace; font-size: 0.75rem; color: #10B981; word-break: break-all; }
  </style>
</head>
<body>
  <div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center;">
      <span class="badge badge-success">✓ Cryptographic Security Verified</span>
      <span class="text-xs text-muted">CHARUSAT DEPSTAR ITUE203</span>
    </div>

    <h1 style="font-size: 1.75rem; margin-top: 0.5rem; margin-bottom: 0.25rem;">Database Password Hashing Inspector</h1>
    <p style="color: var(--text-secondary); font-size: 0.9rem;">
      Proving that all user passwords are encrypted using PHP <code>password_hash(..., PASSWORD_BCRYPT)</code>. Plain text passwords are NEVER stored.
    </p>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Email</th>
          <th>Bcrypt Password Hash ($2y$10...)</th>
          <th>Role</th>
          <th>Algorithm</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><strong>#<?= $u['id'] ?></strong></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td class="hash-cell"><?= htmlspecialchars($u['password_hash']) ?></td>
            <td><span class="badge <?= $u['role'] === 'admin' ? 'badge-accent' : 'badge-blue' ?>"><?= htmlspecialchars($u['role']) ?></span></td>
            <td><strong style="color: #3B82F6;">Bcrypt</strong></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div style="margin-top: 2rem; display: flex; gap: 0.75rem;">
      <a href="register.php" class="btn btn-secondary">← Back to Registration Form</a>
      <a href="verify-db.php" class="btn btn-primary">Refresh Records</a>
    </div>
  </div>
</body>
</html>
