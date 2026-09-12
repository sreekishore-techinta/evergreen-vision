<?php
// ============================================================
// Application Configuration
// ============================================================

// Session hardening
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime', 3600); // 1 hour

define('APP_NAME',    'Evergreen Admin');
define('APP_VERSION', '1.0.0');

// Base URL — auto-detected from server vars, no hardcoding needed
$_scheme   = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$_host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
// Walk up from the current script to find the project root
// Works for: /evergreen-vision/admin/xxx.php  →  /evergreen-vision
$_script   = $_SERVER['SCRIPT_NAME'] ?? '';
$_parts    = explode('/', trim($_script, '/'));
// The project root is two levels up from admin/*.php or one level up from admin/includes/*.php
$_root_pos = array_search('evergreen-vision', $_parts);
$_root     = $_root_pos !== false
    ? '/' . implode('/', array_slice($_parts, 0, $_root_pos + 1))
    : '/evergreen-vision';

define('BASE_URL',  $_scheme . '://' . $_host . $_root);
define('ADMIN_URL', BASE_URL . '/admin');
define('API_URL',   BASE_URL . '/backend/api');

// Session key used to track logged-in admin
define('SESSION_KEY', 'ev_admin');

// CORS origins allowed for the API (front-end dev server)
define('ALLOWED_ORIGINS', ['http://localhost:3000', 'http://localhost:5173', 'http://localhost', BASE_URL]);

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Error display — set false in production
define('DEBUG_MODE', true);
if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
