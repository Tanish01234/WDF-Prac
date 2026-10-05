<?php
// Registration form processor (Practical 7)

// Ensure request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $response = [
        'success' => false,
        'message' => 'Method Not Allowed. Only POST requests are accepted.'
    ];
    if (isAjaxRequest()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
    } else {
        renderHtmlResponse(false, [$response['message']]);
    }
    exit;
}

// Read and sanitize input fields
$name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
$course = isset($_POST['course']) ? trim(strip_tags($_POST['course'])) : '';
$year = isset($_POST['year']) ? trim($_POST['year']) : '';
$gender = isset($_POST['gender']) ? trim(strip_tags($_POST['gender'])) : '';
$terms = isset($_POST['terms']) ? $_POST['terms'] : null;

// Server-side validation
$errors = [];

// Name validation
if (empty($name)) {
    $errors[] = 'Full name is required.';
} elseif (strlen($name) < 3 || strlen($name) > 50) {
    $errors[] = 'Full name must be between 3 and 50 characters.';
} elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
    $errors[] = 'Full name must contain letters and spaces only.';
}

// Email validation
if (empty($email)) {
    $errors[] = 'University email address is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address (e.g. student@charusat.edu.in).';
}

// Mobile validation (10-digit Indian format)
$cleanedMobile = preg_replace('/[\s\-]/', '', $mobile);
if (empty($cleanedMobile)) {
    $errors[] = 'Mobile number is required.';
} elseif (!preg_match('/^[6-9]\d{9}$/', $cleanedMobile)) {
    $errors[] = 'Mobile number must be a valid 10-digit Indian number starting with 6-9.';
}

// Course validation
$allowedCourses = ['B.Tech IT', 'B.Tech CSE', 'B.Tech CE', 'B.Tech EC'];
if (empty($course)) {
    $errors[] = 'Academic course selection is required.';
} elseif (!in_array($course, $allowedCourses)) {
    $errors[] = 'Invalid academic course selected.';
}

// Year validation
$allowedYears = ['1', '2', '3', '4'];
if (empty($year)) {
    $errors[] = 'Year of study is required.';
} elseif (!in_array($year, $allowedYears)) {
    $errors[] = 'Invalid year of study selected.';
}

// Gender validation
$allowedGenders = ['Male', 'Female', 'Other'];
if (empty($gender)) {
    $errors[] = 'Gender selection is required.';
} elseif (!in_array($gender, $allowedGenders)) {
    $errors[] = 'Invalid gender selected.';
}

// Terms acceptance validation
if (empty($terms)) {
    $errors[] = 'You must accept the campus IT guidelines and portal usage policy.';
}

// If validation fails, return errors
if (!empty($errors)) {
    http_response_code(422);
    if (isAjaxRequest()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => 'Validation failed. Please correct the highlighted errors.',
            'errors' => $errors
        ]);
    } else {
        renderHtmlResponse(false, $errors);
    }
    exit;
}

// Store validated record into CSV (Practical 7)
$storageDir = __DIR__ . '/../storage';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
}

$csvFilePath = $storageDir . '/registrations.csv';
$fileExists = file_exists($csvFilePath);

$fileHandle = @fopen($csvFilePath, 'a');
if (!$fileHandle) {
    http_response_code(500);
    $msg = 'Storage error: Unable to open registrations file for writing.';
    if (isAjaxRequest()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => $msg]);
    } else {
        renderHtmlResponse(false, [$msg]);
    }
    exit;
}

// Acquire exclusive file lock
flock($fileHandle, LOCK_EX);

// Write CSV header if creating a new file
if (!$fileExists || filesize($csvFilePath) === 0) {
    fputcsv($fileHandle, ['Name', 'Email', 'Mobile', 'Course', 'Year', 'Gender', 'Submitted At']);
}

