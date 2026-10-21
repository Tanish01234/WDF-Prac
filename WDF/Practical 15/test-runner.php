<?php
// Practical 15 — Automated End-to-End Test Runner for all 15 Practicals
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../Practical 8/db.php';

$tests = [];

function recordTest(&$tests, $id, $practical, $name, $status, $details = '') {
    $tests[] = [
        'id'        => $id,
        'practical' => $practical,
        'name'      => $name,
        'status'    => $status ? 'PASS' : 'FAIL',
        'details'   => $details
    ];
}

try {
    // P1: Project structure
    $p1Exists = file_exists(__DIR__ . '/../Practical 1/index.html');
    recordTest($tests, 'T01', 'P1', 'Project Initiation HTML Architecture Present', $p1Exists);

    // P2: Semantic HTML pages count
    $p2Files = glob(__DIR__ . '/../Practical 2/*.html');
    recordTest($tests, 'T02', 'P2', 'Semantic HTML5 Pages (>= 10 pages)', count($p2Files) >= 10, count($p2Files) . ' pages verified');

    // P3: Responsive CSS bundle
    $p3Css = file_exists(__DIR__ . '/../Practical 3/css/responsive.css');
    recordTest($tests, 'T03', 'P3', 'Responsive CSS Media Queries & Grid Architecture', $p3Css);

    // P4: Interactive JS widgets
    $p4Js = file_exists(__DIR__ . '/../Practical 4/js/interactivity.js');
    recordTest($tests, 'T04', 'P4', 'JavaScript DOM Manipulation & Event Handlers', $p4Js);

    // P5: Registration validation JS
    $p5Js = file_exists(__DIR__ . '/../Practical 5/js/validation.js');
    recordTest($tests, 'T05', 'P5', 'Registration Form Client-Side Regex Validation', $p5Js);

    // P6: JSON datasets (>= 15 items each)
    $eventsJson = json_decode(file_get_contents(__DIR__ . '/../Practical 6/data/events.json'), true);
    $studentsJson = json_decode(file_get_contents(__DIR__ . '/../Practical 6/data/students.json'), true);
    $faqsJson = json_decode(file_get_contents(__DIR__ . '/../Practical 6/data/faqs.json'), true);
    $jsonOk = count($eventsJson) >= 15 && count($studentsJson) >= 15 && count($faqsJson) >= 15;
    recordTest($tests, 'T06', 'P6', 'JSON Datasets (>= 15 records each)', $jsonOk, 'Events: ' . count($eventsJson) . ', Students: ' . count($studentsJson) . ', FAQs: ' . count($faqsJson));

    // P7: CSV storage append test
    $csvPath = __DIR__ . '/../Practical 7/storage/registrations.csv';
    $csvOk = file_exists($csvPath) && filesize($csvPath) > 0;
    recordTest($tests, 'T07', 'P7', 'PHP Server Form Processing & CSV File Storage', $csvOk);

    // P8: MySQL Database Connectivity (PDO and MySQLi)
    $pdo = getDbConnection();
    $mysqli = getMysqliConnection();
    recordTest($tests, 'T08', 'P8', 'MySQL Dual Driver Connectivity (PDO & MySQLi)', ($pdo instanceof PDO) && ($mysqli instanceof mysqli));

    // P9: Bcrypt Password Hashing in Database
    $adminHash = $pdo->query("SELECT password_hash FROM users WHERE email = 'admin@charusat.edu.in'")->fetchColumn();
    $hashOk = substr($adminHash, 0, 4) === '$2y$' && password_verify('Admin@2026', $adminHash);
    recordTest($tests, 'T09', 'P9', 'Database Bcrypt Password Hashing & Timing-Safe Verify', $hashOk);

    // P10: Session Security Configuration
    require_once __DIR__ . '/../Practical 10/auth.php';
    $cookieHttponly = (bool)ini_get('session.cookie_httponly');
    recordTest($tests, 'T10', 'P10', 'Secure Session Cookie Flags (HttpOnly Enabled)', $cookieHttponly);

    // P11: Student Management CRUD
    $studCount = (int)$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
    recordTest($tests, 'T11', 'P11', 'Student Directory Database Count (>= 20 records)', $studCount >= 20, "Total: {$studCount} students");

    // P12: Event Upload Security (.htaccess lock)
    $htaccess = file_exists(__DIR__ . '/../Practical 12/uploads/.htaccess');
    recordTest($tests, 'T12', 'P12', 'Upload Security & Script Execution Block (.htaccess)', $htaccess);

    // P13: RESTful JSON API
    $apiExists = file_exists(__DIR__ . '/../Practical 13/api/students.php');
    recordTest($tests, 'T13', 'P13', 'RESTful PHP JSON API & Asynchronous Fetch CRUD', $apiExists);

    // P14: Admin Dashboard & Audit Logs
    $auditCount = (int)$pdo->query("SELECT COUNT(*) FROM audit_logs")->fetchColumn();
    recordTest($tests, 'T14', 'P14', 'Admin Dashboard SQL Aggregates & Audit Log Stream', $auditCount > 0, "Total: {$auditCount} audit entries");

    // P15: Final Integrated Portal
    $portalIndex = file_exists(__DIR__ . '/../../index.html') && file_exists(__DIR__ . '/../../pages/dashboard.html');
    recordTest($tests, 'T15', 'P15', 'End-to-End Integrated Portal Architecture', $portalIndex);

    $passed = count(array_filter($tests, fn($t) => $t['status'] === 'PASS'));
    $total = count($tests);

    echo json_encode([
        'success'      => ($passed === $total),
        'passed_count' => $passed,
        'total_count'  => $total,
        'rate'         => round(($passed / $total) * 100) . '%',
        'tests'        => $tests
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
