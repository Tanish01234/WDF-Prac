<?php
// Practical 11 — Student Management CRUD (Read & Search) with MySQLi Prepared Statements
require_once __DIR__ . '/../Practical_08_MySQL_Schema_Connectivity/db.php';

$mysqli = getMysqliConnection();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$batch  = isset($_GET['batch']) ? trim($_GET['batch']) : '';
$page   = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$limit  = 10;
$offset = ($page - 1) * $limit;

$whereClauses = ['1=1'];
$paramTypes = '';
$paramValues = [];

if (!empty($search)) {
    $whereClauses[] = '(name LIKE ? OR roll_no LIKE ? OR email LIKE ?)';
    $paramTypes .= 'sss';
    $searchWild = "%{$search}%";
    $paramValues[] = $searchWild;
    $paramValues[] = $searchWild;
    $paramValues[] = $searchWild;
}

if (!empty($batch) && $batch !== 'all') {
    $whereClauses[] = 'batch = ?';
    $paramTypes .= 's';
    $paramValues[] = $batch;
}

$whereSql = implode(' AND ', $whereClauses);

// Count total matching
$cStmt = $mysqli->prepare("SELECT COUNT(*) as total FROM students WHERE {$whereSql}");
if (!empty($paramValues)) {
    $cStmt->bind_param($paramTypes, ...$paramValues);
}
$cStmt->execute();
$totalRecords = (int)$cStmt->get_result()->fetch_assoc()['total'];

// Fetch page
$dataSql = "SELECT * FROM students WHERE {$whereSql} ORDER BY roll_no ASC LIMIT ? OFFSET ?";
$paramTypes .= 'ii';
$paramValues[] = $limit;
$paramValues[] = $offset;

$dStmt = $mysqli->prepare($dataSql);
$dStmt->bind_param($paramTypes, ...$paramValues);
$dStmt->execute();
$students = $dStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$totalPages = ceil($totalRecords / $limit) ?: 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Practical 11: Student Management CRUD with MySQLi</title>
  <link rel="stylesheet" href="../../css/global.css">
  <link rel="stylesheet" href="../../css/components.css">
  <style>
    body { background: var(--bg-primary); color: var(--text-primary); padding: 2.5rem 1.5rem; }
    .container { max-width: 1040px; margin: 0 auto; }
    .card { background: var(--bg-card); border: 1px solid var(--border-medium); border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-lg); }
    table { width: 100%; border-collapse: collapse; margin-top: 1rem; font-size: 0.875rem; }
    th, td { padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-subtle); text-align: left; }
    th { background: var(--bg-elevated); color: var(--text-secondary); text-transform: uppercase; font-size: 0.75rem; }
    .filter-bar { display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1.5rem; justify-content: space-between; }
  </style>
</head>
<body>
  <div class="container">
    <header style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      <div>
        <span class="badge badge-accent">Practical 11 Implementation</span>
        <h1 style="font-size: 1.85rem; font-weight: 700; margin-top: 0.25rem;">Student Directory CRUD Module</h1>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
          MySQLi Prepared Statements • Search & Filter • Pagination • Record Editing
        </p>
      </div>
      <div>
        <a href="create.php" class="btn btn-primary">+ Add New Student</a>
      </div>
    </header>

    <?php if (isset($_GET['msg'])): ?>
      <div class="alert alert-success" style="margin-bottom: 1.5rem;">
        <?= htmlspecialchars($_GET['msg']) ?>
      </div>
    <?php endif; ?>

    <div class="card">
      <form method="GET" action="index.php" class="filter-bar">
        <div style="flex: 1; min-width: 250px;">
          <input type="text" name="search" class="form-input" placeholder="🔍 Search name, roll no, or email..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div style="display: flex; gap: 0.5rem;">
          <select name="batch" class="form-select">
            <option value="all">All Batches</option>
            <option value="Batch A" <?= $batch === 'Batch A' ? 'selected' : '' ?>>Batch A</option>
            <option value="Batch B" <?= $batch === 'Batch B' ? 'selected' : '' ?>>Batch B</option>
            <option value="Batch C" <?= $batch === 'Batch C' ? 'selected' : '' ?>>Batch C</option>
          </select>
          <button type="submit" class="btn btn-secondary">Filter</button>
        </div>
      </form>

      <div style="overflow-x: auto;">
        <table>
          <thead>
            <tr>
              <th>Roll No</th>
              <th>Full Name</th>
              <th>Email</th>
              <th>Mobile</th>
              <th>Batch</th>
              <th>Program</th>
              <th style="text-align: right;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($students)): ?>
              <tr><td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">No students matched the query.</td></tr>
            <?php else: ?>
              <?php foreach ($students as $s): ?>
                <tr>
                  <td><strong style="color: var(--accent-primary); font-family: monospace;"><?= htmlspecialchars($s['roll_no']) ?></strong></td>
                  <td><strong><?= htmlspecialchars($s['name']) ?></strong></td>
                  <td><?= htmlspecialchars($s['email']) ?></td>
                  <td><?= htmlspecialchars($s['mobile']) ?></td>
                  <td><span class="badge badge-accent"><?= htmlspecialchars($s['batch']) ?></span></td>
                  <td><?= htmlspecialchars($s['course']) ?></td>
                  <td style="text-align: right; white-space: nowrap;">
                    <a href="edit.php?id=<?= $s['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                    <a href="delete.php?id=<?= $s['id'] ?>" class="btn btn-ghost btn-sm" style="color: #ef4444;" onclick="return confirm('Delete student <?= htmlspecialchars($s['roll_no']) ?>?')">Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; font-size: 0.85rem; color: var(--text-muted);">
        <div>Showing page <?= $page ?> of <?= $totalPages ?> (<?= $totalRecords ?> total records)</div>
        <div style="display: flex; gap: 0.35rem;">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="index.php?page=<?= $i ?>&search=<?= urlencode($search) ?>&batch=<?= urlencode($batch) ?>" class="btn <?= $i === $page ? 'btn-primary' : 'btn-secondary' ?> btn-sm"><?= $i ?></a>
          <?php endfor; ?>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
