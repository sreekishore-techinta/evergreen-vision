<?php
// ============================================================
// Application Configuration
// ============================================================

// Session hardening
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime', 3600);

define('APP_NAME',    'Evergreen Admin');
define('APP_VERSION', '1.0.0');

// ── Base URL: bulletproof detection for any host / subfolder ──
// Works for:
//   localhost/evergreen-vision/admin/login.php  → BASE_URL = http://localhost/evergreen-vision
//   yourdomain.com/admin/login.php              → BASE_URL = https://yourdomain.com
//   yourdomain.com/login.php (root)             → BASE_URL = https://yourdomain.com
(function () {
    $scheme = 'http';
    if (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
        (!empty($_SERVER['HTTP_X_FORWARDED_SSL'])   && $_SERVER['HTTP_X_FORWARDED_SSL']   === 'on') ||
        (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
    ) {
        $scheme = 'https';
    }

    $host   = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';

    // Find the project root by looking for the "admin" directory in the path
    // e.g. /evergreen-vision/admin/login.php  →  keep /evergreen-vision
    // e.g. /admin/login.php                   →  keep '' (root)
    $dir   = dirname($script);              // e.g. /evergreen-vision/admin
    $parts = explode('/', trim($dir, '/'));  // ['evergreen-vision', 'admin']

    // Strip trailing admin / backend / api segments
    $strip = ['admin', 'includes', 'backend', 'api'];
    while (!empty($parts) && in_array(end($parts), $strip, true)) {
        array_pop($parts);
    }

    $root = empty($parts) || $parts === [''] ? '' : '/' . implode('/', $parts);

    define('BASE_URL',  $scheme . '://' . $host . $root);
    define('ADMIN_URL', BASE_URL . '/admin');
    define('API_URL',   BASE_URL . '/backend/api');
})();

// Session key
define('SESSION_KEY', 'ev_admin');

// CORS
define('ALLOWED_ORIGINS', [BASE_URL, 'http://localhost:3000', 'http://localhost:5173', 'http://localhost']);

// Timezone
date_default_timezone_set('Asia/Kolkata');

// ── Production mode ───────────────────────────────────────────
define('DEBUG_MODE', false);   // ← OFF for live server
if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
