<?php
// Contact form processor (Practical 7)

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
        renderContactResponse(false, [$response['message']]);
    }
    exit;
}

// Read and sanitize input fields
$name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim(strip_tags($_POST['subject'])) : '';
$message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';

// Server-side validation
$errors = [];

if (empty($name)) {
    $errors[] = 'Full name is required.';
} elseif (strlen($name) < 3 || strlen($name) > 50) {
    $errors[] = 'Name must be between 3 and 50 characters.';
}

if (empty($email)) {
    $errors[] = 'Email address is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

if (empty($subject)) {
    $errors[] = 'Inquiry subject is required.';
} elseif (strlen($subject) < 4) {
    $errors[] = 'Subject must be at least 4 characters long.';
}

if (empty($message)) {
    $errors[] = 'Message details are required.';
} elseif (strlen($message) < 10) {
    $errors[] = 'Message must be at least 10 characters long.';
}

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
        renderContactResponse(false, $errors);
    }
    exit;
}

// Store into contacts.csv
$storageDir = __DIR__ . '/../storage';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
}

$csvFilePath = $storageDir . '/contacts.csv';
$fileExists = file_exists($csvFilePath);

$fileHandle = @fopen($csvFilePath, 'a');
if (!$fileHandle) {
    http_response_code(500);
    $msg = 'Storage error: Unable to open contacts file for writing.';
    if (isAjaxRequest()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => $msg]);
    } else {
        renderContactResponse(false, [$msg]);
    }
    exit;
}

flock($fileHandle, LOCK_EX);

if (!$fileExists || filesize($csvFilePath) === 0) {
    fputcsv($fileHandle, ['Name', 'Email', 'Subject', 'Message', 'Submitted At']);
}

$timestamp = date('Y-m-d H:i:s');
fputcsv($fileHandle, [$name, $email, $subject, $message, $timestamp]);

flock($fileHandle, LOCK_UN);
fclose($fileHandle);

$successMsg = "Your inquiry has been successfully delivered to the IT Department desk.";

if (isAjaxRequest()) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => $successMsg,
        'data' => [
            'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            'email' => htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
            'subject' => htmlspecialchars($subject, ENT_QUOTES, 'UTF-8'),
            'submitted_at' => $timestamp
        ]
    ]);
} else {
    renderContactResponse(true, [$successMsg], [
        'Name' => $name,
        'Email' => $email,
        'Subject' => $subject,
        'Submitted At' => $timestamp
    ]);
}

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

function renderContactResponse($success, $messages, $data = []) {
    $statusBadge = $success ? 'badge-success' : 'badge-danger';
    $title = $success ? 'Inquiry Sent' : 'Inquiry Error';
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
            .response-card { background: var(--bg-card); border: 1px solid var(--border-medium); border-radius: var(--radius-xl); max-width: 540px; width: 100%; padding: 2.5rem; box-shadow: var(--shadow-lg); }
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
                <span class="badge <?= $statusBadge ?>"><?= $success ? 'Delivered' : 'Action Required' ?></span>
                <span class="text-xs text-muted font-mono">DEPSTAR IT Desk</span>
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
            <div style="margin-top: 2rem; display: flex; gap: 0.75rem;">
                <a href="../../pages/contact.html" class="btn btn-primary">Return to Contact Desk →</a>
                <a href="../../pages/dashboard.html" class="btn btn-secondary">Dashboard</a>
            </div>
        </div>
    </body>
    </html>
    <?php
}
