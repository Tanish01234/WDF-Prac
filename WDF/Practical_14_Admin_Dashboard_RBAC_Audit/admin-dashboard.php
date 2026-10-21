<?php
// Practical 14 — Admin Dashboard with Real SQL Aggregates & Audit Log Stream
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../Practical_08_MySQL_Schema_Connectivity/db.php';

$pdo = getDbConnection();

// Handle manual test audit action
if (isset($_POST['action_name'])) {
    $action = trim($_POST['action_name']);
    $details = trim($_POST['details'] ?? 'Manual admin action triggered from Practical 14 dashboard');
    $ins = $pdo->prepare('INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (1, :a, :d, :ip)');
    $ins->execute([
        ':a'  => $action,
        ':d'  => $details,
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
    ]);
    header('Location: admin-dashboard.php?logged=1');
    exit;
}

// 1. Real SQL Aggregates
$totalStudents = (int)$pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
$totalUsers    = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalEvents   = (int)$pdo->query('SELECT COUNT(*) FROM events')->fetchColumn();
$totalRegs     = (int)$pdo->query('SELECT COUNT(*) FROM registrations')->fetchColumn();

// 2. Batch breakdown
$batches = $pdo->query('SELECT batch, COUNT(*) as count FROM students GROUP BY batch ORDER BY batch ASC')->fetchAll();

// 3. Recent Registrations
$recentRegs = $pdo->query(
    'SELECT r.id, s.roll_no, s.name, e.title, r.status, r.registered_at 
     FROM registrations r 
     JOIN students s ON r.student_id = s.id 
     JOIN events e ON r.event_id = e.id 
     ORDER BY r.registered_at DESC LIMIT 5'
)->fetchAll();

