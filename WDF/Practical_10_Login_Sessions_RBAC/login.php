<?php
// Practical 10 — Login Gateway with Password Verification & Session Regeneration
require_once __DIR__ . '/auth.php';

$error = null;
$message = null;

if (isset($_GET['error'])) {
    if ($_GET['error'] === 'unauthorized') $error = 'Please sign in to access that protected portal area.';
    if ($_GET['error'] === 'admin_only') $error = 'Administrator privileges required for that section.';
    if ($_GET['error'] === 'timeout') $error = 'Your session expired due to 30 minutes of inactivity. Please sign in again.';
}
if (isset($_GET['logged_out'])) {
    $message = 'You have logged out securely. Your session has been destroyed.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password   = (string)($_POST['password'] ?? '');

    if (empty($identifier) || empty($password)) {
        $error = 'Both email/username and password are required.';
    } else {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare(
                'SELECT u.*, s.roll_no, s.batch FROM users u 
                 LEFT JOIN students s ON u.id = s.user_id 
                 WHERE LOWER(u.email) = LOWER(:id1) OR LOWER(u.username) = LOWER(:id2) OR LOWER(s.roll_no) = LOWER(:id3) 
                 LIMIT 1'
            );
            $stmt->execute([':id1' => $identifier, ':id2' => $identifier, ':id3' => $identifier]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                // Prevent Session Fixation (Practical 10 Requirement)
                session_regenerate_id(true);

                $_SESSION['p10_user'] = [
                    'id'        => (int)$user['id'],
                    'username'  => $user['username'],
                    'email'     => $user['email'],
                    'full_name' => $user['full_name'],
                    'role'      => $user['role'],
                    'roll_no'   => $user['roll_no'] ?? 'N/A'
                ];
                $_SESSION['p10_last_activity'] = time();

                // Role-based redirection
                if ($user['role'] === 'admin') {
                    header('Location: admin-dashboard.php');
                } else {
                    header('Location: student-dashboard.php');
                }
                exit;
            } else {
                $error = 'Invalid credentials. Please verify your username/email and password.';
            }
        } catch (Exception $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Practical 10: Secure Login & Role-Based Access</title>
  <link rel="stylesheet" href="../../css/global.css">
  <link rel="stylesheet" href="../../css/components.css">
  <style>
    body { background: var(--bg-primary); color: var(--text-primary); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem 1rem; }
    .login-box { background: var(--bg-card); border: 1px solid var(--border-medium); border-radius: var(--radius-lg); padding: 2.5rem; max-width: 460px; width: 100%; box-shadow: var(--shadow-lg); }
  </style>
</head>
<body>
  <div class="login-box">
    <div style="text-align: center; margin-bottom: 2rem;">
      <span class="badge badge-accent" style="margin-bottom: 0.5rem;">Practical 10 Implementation</span>
      <h1 style="font-size: 1.65rem; font-weight: 700;">Student Portal Login</h1>
      <p style="color: var(--text-secondary); font-size: 0.85rem;">Role-Based Access (Student / Admin) & Session Timeout</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger" style="margin-bottom: 1.25rem; font-size: 0.85rem;">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <?php if ($message): ?>
      <div class="alert alert-success" style="margin-bottom: 1.25rem; font-size: 0.85rem;">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <!-- Quick test credentials pills -->
    <div style="background: var(--bg-elevated); border: 1px solid var(--border-subtle); padding: 0.75rem; border-radius: 6px; margin-bottom: 1.5rem; font-size: 0.8rem;">
      <div style="font-weight: 600; margin-bottom: 0.4rem;">Test Accounts (Click to autofill):</div>
      <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <button type="button" class="btn btn-secondary btn-sm" onclick="fill('25dit006@charusat.edu.in', 'Student@2026')">
          👨‍🎓 Student: 25DIT006
        </button>
        <button type="button" class="btn btn-secondary btn-sm" onclick="fill('admin@charusat.edu.in', 'Admin@2026')">
          🛡️ Admin: Dr. Parth Goel
        </button>
      </div>
    </div>

    <form method="POST" action="login.php">
      <div class="form-group">
        <label class="form-label" for="idField">Email / Roll No / Username</label>
        <input type="text" id="idField" name="identifier" class="form-input" value="25dit006@charusat.edu.in" required>
      </div>

      <div class="form-group" style="margin-bottom: 1.5rem;">
        <label class="form-label" for="passField">Password</label>
        <input type="password" id="passField" name="password" class="form-input" value="Student@2026" required>
      </div>

      <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
        Sign In with Session Regeneration →
      </button>
    </form>
  </div>

  <script>
    function fill(id, pass) {
      document.getElementById('idField').value = id;
      document.getElementById('passField').value = pass;
    }
  </script>
</body>
</html>
