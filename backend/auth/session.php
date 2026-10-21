<?php
// Session Management, Security Middleware & Role-Based Access Control (Practical 10 & 14)

if (session_status() === PHP_SESSION_NONE) {
    // Configure secure session cookie parameters
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax');

    session_start();
}

require_once __DIR__ . '/../config/db.php';

// Idle Session Timeout threshold: 30 minutes (1800 seconds)
define('SESSION_TIMEOUT_SECONDS', 1800);

/**
 * Validates session activity and handles timeout
 */
function checkSessionTimeout() {
    if (isset($_SESSION['user']) && isset($_SESSION['last_activity'])) {
        if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT_SECONDS) {
            // Session expired
            $userId = $_SESSION['user']['id'] ?? null;
            if ($userId) {
                logAuditAction($userId, 'SESSION_TIMEOUT', 'User session expired due to 30-minute inactivity');
            }
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params['path'], $params['domain'],
                    $params['secure'], $params['httponly']
                );
            }
            session_destroy();
            return false;
        }
    }
    if (isset($_SESSION['user'])) {
        $_SESSION['last_activity'] = time();
    }
    return true;
}

checkSessionTimeout();

/**
 * Returns whether a user is currently logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
}

/**
 * Returns current authenticated user record or null
 * @return array|null
 */
function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

/**
 * Enforces authenticated login; redirects or responds with 401 if unauthenticated
 * @param bool $jsonResponse
 */
function requireLogin($jsonResponse = false) {
    if (!isLoggedIn()) {
        if ($jsonResponse) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Authentication required. Please sign in.']);
            exit;
        }
        header('Location: ../pages/login.html?error=unauthorized');
        exit;
    }
}

/**
 * Enforces administrator role access (Practical 10 & 14 RBAC)
 * @param bool $jsonResponse
 */
function requireAdmin($jsonResponse = false) {
    requireLogin($jsonResponse);
    $user = getCurrentUser();
    if ($user['role'] !== 'admin') {
        if ($jsonResponse) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden: Administrator privileges required.']);
            exit;
        }
        header('Location: ../pages/dashboard.html?error=access_denied');
        exit;
    }
}

/**
 * Records system and administrative activity into audit_logs table (Practical 14)
 * @param int|null $userId
 * @param string $action
 * @param string $details
 */
function logAuditAction($userId, $action, $details) {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (:user_id, :action, :details, :ip)');
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $stmt->execute([
            ':user_id' => $userId,
            ':action'  => substr($action, 0, 100),
            ':details' => $details,
            ':ip'      => $ip
        ]);
    } catch (Exception $e) {
        error_log('Audit logging failed: ' . $e->getMessage());
    }
}

// If accessed directly via GET as API, return JSON status
if (basename($_SERVER['PHP_SELF']) === 'session.php') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success'   => true,
        'logged_in' => isLoggedIn(),
        'user'      => getCurrentUser()
    ]);
    exit;
}
