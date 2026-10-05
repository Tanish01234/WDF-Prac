<?php
// Feedback form processor (Practical 7 JSON Storage)

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
        renderFeedbackResponse(false, [$response['message']]);
    }
    exit;
}

// Read and sanitize input fields
$name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$category = isset($_POST['category']) ? trim(strip_tags($_POST['category'])) : '';
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 5;
$comments = isset($_POST['comments']) ? trim(strip_tags($_POST['comments'])) : '';

// Server-side validation
$errors = [];

if (empty($name)) {
    $errors[] = 'Your name is required.';
} elseif (strlen($name) < 2 || strlen($name) > 50) {
    $errors[] = 'Name must be between 2 and 50 characters.';
}

if (empty($email)) {
    $errors[] = 'Email address is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid student email address.';
}

if (empty($category)) {
    $errors[] = 'Feedback category selection is required.';
}

if ($rating < 1 || $rating > 5) {
    $errors[] = 'Rating must be an integer between 1 and 5.';
}

if (empty($comments)) {
    $errors[] = 'Feedback comments are required.';
} elseif (strlen($comments) < 10) {
    $errors[] = 'Please provide at least 10 characters of feedback detail.';
}

if (!empty($errors)) {
    http_response_code(422);
    if (isAjaxRequest()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => 'Validation failed. Please correct the errors.',
            'errors' => $errors
        ]);
    } else {
        renderFeedbackResponse(false, $errors);
    }
    exit;
}

// Store into JSON (Practical 7)
$storageDir = __DIR__ . '/../storage';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
}

$jsonFilePath = $storageDir . '/feedback.json';
$feedbackList = [];

if (file_exists($jsonFilePath)) {
    $rawContent = file_get_contents($jsonFilePath);
    if (!empty($rawContent)) {
        $decoded = json_decode($rawContent, true);
        if (is_array($decoded)) {
            $feedbackList = $decoded;
        }
    }
}

$timestamp = date('Y-m-d H:i:s');
$newEntry = [
    'id' => count($feedbackList) + 1,
    'name' => $name,
    'email' => $email,
    'category' => $category,
    'rating' => $rating,
    'comments' => $comments,
    'submitted_at' => $timestamp
];

$feedbackList[] = $newEntry;

$encoded = json_encode($feedbackList, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
$writeSuccess = @file_put_contents($jsonFilePath, $encoded, LOCK_EX);

if ($writeSuccess === false) {
    http_response_code(500);
    $msg = 'Storage error: Unable to record feedback into JSON file.';
    if (isAjaxRequest()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => $msg]);
    } else {
        renderFeedbackResponse(false, [$msg]);
    }
    exit;
}

$successMsg = "Thank you! Your feedback for {$category} has been recorded in feedback.json.";

if (isAjaxRequest()) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => $successMsg,
        'data' => [
            'id' => $newEntry['id'],
            'category' => htmlspecialchars($category, ENT_QUOTES, 'UTF-8'),
            'rating' => $rating,
            'submitted_at' => $timestamp
        ]
    ]);
} else {
    renderFeedbackResponse(true, [$successMsg], [
        'Entry ID' => '#' . $newEntry['id'],
        'Student' => $name,
        'Category' => $category,
        'Rating' => str_repeat('⭐', $rating),
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

function renderFeedbackResponse($success, $messages, $data = []) {
    $statusBadge = $success ? 'badge-success' : 'badge-danger';
    $title = $success ? 'Feedback Recorded' : 'Feedback Error';
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
                <span class="badge <?= $statusBadge ?>"><?= $success ? 'Recorded' : 'Action Required' ?></span>
                <span class="text-xs text-muted font-mono">JSON Storage Engine</span>
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
                <a href="../../pages/feedback.html" class="btn btn-primary">Return to Feedback →</a>
                <a href="../../pages/dashboard.html" class="btn btn-secondary">Dashboard</a>
            </div>
        </div>
    </body>
    </html>
    <?php
}
