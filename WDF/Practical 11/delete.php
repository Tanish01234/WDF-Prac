<?php
// Practical 11 — Delete Student Record
require_once __DIR__ . '/../Practical 8/db.php';
$mysqli = getMysqliConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    // Delete student
    $stmt = $mysqli->prepare('DELETE FROM students WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    header('Location: index.php?msg=' . urlencode("Student record #{$id} deleted successfully."));
    exit;
}

header('Location: index.php');
exit;
