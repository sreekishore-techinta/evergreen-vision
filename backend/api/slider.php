<?php
// ============================================================
// API: Hero Slider — /backend/api/slider.php
// GET              → list all slides (public)
// POST             → create slide  (admin)
// POST ?action=update&id=N → update slide (admin)
// POST ?action=delete&id=N → delete slide (admin)
// POST ?action=reorder     → reorder slides (admin)
// ============================================================
require_once __DIR__ . '/../config/helpers.php';
set_cors_headers();
start_session();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ── Ensure table exists ────────────────────────────────────
$pdo = db();
$pdo->exec("
    CREATE TABLE IF NOT EXISTS `hero_slides` (
      `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
      `title`       VARCHAR(200)  NOT NULL DEFAULT '',
      `subtitle`    VARCHAR(300)  NOT NULL DEFAULT '',
      `description` TEXT,
      `button_text` VARCHAR(100)  NOT NULL DEFAULT '',
      `button_url`  VARCHAR(255)  NOT NULL DEFAULT '',
      `image_url`   VARCHAR(255)  NOT NULL DEFAULT '',
      `is_active`   TINYINT(1)    NOT NULL DEFAULT 1,
      `sort_order`  INT           NOT NULL DEFAULT 0,
      `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      INDEX `idx_sort` (`sort_order`),
      INDEX `idx_active` (`is_active`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// ── GET — public list ──────────────────────────────────────
if ($method === 'GET') {
    $activeOnly = ($_GET['active'] ?? '1') === '1';
    $sql = 'SELECT * FROM hero_slides' . ($activeOnly ? ' WHERE is_active=1' : '') . ' ORDER BY sort_order ASC, id ASC';
    $slides = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    json_ok($slides, 'Slides fetched.');
}

// ── Require admin for write ops ────────────────────────────
require_login();

// ── POST reorder ───────────────────────────────────────────
if ($method === 'POST' && $action === 'reorder') {
    $body = get_body();
    $order = $body['order'] ?? [];
    if (!is_array($order)) json_err('Invalid order data.', 400);
    $stmt = $pdo->prepare('UPDATE hero_slides SET sort_order=? WHERE id=?');
    foreach ($order as $pos => $sid) {
        $stmt->execute([(int)$pos + 1, (int)$sid]);
    }
    log_activity('slider_reorder', 'Hero slides reordered.');
    json_ok(null, 'Order updated.');
}

// ── POST update ───────────────────────────────────────────
if ($method === 'POST' && $action === 'update') {
    if (!$id) json_err('Slide ID required.', 400);
    $title       = sanitize($_POST['title']       ?? '');
    $subtitle    = sanitize($_POST['subtitle']    ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $button_text = sanitize($_POST['button_text'] ?? '');
    $button_url  = sanitize($_POST['button_url']  ?? '');
    $is_active   = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;
    $sort_order  = isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0;
    $image_url   = sanitize($_POST['image_url']   ?? '');

    if (!empty($_FILES['image']['name'])) {
        $image_url = handle_slide_upload($_FILES['image']);
    }

    $stmt = $pdo->prepare(
        'UPDATE hero_slides SET title=?, subtitle=?, description=?, button_text=?, button_url=?, image_url=?, is_active=?, sort_order=? WHERE id=?'
    );
    $stmt->execute([$title, $subtitle, $description, $button_text, $button_url, $image_url, $is_active, $sort_order, $id]);
    log_activity('slider_update', "Updated slide #$id: $title");
    $slide = $pdo->query("SELECT * FROM hero_slides WHERE id=$id")->fetch(PDO::FETCH_ASSOC);
    json_ok($slide, 'Slide updated.');
}

// ── POST delete ───────────────────────────────────────────
if ($method === 'POST' && $action === 'delete') {
    if (!$id) json_err('Slide ID required.', 400);
    $slide = $pdo->query("SELECT * FROM hero_slides WHERE id=$id")->fetch(PDO::FETCH_ASSOC);
    if (!$slide) json_err('Slide not found.', 404);
    $pdo->exec("DELETE FROM hero_slides WHERE id=$id");
    log_activity('slider_delete', "Deleted slide #$id.");
    json_ok(null, 'Slide deleted.');
}

// ── POST — create ─────────────────────────────────────────
if ($method === 'POST') {
    $title       = sanitize($_POST['title']       ?? '');
    $subtitle    = sanitize($_POST['subtitle']    ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $button_text = sanitize($_POST['button_text'] ?? '');
    $button_url  = sanitize($_POST['button_url']  ?? '');
    $is_active   = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;
    $image_url   = sanitize($_POST['image_url']   ?? '');

    // Auto sort_order = max + 1
    $max_order = (int)$pdo->query('SELECT COALESCE(MAX(sort_order),0) FROM hero_slides')->fetchColumn();
    $sort_order = $max_order + 1;

    if (!empty($_FILES['image']['name'])) {
        $image_url = handle_slide_upload($_FILES['image']);
    }

    $stmt = $pdo->prepare(
        'INSERT INTO hero_slides (title, subtitle, description, button_text, button_url, image_url, is_active, sort_order)
         VALUES (?,?,?,?,?,?,?,?)'
    );
    $stmt->execute([$title, $subtitle, $description, $button_text, $button_url, $image_url, $is_active, $sort_order]);
    $new_id = $pdo->lastInsertId();
    log_activity('slider_create', "Created slide #$new_id: $title");
    $slide = $pdo->query("SELECT * FROM hero_slides WHERE id=$new_id")->fetch(PDO::FETCH_ASSOC);
    json_ok($slide, 'Slide created.', 201);
}

json_err('Unknown request.', 400);

// ── File upload helper ─────────────────────────────────────
function handle_slide_upload(array $file): string {
    $upload_dir_rel = 'uploads/slides/';
    $upload_dir_abs = dirname(__DIR__, 2) . '/' . $upload_dir_rel;
    if (!is_dir($upload_dir_abs)) mkdir($upload_dir_abs, 0755, true);

    $max_bytes = 5 * 1024 * 1024;
    $allowed   = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    if ($file['error'] !== UPLOAD_ERR_OK) json_err('Upload error: ' . $file['error'], 400);
    if ($file['size'] > $max_bytes)       json_err('File exceeds 5 MB limit.', 413);

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $allowed, true)) json_err('Only JPEG, PNG, WebP, GIF allowed.', 415);

    $ext_map  = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
    $filename = date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ext_map[$mime];
    $dest     = $upload_dir_abs . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) json_err('Failed to save file.', 500);

    $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $parts  = explode('/', ltrim($_SERVER['SCRIPT_NAME'], '/'));
    $root   = count($parts) > 2 ? '/' . $parts[0] : '';

    return $scheme . '://' . $host . $root . '/' . $upload_dir_rel . $filename;
}
