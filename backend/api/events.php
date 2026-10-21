<?php
// Practical 12 & 13 — Event Management RESTful CRUD API with Safe Poster Uploads & MIME Validation
// CHARUSAT DEPSTAR Information Technology — ITUE203 Web Development Frameworks

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
} elseif (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
    $method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
}

$uploadDir = __DIR__ . '/../../uploads/events/';
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

/**
 * Validates uploaded poster file with strict MIME and size checks (Practical 12)
 * @param array $file
 * @return string Relative URL of saved image
 * @throws RuntimeException
 */
function handlePosterUpload($file, $uploadDir) {
    if (!isset($file['error']) || is_array($file['error'])) {
        throw new RuntimeException('Invalid upload parameters.');
    }

    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('File upload error code: ' . $file['error']);
    }

    // Max 2MB limit
    if ($file['size'] > 2 * 1024 * 1024) {
        throw new RuntimeException('Uploaded file exceeds maximum allowed size of 2MB.');
    }

    // Strict MIME type verification using finfo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    if (!array_key_exists($mimeType, $allowedMimes)) {
        throw new RuntimeException('Invalid image format (' . htmlspecialchars($mimeType) . '). Only JPEG, PNG, and WebP are allowed.');
    }

    // Reject dangerous filename extensions
    $origName = $file['name'];
    if (preg_match('/\.(php|phtml|exe|sh|cgi|pl|js|html)$/i', $origName)) {
        throw new RuntimeException('Security violation: Dangerous file extension detected.');
    }

    $ext = $allowedMimes[$mimeType];
    $safeFilename = 'poster_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
    $targetPath = $uploadDir . $safeFilename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new RuntimeException('Failed to move uploaded file to destination directory.');
    }

    return 'uploads/events/' . $safeFilename;
}

