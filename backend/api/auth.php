<?php
// ============================================================
// API: Authentication  — /backend/api/auth.php
// POST /auth.php?action=login
// POST /auth.php?action=logout
// GET  /auth.php?action=me
// ============================================================
require_once __DIR__ . '/../config/helpers.php';
set_cors_headers();
start_session();

$action = $_GET['action'] ?? '';

match ($action) {
    'login'  => handle_login(),
    'logout' => handle_logout(),
    'me'     => handle_me(),
    default  => json_err('Unknown action.', 404),
};

// ─────────────────────────────────────────────────────────────
function handle_login(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_err('Method not allowed.', 405);
    }

    $body     = get_body();
    $username = trim($body['username'] ?? '');
    $password = trim($body['password'] ?? '');

    if ($username === '' || $password === '') {
        json_err('Username and password are required.');
    }

    $pdo  = db();
    $stmt = $pdo->prepare(
        'SELECT id, username, email, full_name, role, password_hash, is_active
         FROM admin_users WHERE username = ? OR email = ? LIMIT 1'
    );
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        json_err('Invalid username or password.', 401);
    }

    if (!$user['is_active']) {
        json_err('Your account has been disabled. Contact the superadmin.', 403);
    }

    // Regenerate session ID to prevent fixation
    session_regenerate_id(true);

    $_SESSION[SESSION_KEY] = [
        'id'        => $user['id'],
        'username'  => $user['username'],
        'email'     => $user['email'],
        'full_name' => $user['full_name'],
        'role'      => $user['role'],
    ];

    // Update last login timestamp
    $pdo->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = ?')
        ->execute([$user['id']]);

    log_activity('login', 'Admin logged in from ' . ($_SERVER['REMOTE_ADDR'] ?? ''));

    json_ok([
        'id'        => $user['id'],
        'username'  => $user['username'],
        'email'     => $user['email'],
        'full_name' => $user['full_name'],
        'role'      => $user['role'],
    ], 'Login successful.');
}

function handle_logout(): void {
    if (is_logged_in()) {
        log_activity('logout', 'Admin logged out.');
    }
    $_SESSION = [];
    session_destroy();
    json_ok(null, 'Logged out successfully.');
}

function handle_me(): void {
    require_login();
    json_ok(current_admin());
}
