<?php
// ============================================================
// API: Categories — /backend/api/categories.php
// GET  ?action=list              (public)
// POST ?action=create            (admin)
// POST ?action=update            (admin)
// POST ?action=delete            (admin)
// POST ?action=reorder           (admin)
// ============================================================
require_once __DIR__ . '/../config/helpers.php';
set_cors_headers();
start_session();

$action = $_GET['action'] ?? 'list';

match ($action) {
    'list'    => list_categories(),
    'create'  => create_category(),
    'update'  => update_category(),
    'delete'  => delete_category(),
    'reorder' => reorder_categories(),
    default   => json_err('Unknown action.', 404),
};

function list_categories(): void {
    $pdo  = db();
    $admin = is_logged_in();
    $where = $admin ? '' : 'WHERE is_active = 1';
    $rows  = $pdo->query(
        "SELECT c.*, COUNT(p.id) AS product_count
         FROM categories c
         LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1
         $where
         GROUP BY c.id
         ORDER BY c.sort_order ASC, c.name ASC"
    )->fetchAll();
    json_ok($rows);
}

function create_category(): void {
    require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_err('Method not allowed.', 405);

    $body = get_body();
    $name = sanitize(trim($body['name'] ?? ''));
    if ($name === '') json_err('Category name is required.');

    $pdo  = db();
    $slug = make_cat_slug($name, $pdo);

    try {
        $pdo->prepare(
            'INSERT INTO categories (name, slug, description, sort_order)
             VALUES (?,?,?,?)'
        )->execute([
            $name,
            $slug,
            sanitize($body['description'] ?? ''),
            (int)($body['sort_order'] ?? 0),
        ]);
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'Duplicate')) json_err('Category name already exists.', 409);
        throw $e;
    }

    log_activity('category_create', "Category '$name' created.");
    json_ok(['id' => $pdo->lastInsertId(), 'slug' => $slug], 'Category created.', 201);
}

function update_category(): void {
    require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_err('Method not allowed.', 405);

    $body = get_body();
    $id   = (int)($body['id'] ?? 0);
    $name = sanitize(trim($body['name'] ?? ''));
    if ($id < 1)   json_err('Invalid ID.');
    if ($name === '') json_err('Category name is required.');

    $pdo = db();
    $pdo->prepare(
        'UPDATE categories SET name=?, description=?, sort_order=?, is_active=? WHERE id=?'
    )->execute([
        $name,
        sanitize($body['description'] ?? ''),
        (int)($body['sort_order'] ?? 0),
        (int)(bool)($body['is_active'] ?? true),
        $id,
    ]);

    // Sync category name string on products table
    $pdo->prepare('UPDATE products SET category=? WHERE category_id=?')->execute([$name, $id]);

    log_activity('category_update', "Category #$id '$name' updated.");
    json_ok(null, 'Category updated.');
}

function delete_category(): void {
    require_login();
    $body = get_body();
    $id   = (int)($body['id'] ?? ($_GET['id'] ?? 0));
    if ($id < 1) json_err('Invalid ID.');

    $pdo = db();
    // Un-assign products
    $pdo->prepare('UPDATE products SET category_id=NULL, category="" WHERE category_id=?')->execute([$id]);
    $pdo->prepare('DELETE FROM categories WHERE id=?')->execute([$id]);

    log_activity('category_delete', "Category #$id deleted.");
    json_ok(null, 'Category deleted.');
}

function reorder_categories(): void {
    require_login();
    $body  = get_body();
    $order = $body['order'] ?? [];
    if (!is_array($order)) json_err('Invalid payload.');

    $pdo  = db();
    $stmt = $pdo->prepare('UPDATE categories SET sort_order=? WHERE id=?');
    foreach ($order as $item) {
        $stmt->execute([(int)($item['sort_order'] ?? 0), (int)($item['id'] ?? 0)]);
    }
    json_ok(null, 'Order saved.');
}

function make_cat_slug(string $name, PDO $pdo): string {
    $base = slugify($name);
    $slug = $base;
    $i    = 1;
    while (true) {
        $s = $pdo->prepare('SELECT id FROM categories WHERE slug=?');
        $s->execute([$slug]);
        if (!$s->fetch()) break;
        $slug = $base . '-' . $i++;
    }
    return $slug;
}
