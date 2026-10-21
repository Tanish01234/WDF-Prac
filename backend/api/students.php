<?php
// Practical 11 & 13 — Student Management RESTful CRUD API with Prepared MySQLi Statements
// CHARUSAT DEPSTAR Information Technology — ITUE203 Web Development Frameworks

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../config/db.php';

// Parse HTTP Method (support method spoofing via _method parameter or X-HTTP-Method-Override header)
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
} elseif (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
    $method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
}

// Helper to parse input for PUT/POST/DELETE
function getRequestData() {
    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);
    if (is_array($json)) {
        return $json;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        return $_POST;
    }
    parse_str($raw, $parsed);
    return is_array($parsed) ? $parsed : [];
}

try {
    $mysqli = getMysqliConnection();

    switch ($method) {
        // =========================================================
        // READ / LIST STUDENTS (GET)
        // =========================================================
        case 'GET':
            // Check if requesting single student by ID
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $id = (int)$_GET['id'];
                $stmt = $mysqli->prepare(
                    'SELECT s.*, u.username, u.role, u.status AS user_status 
                     FROM students s 
                     LEFT JOIN users u ON s.user_id = u.id 
                     WHERE s.id = ? LIMIT 1'
                );
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $student = $result->fetch_assoc();

                if (!$student) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => "Student record #{$id} not found."]);
                    exit;
                }

                echo json_encode(['success' => true, 'data' => $student]);
                exit;
            }

            // List with Search, Filter & Pagination
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $batch  = isset($_GET['batch']) ? trim($_GET['batch']) : '';
            $course = isset($_GET['course']) ? trim($_GET['course']) : '';
            $page   = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
            $limit  = isset($_GET['limit']) && (int)$_GET['limit'] > 0 ? min((int)$_GET['limit'], 50) : 10;
            $offset = ($page - 1) * $limit;

            $whereClauses = ['1=1'];
            $paramTypes   = '';
            $paramValues  = [];

            if (!empty($search)) {
                $whereClauses[] = '(s.name LIKE ? OR s.roll_no LIKE ? OR s.email LIKE ?)';
                $paramTypes .= 'sss';
                $searchWildcard = "%{$search}%";
                $paramValues[] = $searchWildcard;
                $paramValues[] = $searchWildcard;
                $paramValues[] = $searchWildcard;
            }

            if (!empty($batch) && $batch !== 'all') {
                $whereClauses[] = 's.batch = ?';
                $paramTypes .= 's';
                $paramValues[] = $batch;
            }

            if (!empty($course) && $course !== 'all') {
                $whereClauses[] = 's.course = ?';
                $paramTypes .= 's';
                $paramValues[] = $course;
            }

            $whereSql = implode(' AND ', $whereClauses);

            // 1. Get total record count
            $countSql = "SELECT COUNT(*) as total FROM students s WHERE {$whereSql}";
            $countStmt = $mysqli->prepare($countSql);
            if (!empty($paramValues)) {
                $countStmt->bind_param($paramTypes, ...$paramValues);
            }
            $countStmt->execute();
            $countRes = $countStmt->get_result()->fetch_assoc();
            $total = (int)$countRes['total'];

            // 2. Query page slice
            $dataSql = "SELECT s.*, u.username, u.role, u.status AS user_status 
                        FROM students s 
                        LEFT JOIN users u ON s.user_id = u.id 
                        WHERE {$whereSql} 
                        ORDER BY s.roll_no ASC 
                        LIMIT ? OFFSET ?";
            $paramTypes .= 'ii';
            $paramValues[] = $limit;
            $paramValues[] = $offset;

            $dataStmt = $mysqli->prepare($dataSql);
            $dataStmt->bind_param($paramTypes, ...$paramValues);
            $dataStmt->execute();
            $dataRes = $dataStmt->get_result();

            $students = [];
            while ($row = $dataRes->fetch_assoc()) {
                $students[] = $row;
            }

            echo json_encode([
                'success' => true,
                'data'    => $students,
                'pagination' => [
                    'total'       => $total,
                    'page'        => $page,
                    'limit'       => $limit,
                    'total_pages' => ceil($total / $limit)
                ]
            ]);
            break;

        // =========================================================
        // CREATE STUDENT (POST)
        // =========================================================
        case 'POST':
            requireAdmin(true);

            $data = getRequestData();

            $name     = isset($data['name']) ? trim(strip_tags($data['name'])) : '';
            $rollNo   = isset($data['roll_no']) ? strtoupper(trim(strip_tags($data['roll_no']))) : '';
            $email    = isset($data['email']) ? trim(filter_var($data['email'], FILTER_SANITIZE_EMAIL)) : '';
            $mobile   = isset($data['mobile']) ? trim(preg_replace('/[\s\-]/', '', $data['mobile'])) : '';
            $course   = isset($data['course']) ? trim(strip_tags($data['course'])) : 'B.Tech IT';
            $batch    = isset($data['batch']) ? trim(strip_tags($data['batch'])) : 'Batch A';
            $division = isset($data['division']) ? trim(strip_tags($data['division'])) : '3IT-A';
            $semester = isset($data['semester']) ? (int)$data['semester'] : 3;
            $year     = isset($data['year']) ? (int)$data['year'] : 2;
            $gender   = isset($data['gender']) && in_array($data['gender'], ['Male', 'Female', 'Other']) ? $data['gender'] : 'Male';

            if (empty($name) || empty($rollNo) || empty($email) || empty($mobile)) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Name, Roll Number, Email, and Mobile are mandatory fields.']);
                exit;
            }

            // Check duplicate roll number
            $chkStmt = $mysqli->prepare('SELECT id FROM students WHERE roll_no = ? LIMIT 1');
            $chkStmt->bind_param('s', $rollNo);
            $chkStmt->execute();
            if ($chkStmt->get_result()->fetch_assoc()) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => "Student with Roll Number {$rollNo} already exists."]);
                exit;
            }

            // Check duplicate email
            $chkMail = $mysqli->prepare('SELECT id FROM students WHERE email = ? LIMIT 1');
            $chkMail->bind_param('s', $email);
            $chkMail->execute();
            if ($chkMail->get_result()->fetch_assoc()) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => "Student with Email {$email} already exists."]);
                exit;
            }

            // Insert student record
            $insStmt = $mysqli->prepare(
                'INSERT INTO students (roll_no, name, email, mobile, course, semester, year, gender, division, batch)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $insStmt->bind_param('sssssiisss', $rollNo, $name, $email, $mobile, $course, $semester, $year, $gender, $division, $batch);
            $insStmt->execute();
            $newId = $mysqli->insert_id;

            $currentUser = getCurrentUser();
            logAuditAction(
                $currentUser['id'] ?? null,
                'STUDENT_CREATED',
                "Admin created student {$name} ({$rollNo})"
            );

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => "Student {$name} ({$rollNo}) created successfully.",
                'id'      => $newId
            ]);
            break;

        // =========================================================
        // UPDATE STUDENT (PUT)
        // =========================================================
        case 'PUT':
            requireAdmin(true);

            $data = getRequestData();
            $id   = isset($data['id']) ? (int)$data['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);

            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Valid Student ID is required for update.']);
                exit;
            }

            // Verify existence
            $verStmt = $mysqli->prepare('SELECT id, roll_no, name FROM students WHERE id = ? LIMIT 1');
            $verStmt->bind_param('i', $id);
            $verStmt->execute();
            $existing = $verStmt->get_result()->fetch_assoc();
            if (!$existing) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => "Student #{$id} not found."]);
                exit;
            }

            $name     = isset($data['name']) ? trim(strip_tags($data['name'])) : '';
            $rollNo   = isset($data['roll_no']) ? strtoupper(trim(strip_tags($data['roll_no']))) : '';
            $email    = isset($data['email']) ? trim(filter_var($data['email'], FILTER_SANITIZE_EMAIL)) : '';
            $mobile   = isset($data['mobile']) ? trim(preg_replace('/[\s\-]/', '', $data['mobile'])) : '';
            $course   = isset($data['course']) ? trim(strip_tags($data['course'])) : 'B.Tech IT';
            $batch    = isset($data['batch']) ? trim(strip_tags($data['batch'])) : 'Batch A';
            $division = isset($data['division']) ? trim(strip_tags($data['division'])) : '3IT-A';
            $semester = isset($data['semester']) ? (int)$data['semester'] : 3;
            $year     = isset($data['year']) ? (int)$data['year'] : 2;
            $gender   = isset($data['gender']) && in_array($data['gender'], ['Male', 'Female', 'Other']) ? $data['gender'] : 'Male';

            if (empty($name) || empty($rollNo) || empty($email) || empty($mobile)) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Name, Roll Number, Email, and Mobile cannot be empty.']);
                exit;
            }

            // Check duplicate roll number on other records
            $chkStmt = $mysqli->prepare('SELECT id FROM students WHERE roll_no = ? AND id != ? LIMIT 1');
            $chkStmt->bind_param('si', $rollNo, $id);
            $chkStmt->execute();
            if ($chkStmt->get_result()->fetch_assoc()) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => "Roll Number {$rollNo} is already assigned to another student."]);
                exit;
            }

            $updStmt = $mysqli->prepare(
                'UPDATE students 
                 SET roll_no = ?, name = ?, email = ?, mobile = ?, course = ?, semester = ?, year = ?, gender = ?, division = ?, batch = ? 
                 WHERE id = ?'
            );
            $updStmt->bind_param('sssssiisssi', $rollNo, $name, $email, $mobile, $course, $semester, $year, $gender, $division, $batch, $id);
            $updStmt->execute();

            $currentUser = getCurrentUser();
            logAuditAction(
                $currentUser['id'] ?? null,
                'STUDENT_UPDATED',
                "Admin updated student record #{$id} ({$rollNo} - {$name})"
            );

            echo json_encode([
                'success' => true,
                'message' => "Student record {$rollNo} updated successfully."
            ]);
            break;

        // =========================================================
        // DELETE STUDENT (DELETE)
        // =========================================================
        case 'DELETE':
            requireAdmin(true);

            $data = getRequestData();
            $id   = isset($data['id']) ? (int)$data['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);

            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Valid Student ID is required for deletion.']);
                exit;
            }

            // Fetch info before delete for audit
            $infoStmt = $mysqli->prepare('SELECT roll_no, name FROM students WHERE id = ? LIMIT 1');
            $infoStmt->bind_param('i', $id);
            $infoStmt->execute();
            $target = $infoStmt->get_result()->fetch_assoc();

            if (!$target) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => "Student #{$id} not found."]);
                exit;
            }

            $delStmt = $mysqli->prepare('DELETE FROM students WHERE id = ?');
            $delStmt->bind_param('i', $id);
            $delStmt->execute();

            $currentUser = getCurrentUser();
            logAuditAction(
                $currentUser['id'] ?? null,
                'STUDENT_DELETED',
                "Admin deleted student #{$id} ({$target['roll_no']} - {$target['name']})"
            );

            echo json_encode([
                'success' => true,
                'message' => "Student {$target['roll_no']} ({$target['name']}) has been deleted."
            ]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => "Method {$method} Not Allowed."]);
            break;
    }

} catch (Exception $e) {
    error_log('Students API Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'A database error occurred while processing student request: ' . $e->getMessage()
    ]);
}
