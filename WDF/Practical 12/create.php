<?php
// Practical 12 — Create Campus Event with Validated Poster Upload
require_once __DIR__ . '/../Practical 8/db.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $category    = trim($_POST['category'] ?? 'Workshop');
    $eventDate   = trim($_POST['event_date'] ?? '');
    $time        = trim($_POST['time'] ?? '10:00 AM – 01:00 PM');
    $location    = trim($_POST['location'] ?? 'DEPSTAR Auditorium');
    $capacity    = (int)($_POST['capacity'] ?? 50);
    $description = trim($_POST['description'] ?? '');

    if (empty($title) || empty($eventDate) || empty($location)) {
        $error = 'Title, Date, and Location are required.';
    } else {
        $posterUrl = null;

        // Poster upload handling
        if (isset($_FILES['poster']) && $_FILES['poster']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['poster'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $error = 'File upload error code: ' . $file['error'];
            } elseif ($file['size'] > 2 * 1024 * 1024) {
                $error = 'Uploaded file exceeds 2MB limit.';
            } else {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($file['tmp_name']);
                $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

                if (!array_key_exists($mime, $allowed)) {
                    $error = 'Invalid image format (' . htmlspecialchars($mime) . '). Only JPEG, PNG, or WebP allowed.';
                } else {
                    $ext = $allowed[$mime];
                    $safeName = 'poster_' . bin2hex(random_bytes(8)) . '.' . $ext;
                    $dest = __DIR__ . '/uploads/' . $safeName;
                    if (move_uploaded_file($file['tmp_name'], $dest)) {
                        $posterUrl = 'uploads/' . $safeName;
                    } else {
                        $error = 'Failed to move uploaded file.';
                    }
                }
            }
        }

        if (!$error) {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare(
                'INSERT INTO events (title, description, category, event_date, time, location, organizer, capacity, poster_url, status)
                 VALUES (:t, :d, :c, :ed, :tm, :loc, "DEPSTAR IT Dept", :cap, :post, "Open")'
            );
            $stmt->execute([
                ':t'    => $title,
                ':d'    => $description,
                ':c'    => $category,
                ':ed'   => $eventDate,
                ':tm'   => $time,
                ':loc'  => $location,
                ':cap'  => $capacity,
                ':post' => $posterUrl
            ]);

            header('Location: index.php?msg=' . urlencode("Event '{$title}' created successfully with verified poster upload."));
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Practical 12: Create Event with Poster Upload</title>
  <link rel="stylesheet" href="../../css/global.css">
  <link rel="stylesheet" href="../../css/components.css">
  <style>
    body { background: var(--bg-primary); color: var(--text-primary); padding: 2.5rem 1.5rem; display: flex; justify-content: center; }
    .card { background: var(--bg-card); border: 1px solid var(--border-medium); border-radius: var(--radius-lg); padding: 2.5rem; max-width: 640px; width: 100%; box-shadow: var(--shadow-lg); }
  </style>
</head>
<body>
  <div class="card">
    <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Create Event with Poster Upload</h2>
    <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 1.5rem;">
      Practical 12: Server-Side File Validation (MIME Verification with finfo & 2MB Limit)
    </p>

    <?php if ($error): ?>
      <div class="alert alert-danger" style="margin-bottom: 1.25rem; font-size: 0.85rem;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="create.php" enctype="multipart/form-data">
      <div class="form-group">
        <label class="form-label">Event Title <span class="required">*</span></label>
        <input type="text" name="title" class="form-input" placeholder="e.g. AI Innovation Hackathon" required>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Category</label>
          <select name="category" class="form-select">
            <option value="Workshop">Workshop</option>
            <option value="Hackathon">Hackathon</option>
            <option value="Seminar">Seminar</option>
            <option value="Academic">Academic</option>
            <option value="Cultural">Cultural</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Event Date <span class="required">*</span></label>
          <input type="date" name="event_date" class="form-input" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Location</label>
          <input type="text" name="location" class="form-input" value="DEPSTAR Auditorium" required>
        </div>
        <div class="form-group">
          <label class="form-label">Capacity</label>
          <input type="number" name="capacity" class="form-input" value="60" min="10" max="500">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-textarea" rows="3" placeholder="Full workshop agenda and topics..."></textarea>
      </div>

      <div class="form-group" style="margin-bottom: 1.5rem;">
        <label class="form-label">
          <span>Event Poster Image</span>
          <span class="text-xs text-muted">JPEG, PNG, WebP (Max 2MB)</span>
        </label>
        <input type="file" name="poster" class="form-input" accept="image/jpeg,image/png,image/webp">
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
        <a href="index.php" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Validate & Publish Event</button>
      </div>
    </form>
  </div>
</body>
</html>
