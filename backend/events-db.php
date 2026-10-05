<?php
// Practical 8 — MySQL Database Connectivity & Relational Query Demonstration
// Connects to MySQL using PDO and queries students, events, and registrations tables.

require_once __DIR__ . '/config/db.php';

$isCli = (php_sapi_name() === 'cli');
$wantsJson = (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

$response = [
    'success' => false,
    'message' => '',
    'events' => [],
    'registrations' => []
];

try {
    $pdo = getDbConnection();
    $response['success'] = true;
    $response['message'] = 'Connected to MySQL database "synora" successfully.';

    // Prepared Statement 1: Query events
    $eventsStmt = $pdo->prepare(
        'SELECT id, title, description, category, event_date, location, created_at 
         FROM events 
         ORDER BY event_date ASC'
    );
    $eventsStmt->execute();
    $response['events'] = $eventsStmt->fetchAll();

    // Prepared Statement 2: Parameterized query demonstrating Foreign Key JOIN
    $filterStatus = isset($_GET['status']) ? trim($_GET['status']) : 'Registered';
    $sql = 'SELECT 
            r.id AS registration_id,
            s.roll_no,
            s.name AS student_name,
            s.course,
            s.batch,
            e.title AS event_title,
            e.category AS event_category,
            e.event_date,
            r.status,
            r.registered_at
         FROM registrations r
         INNER JOIN students s ON r.student_id = s.id
         INNER JOIN events e ON r.event_id = e.id';

    if ($filterStatus !== 'all') {
        $sql .= ' WHERE r.status = :status';
    }
    $sql .= ' ORDER BY r.registered_at DESC';

    $regStmt = $pdo->prepare($sql);
    if ($filterStatus !== 'all') {
        $regStmt->execute([':status' => $filterStatus]);
    } else {
        $regStmt->execute();
    }
    $response['registrations'] = $regStmt->fetchAll();

} catch (Exception $e) {
    http_response_code(500);
    $response['success'] = false;
    $response['message'] = 'Database query failed: ' . $e->getMessage();
}

// CLI Output
if ($isCli) {
    echo "====================================================\n";
    echo "SYNORA PRACTICAL 8 — DATABASE QUERY DEMONSTRATION\n";
    echo "====================================================\n";
    echo "Status: " . ($response['success'] ? "SUCCESS" : "FAILED") . "\n";
    echo "Message: " . $response['message'] . "\n\n";

    if ($response['success']) {
        echo "Events in MySQL Database (" . count($response['events']) . "):\n";
        foreach ($response['events'] as $ev) {
            echo "  [#{$ev['id']}] {$ev['title']} ({$ev['category']}) - Date: {$ev['event_date']}\n";
        }

        echo "\nRegistrations (FK Joined with Students & Events, Status: {$filterStatus}):\n";
        foreach ($response['registrations'] as $reg) {
            echo "  - {$reg['roll_no']} ({$reg['student_name']}) -> {$reg['event_title']} [{$reg['status']}]\n";
        }
    }
    echo "====================================================\n";
    exit($response['success'] ? 0 : 1);
}

// JSON Output
if ($wantsJson) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

// HTML Output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>P8 Database Integration — SYNORA Portal</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/components.css">
    <style>
        .p8-page { max-width: 1100px; margin: 0 auto; padding: 2.5rem 1.5rem; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .data-table th, .data-table td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid var(--border-subtle); font-size: 0.875rem; }
        .data-table th { background: var(--bg-elevated); color: var(--text-secondary); font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; }
        .data-table tr:hover td { background: var(--bg-hover); }
        .section-card { background: var(--bg-card); border: 1px solid var(--border-medium); border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 2rem; box-shadow: var(--shadow-sm); }
    </style>
</head>
<body>
    <div class="p8-page">
        <!-- Header -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <a href="../pages/dashboard.html" class="brand-logo" style="text-decoration: none; margin-bottom: 0.5rem; display: inline-flex;">
                    <div class="brand-icon">S</div>
                    <span>SYNORA PORTAL</span>
                </a>
                <h1 style="font-size: 1.6rem; margin-top: 0.25rem;">Practical 8: MySQL Schema &amp; PDO Connectivity</h1>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">
                    Relational model demonstration: <code>students</code>, <code>events</code>, and <code>registrations</code> foreign key joins.
                </p>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <span class="badge <?= $response['success'] ? 'badge-success' : 'badge-danger' ?>" style="font-size: 0.85rem; padding: 0.5rem 0.9rem;">
                    <?= $response['success'] ? 'MySQL: synora (Connected)' : 'Connection Error' ?>
                </span>
                <a href="test-db.php" class="btn btn-secondary btn-sm">DB Test Diagnostic</a>
                <a href="../pages/events.html" class="btn btn-outline btn-sm">Frontend Events →</a>
            </div>
        </div>

        <?php if (!$response['success']): ?>
            <div class="alert alert-danger" style="margin-bottom: 2rem;">
                <h4>Database Connection Failed</h4>
                <p><?= htmlspecialchars($response['message']) ?></p>
            </div>
        <?php else: ?>

            <!-- Table 1: Events -->
            <div class="section-card">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h2 style="font-size: 1.15rem; margin-bottom: 0.25rem;">1. Events Entity (<code>events</code> table)</h2>
                        <p class="text-xs text-muted">Primary Key: <code>id</code> • Fetched using PDO Prepared Statement</p>
                    </div>
                    <span class="badge badge-accent"><?= count($response['events']) ?> Events</span>
                </div>

                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>PK ID</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Event Date</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($response['events'] as $event): ?>
                                <tr>
                                    <td class="font-mono text-muted">#<?= htmlspecialchars($event['id']) ?></td>
                                    <td><strong><?= htmlspecialchars($event['title']) ?></strong></td>
                                    <td><span class="badge badge-subtle"><?= htmlspecialchars($event['category']) ?></span></td>
                                    <td class="font-mono"><?= htmlspecialchars($event['event_date']) ?></td>
                                    <td><?= htmlspecialchars($event['location']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table 2: Registrations with Foreign Key Joins -->
            <div class="section-card">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h2 style="font-size: 1.15rem; margin-bottom: 0.25rem;">2. Registrations Entity (<code>registrations</code> table)</h2>
                        <p class="text-xs text-muted">Foreign Keys: <code>student_id → students.id</code>, <code>event_id → events.id</code></p>
                    </div>
                    <span class="badge badge-success"><?= count($response['registrations']) ?> Confirmed</span>
                </div>

                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Reg ID</th>
                                <th>Student Roll No</th>
                                <th>Student Name</th>
                                <th>Batch</th>
                                <th>Event Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Registered At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($response['registrations'] as $reg): ?>
                                <tr>
                                    <td class="font-mono text-muted">#<?= htmlspecialchars($reg['registration_id']) ?></td>
                                    <td class="font-mono font-bold"><?= htmlspecialchars($reg['roll_no']) ?></td>
                                    <td><?= htmlspecialchars($reg['student_name']) ?></td>
                                    <td><span class="badge badge-subtle"><?= htmlspecialchars($reg['batch']) ?></span></td>
                                    <td><?= htmlspecialchars($reg['event_title']) ?></td>
                                    <td><?= htmlspecialchars($reg['event_category']) ?></td>
                                    <td><span class="badge badge-success"><?= htmlspecialchars($reg['status']) ?></span></td>
                                    <td class="font-mono text-xs text-muted"><?= htmlspecialchars($reg['registered_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Query Verification & Code Inspection -->
            <div style="background: var(--bg-elevated); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1.25rem;">
                <h4 style="font-size: 0.9rem; margin-bottom: 0.5rem;">Prepared Statement Executed:</h4>
                <pre style="background: var(--bg-card); padding: 0.75rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle); font-size: 0.8rem; overflow-x: auto; color: var(--text-primary);">SELECT r.id, s.roll_no, s.name, s.batch, e.title, e.category, r.status, r.registered_at
FROM registrations r
INNER JOIN students s ON r.student_id = s.id
INNER JOIN events e ON r.event_id = e.id
WHERE r.status = :status
ORDER BY r.registered_at DESC;</pre>
                <div class="text-xs text-muted" style="margin-top: 0.5rem;">
                    Bound Parameter: <code>:status = '<?= htmlspecialchars($filterStatus) ?>'</code> • Driver: PHP PDO MySQL
                </div>
            </div>

        <?php endif; ?>

        <div style="margin-top: 2rem; text-align: center;">
            <a href="../pages/dashboard.html" class="btn btn-secondary">← Back to Portal Dashboard</a>
        </div>
    </div>
</body>
</html>
