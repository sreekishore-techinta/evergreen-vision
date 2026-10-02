<?php
require_once __DIR__ . '/../backend/config/helpers.php';
start_session();

// Already logged in? Go to dashboard
if (is_logged_in()) {
    header('Location: ' . ADMIN_URL . '/dashboard.php');
    exit;
}

// login.php lives at admin/login.php — assets are always relative
// No PHP URL needed; browser resolves assets/css/admin.css from admin/

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
  <link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="assets/css/admin.css?v=<?= file_exists(__DIR__ . '/assets/css/admin.css') ? filemtime(__DIR__ . '/assets/css/admin.css') : time() ?>">
  <style>
    .password-field-wrapper {
      position: relative !important;
      display: block !important;
      width: 100% !important;
    }
    .password-field-wrapper input {
      width: 100% !important;
      padding: 12px 46px 12px 40px !important;
      border: 1.5px solid var(--border-dark, #e5e7eb) !important;
      border-radius: 10px !important;
      font-size: .88rem !important;
      color: var(--ink, #1f2937) !important;
      background: #fff !important;
      outline: none !important;
      box-sizing: border-box !important;
      transition: all var(--transition, 0.2s) !important;
    }
    .password-field-wrapper input:focus {
      border-color: var(--green-500, #22c55e) !important;
      box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15) !important;
    }
    .password-field-wrapper .input-leading-icon {
      position: absolute !important;
      left: 13px !important;
      top: 50% !important;
      transform: translateY(-50%) !important;
      width: 16px !important;
      height: 16px !important;
      color: var(--muted, #9ca3af) !important;
      pointer-events: none !important;
      z-index: 2 !important;
    }
    .password-eye-btn {
      position: absolute !important;
      right: 8px !important;
      top: 50% !important;
      transform: translateY(-50%) !important;
      width: 34px !important;
      height: 34px !important;
      padding: 0 !important;
      margin: 0 !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      background: transparent !important;
      border: none !important;
      outline: none !important;
      border-radius: 7px !important;
      cursor: pointer !important;
      color: #94a3b8 !important;
      z-index: 10 !important;
      transition: color 0.15s ease, background-color 0.15s ease !important;
    }
    .password-eye-btn:hover {
      color: #16a34a !important;
      background-color: rgba(22, 163, 74, 0.1) !important;
    }
    .password-eye-btn svg {
      width: 18px !important;
      height: 18px !important;
      position: static !important;
      left: auto !important;
      top: auto !important;
      transform: none !important;
      pointer-events: none !important;
      color: inherit !important;
    }
    input::-ms-reveal,
    input::-ms-clear {
      display: none !important;
    }
  </style>
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
        <div class="password-field-wrapper">
          <svg class="input-leading-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
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
          <button
            type="button"
            id="togglePassword"
            class="password-eye-btn"
            title="Show password"
            aria-label="Show password"
          >
            <!-- Standard Eye icon (Show password) -->
            <svg class="eye-icon-show" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
              <circle cx="12" cy="12" r="3"></circle>
            </svg>
            <!-- Standard Eye-Off icon (Hide password) -->
            <svg class="eye-icon-hide" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
              <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
              <line x1="1" y1="1" x2="23" y2="23"></line>
            </svg>
          </button>
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
      &larr; <a href="../">Back to website</a>
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

// ── Password visibility toggle ────────────────────────────
function togglePwd(e) {
  if (e) {
    e.preventDefault();
    e.stopPropagation();
  }
  const input = document.getElementById('password');
  const btn   = document.getElementById('togglePassword');
  if (!input || !btn) return;

  const showIcon = btn.querySelector('.eye-icon-show');
  const hideIcon = btn.querySelector('.eye-icon-hide');
  const isCurrentlyPassword = (input.type === 'password');

  if (isCurrentlyPassword) {
    input.setAttribute('type', 'text');
    input.type = 'text';
    if (showIcon) showIcon.style.display = 'none';
    if (hideIcon) hideIcon.style.display = 'block';
    btn.setAttribute('title', 'Hide password');
    btn.setAttribute('aria-label', 'Hide password');
  } else {
    input.setAttribute('type', 'password');
    input.type = 'password';
    if (showIcon) showIcon.style.display = 'block';
    if (hideIcon) hideIcon.style.display = 'none';
    btn.setAttribute('title', 'Show password');
    btn.setAttribute('aria-label', 'Show password');
  }

  try {
    const len = input.value.length;
    input.setSelectionRange(len, len);
  } catch(_) {}
  input.focus();
}

const toggleBtn = document.getElementById('togglePassword');
if (toggleBtn) {
  toggleBtn.onclick = togglePwd;
}
</script>
<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>
</body>
</html>
