<?php
// Practical 09 — Secure Registration Processor with Bcrypt Hashing & Duplicate Checks
// CHARUSAT DEPSTAR Information Technology — ITUE203 Web Development Frameworks

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../Practical 8/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed. Only POST is accepted.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$name            = isset($input['name']) ? trim(strip_tags($input['name'])) : '';
$email           = isset($input['email']) ? trim(filter_var($input['email'], FILTER_SANITIZE_EMAIL)) : '';
$rollNo          = isset($input['roll_no']) ? strtoupper(trim(strip_tags($input['roll_no']))) : '';
$mobile          = isset($input['mobile']) ? trim(preg_replace('/[\s\-]/', '', $input['mobile'])) : '';
$course          = isset($input['course']) ? trim(strip_tags($input['course'])) : 'B.Tech IT';
$batch           = isset($input['batch']) ? trim(strip_tags($input['batch'])) : 'Batch A';
$gender          = isset($input['gender']) && in_array($input['gender'], ['Male', 'Female', 'Other']) ? $input['gender'] : 'Male';
$password        = isset($input['password']) ? (string)$input['password'] : '';
$confirmPassword = isset($input['confirmPassword']) ? (string)$input['confirmPassword'] : (isset($input['confirm_password']) ? (string)$input['confirm_password'] : '');

$errors = [];

// Name validation
if (empty($name) || strlen($name) < 3 || strlen($name) > 50 || !preg_match("/^[a-zA-Z\s]+$/", $name)) {
    $errors[] = 'Full name must contain 3–50 letters and spaces only.';
}

// Email validation
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid university email address.';
}

// Mobile validation
if (empty($mobile) || !preg_match('/^[6-9]\d{9}$/', $mobile)) {
    $errors[] = 'Mobile number must be a valid 10-digit Indian number.';
}

// Password validation
if (strlen($password) < 8 || !preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password) || !preg_match('/\d/', $password) || !preg_match('/[@$!%*?&#^()_+\-=\[\]{}|;:,.<>]/', $password)) {
    $errors[] = 'Password must be at least 8 characters long and include uppercase, lowercase, digit, and symbol.';
}

if ($password !== $confirmPassword) {
    $errors[] = 'Password confirmation does not match.';
}

// Auto-derive username and roll_no if omitted
$username = strtolower(strstr($email, '@', true) ?: preg_replace('/[^a-zA-Z0-9]/', '', $name));
if (empty($rollNo)) {
    $rollNo = '25DIT' . str_pad((string)rand(100, 999), 3, '0', STR_PAD_LEFT);
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors), 'errors' => $errors]);
    exit;
}

try {
    $pdo = getDbConnection();

    // 1. Duplicate Email Check in users
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => "An account with email {$email} already exists."]);
        exit;
    }

    // 2. Duplicate Roll No Check in students
    $stmt = $pdo->prepare('SELECT id FROM students WHERE roll_no = :roll LIMIT 1');
    $stmt->execute([':roll' => $rollNo]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => "Student Roll Number {$rollNo} is already registered."]);
        exit;
    }

    // 3. Secure Bcrypt Hash
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    $pdo->beginTransaction();

    // Insert user record
    $uStmt = $pdo->prepare('INSERT INTO users (username, email, password_hash, role, full_name, status) VALUES (:u, :e, :p, :r, :name, "active")');
    $uStmt->execute([
        ':u'    => $username,
        ':e'    => $email,
        ':p'    => $passwordHash,
        ':r'    => 'student',
        ':name' => $name
    ]);
    $userId = (int)$pdo->lastInsertId();

    // Insert student record
    $sStmt = $pdo->prepare('INSERT INTO students (user_id, roll_no, name, email, mobile, course, semester, year, gender, division, batch) VALUES (:uid, :roll, :name, :e, :m, :c, 3, 2, :g, "3IT-A", :b)');
    $sStmt->execute([
        ':uid'  => $userId,
        ':roll' => $rollNo,
        ':name' => $name,
        ':e'    => $email,
        ':m'    => $mobile,
        ':c'    => $course,
        ':g'    => $gender,
        ':b'    => $batch
    ]);
    $studentId = (int)$pdo->lastInsertId();

    // Record audit log
    $aStmt = $pdo->prepare('INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (:uid, :action, :details, :ip)');
    $aStmt->execute([
        ':uid'     => $userId,
        ':action'  => 'P09_REGISTRATION',
        ':details' => "Registered student {$name} ({$rollNo}) with Bcrypt password hashing",
        ':ip'      => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
    ]);

    $pdo->commit();

    http_response_code(201);
    echo json_encode([
        'success'    => true,
        'message'    => "Student account for {$name} ({$rollNo}) created in MySQL database with Bcrypt password hashing.",
        'user_id'    => $userId,
        'student_id' => $studentId,
        'roll_no'    => $rollNo,
        'hash_type'  => 'Bcrypt ($2y$10)'
    ]);

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Practical 9 Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal database error during registration.']);
}
