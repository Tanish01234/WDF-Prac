<?php
// Practical 14 — Admin Dashboard Metrics, SQL Aggregates & Audit Log Stream
// CHARUSAT DEPSTAR Information Technology — ITUE203 Web Development Frameworks

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../config/db.php';

// Enforce admin RBAC (Practical 14 Requirement)
requireAdmin(true);

try {
    $pdo = getDbConnection();

    // 1. Total counts (SQL Aggregates)
    $totalStudents = (int)$pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
    $totalUsers    = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    $totalEvents   = (int)$pdo->query('SELECT COUNT(*) FROM events')->fetchColumn();
    $openEvents    = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE status = 'Open'")->fetchColumn();
    $totalRegs     = (int)$pdo->query('SELECT COUNT(*) FROM registrations')->fetchColumn();

    // 2. Batch breakdown
    $batchStmt = $pdo->query('SELECT batch, COUNT(*) as count FROM students GROUP BY batch ORDER BY batch ASC');
    $batchDistribution = $batchStmt->fetchAll();

    // 3. Event categories
    $catStmt = $pdo->query('SELECT category, COUNT(*) as count FROM events GROUP BY category ORDER BY count DESC');
    $categoryDistribution = $catStmt->fetchAll();

    // 4. Latest Event Registrations
    $recentRegsStmt = $pdo->query(
        'SELECT 
            r.id,
            r.status,
            r.registered_at,
            s.roll_no,
            s.name AS student_name,
            s.batch,
            e.title AS event_title,
            e.category AS event_category
         FROM registrations r
         INNER JOIN students s ON r.student_id = s.id
         INNER JOIN events e ON r.event_id = e.id
         ORDER BY r.registered_at DESC
         LIMIT 6'
    );
    $recentRegistrations = $recentRegsStmt->fetchAll();

    // 5. Recent System & Security Audit Logs (Practical 14)
    $auditStmt = $pdo->query(
        'SELECT 
            a.id,
            a.action,
            a.details,
            a.ip_address,
            a.created_at,
            u.email,
            u.role
         FROM audit_logs a
         LEFT JOIN users u ON a.user_id = u.id
         ORDER BY a.created_at DESC
         LIMIT 12'
    );
    $auditLogs = $auditStmt->fetchAll();

    echo json_encode([
        'success' => true,
        'metrics' => [
            'total_students'      => $totalStudents,
            'total_users'         => $totalUsers,
            'total_events'        => $totalEvents,
            'open_events'         => $openEvents,
            'total_registrations' => $totalRegs
        ],
        'batch_distribution'     => $batchDistribution,
        'category_distribution'  => $categoryDistribution,
        'recent_registrations'   => $recentRegistrations,
        'audit_logs'             => $auditLogs,
        'server_time'            => date('Y-m-d H:i:s')
    ]);

} catch (Exception $e) {
    error_log('Admin Dashboard API Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load dashboard metrics: ' . $e->getMessage()
    ]);
}
