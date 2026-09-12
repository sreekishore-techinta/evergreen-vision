<?php
// ============================================================
// API: Site Settings — /backend/api/settings.php
// GET  ?action=get_all           (admin)
// GET  ?action=get&key=X         (public — safe keys only)
// POST ?action=update            (admin)
// ============================================================
require_once __DIR__ . '/../config/helpers.php';
set_cors_headers();
start_session();

$action = $_GET['action'] ?? 'get_all';

match ($action) {
    'get_all' => get_all_settings(),
    'get'     => get_setting(),
    'update'  => update_settings(),
    default   => json_err('Unknown action.', 404),
};

function get_all_settings(): void {
    require_login();
    $pdo  = db();
    $rows = $pdo->query(
        'SELECT setting_key, setting_val, label, group_name, sort_order
         FROM site_settings ORDER BY group_name, sort_order'
    )->fetchAll();
    json_ok($rows);
}

function get_setting(): void {
    // Allow public access to a curated whitelist of keys
    $public_keys = [
        'company_name', 'tagline', 'contact_email', 'contact_phone',
        'contact_address', 'facebook_url', 'instagram_url', 'linkedin_url',
        'twitter_url', 'meta_title', 'meta_description',
    ];

    $key = trim($_GET['key'] ?? '');
    if ($key === '') json_err('Key is required.');

    if (!is_logged_in() && !in_array($key, $public_keys, true)) {
        json_err('Unauthorized.', 401);
    }

    $pdo  = db();
    $stmt = $pdo->prepare('SELECT setting_val FROM site_settings WHERE setting_key = ?');
    $stmt->execute([$key]);
    $row  = $stmt->fetch();
    if (!$row) json_err('Setting not found.', 404);

    json_ok(['key' => $key, 'value' => $row['setting_val']]);
}

function update_settings(): void {
    require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_err('Method not allowed.', 405);

    $body = get_body();
    // Expect: {"settings": [{"key":"company_name","value":"Acme"}, ...]}
    // OR a flat key-value map: {"company_name":"Acme", ...}
    $pdo  = db();

    if (isset($body['settings']) && is_array($body['settings'])) {
        $pairs = $body['settings'];
    } else {
        // flat map
        $pairs = array_map(
            fn($k, $v) => ['key' => $k, 'value' => $v],
            array_keys($body),
            array_values($body)
        );
    }

    $stmt = $pdo->prepare(
        'UPDATE site_settings SET setting_val = ? WHERE setting_key = ?'
    );
    $updated = 0;
    foreach ($pairs as $pair) {
        $key = trim($pair['key'] ?? '');
        $val = $pair['value'] ?? '';
        if ($key === '') continue;
        $stmt->execute([sanitize((string)$val), $key]);
        $updated += $stmt->rowCount();
    }

    log_activity('settings_update', "$updated setting(s) updated.");
    json_ok(['updated' => $updated], 'Settings saved.');
}
