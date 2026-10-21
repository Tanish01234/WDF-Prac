<?php
// Practical 12 — Delete Event and Associated Poster File
require_once __DIR__ . '/../Practical_08_MySQL_Schema_Connectivity/db.php';
$pdo = getDbConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    $stmt = $pdo->prepare('SELECT poster_url, title FROM events WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $ev = $stmt->fetch();

    if ($ev) {
        if (!empty($ev['poster_url'])) {
            $path = __DIR__ . '/' . $ev['poster_url'];
            if (file_exists($path)) {
                @unlink($path);
            }
        }
        $del = $pdo->prepare('DELETE FROM events WHERE id = :id');
        $del->execute([':id' => $id]);
        header('Location: index.php?msg=' . urlencode("Event '{$ev['title']}' deleted successfully."));
        exit;
    }
}

header('Location: index.php');
exit;
