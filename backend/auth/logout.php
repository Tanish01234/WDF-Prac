<?php
// Practical 10 — Secure User Logout & Session Destruction
// CHARUSAT DEPSTAR Information Technology — ITUE203 Web Development Frameworks

require_once __DIR__ . '/session.php';

$wantsJson = (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
          || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

if (isLoggedIn()) {
    $user = getCurrentUser();
    logAuditAction(
        $user['id'] ?? null,
        'USER_LOGOUT',
        "User {$user['email']} logged out manually"
    );
}

// Clear all session variables
$_SESSION = [];

// Delete the session cookie from client
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destroy session on server
session_destroy();

if ($wantsJson) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success'  => true,
        'message'  => 'You have been logged out securely.',
        'redirect' => 'login.html?logged_out=1'
    ]);
    exit;
}

header('Location: ../../pages/login.html?logged_out=1');
exit;
