<?php
// Practical 9 — Secure User Registration with Database Insert, Duplicate Checks & Password Hashing
// CHARUSAT DEPSTAR Information Technology — ITUE203 Web Development Frameworks

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../config/db.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed. Only POST is accepted.']);
    exit;
}

// Support both JSON payload and standard form-data
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = $_POST;
}

// Extract and sanitize inputs
$name            = isset($input['name']) ? trim(strip_tags($input['name'])) : '';
$email           = isset($input['email']) ? trim(filter_var($input['email'], FILTER_SANITIZE_EMAIL)) : '';
$mobile          = isset($input['mobile']) ? trim(preg_replace('/[\s\-]/', '', $input['mobile'])) : '';
$course          = isset($input['course']) ? trim(strip_tags($input['course'])) : 'B.Tech IT';
$year            = isset($input['year']) ? (int)$input['year'] : 2;
$semester        = isset($input['semester']) ? (int)$input['semester'] : ($year * 2 - 1);
$gender          = isset($input['gender']) && in_array($input['gender'], ['Male', 'Female', 'Other']) ? $input['gender'] : 'Male';
$batch           = isset($input['batch']) ? trim(strip_tags($input['batch'])) : 'Batch A';
$division        = isset($input['division']) ? trim(strip_tags($input['division'])) : '3IT-A';
$rollNo          = isset($input['roll_no']) ? strtoupper(trim(strip_tags($input['roll_no']))) : '';
$password        = isset($input['password']) ? (string)$input['password'] : '';
$confirmPassword = isset($input['confirmPassword']) ? (string)$input['confirmPassword'] : (isset($input['confirm_password']) ? (string)$input['confirm_password'] : '');

$errors = [];

// Validation: Name
if (empty($name)) {
    $errors[] = 'Full name is required.';
} elseif (strlen($name) < 3 || strlen($name) > 50) {
    $errors[] = 'Full name must be between 3 and 50 characters.';
} elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
    $errors[] = 'Full name must contain letters and spaces only.';
}

// Validation: Email
if (empty($email)) {
    $errors[] = 'Email address is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

// Validation: Mobile
if (empty($mobile)) {
    $errors[] = 'Mobile number is required.';
} elseif (!preg_match('/^[6-9]\d{9}$/', $mobile)) {
    $errors[] = 'Mobile number must be a 10-digit Indian number starting with 6, 7, 8, or 9.';
}

// Validation: Password
if (empty($password)) {
    $errors[] = 'Password is required.';
} elseif (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters long.';
} else {
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password must include at least one lowercase letter.';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must include at least one uppercase letter.';
    }
    if (!preg_match('/\d/', $password)) {
        $errors[] = 'Password must include at least one numerical digit.';
    }
    if (!preg_match('/[@$!%*?&#^()_+\-=\[\]{}|;:,.<>]/', $password)) {
        $errors[] = 'Password must include at least one special character.';
    }
}

// Validation: Password Confirmation
if ($password !== $confirmPassword) {
    $errors[] = 'Password and confirmation password do not match.';
}

// Auto-derive username if not given
$username = isset($input['username']) && !empty(trim($input['username'])) 
    ? strtolower(trim(strip_tags($input['username'])))
    : strtolower(strstr($email, '@', true) ?: preg_replace('/[^a-zA-Z0-9]/', '', $name));

if (empty($username) || strlen($username) < 3) {
    $username = 'student_' . substr(md5(uniqid('', true)), 0, 6);
}

// Auto-generate roll_no if omitted
if (empty($rollNo)) {
    // Generate next 25DITxxx
    $rollNo = '25DIT' . str_pad((string)rand(100, 999), 3, '0', STR_PAD_LEFT);
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Validation failed: ' . implode(' ', $errors),
        'errors'  => $errors
    ]);
    exit;
}

try {
    $pdo = getDbConnection();

    // 1. Duplicate check for Email (in users)
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'An account with this email address already exists. Please sign in instead.'
        ]);
        exit;
    }

    // 2. Duplicate check for Username (in users)
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
    $stmt->execute([':username' => $username]);
    if ($stmt->fetch()) {
        // Append random suffix to make username unique
        $username .= '_' . rand(10, 99);
    }

    // 3. Duplicate check for Roll No (in students)
    $stmt = $pdo->prepare('SELECT id FROM students WHERE roll_no = :roll_no LIMIT 1');
    $stmt->execute([':roll_no' => $rollNo]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => "Student Roll Number {$rollNo} is already registered in the directory."
        ]);
        exit;
    }

    // Hash password securely using Bcrypt (cost 10 default)
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // Transactional insert to maintain referential integrity
    $pdo->beginTransaction();

    // Insert into users
    $userStmt = $pdo->prepare(
        'INSERT INTO users (username, email, password_hash, role, full_name, status) 
         VALUES (:username, :email, :password_hash, :role, :full_name, :status)'
    );
    $userStmt->execute([
        ':username'      => $username,
        ':email'         => $email,
        ':password_hash' => $passwordHash,
        ':role'          => 'student',
        ':full_name'     => $name,
        ':status'        => 'active'
    ]);
    $userId = (int)$pdo->lastInsertId();

    // Insert into students
    $studentStmt = $pdo->prepare(
        'INSERT INTO students (user_id, roll_no, name, email, mobile, course, semester, year, gender, division, batch)
         VALUES (:user_id, :roll_no, :name, :email, :mobile, :course, :semester, :year, :gender, :division, :batch)'
    );
    $studentStmt->execute([
        ':user_id'  => $userId,
        ':roll_no'  => $rollNo,
        ':name'     => $name,
        ':email'    => $email,
        ':mobile'   => $mobile,
        ':course'   => $course,
        ':semester' => $semester,
        ':year'     => $year,
        ':gender'   => $gender,
        ':division' => $division,
        ':batch'    => $batch
    ]);
    $studentId = (int)$pdo->lastInsertId();

    // Commit transaction
    $pdo->commit();

    // Record audit log
    logAuditAction(
        $userId,
        'STUDENT_REGISTER',
        "Registered student {$name} ({$rollNo}, {$email}) via Practical 9 registration endpoint"
    );

    http_response_code(201);
    echo json_encode([
        'success'    => true,
        'message'    => 'Account created successfully! Your credentials have been secured in MySQL.',
        'user_id'    => $userId,
        'student_id' => $studentId,
        'roll_no'    => $rollNo,
        'email'      => $email,
        'redirect'   => 'login.html?registered=1'
    ]);

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Registration Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error while saving registration. Please try again.'
    ]);
}
