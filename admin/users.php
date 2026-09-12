<?php
require_once __DIR__ . '/includes/auth_check.php';

// Superadmin only
if ($current_admin['role'] !== 'superadmin') {
    header('Location: dashboard.php');
    exit;
}

$pdo   = db();
$flash = '';
$flash_type = 'success';

// ── Handle POST ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $username  = sanitize($_POST['username'] ?? '');
        $email     = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $full_name = sanitize($_POST['full_name'] ?? '');
        $password  = $_POST['password'] ?? '';
        $role      = $_POST['role'] ?? 'admin';
        $allowed_roles = ['superadmin','admin','editor'];

        if (!$username || !$email || strlen($password) < 8 || !in_array($role, $allowed_roles, true)) {
            $flash = 'Please fill all required fields correctly (password min 8 chars).';
            $flash_type = 'error';
        } else {
            try {
                $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $pdo->prepare(
                    'INSERT INTO admin_users (username,email,password_hash,full_name,role) VALUES (?,?,?,?,?)'
                )->execute([$username, $email, $hash, $full_name, $role]);
                log_activity('user_create', "Admin '$username' created.");
                $flash = "User '$username' created successfully.";
            } catch (PDOException $e) {
                $flash = str_contains($e->getMessage(), 'Duplicate') ? 'Username or email already exists.' : 'Error creating user.';
                $flash_type = 'error';
            }
        }

    } elseif ($action === 'toggle_active') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id && $id !== (int)$current_admin['id']) {
            $pdo->prepare('UPDATE admin_users SET is_active = NOT is_active WHERE id=?')->execute([$id]);
            $flash = 'User status toggled.';
        } else {
            $flash = 'Cannot disable your own account.';
            $flash_type = 'error';
        }

    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id && $id !== (int)$current_admin['id']) {
            $pdo->prepare('DELETE FROM admin_users WHERE id=?')->execute([$id]);
            log_activity('user_delete', "Admin user #$id deleted.");
            $flash = 'User deleted.';
        } else {
            $flash = 'Cannot delete your own account.';
            $flash_type = 'error';
        }
    }
}

// ── Fetch users ───────────────────────────────────────────────
$users = $pdo->query(
    'SELECT id, username, email, full_name, role, is_active, last_login, created_at
     FROM admin_users ORDER BY id ASC'
)->fetchAll();

$page_title  = 'Admin Users';
$breadcrumbs = [['Admin Users', '']];
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Admin Users</h1>
    <p class="page-subtitle"><?= count($users) ?> admin account<?= count($users)!==1?'s':'' ?></p>
  </div>
  <button class="btn btn-primary" onclick="Modal.open('addUserModal')">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/></svg>
    Add Admin User
  </button>
</div>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash_type ?>">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  <?= htmlspecialchars($flash) ?>
</div>
<?php endif; ?>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>User</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Last Login</th>
          <th>Joined</th>
          <th style="width:100px">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td class="text-muted text-sm"><?= $u['id'] ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:10px">
              <div style="width:34px;height:34px;background:linear-gradient(135deg,var(--green-600),var(--green-400));border-radius:8px;display:grid;place-items:center;font-size:.8rem;font-weight:700;color:#fff;flex-shrink:0">
                <?= strtoupper(substr($u['full_name'] ?: $u['username'], 0, 1)) ?>
              </div>
              <div>
                <div class="fw-600"><?= htmlspecialchars($u['full_name'] ?: $u['username']) ?></div>
                <div class="text-muted text-sm">@<?= htmlspecialchars($u['username']) ?></div>
              </div>
            </div>
          </td>
          <td class="td-email"><?= htmlspecialchars($u['email']) ?></td>
          <td><span class="badge badge-<?= $u['role'] ?>"><?= $u['role'] ?></span></td>
          <td>
            <?php if ($u['id'] !== (int)$current_admin['id']): ?>
            <form method="POST" style="display:inline">
              <input type="hidden" name="action" value="toggle_active">
              <input type="hidden" name="id" value="<?= $u['id'] ?>">
              <button type="submit" class="badge <?= $u['is_active'] ? 'badge-active' : 'badge-inactive' ?>" style="border:none;cursor:pointer;background:none;padding:3px 10px">
                <?= $u['is_active'] ? 'Active' : 'Inactive' ?>
              </button>
            </form>
            <?php else: ?>
              <span class="badge badge-active">Active (you)</span>
            <?php endif; ?>
          </td>
          <td class="td-date"><?= $u['last_login'] ? date('d M Y, H:i', strtotime($u['last_login'])) : '—' ?></td>
          <td class="td-date"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
          <td>
            <?php if ($u['id'] !== (int)$current_admin['id']): ?>
            <form method="POST" onsubmit="return confirm('Delete admin user <?= htmlspecialchars(addslashes($u['username'])) ?>?')" style="display:inline">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= $u['id'] ?>">
              <button type="submit" class="btn btn-ghost btn-icon btn-sm" style="color:#dc2626" title="Delete">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
              </button>
            </form>
            <?php else: ?>
              <span class="text-muted text-sm">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add User Modal -->
<div class="modal-overlay" id="addUserModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Add Admin User</div>
      <button class="modal-close" onclick="Modal.close('addUserModal')">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form method="POST" action="users.php">
      <input type="hidden" name="action" value="create">
      <div class="modal-body">
        <div class="grid-2">
          <div class="form-group">
            <label class="form-label">Username <span class="required">*</span></label>
            <input type="text" name="username" class="form-control" required placeholder="johndoe">
          </div>
          <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" placeholder="John Doe">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Email <span class="required">*</span></label>
          <input type="email" name="email" class="form-control" required placeholder="john@evergreenindustry.com">
        </div>
        <div class="grid-2">
          <div class="form-group">
            <label class="form-label">Password <span class="required">*</span></label>
            <input type="password" name="password" class="form-control" required minlength="8" placeholder="Min. 8 characters">
          </div>
          <div class="form-group">
            <label class="form-label">Role <span class="required">*</span></label>
            <select name="role" class="form-control">
              <option value="admin">Admin</option>
              <option value="editor">Editor</option>
              <option value="superadmin">Superadmin</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="Modal.close('addUserModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Create User</button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