// Append validated record (passwords are deliberately not stored)
$timestamp = date('Y-m-d H:i:s');
$record = [$name, $email, $cleanedMobile, $course, 'Year ' . $year, $gender, $timestamp];
fputcsv($fileHandle, $record);

// Release lock and close
flock($fileHandle, LOCK_UN);
fclose($fileHandle);

// Return success response
$successMessage = "Student registration for {$name} ({$course}) was successfully processed and recorded in registrations.csv.";

if (isAjaxRequest()) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => $successMessage,
        'data' => [
            'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            'email' => htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
            'course' => htmlspecialchars($course, ENT_QUOTES, 'UTF-8'),
            'year' => htmlspecialchars($year, ENT_QUOTES, 'UTF-8'),
            'gender' => htmlspecialchars($gender, ENT_QUOTES, 'UTF-8'),
            'submitted_at' => $timestamp
        ]
    ]);
} else {
    renderHtmlResponse(true, [$successMessage], [
        'Name' => $name,
        'Email' => $email,
        'Mobile' => $cleanedMobile,
        'Course' => $course,
        'Year' => 'Year ' . $year,
        'Gender' => $gender,
        'Timestamp' => $timestamp
    ]);
}

// Helper: detect AJAX / Fetch API requests
function isAjaxRequest() {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        return true;
    }
    if (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        return true;
    }
    if (!empty($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
        return true;
    }
    return false;
}

// Helper: render clean user-facing HTML result
function renderHtmlResponse($success, $messages, $data = []) {
    $statusClass = $success ? 'success' : 'danger';
    $statusBadge = $success ? 'badge-success' : 'badge-danger';
    $title = $success ? 'Registration Successful' : 'Registration Error';
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($title) ?> — SYNORA Portal</title>
        <link rel="stylesheet" href="../../css/global.css">
        <link rel="stylesheet" href="../../css/components.css">
        <style>
            body { display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1.5rem; background: var(--bg-primary); }
            .response-card { background: var(--bg-card); border: 1px solid var(--border-medium); border-radius: var(--radius-xl); max-width: 580px; width: 100%; padding: 2.5rem; box-shadow: var(--shadow-lg); }
            .response-list { margin: 1.25rem 0; padding-left: 1.25rem; color: var(--text-secondary); font-size: 0.95rem; }
            .response-list li { margin-bottom: 0.5rem; }
            .data-table { width: 100%; border-collapse: collapse; margin-top: 1.25rem; font-size: 0.9rem; }
            .data-table td { padding: 0.5rem 0.75rem; border-bottom: 1px solid var(--border-subtle); }
            .data-table td:first-child { font-weight: 600; color: var(--text-muted); width: 35%; }
        </style>
    </head>
    <body>
        <div class="response-card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                <span class="badge <?= $statusBadge ?>"><?= $success ? 'Practical 7: Processed' : 'Action Required' ?></span>
                <span class="text-xs text-muted font-mono">CSV Storage Engine</span>
            </div>
            <h1 style="font-size: 1.75rem; margin-bottom: 0.75rem;"><?= htmlspecialchars($title) ?></h1>
            <ul class="response-list">
                <?php foreach ($messages as $msg): ?>
                    <li><?= htmlspecialchars($msg) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php if (!empty($data)): ?>
                <table class="data-table">
                    <?php foreach ($data as $k => $v): ?>
                        <tr>
                            <td><?= htmlspecialchars($k) ?></td>
                            <td><?= htmlspecialchars($v) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
            <div style="margin-top: 2rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <?php if ($success): ?>
                    <a href="../../pages/dashboard.html" class="btn btn-primary">Go to Dashboard →</a>
                    <a href="../../pages/register.html" class="btn btn-secondary">Submit Another</a>
                <?php else: ?>
                    <a href="javascript:history.back()" class="btn btn-primary">← Go Back and Fix</a>
                <?php endif; ?>
            </div>
        </div>
    </body>
    </html>
    <?php
}
