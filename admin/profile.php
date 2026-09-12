<?php
require_once __DIR__ . '/includes/auth_check.php';

$pdo   = db();
$flash = '';
$flash_type = 'success';

// ── Fetch full admin record ───────────────────────────────────
$stmt = $pdo->prepare('SELECT * FROM admin_users WHERE id=?');
$stmt->execute([$current_admin['id']]);
$admin = $stmt->fetch();

// ── Handle POST ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $full_name = sanitize($_POST['full_name'] ?? '');
        $email     = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);

        if (!$email) {
            $flash = 'Please enter a valid email address.';
            $flash_type = 'error';
        } else {
            $pdo->prepare('UPDATE admin_users SET full_name=?, email=? WHERE id=?')
                ->execute([$full_name, $email, $admin['id']]);
            // Refresh session
            $_SESSION[SESSION_KEY]['full_name'] = $full_name;
            $_SESSION[SESSION_KEY]['email']     = $email;
            $admin['full_name'] = $full_name;
            $admin['email']     = $email;
            $current_admin['full_name'] = $full_name;
            log_activity('profile_update', 'Profile updated.');
            $flash = 'Profile updated successfully.';
        }

    } elseif ($action === 'change_password') {
        $current_pw = $_POST['current_password'] ?? '';
        $new_pw     = $_POST['new_password'] ?? '';
        $confirm_pw = $_POST['confirm_password'] ?? '';

        if (!password_verify($current_pw, $admin['password_hash'])) {
            $flash = 'Current password is incorrect.';
            $flash_type = 'error';
        } elseif (strlen($new_pw) < 8) {
            $flash = 'New password must be at least 8 characters.';
            $flash_type = 'error';
        } elseif ($new_pw !== $confirm_pw) {
            $flash = 'New passwords do not match.';
            $flash_type = 'error';
        } else {
            $hash = password_hash($new_pw, PASSWORD_BCRYPT, ['cost' => 12]);
            $pdo->prepare('UPDATE admin_users SET password_hash=? WHERE id=?')
                ->execute([$hash, $admin['id']]);
            log_activity('password_change', 'Password changed.');
            $flash = 'Password changed successfully.';
        }
    }
}

$page_title  = 'My Profile';
$breadcrumbs = [['Profile', '']];
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div>
    <h1 class="page-title">My Profile</h1>
    <p class="page-subtitle">Manage your account details and password.</p>
  </div>
</div>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash_type ?>">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
    <?php if ($flash_type === 'success'): ?>
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    <?php else: ?>
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
    <?php endif; ?>
  </svg>
  <?= htmlspecialchars($flash) ?>
</div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px" class="profile-grid">

  <!-- Profile card -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
        Account Info
      </div>
    </div>
    <div class="card-body">

      <!-- Avatar display -->
      <div style="display:flex;align-items:center;gap:16px;padding:16px;background:var(--green-50);border-radius:12px;margin-bottom:24px;border:1px solid var(--green-100)">
        <div style="width:56px;height:56px;background:linear-gradient(135deg,var(--green-600),var(--green-400));border-radius:14px;display:grid;place-items:center;font-size:1.3rem;font-weight:800;color:#fff;flex-shrink:0">
          <?= strtoupper(substr($admin['full_name'] ?: $admin['username'], 0, 1)) ?>
        </div>
        <div>
          <div style="font-weight:700;font-size:.95rem"><?= htmlspecialchars($admin['full_name'] ?: $admin['username']) ?></div>
          <div style="font-size:.8rem;color:var(--muted);margin-top:2px"><?= htmlspecialchars($admin['email']) ?></div>
          <span class="badge badge-<?= $admin['role'] ?>" style="margin-top:6px"><?= $admin['role'] ?></span>
        </div>
      </div>

      <!-- Info stats -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:24px">
        <div style="padding:12px;background:var(--sand);border-radius:10px">
          <div class="meta-label">Member Since</div>
          <div class="meta-value"><?= date('d M Y', strtotime($admin['created_at'])) ?></div>
        </div>
        <div style="padding:12px;background:var(--sand);border-radius:10px">
          <div class="meta-label">Last Login</div>
          <div class="meta-value"><?= $admin['last_login'] ? date('d M Y, H:i', strtotime($admin['last_login'])) : 'Never' ?></div>
        </div>
      </div>

      <form method="POST" action="profile.php">
        <input type="hidden" name="action" value="update_profile">
        <div class="form-group">
          <label class="form-label" for="full_name">Full Name</label>
          <input type="text" id="full_name" name="full_name" class="form-control"
                 value="<?= htmlspecialchars($admin['full_name'] ?? '') ?>"
                 placeholder="Your full name">
        </div>
        <div class="form-group">
          <label class="form-label" for="email">Email Address <span class="required">*</span></label>
          <input type="email" id="email" name="email" class="form-control" required
                 value="<?= htmlspecialchars($admin['email']) ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Username</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($admin['username']) ?>" disabled
                 style="background:var(--sand);cursor:not-allowed;opacity:.7">
          <div class="form-hint">Username cannot be changed.</div>
        </div>
        <button type="submit" class="btn btn-primary">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          Save Profile
        </button>
      </form>
    </div>
  </div>

  <!-- Change password -->
  <div>
    <div class="card" style="margin-bottom:20px">
      <div class="card-header">
        <div class="card-title">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
          Change Password
        </div>
      </div>
      <div class="card-body">
        <form method="POST" action="profile.php" id="pwForm">
          <input type="hidden" name="action" value="change_password">
          <div class="form-group">
            <label class="form-label" for="current_password">Current Password <span class="required">*</span></label>
            <input type="password" id="current_password" name="current_password" class="form-control" required autocomplete="current-password">
          </div>
          <div class="form-group">
            <label class="form-label" for="new_password">New Password <span class="required">*</span></label>
            <input type="password" id="new_password" name="new_password" class="form-control" required
                   minlength="8" autocomplete="new-password">
            <div class="form-hint">Minimum 8 characters.</div>
          </div>
          <div class="form-group">
            <label class="form-label" for="confirm_password">Confirm New Password <span class="required">*</span></label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required autocomplete="new-password">
          </div>
          <button type="submit" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
            Update Password
          </button>
        </form>
      </div>
    </div>

    <!-- Session info -->
    <div class="card">
      <div class="card-header">
        <div class="card-title">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
          Security
        </div>
      </div>
      <div class="card-body">
        <div class="activity-item">
          <div class="activity-dot"></div>
          <div>
            <div class="activity-text">Role: <strong><?= htmlspecialchars($admin['role']) ?></strong></div>
            <div class="activity-time">Access level</div>
          </div>
        </div>
        <div class="activity-item">
          <div class="activity-dot"></div>
          <div>
            <div class="activity-text">Account <strong><?= $admin['is_active'] ? 'Active' : 'Inactive' ?></strong></div>
            <div class="activity-time">Status</div>
          </div>
        </div>
        <div class="activity-item" style="border:none">
          <div class="activity-dot" style="background:#dc2626"></div>
          <div>
            <button onclick="doLogout()" class="activity-text" style="background:none;border:none;cursor:pointer;color:#dc2626;font-size:.82rem;padding:0">
              Sign out from all devices
            </button>
            <div class="activity-time">End current session</div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<style>
@media(max-width:900px){.profile-grid{grid-template-columns:1fr!important}}
</style>

<script>
// Client-side password match validation
document.getElementById('pwForm')?.addEventListener('submit', function(e) {
  const np = document.getElementById('new_password').value;
  const cp = document.getElementById('confirm_password').value;
  if (np !== cp) {
    e.preventDefault();
    alert('New passwords do not match.');
  }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
