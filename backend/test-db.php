<?php
// Database connection and query test script (Practical 8)

require_once __DIR__ . '/config/db.php';

$isCli = (php_sapi_name() === 'cli');
$wantsJson = (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

$result = [
    'success' => false,
    'message' => '',
    'diagnostics' => []
];

try {
    // 1. Establish PDO Connection
    $pdo = getDbConnection();
    $result['success'] = true;
    $result['message'] = 'Database connection successful.';

    // 2. Query Table Row Counts across all 6 entities
    $tables = ['users', 'students', 'events', 'registrations', 'feedback', 'audit_logs'];
    $counts = [];

    foreach ($tables as $tbl) {
        $stmt = $pdo->query("SELECT COUNT(*) AS total FROM `{$tbl}`");
        $row = $stmt->fetch();
        $counts[$tbl] = (int)$row['total'];
    }

    $result['diagnostics']['table_counts'] = $counts;

    // 3. Demonstrate Parameterized Prepared Statement (PDO)
    $sampleRollNo = '25DIT006';
    $prepStmt = $pdo->prepare(
        'SELECT id, roll_no, name, email, course, semester, division, batch 
         FROM students 
         WHERE roll_no = :roll_no'
    );
    $prepStmt->execute([':roll_no' => $sampleRollNo]);
    $sampleStudent = $prepStmt->fetch();

    // 4. Demonstrate MySQLi Connection & Prepared Statement
    $mysqli = getMysqliConnection();
    $mysqliStmt = $mysqli->prepare('SELECT id, username, email, role FROM users WHERE username = ?');
    $adminUser = 'admin';
    $mysqliStmt->bind_param('s', $adminUser);
    $mysqliStmt->execute();
    $mysqliRes = $mysqliStmt->get_result();
    $adminRow = $mysqliRes->fetch_assoc();

    $result['diagnostics']['sample_query'] = [
        'pdo_student_query' => [
            'parameter' => $sampleRollNo,
            'matched_record' => $sampleStudent ?: 'No matching record'
        ],
        'mysqli_user_query' => [
            'parameter' => $adminUser,
            'matched_record' => $adminRow ?: 'No matching record'
        ]
    ];

} catch (Exception $e) {
    http_response_code(500);
    $result['success'] = false;
    $result['message'] = 'Database connection failed: ' . $e->getMessage();
}

// CLI Output
if ($isCli) {
    echo "====================================================\n";
    echo "SYNORA DATABASE CONNECTIVITY TEST (PRACTICAL 8)\n";
    echo "====================================================\n";
    echo "Status: " . ($result['success'] ? "SUCCESS" : "FAILED") . "\n";
    echo "Message: " . $result['message'] . "\n\n";

    if ($result['success']) {
        echo "Table Records:\n";
        foreach ($result['diagnostics']['table_counts'] as $tbl => $cnt) {
            echo "  - {$tbl}: {$cnt} rows\n";
        }
        echo "\nPrepared Statement Demonstration:\n";
        $student = $result['diagnostics']['sample_query']['pdo_student_query']['matched_record'];
        if (is_array($student)) {
            echo "  [PDO] :roll_no = '{$sampleRollNo}' -> {$student['name']} ({$student['roll_no']}) - {$student['course']} Sem {$student['semester']}\n";
        }
        $admin = $result['diagnostics']['sample_query']['mysqli_user_query']['matched_record'];
        if (is_array($admin)) {
            echo "  [MySQLi] username = '{$adminUser}' -> {$admin['username']} ({$admin['email']}) Role: {$admin['role']}\n";
        }
    }
    echo "====================================================\n";
    exit($result['success'] ? 0 : 1);
}

// JSON Output
if ($wantsJson) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result, JSON_PRETTY_PRINT);
    exit;
}

// HTML Output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connectivity Test — SYNORA Portal</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/components.css">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 2rem; background: var(--bg-primary); }
        .test-card { background: var(--bg-card); border: 1px solid var(--border-medium); border-radius: var(--radius-xl); max-width: 620px; width: 100%; padding: 2.5rem; box-shadow: var(--shadow-lg); }
        .metric-box { background: var(--bg-elevated); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1rem 1.25rem; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="test-card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
            <span class="badge <?= $result['success'] ? 'badge-success' : 'badge-danger' ?>">
                <?= $result['success'] ? 'P8 Test Passed' : 'Connection Failed' ?>
            </span>
            <span class="text-xs text-muted font-mono">PHP PDO Driver</span>
        </div>

        <h1 style="font-size: 1.75rem; margin-bottom: 0.5rem;">Database Connectivity Test</h1>
        <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
            <?= htmlspecialchars($result['message']) ?>
        </p>

        <?php if ($result['success']): ?>
            <div class="metric-box">
                <h4 style="font-size: 0.95rem; margin-bottom: 0.75rem;">Verified Database Tables</h4>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; text-align: center;">
                    <?php foreach ($result['diagnostics']['table_counts'] as $tbl => $cnt): ?>
                        <div style="background: var(--bg-card); padding: 0.75rem; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                            <div style="font-size: 1.35rem; font-weight: 700; color: var(--accent-primary);"><?= $cnt ?></div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-top: 0.2rem;"><?= htmlspecialchars($tbl) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="metric-box">
                <h4 style="font-size: 0.95rem; margin-bottom: 0.5rem;">Prepared Statement Demonstrations</h4>
                <p class="text-xs text-muted font-mono" style="margin-bottom: 0.5rem;">
                    [PDO] SELECT * FROM students WHERE roll_no = :roll_no (Param: '25DIT006')
                </p>
                <?php $student = $result['diagnostics']['sample_query']['pdo_student_query']['matched_record']; ?>
                <?php if (is_array($student)): ?>
                    <div style="font-size: 0.85rem; line-height: 1.6; color: var(--text-primary); margin-bottom: 0.75rem;">
                        <strong><?= htmlspecialchars($student['name']) ?></strong> (<?= htmlspecialchars($student['roll_no']) ?>)<br>
                        <span class="text-muted"><?= htmlspecialchars($student['email']) ?> • <?= htmlspecialchars($student['course']) ?> Sem <?= htmlspecialchars($student['semester']) ?> (<?= htmlspecialchars($student['batch']) ?>)</span>
                    </div>
                <?php endif; ?>

                <p class="text-xs text-muted font-mono" style="margin-bottom: 0.5rem; border-top: 1px solid var(--border-subtle); padding-top: 0.5rem;">
                    [MySQLi] SELECT * FROM users WHERE username = ? (Param: 'admin')
                </p>
                <?php $admin = $result['diagnostics']['sample_query']['mysqli_user_query']['matched_record']; ?>
                <?php if (is_array($admin)): ?>
                    <div style="font-size: 0.85rem; line-height: 1.6; color: var(--text-primary);">
                        <strong><?= htmlspecialchars($admin['username']) ?></strong> (<?= htmlspecialchars($admin['email']) ?>)<br>
                        <span class="badge badge-accent">Role: <?= htmlspecialchars($admin['role']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div style="margin-top: 2rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="../pages/dashboard.html" class="btn btn-primary">Return to Dashboard →</a>
            <a href="test-db.php" class="btn btn-secondary">Re-test Connection</a>
        </div>
    </div>
</body>
</html>
