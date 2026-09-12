<?php
require_once __DIR__ . '/../backend/config/helpers.php';
start_session();

// Already logged in? Go to dashboard
if (is_logged_in()) {
    header('Location: ' . ADMIN_URL . '/dashboard.php');
    exit;
}

// Absolute asset base for this page
$assets_url = ADMIN_URL . '/assets';

$error   = '';
$success = '';

// Handle PHP-native login (fallback if JS is disabled)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $pdo  = db();
        $stmt = $pdo->prepare(
            'SELECT id, username, email, full_name, role, password_hash, is_active
             FROM admin_users WHERE username = ? OR email = ? LIMIT 1'
        );
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash']) && $user['is_active']) {
            session_regenerate_id(true);
            $_SESSION[SESSION_KEY] = [
                'id'        => $user['id'],
                'username'  => $user['username'],
                'email'     => $user['email'],
                'full_name' => $user['full_name'],
                'role'      => $user['role'],
            ];
            $pdo->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = ?')
                ->execute([$user['id']]);
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Please enter your username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Admin Login — Evergreen Industry</title>
  <link rel="icon" href="<?= BASE_URL ?>/public/favicon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="<?= $assets_url ?>/css/admin.css">
</head>
<body>
<div class="login-page">

  <!-- Background shapes -->
  <div class="login-bg-shape login-bg-shape-1"></div>
  <div class="login-bg-shape login-bg-shape-2"></div>

  <!-- Floating grid dots -->
  <div style="position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,.04) 1px,transparent 1px);background-size:32px 32px;pointer-events:none;"></div>

  <div class="login-card">

    <!-- Logo -->
    <div class="login-logo">
      <div class="login-logo-icon">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 3C7 3 3 7.5 3 12c0 3.5 2 6.5 5 8v-4c-1.5-.8-2.5-2.4-2.5-4C5.5 9 8.4 6 12 6s6.5 3 6.5 6c0 1.6-1 3.2-2.5 4v4c3-1.5 5-4.5 5-8 0-4.5-4-9-9-9z"/>
          <rect x="11" y="17" width="2" height="4" rx="1"/>
        </svg>
      </div>
      <div class="login-logo-text">
        <div class="login-logo-name">EVERGREENINDUSTRY</div>
        <div class="login-logo-sub">Admin Portal</div>
      </div>
    </div>

    <h1 class="login-title">Welcome back</h1>
    <p class="login-subtitle">Sign in to manage your site content and enquiries.</p>

    <!-- Alerts -->
    <?php if ($error): ?>
    <div class="alert alert-error" id="error-alert">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
      </svg>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <div id="js-alert" style="display:none"></div>

    <!-- Login form -->
    <form class="login-form" id="loginForm" method="POST" action="">
      <div class="form-group">
        <label class="form-label" for="username">Username or Email</label>
        <div class="login-input-wrap">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
          </svg>
          <input
            type="text"
            id="username"
            name="username"
            placeholder="admin"
            autocomplete="username"
            required
            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
          >
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <div class="login-input-wrap">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
          </svg>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="••••••••"
            autocomplete="current-password"
            required
          >
        </div>
      </div>

      <button type="submit" class="login-btn" id="loginBtn">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
        </svg>
        Sign In
      </button>
    </form>

    <div class="login-footer">
      &larr; <a href="<?= BASE_URL ?>">Back to website</a>
      &nbsp;&nbsp;·&nbsp;&nbsp;
      Default: <strong>admin</strong> / <strong>Admin@1234</strong>
    </div>
  </div>
</div>

<script>
// Progressive enhancement — AJAX login
const form    = document.getElementById('loginForm');
const alertEl = document.getElementById('js-alert');
const phpAlert= document.getElementById('error-alert');
const btn     = document.getElementById('loginBtn');

form.addEventListener('submit', async e => {
  e.preventDefault();
  if (phpAlert) phpAlert.style.display = 'none';
  alertEl.style.display = 'none';

  const username = document.getElementById('username').value.trim();
  const password = document.getElementById('password').value;

  if (!username || !password) {
    showAlert('Please enter your username and password.', 'error');
    return;
  }

  btn.disabled = true;
  btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="animation:spin 1s linear infinite;width:17px;height:17px"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg> Signing in…`;

  try {
    const res = await fetch('<?= API_URL ?>/auth.php?action=login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({ username, password }),
    });
    const data = await res.json();

    if (data.success) {
      btn.innerHTML = `✓ Success! Redirecting…`;
      window.location.href = 'dashboard.php';
    } else {
      showAlert(data.message || 'Invalid credentials.', 'error');
      resetBtn();
    }
  } catch(err) {
    // Fall back to native form submit on network error
    form.submit();
  }
});

function showAlert(msg, type) {
  alertEl.className = `alert alert-${type}`;
  alertEl.innerHTML = msg;
  alertEl.style.display = 'flex';
}

function resetBtn() {
  btn.disabled = false;
  btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:17px;height:17px"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg> Sign In`;
}
</script>
<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>
</body>
</html>
