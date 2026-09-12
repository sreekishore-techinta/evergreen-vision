<?php
// ============================================================
// API: File Upload — /backend/api/upload.php
// POST ?type=product|category   (admin only)
// Returns: { success, data: { url, path } }
// ============================================================
require_once __DIR__ . '/../config/helpers.php';
set_cors_headers();
start_session();
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_err('Method not allowed.', 405);

$type      = in_array($_GET['type'] ?? '', ['product','category']) ? $_GET['type'] : 'product';
$upload_dir_rel = 'uploads/' . $type . 's/';
$upload_dir_abs = dirname(__DIR__, 2) . '/' . $upload_dir_rel; // project root / uploads/products/

if (!is_dir($upload_dir_abs)) {
    mkdir($upload_dir_abs, 0755, true);
}

if (empty($_FILES['image'])) {
    json_err('No file uploaded.');
}

$file      = $_FILES['image'];
$max_bytes = 5 * 1024 * 1024; // 5 MB
$allowed   = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

// Validate
if ($file['error'] !== UPLOAD_ERR_OK) {
    $err_map = [
        UPLOAD_ERR_INI_SIZE   => 'File too large (server limit).',
        UPLOAD_ERR_FORM_SIZE  => 'File too large (form limit).',
        UPLOAD_ERR_PARTIAL    => 'File only partially uploaded.',
        UPLOAD_ERR_NO_FILE    => 'No file uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temp directory.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file.',
    ];
    json_err($err_map[$file['error']] ?? 'Upload error.', 400);
}

if ($file['size'] > $max_bytes) {
    json_err('File exceeds 5 MB limit.', 413);
}

// Use finfo for MIME — don't trust $_FILES['type']
$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mime     = $finfo->file($file['tmp_name']);
if (!in_array($mime, $allowed, true)) {
    json_err('Only JPEG, PNG, WebP, and GIF images are allowed.', 415);
}

$ext_map  = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
$ext      = $ext_map[$mime];

// Unique filename: timestamp + random bytes
$filename = date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
$dest     = $upload_dir_abs . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    json_err('Failed to save file. Check folder permissions.', 500);
}

// Build public URL
$scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
// Detect project root from SCRIPT_NAME: /evergreen-vision/backend/api/upload.php
$parts  = explode('/', ltrim($_SERVER['SCRIPT_NAME'], '/'));
$root   = '/' . $parts[0]; // /evergreen-vision
$url    = $scheme . '://' . $host . $root . '/' . $upload_dir_rel . $filename;
$path   = '/' . $upload_dir_rel . $filename; // relative to project root

log_activity('file_upload', "Uploaded $filename to $type.");

json_ok([
    'url'      => $url,
    'path'     => $path,
    'filename' => $filename,
], 'File uploaded successfully.', 201);
