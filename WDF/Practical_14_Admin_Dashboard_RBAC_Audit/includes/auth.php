<?php
// Practical 14 — Admin RBAC Security Middleware
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Practical_08_MySQL_Schema_Connectivity/db.php';

function checkAdminRole() {
    // If user is logged in via main portal session or p10 session, accept admin
    $user = $_SESSION['user'] ?? ($_SESSION['p10_user'] ?? null);
    if (!$user || $user['role'] !== 'admin') {
        // For local practical demonstration, if not logged in as admin, provide a quick login option or banner
        return false;
    }
    return true;
}
