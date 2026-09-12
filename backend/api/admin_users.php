<?php
// ============================================================
// API: Admin Users — /backend/api/admin_users.php
// GET  ?action=list              (superadmin)
// POST ?action=create            (superadmin)
// POST ?action=update_profile    (any admin — own profile)
// POST ?action=change_password   (any admin — own password)
// POST ?action=toggle_active     (superadmin)
// DELETE ?action=delete&id=X     (superadmin)
// GET  ?action=activity_log      (superadmin)
// ============================================================
require_once __DIR__ . '/../config/helpers.php';
set_cors_headers();
start_session();

$action = $_GET['action'] ?? '';

match ($action) {
    'list'             => list_users(),
    'create'           => create_user(),
    'update_profile'   => update_profile(),
    'change_password'  => change_password(),
    'toggle_active'    => toggle_active(),
    'delete'           => delete_user(),
    'activity_log'     => activity_log(),
    default            => json_err('Unknown action.', 404),
};

function require_superadmin(): void {
    require_login();
    $admin = current_admin();
    if ($admin['role'] !== 'superadmin') {
        json_err('Superadmin access required.', 403);
    }
}

function list_users(): void {
    require_superadmin();
    $pdo  = db();
    $rows = $pdo->query(
        'SELECT id, username, email, full_name, role, is_active, last_login, created_at
         FROM admin_users ORDER BY id ASC'
    )->fetchAll();
    json_ok($rows);
}

function create_user(): void {
    require_superadmin();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_err('Method not allowed.', 405);

    $body     = get_body();
    $username = sanitize($body['username'] ?? '');
    $email    = filter_var(trim($body['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $body['password'] ?? '';
    $role     = $body['role'] ?? 'admin';

    $errors = [];
    if ($username === '')              $errors[] = 'Username is required.';
    if (!$email)                       $errors[] = 'Valid email is required.';
    if (strlen($password) < 8)         $errors[] = 'Password must be at least 8 characters.';
    if (!in_array($role, ['superadmin','admin','editor'], true)) $errors[] = 'Invalid role.';
    if ($errors) json_err(implode(' ', $errors), 422);

    $pdo  = db();
    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    try {
        $stmt = $pdo->prepare(
            'INSERT INTO admin_users (username, email, password_hash, full_name, role)
             VALUES (?,?,?,?,?)'
        );
        $stmt->execute([$username, $email, $hash, sanitize($body['full_name'] ?? ''), $role]);
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'Duplicate')) {
            json_err('Username or email already exists.', 409);
        }
        throw $e;
    }

    log_activity('user_create', "Admin user '$username' created.");
    json_ok(['id' => $pdo->lastInsertId()], 'User created.', 201);
}

function update_profile(): void {
    require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_err('Method not allowed.', 405);

    $body      = get_body();
    $admin     = current_admin();
    $full_name = sanitize($body['full_name'] ?? '');
    $email     = filter_var(trim($body['email'] ?? ''), FILTER_VALIDATE_EMAIL);

    if (!$email) json_err('Valid email is required.');

    $pdo  = db();
    $stmt = $pdo->prepare('UPDATE admin_users SET full_name=?, email=? WHERE id=?');
    $stmt->execute([$full_name, $email, $admin['id']]);

    // Refresh session
    $_SESSION[SESSION_KEY]['full_name'] = $full_name;
    $_SESSION[SESSION_KEY]['email']     = $email;

    log_activity('profile_update', 'Profile updated.');
    json_ok(['full_name' => $full_name, 'email' => $email], 'Profile updated.');
}

function change_password(): void {
    require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_err('Method not allowed.', 405);

    $body        = get_body();
    $admin       = current_admin();
    $current_pw  = $body['current_password'] ?? '';
    $new_pw      = $body['new_password'] ?? '';
    $confirm_pw  = $body['confirm_password'] ?? '';

    if ($new_pw !== $confirm_pw) json_err('New passwords do not match.');
    if (strlen($new_pw) < 8)     json_err('Password must be at least 8 characters.');

    $pdo  = db();
    $stmt = $pdo->prepare('SELECT password_hash FROM admin_users WHERE id = ?');
    $stmt->execute([$admin['id']]);
    $row  = $stmt->fetch();

    if (!$row || !password_verify($current_pw, $row['password_hash'])) {
        json_err('Current password is incorrect.', 401);
    }

    $hash = password_hash($new_pw, PASSWORD_BCRYPT, ['cost' => 12]);
    $pdo->prepare('UPDATE admin_users SET password_hash = ? WHERE id = ?')
        ->execute([$hash, $admin['id']]);

    log_activity('password_change', 'Password changed.');
    json_ok(null, 'Password updated successfully.');
}

function toggle_active(): void {
    require_superadmin();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_err('Method not allowed.', 405);

    $body  = get_body();
    $id    = (int)($body['id'] ?? 0);
    $admin = current_admin();
    if ($id < 1)          json_err('Invalid ID.');
    if ($id === (int)$admin['id']) json_err('Cannot disable your own account.');

    $pdo  = db();
    $pdo->prepare('UPDATE admin_users SET is_active = NOT is_active WHERE id = ?')
        ->execute([$id]);
    json_ok(null, 'Status toggled.');
}

function delete_user(): void {
    require_superadmin();
    $id    = (int)($_GET['id'] ?? (get_body()['id'] ?? 0));
    $admin = current_admin();
    if ($id < 1)          json_err('Invalid ID.');
    if ($id === (int)$admin['id']) json_err('Cannot delete your own account.');

    $pdo  = db();
    $stmt = $pdo->prepare('DELETE FROM admin_users WHERE id = ?');
    $stmt->execute([$id]);
    if ($stmt->rowCount() === 0) json_err('User not found.', 404);

    log_activity('user_delete', "Admin user #$id deleted.");
    json_ok(null, 'User deleted.');
}

function activity_log(): void {
    require_superadmin();
    $page    = max(1, (int)($_GET['page'] ?? 1));
    $per_page= 30;
    $offset  = ($page - 1) * $per_page;

    $pdo  = db();
    $rows = $pdo->prepare(
        'SELECT l.*, u.username FROM admin_activity_log l
         LEFT JOIN admin_users u ON u.id = l.admin_id
         ORDER BY l.created_at DESC LIMIT ? OFFSET ?'
    );
    $rows->execute([$per_page, $offset]);

    $total = (int)$pdo->query('SELECT COUNT(*) FROM admin_activity_log')->fetchColumn();
    json_ok([
        'items'      => $rows->fetchAll(),
        'pagination' => paginate($total, $page, $per_page),
    ]);
}
