<?php
// Practical 07 — PHP Form Processing with Server-Side Validation and CSV/JSON Storage
// CHARUSAT DEPSTAR Information Technology — ITUE203 Web Development Frameworks

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed. Only POST is accepted.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$name    = isset($input['name']) ? trim(strip_tags($input['name'])) : '';
$email   = isset($input['email']) ? trim(filter_var($input['email'], FILTER_SANITIZE_EMAIL)) : '';
$mobile  = isset($input['mobile']) ? trim(preg_replace('/[\s\-]/', '', $input['mobile'])) : '';
$course  = isset($input['course']) ? trim(strip_tags($input['course'])) : '';
$format  = isset($input['format']) && $input['format'] === 'json' ? 'json' : 'csv';
$comment = isset($input['comment']) ? trim(strip_tags($input['comment'])) : 'General student registration';

$errors = [];

// Name Validation
if (empty($name)) {
    $errors[] = 'Full name is required.';
} elseif (strlen($name) < 3 || strlen($name) > 50) {
    $errors[] = 'Full name must be between 3 and 50 characters.';
} elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
    $errors[] = 'Full name must contain letters and spaces only.';
}

// Email Validation
if (empty($email)) {
    $errors[] = 'University email address is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

// Mobile Validation
if (empty($mobile)) {
    $errors[] = 'Mobile number is required.';
} elseif (!preg_match('/^[6-9]\d{9}$/', $mobile)) {
    $errors[] = 'Mobile number must be a valid 10-digit Indian number starting with 6, 7, 8, or 9.';
}

// Course Validation
$allowedCourses = ['B.Tech IT', 'B.Tech CSE', 'B.Tech CE', 'B.Tech EC'];
if (empty($course) || !in_array($course, $allowedCourses)) {
    $errors[] = 'Please select a valid academic program from the list.';
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Server validation failed: ' . implode(' ', $errors),
        'errors'  => $errors
    ]);
    exit;
}

$storageDir = __DIR__ . '/storage/';
$timestamp = date('Y-m-d H:i:s');

try {
    if ($format === 'json') {
        // Append to feedback.json safely
        $jsonFile = $storageDir . 'feedback.json';
        $records = [];
        if (file_exists($jsonFile)) {
            $records = json_decode(file_get_contents($jsonFile), true) ?: [];
        }
        $records[] = [
            'id'         => count($records) + 1,
            'name'       => $name,
            'email'      => $email,
            'course'     => $course,
            'comment'    => $comment,
            'created_at' => $timestamp
        ];
        file_put_contents($jsonFile, json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
        $savedFile = 'storage/feedback.json';
    } else {
        // Append to registrations.csv with exclusive file lock
        $csvFile = $storageDir . 'registrations.csv';
        $isNew = !file_exists($csvFile) || filesize($csvFile) === 0;
        
        $fp = fopen($csvFile, 'a');
        if (!$fp) throw new RuntimeException('Cannot open storage file for writing.');

        flock($fp, LOCK_EX);
        if ($isNew) {
            fputcsv($fp, ['Timestamp', 'Full Name', 'Email', 'Mobile', 'Course']);
        }
        fputcsv($fp, [$timestamp, $name, $email, $mobile, $course]);
        flock($fp, LOCK_UN);
        fclose($fp);

        $savedFile = 'storage/registrations.csv';
    }

    http_response_code(201);
    echo json_encode([
        'success'    => true,
        'message'    => "Record for {$name} successfully validated and stored in {$savedFile}.",
        'storage'    => $savedFile,
        'timestamp'  => $timestamp,
        'record'     => [
            'name'   => $name,
            'email'  => $email,
            'mobile' => $mobile,
            'course' => $course
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal storage write error: ' . $e->getMessage()
    ]);
}
