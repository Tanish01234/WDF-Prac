<?php
// Practical 10 — Secure User Authentication, Role-Based Access & Session Management
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

// Support JSON payload and form-data
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = $_POST;
}

$identifier = isset($input['identifier']) ? trim($input['identifier']) : (isset($input['email']) ? trim($input['email']) : (isset($input['loginId']) ? trim($input['loginId']) : ''));
$password   = isset($input['password']) ? (string)$input['password'] : (isset($input['loginPassword']) ? (string)$input['loginPassword'] : '');

if (empty($identifier) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Both Student ID / Email and password are required.']);
    exit;
}

try {
    $pdo = getDbConnection();

    // Query user by email, username, or student roll_no
    $stmt = $pdo->prepare(
        'SELECT 
            u.id,
            u.username,
            u.email,
            u.password_hash,
            u.role,
            u.full_name,
            u.status,
            s.id AS student_id,
            s.roll_no,
            s.course,
            s.batch,
            s.division,
            s.semester
         FROM users u
         LEFT JOIN students s ON u.id = s.user_id
         WHERE LOWER(u.email) = LOWER(:id1)
            OR LOWER(u.username) = LOWER(:id2)
            OR LOWER(s.roll_no) = LOWER(:id3)
         LIMIT 1'
    );
    $stmt->execute([
        ':id1' => $identifier,
        ':id2' => $identifier,
        ':id3' => $identifier
    ]);
    $user = $stmt->fetch();

    // Secure timing-safe verification
    if (!$user || !password_verify($password, $user['password_hash'])) {
        // Log failed attempt if user was found
        if ($user) {
            logAuditAction($user['id'], 'LOGIN_FAILED', "Failed login attempt for user {$user['email']}");
        }
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid credentials. Please verify your Student ID/Email and password.'
        ]);
        exit;
    }

    // Account status check
    if ($user['status'] !== 'active') {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Your account is currently inactive or suspended. Please contact the administrator.'
        ]);
        exit;
    }

    // Prevent session fixation attack (Practical 10 Requirement)
    session_regenerate_id(true);

    // Populate active session
    $_SESSION['user'] = [
        'id'         => (int)$user['id'],
        'username'   => $user['username'],
        'email'      => $user['email'],
        'role'       => $user['role'],
        'full_name'  => $user['full_name'],
        'student_id' => $user['student_id'] ? (int)$user['student_id'] : null,
        'roll_no'    => $user['roll_no'] ?? null,
        'course'     => $user['course'] ?? 'B.Tech IT',
        'batch'      => $user['batch'] ?? 'Batch A',
        'division'   => $user['division'] ?? '3IT-A',
        'semester'   => $user['semester'] ? (int)$user['semester'] : 3
    ];
    $_SESSION['last_activity'] = time();

    // Log successful login in audit trail
    logAuditAction(
        $user['id'],
        'USER_LOGIN',
        "User {$user['email']} ({$user['role']}) logged in successfully"
    );

    // Determine role-based destination redirect
    $redirectUrl = ($user['role'] === 'admin') ? 'admin-dashboard.html' : 'dashboard.html';

    http_response_code(200);
    echo json_encode([
        'success'  => true,
        'message'  => 'Welcome back, ' . htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') . '!',
        'role'     => $user['role'],
        'redirect' => $redirectUrl,
        'user'     => [
            'id'        => (int)$user['id'],
            'name'      => $user['full_name'],
            'email'     => $user['email'],
            'role'      => $user['role'],
            'roll_no'   => $user['roll_no']
        ]
    ]);

} catch (Exception $e) {
    error_log('Login Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected server error occurred during authentication.'
    ]);
}
