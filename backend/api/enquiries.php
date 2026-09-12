<?php
// ============================================================
// API: Contact Enquiries — /backend/api/enquiries.php
// POST   ?action=submit          (public — no auth required)
// GET    ?action=list            (admin)
// GET    ?action=get&id=X        (admin)
// POST   ?action=update_status   (admin)
// DELETE ?action=delete&id=X     (admin)
// GET    ?action=stats           (admin)
// ============================================================
require_once __DIR__ . '/../config/helpers.php';
set_cors_headers();
start_session();

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

match ($action) {
    'submit'        => submit_enquiry(),
    'list'          => list_enquiries(),
    'get'           => get_enquiry(),
    'update_status' => update_status(),
    'delete'        => delete_enquiry(),
    'stats'         => enquiry_stats(),
    default         => json_err('Unknown action.', 404),
};

// ── Public: Submit enquiry ────────────────────────────────────
function submit_enquiry(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_err('Method not allowed.', 405);
    }

    $body    = get_body();
    $name    = sanitize($body['name']    ?? '');
    $company = sanitize($body['company'] ?? '');
    $email   = filter_var(trim($body['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $phone   = sanitize($body['phone']   ?? '');
    $message = sanitize($body['message'] ?? '');

    $errors = [];
    if ($name === '')    $errors[] = 'Name is required.';
    if (!$email)         $errors[] = 'A valid email address is required.';
    if ($message === '') $errors[] = 'Message is required.';
    if (strlen($message) < 10) $errors[] = 'Message must be at least 10 characters.';

    if ($errors) json_err(implode(' ', $errors), 422);

    // Basic rate-limit: 3 enquiries per IP per hour
    $pdo = db();
    $rate = $pdo->prepare(
        'SELECT COUNT(*) FROM contact_enquiries
         WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)'
    );
    $rate->execute([$_SERVER['REMOTE_ADDR'] ?? '']);
    if ((int)$rate->fetchColumn() >= 3) {
        json_err('Too many submissions. Please try again later.', 429);
    }

    $stmt = $pdo->prepare(
        'INSERT INTO contact_enquiries (name, company, email, phone, message, ip_address, user_agent)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $name, $company, $email, $phone, $message,
        $_SERVER['REMOTE_ADDR']    ?? '',
        substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
    ]);

    json_ok(['id' => $pdo->lastInsertId()], 'Enquiry submitted successfully. We\'ll be in touch soon!', 201);
}

// ── Admin: List enquiries ─────────────────────────────────────
function list_enquiries(): void {
    require_login();

    $page     = max(1, (int)($_GET['page'] ?? 1));
    $per_page = min(100, max(10, (int)($_GET['per_page'] ?? 20)));
    $status   = $_GET['status'] ?? '';
    $search   = trim($_GET['search'] ?? '');
    $offset   = ($page - 1) * $per_page;

    $pdo    = db();
    $where  = [];
    $params = [];

    if ($status !== '') {
        $where[]  = 'status = ?';
        $params[] = $status;
    }
    if ($search !== '') {
        $where[]  = '(name LIKE ? OR email LIKE ? OR company LIKE ? OR message LIKE ?)';
        $like     = "%$search%";
        array_push($params, $like, $like, $like, $like);
    }

    $sql_where = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $total_stmt = $pdo->prepare("SELECT COUNT(*) FROM contact_enquiries $sql_where");
    $total_stmt->execute($params);
    $total = (int)$total_stmt->fetchColumn();

    $list_stmt = $pdo->prepare(
        "SELECT id, name, company, email, phone, message, status, created_at
         FROM contact_enquiries $sql_where
         ORDER BY created_at DESC
         LIMIT $per_page OFFSET $offset"
    );
    $list_stmt->execute($params);
    $rows = $list_stmt->fetchAll();

    json_ok([
        'items'      => $rows,
        'pagination' => paginate($total, $page, $per_page),
    ]);
}

// ── Admin: Get single enquiry ─────────────────────────────────
function get_enquiry(): void {
    require_login();

    $id = (int)($_GET['id'] ?? 0);
    if ($id < 1) json_err('Invalid ID.', 400);

    $pdo  = db();
    $stmt = $pdo->prepare('SELECT * FROM contact_enquiries WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if (!$row) json_err('Enquiry not found.', 404);

    // Auto-mark as read when viewed
    if ($row['status'] === 'new') {
        $pdo->prepare('UPDATE contact_enquiries SET status = "read" WHERE id = ?')
            ->execute([$id]);
        $row['status'] = 'read';
    }

    json_ok($row);
}

// ── Admin: Update status ──────────────────────────────────────
function update_status(): void {
    require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_err('Method not allowed.', 405);

    $body   = get_body();
    $id     = (int)($body['id'] ?? 0);
    $status = $body['status'] ?? '';

    if ($id < 1) json_err('Invalid ID.');
    $allowed = ['new', 'read', 'replied', 'archived'];
    if (!in_array($status, $allowed, true)) json_err('Invalid status value.');

    $pdo  = db();
    $stmt = $pdo->prepare('UPDATE contact_enquiries SET status = ? WHERE id = ?');
    $stmt->execute([$status, $id]);

    if ($stmt->rowCount() === 0) json_err('Enquiry not found.', 404);

    log_activity('enquiry_status', "Enquiry #$id status → $status");
    json_ok(null, 'Status updated.');
}

// ── Admin: Delete enquiry ─────────────────────────────────────
function delete_enquiry(): void {
    require_login();

    $id = (int)($_GET['id'] ?? (get_body()['id'] ?? 0));
    if ($id < 1) json_err('Invalid ID.');

    $pdo  = db();
    $stmt = $pdo->prepare('DELETE FROM contact_enquiries WHERE id = ?');
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) json_err('Enquiry not found.', 404);

    log_activity('enquiry_delete', "Enquiry #$id deleted.");
    json_ok(null, 'Enquiry deleted.');
}

// ── Admin: Stats ──────────────────────────────────────────────
function enquiry_stats(): void {
    require_login();

    $pdo  = db();
    $rows = $pdo->query(
        'SELECT status, COUNT(*) AS cnt FROM contact_enquiries GROUP BY status'
    )->fetchAll();

    $stats = ['new' => 0, 'read' => 0, 'replied' => 0, 'archived' => 0, 'total' => 0];
    foreach ($rows as $r) {
        $stats[$r['status']] = (int)$r['cnt'];
        $stats['total'] += (int)$r['cnt'];
    }

    // Last 7 days trend
    $trend = $pdo->query(
        'SELECT DATE(created_at) AS day, COUNT(*) AS cnt
         FROM contact_enquiries
         WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
         GROUP BY DATE(created_at) ORDER BY day ASC'
    )->fetchAll();

    json_ok(['counts' => $stats, 'trend' => $trend]);
}
