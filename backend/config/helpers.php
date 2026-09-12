<?php
// ============================================================
// Helper functions shared across API + Admin
// ============================================================
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

/* ── JSON response helpers ─────────────────────────────── */
function json_ok(mixed $data = null, string $message = 'OK', int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => true, 'message' => $message, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}

function json_err(string $message, int $code = 400, mixed $data = null): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => $message, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}

/* ── CORS ───────────────────────────────────────────────── */
function set_cors_headers(): void {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if (in_array($origin, ALLOWED_ORIGINS, true)) {
        header("Access-Control-Allow-Origin: $origin");
        header('Access-Control-Allow-Credentials: true');
    }
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

/* ── Session / Auth ─────────────────────────────────────── */
function start_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function is_logged_in(): bool {
    start_session();
    return isset($_SESSION[SESSION_KEY]) && !empty($_SESSION[SESSION_KEY]['id']);
}

function require_login(): void {
    if (!is_logged_in()) {
        if (str_contains($_SERVER['REQUEST_URI'] ?? '', '/api/')) {
            json_err('Unauthorized. Please login.', 401);
        } else {
            $redirect = defined('ADMIN_URL') ? ADMIN_URL . '/login.php' : '/evergreen-vision/admin/login.php';
            header('Location: ' . $redirect);
            exit;
        }
    }
}

function current_admin(): array {
    start_session();
    return $_SESSION[SESSION_KEY] ?? [];
}

/* ── Input sanitisation ──────────────────────────────────── */
function sanitize(string $val): string {
    return htmlspecialchars(strip_tags(trim($val)), ENT_QUOTES, 'UTF-8');
}

function get_body(): array {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Fallback to POST
        $data = $_POST;
    }
    return is_array($data) ? $data : [];
}

/* ── Slug generator ──────────────────────────────────────── */
function slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

/* ── Activity logger ─────────────────────────────────────── */
function log_activity(string $action, string $description = ''): void {
    try {
        $admin = current_admin();
        if (empty($admin['id'])) return;
        $pdo = db();
        $stmt = $pdo->prepare(
            'INSERT INTO admin_activity_log (admin_id, action, description, ip_address) VALUES (?,?,?,?)'
        );
        $stmt->execute([
            $admin['id'],
            $action,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? ''
        ]);
    } catch (Throwable) { /* non-fatal */ }
}

/* ── Pagination helper ───────────────────────────────────── */
function paginate(int $total, int $page, int $per_page): array {
    $total_pages = (int) ceil($total / $per_page);
    return [
        'total'       => $total,
        'per_page'    => $per_page,
        'current_page'=> $page,
        'total_pages' => $total_pages,
        'has_next'    => $page < $total_pages,
        'has_prev'    => $page > 1,
    ];
}