try {
    $pdo = getDbConnection();

    switch ($method) {
        // =========================================================
        // READ / LIST EVENTS (GET)
        // =========================================================
        case 'GET':
            // Check single event
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $id = (int)$_GET['id'];
                $stmt = $pdo->prepare('SELECT * FROM events WHERE id = :id LIMIT 1');
                $stmt->execute([':id' => $id]);
                $event = $stmt->fetch();

                if (!$event) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => "Event #{$id} not found."]);
                    exit;
                }

                // Query registration count for this event
                $regStmt = $pdo->prepare('SELECT COUNT(*) as reg_count FROM registrations WHERE event_id = :id');
                $regStmt->execute([':id' => $id]);
                $event['registered_count'] = (int)$regStmt->fetch()['reg_count'];

                echo json_encode(['success' => true, 'data' => $event]);
                exit;
            }

            // Student registered events query
            if (isset($_GET['action']) && $_GET['action'] === 'my_registrations') {
                requireLogin(true);
                $user = getCurrentUser();
                $studentId = $user['student_id'] ?? null;

                if (!$studentId) {
                    echo json_encode(['success' => true, 'data' => []]);
                    exit;
                }

                $stmt = $pdo->prepare(
                    'SELECT r.id AS registration_id, r.status, r.registered_at, e.* 
                     FROM registrations r 
                     INNER JOIN events e ON r.event_id = e.id 
                     WHERE r.student_id = :student_id 
                     ORDER BY r.registered_at DESC'
                );
                $stmt->execute([':student_id' => $studentId]);
                $list = $stmt->fetchAll();
                echo json_encode(['success' => true, 'data' => $list]);
                exit;
            }

            // General list with search & filters
            $category = isset($_GET['category']) ? trim($_GET['category']) : '';
            $status   = isset($_GET['status']) ? trim($_GET['status']) : '';
            $search   = isset($_GET['search']) ? trim($_GET['search']) : '';

            $whereClauses = ['1=1'];
            $params = [];

            if (!empty($category) && $category !== 'all') {
                $whereClauses[] = 'category = :category';
                $params[':category'] = $category;
            }

            if (!empty($status) && $status !== 'all') {
                $whereClauses[] = 'status = :status';
                $params[':status'] = $status;
            }

            if (!empty($search)) {
                $whereClauses[] = '(title LIKE :search OR description LIKE :search OR location LIKE :search)';
                $params[':search'] = "%{$search}%";
            }

            $sql = 'SELECT e.*, 
                    (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) AS registered_count 
                    FROM events e 
                    WHERE ' . implode(' AND ', $whereClauses) . ' 
                    ORDER BY e.event_date ASC';

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $events = $stmt->fetchAll();

            echo json_encode(['success' => true, 'data' => $events, 'total' => count($events)]);
            break;

        // =========================================================
        // CREATE EVENT OR STUDENT REGISTRATION (POST)
        // =========================================================
        case 'POST':
            // 1. Student Event Registration Action
            if (isset($_GET['action']) && $_GET['action'] === 'register') {
                requireLogin(true);
                $user = getCurrentUser();
                $studentId = $user['student_id'] ?? null;

                if (!$studentId) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Your student profile is not linked to your user account.']);
                    exit;
                }

                $raw = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                $eventId = isset($raw['event_id']) ? (int)$raw['event_id'] : 0;

                if ($eventId <= 0) {
                    http_response_code(422);
                    echo json_encode(['success' => false, 'message' => 'Event ID is required to register.']);
                    exit;
                }

                // Check event existence and capacity
                $evStmt = $pdo->prepare('SELECT id, title, capacity, status FROM events WHERE id = :id');
                $evStmt->execute([':id' => $eventId]);
                $event = $evStmt->fetch();

                if (!$event) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Target event not found.']);
                    exit;
                }

                if ($event['status'] !== 'Open') {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => "Registration closed for event '{$event['title']}'."]);
                    exit;
                }

                // Duplicate registration check
                $dupStmt = $pdo->prepare('SELECT id FROM registrations WHERE student_id = :sid AND event_id = :eid');
                $dupStmt->execute([':sid' => $studentId, ':eid' => $eventId]);
                if ($dupStmt->fetch()) {
                    http_response_code(409);
                    echo json_encode(['success' => false, 'message' => 'You are already registered for this event.']);
                    exit;
                }

                // Insert registration
                $regIns = $pdo->prepare('INSERT INTO registrations (student_id, event_id, status) VALUES (:sid, :eid, :status)');
                $regIns->execute([':sid' => $studentId, ':eid' => $eventId, ':status' => 'Registered']);

                logAuditAction($user['id'], 'EVENT_REGISTER', "Student {$user['roll_no']} registered for event {$event['title']}");

                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => "Successfully registered for '{$event['title']}'! We look forward to seeing you."
                ]);
                exit;
            }

            // 2. Admin Event Creation
            requireAdmin(true);

            $title       = isset($_POST['title']) ? trim(strip_tags($_POST['title'])) : '';
            $description = isset($_POST['description']) ? trim(strip_tags($_POST['description'])) : '';
            $category    = isset($_POST['category']) ? trim(strip_tags($_POST['category'])) : 'Workshop';
            $eventDate   = isset($_POST['event_date']) ? trim($_POST['event_date']) : '';
            $time        = isset($_POST['time']) ? trim(strip_tags($_POST['time'])) : '10:00 AM – 01:00 PM';
            $location    = isset($_POST['location']) ? trim(strip_tags($_POST['location'])) : 'DEPSTAR Auditorium';
            $organizer   = isset($_POST['organizer']) ? trim(strip_tags($_POST['organizer'])) : 'DEPSTAR IT Department';
            $capacity    = isset($_POST['capacity']) ? (int)$_POST['capacity'] : 100;
            $status      = isset($_POST['status']) && in_array($_POST['status'], ['Open', 'Closed', 'Cancelled', 'Completed']) ? $_POST['status'] : 'Open';

            if (empty($title) || empty($description) || empty($eventDate) || empty($location)) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Title, description, date, and location are required.']);
                exit;
            }

            // Process uploaded poster with strict validation
            $posterUrl = null;
            if (isset($_FILES['poster'])) {
                try {
                    $posterUrl = handlePosterUpload($_FILES['poster'], $uploadDir);
                } catch (RuntimeException $re) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Poster upload failed: ' . $re->getMessage()]);
                    exit;
                }
            }

            $stmt = $pdo->prepare(
                'INSERT INTO events (title, description, category, event_date, time, location, organizer, capacity, poster_url, status)
                 VALUES (:title, :description, :category, :event_date, :time, :location, :organizer, :capacity, :poster_url, :status)'
            );
            $stmt->execute([
                ':title'       => $title,
                ':description' => $description,
                ':category'    => $category,
                ':event_date'  => $eventDate,
                ':time'        => $time,
                ':location'    => $location,
                ':organizer'   => $organizer,
                ':capacity'    => $capacity,
                ':poster_url'  => $posterUrl,
                ':status'      => $status
            ]);
            $newId = (int)$pdo->lastInsertId();

            $user = getCurrentUser();
            logAuditAction($user['id'] ?? null, 'EVENT_CREATED', "Admin created event #{$newId} ({$title})");

            http_response_code(201);
            echo json_encode([
                'success'    => true,
                'message'    => "Event '{$title}' created successfully.",
                'id'         => $newId,
                'poster_url' => $posterUrl
            ]);
            break;

        // =========================================================
        // UPDATE EVENT (PUT)
        // =========================================================
        case 'PUT':
            requireAdmin(true);

            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true);
            if (!is_array($data)) {
                parse_str($raw, $data);
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = $_POST;
            }

            $id = isset($data['id']) ? (int)$data['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Event ID is required for update.']);
                exit;
            }

            $stmt = $pdo->prepare('SELECT * FROM events WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $existing = $stmt->fetch();
            if (!$existing) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => "Event #{$id} not found."]);
                exit;
            }

            $title       = isset($data['title']) ? trim(strip_tags($data['title'])) : $existing['title'];
            $description = isset($data['description']) ? trim(strip_tags($data['description'])) : $existing['description'];
            $category    = isset($data['category']) ? trim(strip_tags($data['category'])) : $existing['category'];
            $eventDate   = isset($data['event_date']) ? trim($data['event_date']) : $existing['event_date'];
            $time        = isset($data['time']) ? trim(strip_tags($data['time'])) : $existing['time'];
            $location    = isset($data['location']) ? trim(strip_tags($data['location'])) : $existing['location'];
            $organizer   = isset($data['organizer']) ? trim(strip_tags($data['organizer'])) : $existing['organizer'];
            $capacity    = isset($data['capacity']) ? (int)$data['capacity'] : (int)$existing['capacity'];
            $status      = isset($data['status']) ? trim($data['status']) : $existing['status'];
            $posterUrl   = $existing['poster_url'];

            // If a new poster was provided in POST multipart
            if (isset($_FILES['poster'])) {
                try {
                    $newPoster = handlePosterUpload($_FILES['poster'], $uploadDir);
                    if ($newPoster) {
                        $posterUrl = $newPoster;
                    }
                } catch (RuntimeException $re) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Poster update failed: ' . $re->getMessage()]);
                    exit;
                }
            }

            $upd = $pdo->prepare(
                'UPDATE events 
                 SET title = :title, description = :description, category = :category, event_date = :event_date,
                     time = :time, location = :location, organizer = :organizer, capacity = :capacity, 
                     poster_url = :poster_url, status = :status 
                 WHERE id = :id'
            );
            $upd->execute([
                ':title'       => $title,
                ':description' => $description,
                ':category'    => $category,
                ':event_date'  => $eventDate,
                ':time'        => $time,
                ':location'    => $location,
                ':organizer'   => $organizer,
                ':capacity'    => $capacity,
                ':poster_url'  => $posterUrl,
                ':status'      => $status,
                ':id'          => $id
            ]);

            $user = getCurrentUser();
            logAuditAction($user['id'] ?? null, 'EVENT_UPDATED', "Admin updated event #{$id} ({$title})");

            echo json_encode(['success' => true, 'message' => "Event '{$title}' updated successfully."]);
            break;

        // =========================================================
        // DELETE EVENT (DELETE)
        // =========================================================
        case 'DELETE':
            requireAdmin(true);

            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true) ?: [];
            $id = isset($data['id']) ? (int)$data['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);

            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Valid Event ID is required for deletion.']);
                exit;
            }

            $stmt = $pdo->prepare('SELECT id, title, poster_url FROM events WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $existing = $stmt->fetch();

            if (!$existing) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => "Event #{$id} not found."]);
                exit;
            }

            // Remove poster file if exists
            if (!empty($existing['poster_url'])) {
                $filePath = __DIR__ . '/../../' . $existing['poster_url'];
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $delStmt = $pdo->prepare('DELETE FROM events WHERE id = :id');
            $delStmt->execute([':id' => $id]);

            $user = getCurrentUser();
            logAuditAction($user['id'] ?? null, 'EVENT_DELETED', "Admin deleted event #{$id} ({$existing['title']})");

            echo json_encode(['success' => true, 'message' => "Event '{$existing['title']}' deleted successfully."]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => "Method {$method} Not Allowed."]);
            break;
    }

} catch (Exception $e) {
    error_log('Events API Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'A database error occurred while processing event request: ' . $e->getMessage()
    ]);
}
