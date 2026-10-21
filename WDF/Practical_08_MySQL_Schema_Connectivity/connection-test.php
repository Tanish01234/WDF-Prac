<?php
// Practical 08 — Connection Testing & Prepared Statements Demonstration
require_once __DIR__ . '/db.php';

$pdoSuccess = false;
$mysqliSuccess = false;
$tables = [];
$sampleStudent = null;
$sampleUser = null;
$error = null;

try {
    $pdo = getDbConnection();
    $pdoSuccess = true;

    $mysqli = getMysqliConnection();
    $mysqliSuccess = ($mysqli->ping());

    // 1. Inspect table counts
    $tableNames = ['users', 'students', 'events', 'registrations', 'feedback', 'audit_logs'];
    foreach ($tableNames as $tbl) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM `{$tbl}`");
        $tables[$tbl] = (int)$stmt->fetchColumn();
    }

    // 2. Prepared Statement Demonstration 1: PDO with parameter
    $pdoStmt = $pdo->prepare('SELECT name, roll_no, email, course, batch FROM students WHERE roll_no = :roll LIMIT 1');
    $pdoStmt->execute([':roll' => '25DIT006']);
    $sampleStudent = $pdoStmt->fetch();

    // 3. Prepared Statement Demonstration 2: MySQLi with bound parameter
    $mStmt = $mysqli->prepare('SELECT id, username, email, role, status FROM users WHERE username = ? LIMIT 1');
    $usernameParam = 'admin';
    $mStmt->bind_param('s', $usernameParam);
    $mStmt->execute();
    $sampleUser = $mStmt->get_result()->fetch_assoc();

} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Practical 08: MySQL Schema & Connectivity Test</title>
  <link rel="stylesheet" href="../../css/global.css">
  <link rel="stylesheet" href="../../css/components.css">
  <style>
    body { background: var(--bg-primary); color: var(--text-primary); padding: 2.5rem 1.5rem; }
    .card { background: var(--bg-card); border: 1px solid var(--border-medium); border-radius: var(--radius-lg); padding: 2rem; max-width: 820px; margin: 0 auto; box-shadow: var(--shadow-lg); }
    .metric-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin: 1.5rem 0; }
    .metric-box { background: var(--bg-elevated); border: 1px solid var(--border-subtle); padding: 1rem; border-radius: var(--radius-sm); text-align: center; }
  </style>
</head>
<body>
  <div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
      <span class="badge <?= ($pdoSuccess && $mysqliSuccess) ? 'badge-success' : 'badge-danger' ?>">
        <?= ($pdoSuccess && $mysqliSuccess) ? '✓ Dual Drivers Verified' : '✗ Connection Failed' ?>
      </span>
      <span class="text-xs text-muted">CHARUSAT DEPSTAR ITUE203</span>
    </div>

    <h1 style="font-size: 1.75rem; margin-bottom: 0.5rem;">MySQL Schema & Connectivity Verification</h1>
    <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
      Demonstrating relational database connectivity using both PHP PDO and MySQLi drivers with prepared statements.
    </p>

    <?php if ($error): ?>
      <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
        <strong>Error:</strong> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem;">Verified Relational Tables</h3>
    <div class="metric-grid">
      <?php foreach ($tables as $tName => $count): ?>
        <div class="metric-box">
          <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-primary);"><?= $count ?></div>
          <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-top: 0.2rem;"><?= htmlspecialchars($tName) ?></div>
        </div>
      <?php endforeach; ?>
    </div>

    <h3 style="font-size: 1.1rem; margin-top: 1.5rem; margin-bottom: 0.75rem;">Prepared Statement Executions</h3>
    
    <div style="background: var(--bg-elevated); padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem; font-size: 0.85rem;">
      <span class="badge badge-accent" style="margin-bottom: 0.5rem;">PDO Prepared Statement</span>
      <div class="font-mono text-xs text-muted">SELECT name, roll_no, email, course, batch FROM students WHERE roll_no = :roll (Param: '25DIT006')</div>
      <?php if ($sampleStudent): ?>
        <div style="margin-top: 0.5rem; color: var(--text-primary);">
          <strong><?= htmlspecialchars($sampleStudent['name']) ?></strong> (<?= htmlspecialchars($sampleStudent['roll_no']) ?>) • <?= htmlspecialchars($sampleStudent['email']) ?> [<?= htmlspecialchars($sampleStudent['batch']) ?>]
        </div>
      <?php endif; ?>
    </div>

    <div style="background: var(--bg-elevated); padding: 1rem; border-radius: var(--radius-sm); font-size: 0.85rem;">
      <span class="badge badge-accent" style="margin-bottom: 0.5rem;">MySQLi Prepared Statement</span>
      <div class="font-mono text-xs text-muted">SELECT id, username, email, role, status FROM users WHERE username = ? (Param: 'admin')</div>
      <?php if ($sampleUser): ?>
        <div style="margin-top: 0.5rem; color: var(--text-primary);">
          <strong><?= htmlspecialchars($sampleUser['username']) ?></strong> (<?= htmlspecialchars($sampleUser['email']) ?>) • Role: <span class="badge badge-success"><?= htmlspecialchars($sampleUser['role']) ?></span> • Status: <?= htmlspecialchars($sampleUser['status']) ?>
        </div>
      <?php endif; ?>
    </div>

    <div style="margin-top: 2rem; display: flex; gap: 0.75rem;">
      <a href="index.php" class="btn btn-secondary">View ER Model Diagram →</a>
      <a href="connection-test.php" class="btn btn-primary">Re-Run Connection Test</a>
    </div>
  </div>
</body>
</html>