// 4. Live Audit Logs
$auditLogs = $pdo->query(
    'SELECT a.*, u.email, u.role FROM audit_logs a 
     LEFT JOIN users u ON a.user_id = u.id 
     ORDER BY a.created_at DESC LIMIT 10'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Practical 14: Admin Dashboard with RBAC & Audit Log</title>
  <link rel="stylesheet" href="../../css/global.css">
  <link rel="stylesheet" href="../../css/components.css">
  <style>
    body { background: var(--bg-primary); color: var(--text-primary); padding: 2.5rem 1.5rem; }
    .container { max-width: 1100px; margin: 0 auto; }
    .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .kpi-card { background: var(--bg-card); border: 1px solid var(--border-medium); border-radius: var(--radius-md); padding: 1.5rem; }
    .kpi-num { font-size: 2rem; font-weight: 800; color: var(--accent-primary); margin-top: 0.25rem; }
    .card { background: var(--bg-card); border: 1px solid var(--border-medium); border-radius: var(--radius-lg); padding: 1.75rem; margin-bottom: 2rem; }
    table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    th, td { padding: 0.65rem 0.75rem; border-bottom: 1px solid var(--border-subtle); text-align: left; }
    th { background: var(--bg-elevated); color: var(--text-secondary); text-transform: uppercase; font-size: 0.75rem; }
    .audit-item { background: var(--bg-elevated); border: 1px solid var(--border-subtle); border-radius: 6px; padding: 0.75rem 1rem; margin-bottom: 0.6rem; font-size: 0.85rem; }
  </style>
</head>
<body>
  <div class="container">
    <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
      <div>
        <span class="badge badge-accent">Practical 14 Implementation</span>
        <h1 style="font-size: 1.85rem; font-weight: 700; margin-top: 0.25rem;">Administrator Control Console</h1>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
          Real MySQL SQL Aggregates • Live Audit Trail Stream • Role-Based Access Control
        </p>
      </div>
      <div>
        <span class="badge badge-accent" style="font-size: 0.85rem;">🛡️ Admin Role Authenticated</span>
      </div>
    </header>

    <!-- KPI Aggregates Grid -->
    <div class="kpi-grid">
      <div class="kpi-card">
        <span class="text-xs text-muted font-bold uppercase">Enrolled Students</span>
        <div class="kpi-num"><?= $totalStudents ?></div>
        <div class="text-xs text-muted" style="margin-top: 0.35rem;">
          <?php foreach ($batches as $b): ?>
            <?= htmlspecialchars($b['batch']) ?>: <?= $b['count'] ?> &nbsp;
          <?php endforeach; ?>
        </div>
      </div>

      <div class="kpi-card">
        <span class="text-xs text-muted font-bold uppercase">Registered Users</span>
        <div class="kpi-num"><?= $totalUsers ?></div>
        <div class="text-xs text-muted" style="margin-top: 0.35rem;">Bcrypt Encrypted Accounts</div>
      </div>

      <div class="kpi-card">
        <span class="text-xs text-muted font-bold uppercase">Campus Events</span>
        <div class="kpi-num"><?= $totalEvents ?></div>
        <div class="text-xs text-muted" style="margin-top: 0.35rem;">With Verified Posters</div>
      </div>

      <div class="kpi-card">
        <span class="text-xs text-muted font-bold uppercase">Event Registrations</span>
        <div class="kpi-num"><?= $totalRegs ?></div>
        <div class="text-xs text-muted" style="margin-top: 0.35rem;">Relational M:N Records</div>
      </div>
    </div>

    <!-- Two Columns: Recent Registrations & Live Audit Logs -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
      
      <!-- Left: Recent Registrations -->
      <div class="card">
        <h3 style="font-size: 1.15rem; margin-bottom: 0.35rem;">Recent Event Enrollments</h3>
        <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 1rem;">Live foreign key join between registrations, students, and events</p>

        <div style="overflow-x: auto;">
          <table>
            <thead>
              <tr>
                <th>Student</th>
                <th>Event</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentRegs as $r): ?>
                <tr>
                  <td>
                    <strong><?= htmlspecialchars($r['name']) ?></strong><br>
                    <span class="text-xs text-muted"><?= htmlspecialchars($r['roll_no']) ?></span>
                  </td>
                  <td><?= htmlspecialchars($r['title']) ?></td>
                  <td><span class="badge badge-success text-xs"><?= htmlspecialchars($r['status']) ?></span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Right: Real-Time Audit Log Feed -->
      <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
          <h3 style="font-size: 1.15rem;">Live Security & Audit Trail</h3>
          <span class="badge badge-accent">Practical 14</span>
        </div>
        <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 1rem;">Non-repudiation audit records populated by actual operations</p>

        <!-- Dispatch Test Audit Event Form -->
        <form method="POST" action="admin-dashboard.php" style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
          <input type="text" name="action_name" class="form-input" style="font-size: 0.8rem; padding: 0.4rem 0.6rem;" value="MANUAL_AUDIT_PROBE" required>
          <button type="submit" class="btn btn-secondary btn-sm" style="white-space: nowrap;">+ Log Audit Event</button>
        </form>

        <div style="max-height: 320px; overflow-y: auto;">
          <?php foreach ($auditLogs as $log): ?>
            <div class="audit-item">
              <div style="display: flex; justify-content: space-between; margin-bottom: 0.2rem;">
                <span class="badge badge-blue text-xs font-mono"><?= htmlspecialchars($log['action']) ?></span>
                <span class="text-xs text-muted"><?= htmlspecialchars($log['created_at']) ?> • IP: <?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1') ?></span>
              </div>
              <div style="font-size: 0.8rem; color: var(--text-primary);"><?= htmlspecialchars($log['details']) ?></div>
              <div class="text-xs text-muted" style="margin-top: 0.15rem;">User: <?= htmlspecialchars($log['email'] ?? 'System') ?> (<?= htmlspecialchars($log['role'] ?? 'admin') ?>)</div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div>
  </div>
</body>
</html>
