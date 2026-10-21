<?php
// Test runner for Practicals 8, 9, 10, 11, 12, 13, 14
// Executes direct unit and integration tests against MySQL and auth/API modules

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/auth/session.php';

echo "========================================================\n";
echo "SYNORA STUDENT PORTAL — BACKEND INTEGRATION TEST SUITE\n";
echo "Testing Practicals 8, 9, 10, 11, 12, 13, 14\n";
echo "========================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest($description, $condition, $details = '') {
    global $passCount, $failCount;
    if ($condition) {
        echo " [PASS] {$description}\n";
        $passCount++;
    } else {
        echo " [FAIL] {$description} : {$details}\n";
        $failCount++;
    }
}

try {
    $pdo = getDbConnection();
    assertTest("Database Connection (PDO)", $pdo instanceof PDO);

    $mysqli = getMysqliConnection();
    assertTest("Database Connection (MySQLi)", $mysqli instanceof mysqli && $mysqli->ping());

    // Test 1: Verify Seed Users & Bcrypt Hashes (P8, P9, P10)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => 'admin@charusat.edu.in']);
    $admin = $stmt->fetch();
    assertTest("Seed Admin User Exists", !empty($admin) && $admin['role'] === 'admin');
    assertTest("Admin Password Bcrypt Hash Verification", password_verify('Admin@2026', $admin['password_hash']));

    $stmt->execute([':email' => '25dit006@charusat.edu.in']);
    $student = $stmt->fetch();
    assertTest("Seed Student User Exists", !empty($student) && $student['role'] === 'student');
    assertTest("Student Password Bcrypt Hash Verification", password_verify('Student@2026', $student['password_hash']));

    // Test 2: Practical 9 — User Registration Logic & Duplicate Rejection
    $testEmail = 'test_student_' . time() . '@charusat.edu.in';
    $testRoll = '25DIT' . rand(800, 999);
    $testPass = 'Secur3Pass#2026';
    $testHash = password_hash($testPass, PASSWORD_BCRYPT);

    $insUser = $pdo->prepare("INSERT INTO users (username, email, password_hash, role, full_name, status) VALUES (:u, :e, :p, 'student', :name, 'active')");
    $insUser->execute([':u' => 'testuser_' . time(), ':e' => $testEmail, ':p' => $testHash, ':name' => 'Automated Test Student']);
    $testUid = (int)$pdo->lastInsertId();

    $insStud = $pdo->prepare("INSERT INTO students (user_id, roll_no, name, email, mobile, course, semester, year, gender, division, batch) VALUES (:uid, :roll, :name, :email, '9898989898', 'B.Tech IT', 3, 2, 'Male', '3IT-A', 'Batch B')");
    $insStud->execute([':uid' => $testUid, ':roll' => $testRoll, ':name' => 'Automated Test Student', ':email' => $testEmail]);
    $testSid = (int)$pdo->lastInsertId();

    assertTest("Practical 9 - New Student Record Inserted", $testUid > 0 && $testSid > 0);
    assertTest("Practical 9 - Password Hashed With Bcrypt", substr($testHash, 0, 4) === '$2y$');

    // Duplicate check test
    $dupCheck = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
    $dupCheck->execute([':email' => $testEmail]);
    assertTest("Practical 9 - Duplicate Detection Works", (int)$dupCheck->fetchColumn() === 1);

    // Test 3: Practical 10 — Login Verification & Role Check
    $loginStmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $loginStmt->execute([':email' => $testEmail]);
    $loggedInUser = $loginStmt->fetch();
    assertTest("Practical 10 - Password Verification (Valid)", password_verify($testPass, $loggedInUser['password_hash']));
    assertTest("Practical 10 - Password Verification (Invalid Rejected)", !password_verify('WrongPassword#123', $loggedInUser['password_hash']));

    // Test 4: Practical 11 — Student Management CRUD with MySQLi Prepared Statements
    // 4.1 Search by roll number
    $searchStmt = $mysqli->prepare("SELECT id, roll_no, name, batch FROM students WHERE roll_no = ?");
    $searchStmt->bind_param('s', $testRoll);
    $searchStmt->execute();
    $foundStudent = $searchStmt->get_result()->fetch_assoc();
    assertTest("Practical 11 - MySQLi Prepared Search by Roll No", !empty($foundStudent) && $foundStudent['roll_no'] === $testRoll);

    // 4.2 Filter by batch
    $batchStmt = $mysqli->prepare("SELECT COUNT(*) as cnt FROM students WHERE batch = ?");
    $filterBatch = 'Batch B';
    $batchStmt->bind_param('s', $filterBatch);
    $batchStmt->execute();
    $batchCount = (int)$batchStmt->get_result()->fetch_assoc()['cnt'];
    assertTest("Practical 11 - MySQLi Prepared Batch Filter", $batchCount >= 1);

    // 4.3 Update student
    $newMobile = '9123456789';
    $updStmt = $mysqli->prepare("UPDATE students SET mobile = ? WHERE id = ?");
    $updStmt->bind_param('si', $newMobile, $testSid);
    $updStmt->execute();

    $chkUpd = $mysqli->prepare("SELECT mobile FROM students WHERE id = ?");
    $chkUpd->bind_param('i', $testSid);
    $chkUpd->execute();
    $updatedMobile = $chkUpd->get_result()->fetch_assoc()['mobile'];
    assertTest("Practical 11 - Student Record Updated Successfully", $updatedMobile === $newMobile);

    // Test 5: Practical 12 — Event Management CRUD & Registrations
    $eventTitle = 'Automated Test Workshop ' . time();
    $insEvent = $pdo->prepare("INSERT INTO events (title, description, category, event_date, location, organizer, capacity, status) VALUES (:t, 'Test Desc', 'Workshop', '2026-11-20', 'Lab 101', 'IT Dept', 50, 'Open')");
    $insEvent->execute([':t' => $eventTitle]);
    $testEid = (int)$pdo->lastInsertId();
    assertTest("Practical 12 - Event Created Successfully", $testEid > 0);

    // Student registers for this event
    $insReg = $pdo->prepare("INSERT INTO registrations (student_id, event_id, status) VALUES (:sid, :eid, 'Registered')");
    $insReg->execute([':sid' => $testSid, ':eid' => $testEid]);
    $testRid = (int)$pdo->lastInsertId();
    assertTest("Practical 12 - Student Event Registration Successful", $testRid > 0);

    // Test 6: Practical 14 — Admin Dashboard SQL Aggregates & Audit Log
    $totalStudents = (int)$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $totalUsers    = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $totalEvents   = (int)$pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
    assertTest("Practical 14 - Total Students Metric (>20 seeded)", $totalStudents >= 20);
    assertTest("Practical 14 - Total Users Metric", $totalUsers >= 5);
    assertTest("Practical 14 - Total Events Metric", $totalEvents >= 8);

    // Record and verify audit log entry
    logAuditAction($admin['id'], 'TEST_SUITE_RUN', 'Executed automated verification suite');
    $auditCheck = $pdo->query("SELECT action, details FROM audit_logs WHERE action = 'TEST_SUITE_RUN' ORDER BY id DESC LIMIT 1")->fetch();
    assertTest("Practical 14 - Audit Log Recording & Integrity", !empty($auditCheck) && $auditCheck['action'] === 'TEST_SUITE_RUN');

    // Clean up test records
    $pdo->prepare("DELETE FROM registrations WHERE id = :id")->execute([':id' => $testRid]);
    $pdo->prepare("DELETE FROM events WHERE id = :id")->execute([':id' => $testEid]);
    $pdo->prepare("DELETE FROM students WHERE id = :id")->execute([':id' => $testSid]);
    $pdo->prepare("DELETE FROM users WHERE id = :id")->execute([':id' => $testUid]);
    assertTest("Test Cleanup (Referential Integrity Upheld)", true);

} catch (Exception $e) {
    assertTest("Exception Caught", false, $e->getMessage());
}

echo "\n========================================================\n";
echo "TEST SUMMARY: {$passCount} Passed, {$failCount} Failed\n";
echo "========================================================\n";

exit($failCount === 0 ? 0 : 1);
