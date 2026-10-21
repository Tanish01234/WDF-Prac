<?php
// Practical 12 — Event Management Listing & Poster Display
require_once __DIR__ . '/../Practical 8/db.php';
$pdo = getDbConnection();

$events = $pdo->query('SELECT * FROM events ORDER BY event_date ASC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Practical 12: Event Management CRUD & Poster Upload</title>
  <link rel="stylesheet" href="../../css/global.css">
  <link rel="stylesheet" href="../../css/components.css">
  <style>
    body { background: var(--bg-primary); color: var(--text-primary); padding: 2.5rem 1.5rem; }
    .container { max-width: 1040px; margin: 0 auto; }
    .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(310px, 1fr)); gap: 1.5rem; margin-top: 1.5rem; }
    .event-card { background: var(--bg-card); border: 1px solid var(--border-medium); border-radius: var(--radius-lg); overflow: hidden; display: flex; flex-direction: column; }
    .thumb { height: 140px; background: var(--bg-elevated); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; }
    .thumb img { width: 100%; height: 100%; object-fit: cover; }
  </style>
</head>
<body>
  <div class="container">
    <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
      <div>
        <span class="badge badge-accent">Practical 12 Implementation</span>
        <h1 style="font-size: 1.85rem; font-weight: 700; margin-top: 0.25rem;">Event Management & Poster Uploads</h1>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
          CRUD Operations with Strict Server-Side File MIME Validation (finfo) & Directory Protection
        </p>
      </div>
      <div>
        <a href="create.php" class="btn btn-primary">+ Add New Event</a>
      </div>
    </header>

    <?php if (isset($_GET['msg'])): ?>
      <div class="alert alert-success" style="margin-bottom: 1.5rem;">
        <?= htmlspecialchars($_GET['msg']) ?>
      </div>
    <?php endif; ?>

    <div class="grid">
      <?php foreach ($events as $e): ?>
        <div class="event-card">
          <div class="thumb">
            <?php if (!empty($e['poster_url'])): ?>
              <img src="<?= htmlspecialchars($e['poster_url']) ?>" alt="Poster" onerror="this.src='../../assets/images/workshop.svg'">
            <?php else: ?>
              <span style="font-size: 2.5rem;">📅</span>
            <?php endif; ?>
            <span class="badge badge-success" style="position: absolute; top: 0.5rem; right: 0.5rem;"><?= htmlspecialchars($e['status']) ?></span>
          </div>
          <div style="padding: 1.25rem; display: flex; flex-direction: column; flex: 1;">
            <span class="badge badge-accent text-xs" style="align-self: flex-start; margin-bottom: 0.4rem;"><?= htmlspecialchars($e['category']) ?></span>
            <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem;"><?= htmlspecialchars($e['title']) ?></h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1rem; flex: 1;"><?= htmlspecialchars($e['description']) ?></p>
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem;">
              <div>📍 <?= htmlspecialchars($e['location']) ?></div>
              <div>🗓️ <?= htmlspecialchars($e['event_date']) ?> • 👥 Capacity: <?= $e['capacity'] ?></div>
            </div>
            <div style="display: flex; gap: 0.5rem; border-top: 1px solid var(--border-subtle); padding-top: 0.75rem;">
              <a href="delete.php?id=<?= $e['id'] ?>" class="btn btn-ghost btn-sm" style="color: #ef4444; width: 100%; justify-content: center;" onclick="return confirm('Delete event \'<?= htmlspecialchars(addslashes($e['title'])) ?>\'?')">Delete Event</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
