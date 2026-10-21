<?php
// Practical 10 — Authentication Middleware, Session Security & RBAC Guard
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

require_once __DIR__ . '/../Practical_08_MySQL_Schema_Connectivity/db.php';

define('SESSION_TIMEOUT_SECONDS', 1800); // 30 minutes

function checkSessionTimeout() {
    if (isset($_SESSION['p10_user']) && isset($_SESSION['p10_last_activity'])) {
        if (time() - $_SESSION['p10_last_activity'] > SESSION_TIMEOUT_SECONDS) {
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
            }
            session_destroy();
            header('Location: login.php?error=timeout');
            exit;
        }
    }
    if (isset($_SESSION['p10_user'])) {
        $_SESSION['p10_last_activity'] = time();
    }
}

checkSessionTimeout();

function isLoggedIn() {
    return isset($_SESSION['p10_user']) && !empty($_SESSION['p10_user']['id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php?error=unauthorized');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if ($_SESSION['p10_user']['role'] !== 'admin') {
        header('Location: student-dashboard.php?error=admin_only');
        exit;
    }
}
