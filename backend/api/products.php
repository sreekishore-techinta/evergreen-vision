<?php
// ============================================================
// API: Products — /backend/api/products.php
// GET    ?action=list            (public)
// GET    ?action=get&id=X        (public)
// POST   ?action=create          (admin)
// POST   ?action=update          (admin)
// DELETE ?action=delete&id=X     (admin)
// POST   ?action=reorder         (admin)
// ============================================================
require_once __DIR__ . '/../config/helpers.php';
set_cors_headers();
start_session();

$action = $_GET['action'] ?? 'list';
$method = $_SERVER['REQUEST_METHOD'];

match ($action) {
    'list'    => list_products(),
    'get'     => get_product(),
    'create'  => create_product(),
    'update'  => update_product(),
    'delete'  => delete_product(),
    'reorder' => reorder_products(),
    default   => json_err('Unknown action.', 404),
};

// ── List products ─────────────────────────────────────────────
function list_products(): void {
    $pdo     = db();
    $admin   = is_logged_in(); // show inactive to admin
    $where   = $admin ? '' : 'WHERE is_active = 1';
    $search  = trim($_GET['search'] ?? '');
    $cat     = trim($_GET['category'] ?? '');
    $params  = [];

    if (!$admin) {
        $conditions = ['is_active = 1'];
        if ($search) { $conditions[] = '(name LIKE ? OR description LIKE ?)'; $like = "%$search%"; $params[] = $like; $params[] = $like; }
        if ($cat)    { $conditions[] = 'category = ?'; $params[] = $cat; }
        $where = 'WHERE ' . implode(' AND ', $conditions);
    } else {
        $conditions = [];
        if ($search) { $conditions[] = '(name LIKE ? OR description LIKE ?)'; $like = "%$search%"; $params[] = $like; $params[] = $like; }
        if ($cat)    { $conditions[] = 'category = ?'; $params[] = $cat; }
        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
    }

    $stmt = $pdo->prepare("SELECT * FROM products $where ORDER BY sort_order ASC, id ASC");
    $stmt->execute($params);
    json_ok($stmt->fetchAll());
}

// ── Get single product ────────────────────────────────────────
function get_product(): void {
    $id   = (int)($_GET['id'] ?? 0);
    $slug = trim($_GET['slug'] ?? '');
    if ($id < 1 && $slug === '') json_err('ID or slug required.', 400);

    $pdo = db();
    if ($id > 0) {
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$id]);
    } else {
        $stmt = $pdo->prepare('SELECT * FROM products WHERE slug = ?');
        $stmt->execute([$slug]);
    }
    $row = $stmt->fetch();
    if (!$row) json_err('Product not found.', 404);
    json_ok($row);
}

// ── Admin: Create product ─────────────────────────────────────
function create_product(): void {
    require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_err('Method not allowed.', 405);

    $body = get_body();
    [$name, $errors] = validate_product($body);
    if ($errors) json_err(implode(' ', $errors), 422);

    $pdo  = db();
    $slug = make_unique_slug($body['name'], $pdo);

    $stmt = $pdo->prepare(
        'INSERT INTO products
         (name, slug, category, description, features, applications, image_url, certifications, is_featured, is_active, sort_order)
         VALUES (?,?,?,?,?,?,?,?,?,?,?)'
    );
    $stmt->execute([
        sanitize($body['name']),
        $slug,
        sanitize($body['category'] ?? ''),
        sanitize($body['description'] ?? ''),
        sanitize($body['features'] ?? ''),
        sanitize($body['applications'] ?? ''),
        sanitize($body['image_url'] ?? ''),
        sanitize($body['certifications'] ?? ''),
        (int)(bool)($body['is_featured'] ?? false),
        (int)(bool)($body['is_active'] ?? true),
        (int)($body['sort_order'] ?? 0),
    ]);

    $id = $pdo->lastInsertId();
    log_activity('product_create', "Product #$id '{$body['name']}' created.");
    json_ok(['id' => $id, 'slug' => $slug], 'Product created.', 201);
}

// ── Admin: Update product ─────────────────────────────────────
function update_product(): void {
    require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_err('Method not allowed.', 405);

    $body = get_body();
    $id   = (int)($body['id'] ?? 0);
    if ($id < 1) json_err('Invalid ID.');
    [, $errors] = validate_product($body);
    if ($errors) json_err(implode(' ', $errors), 422);

    $pdo  = db();
    $stmt = $pdo->prepare(
        'UPDATE products SET
           name=?, category=?, description=?, features=?, applications=?,
           image_url=?, certifications=?, is_featured=?, is_active=?, sort_order=?
         WHERE id=?'
    );
    $stmt->execute([
        sanitize($body['name']),
        sanitize($body['category'] ?? ''),
        sanitize($body['description'] ?? ''),
        sanitize($body['features'] ?? ''),
        sanitize($body['applications'] ?? ''),
        sanitize($body['image_url'] ?? ''),
        sanitize($body['certifications'] ?? ''),
        (int)(bool)($body['is_featured'] ?? false),
        (int)(bool)($body['is_active'] ?? true),
        (int)($body['sort_order'] ?? 0),
        $id,
    ]);

    log_activity('product_update', "Product #$id updated.");
    json_ok(null, 'Product updated.');
}

// ── Admin: Delete product ─────────────────────────────────────
function delete_product(): void {
    require_login();
    $id = (int)($_GET['id'] ?? (get_body()['id'] ?? 0));
    if ($id < 1) json_err('Invalid ID.');

    $pdo  = db();
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$id]);
    if ($stmt->rowCount() === 0) json_err('Product not found.', 404);

    log_activity('product_delete', "Product #$id deleted.");
    json_ok(null, 'Product deleted.');
}

// ── Admin: Reorder products ───────────────────────────────────
function reorder_products(): void {
    require_login();
    $body  = get_body();
    $order = $body['order'] ?? []; // [{id:1, sort_order:0}, ...]
    if (!is_array($order)) json_err('Invalid payload.');

    $pdo  = db();
    $stmt = $pdo->prepare('UPDATE products SET sort_order = ? WHERE id = ?');
    foreach ($order as $item) {
        $stmt->execute([(int)($item['sort_order'] ?? 0), (int)($item['id'] ?? 0)]);
    }
    json_ok(null, 'Order saved.');
}

// ── Helpers ───────────────────────────────────────────────────
function validate_product(array $body): array {
    $errors = [];
    $name   = trim($body['name'] ?? '');
    if ($name === '') $errors[] = 'Product name is required.';
    if (strlen($name) > 200) $errors[] = 'Name must be ≤ 200 characters.';
    return [$name, $errors];
}

function make_unique_slug(string $name, PDO $pdo): string {
    $base = slugify($name);
    $slug = $base;
    $i    = 1;
    while (true) {
        $s = $pdo->prepare('SELECT id FROM products WHERE slug = ?');
        $s->execute([$slug]);
        if (!$s->fetch()) break;
        $slug = $base . '-' . $i++;
    }
    return $slug;
}
