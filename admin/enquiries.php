<?php
require_once __DIR__ . '/includes/auth_check.php';

$pdo = db();

// ── Handle status update (POST) ──────────────────────────────
$flash = '';
$flash_type = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);

    if ($action === 'update_status' && $id > 0) {
        $status   = $_POST['status'] ?? '';
        $allowed  = ['new','read','replied','archived'];
        if (in_array($status, $allowed, true)) {
            $pdo->prepare('UPDATE contact_enquiries SET status=? WHERE id=?')->execute([$status, $id]);
            log_activity('enquiry_status', "Enquiry #$id status → $status");
            $flash = 'Status updated successfully.';
        }
    } elseif ($action === 'delete' && $id > 0) {
        $pdo->prepare('DELETE FROM contact_enquiries WHERE id=?')->execute([$id]);
        log_activity('enquiry_delete', "Enquiry #$id deleted.");
        $flash = 'Enquiry deleted.';
        header('Location: enquiries.php');
        exit;
    }
}

// ── Fetch / filter ────────────────────────────────────────────
$page     = max(1, (int)($_GET['page'] ?? 1));
$per_page = 15;
$offset   = ($page - 1) * $per_page;
$status_f = $_GET['status'] ?? '';
$search   = trim($_GET['search'] ?? '');

// View single enquiry
$view_id  = (int)($_GET['view'] ?? 0);
$view_enq = null;
if ($view_id > 0) {
    $s = $pdo->prepare('SELECT * FROM contact_enquiries WHERE id=?');
    $s->execute([$view_id]);
    $view_enq = $s->fetch();
    // Auto-mark as read
    if ($view_enq && $view_enq['status'] === 'new') {
        $pdo->prepare('UPDATE contact_enquiries SET status="read" WHERE id=?')->execute([$view_id]);
        $view_enq['status'] = 'read';
    }
}

// Build WHERE
$where  = [];
$params = [];
if ($status_f !== '') { $where[] = 'status=?';       $params[] = $status_f; }
if ($search !== '')   {
    $where[] = '(name LIKE ? OR email LIKE ? OR company LIKE ? OR message LIKE ?)';
    $like = "%$search%";
    array_push($params, $like, $like, $like, $like);
}
$sql_where = $where ? 'WHERE '.implode(' AND ',$where) : '';

$total = (int)$pdo->prepare("SELECT COUNT(*) FROM contact_enquiries $sql_where")->execute($params) ?
         $pdo->prepare("SELECT COUNT(*) FROM contact_enquiries $sql_where")->execute($params) : 0;

$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM contact_enquiries $sql_where");
$count_stmt->execute($params);
$total = (int)$count_stmt->fetchColumn();
$total_pages = (int)ceil($total / $per_page);

$list_stmt = $pdo->prepare("SELECT * FROM contact_enquiries $sql_where ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");
$list_stmt->execute($params);
$enquiries = $list_stmt->fetchAll();

// Badge counts for tabs
$counts = ['all'=>0,'new'=>0,'read'=>0,'replied'=>0,'archived'=>0];
foreach ($pdo->query("SELECT status, COUNT(*) AS c FROM contact_enquiries GROUP BY status")->fetchAll() as $r) {
    $counts[$r['status']] = (int)$r['c'];
    $counts['all'] += (int)$r['c'];
}

$page_title  = 'Enquiries';
$breadcrumbs = [['Enquiries', '']];
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Enquiries</h1>
    <p class="page-subtitle"><?= $counts['all'] ?> total &mdash; <?= $counts['new'] ?> unread</p>
  </div>
</div>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash_type ?>" id="flash-msg">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  <?= htmlspecialchars($flash) ?>
</div>
<?php endif; ?>

<!-- View detail modal -->
<?php if ($view_enq): ?>
<div class="modal-overlay open" id="viewModal">
  <div class="modal" style="max-width:640px">
    <div class="modal-header">
      <div class="modal-title">Enquiry #<?= $view_enq['id'] ?></div>
      <button class="modal-close" onclick="window.location='enquiries.php<?= $search||$status_f ? '?'.http_build_query(array_filter(['search'=>$search,'status'=>$status_f])) : '' ?>'">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <div class="enquiry-meta">
        <div class="meta-item">
          <div class="meta-label">Name</div>
          <div class="meta-value"><?= htmlspecialchars($view_enq['name']) ?></div>
        </div>
        <div class="meta-item">
          <div class="meta-label">Company</div>
          <div class="meta-value"><?= htmlspecialchars($view_enq['company'] ?: '—') ?></div>
        </div>
        <div class="meta-item">
          <div class="meta-label">Email</div>
          <div class="meta-value"><a href="mailto:<?= htmlspecialchars($view_enq['email']) ?>" style="color:var(--green-600)"><?= htmlspecialchars($view_enq['email']) ?></a></div>
        </div>
        <div class="meta-item">
          <div class="meta-label">Phone</div>
          <div class="meta-value"><?= htmlspecialchars($view_enq['phone'] ?: '—') ?></div>
        </div>
        <div class="meta-item">
          <div class="meta-label">Received</div>
          <div class="meta-value"><?= date('d M Y, H:i', strtotime($view_enq['created_at'])) ?></div>
        </div>
        <div class="meta-item">
          <div class="meta-label">Status</div>
          <div class="meta-value">
            <?php $sc=['new'=>'badge-new','read'=>'badge-read','replied'=>'badge-replied','archived'=>'badge-archived']; ?>
            <span class="badge <?= $sc[$view_enq['status']] ?? 'badge-read' ?>"><?= $view_enq['status'] ?></span>
          </div>
        </div>
      </div>
      <div class="form-label" style="margin-bottom:8px">Message</div>
      <div class="enquiry-msg"><?= htmlspecialchars($view_enq['message']) ?></div>

      <!-- Quick status update -->
      <form method="POST" action="enquiries.php" style="margin-top:20px;display:flex;gap:10px;align-items:center;flex-wrap:wrap">
        <input type="hidden" name="action" value="update_status">
        <input type="hidden" name="id" value="<?= $view_enq['id'] ?>">
        <select name="status" class="filter-select">
          <?php foreach(['new','read','replied','archived'] as $s): ?>
            <option value="<?= $s ?>" <?= $view_enq['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
        <a href="mailto:<?= htmlspecialchars($view_enq['email']) ?>" class="btn btn-secondary btn-sm">Reply via Email</a>
      </form>
    </div>
    <div class="modal-footer">
      <form method="POST" action="enquiries.php" onsubmit="return confirm('Delete this enquiry permanently?')">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?= $view_enq['id'] ?>">
        <button type="submit" class="btn btn-danger btn-sm">Delete Enquiry</button>
      </form>
      <a href="enquiries.php<?= $search||$status_f ? '?'.http_build_query(array_filter(['search'=>$search,'status'=>$status_f])) : '' ?>" class="btn btn-secondary btn-sm">Close</a>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Filter tabs -->
<div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:18px">
  <?php
  $tabs = [''=>'All','new'=>'New','read'=>'Read','replied'=>'Replied','archived'=>'Archived'];
  foreach ($tabs as $val => $label):
    $active = ($status_f === $val);
    $cnt    = $val === '' ? $counts['all'] : ($counts[$val] ?? 0);
    $href   = 'enquiries.php?status='.$val.($search?'&search='.urlencode($search):'');
  ?>
  <a href="<?= $href ?>" class="btn btn-sm <?= $active ? 'btn-primary' : 'btn-secondary' ?>">
    <?= $label ?> <span style="opacity:.7;font-weight:400">(<?= $cnt ?>)</span>
  </a>
  <?php endforeach; ?>
</div>

<!-- Toolbar -->
<div class="toolbar">
  <form method="GET" action="enquiries.php" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    <?php if ($status_f): ?><input type="hidden" name="status" value="<?= htmlspecialchars($status_f) ?>"><?php endif; ?>
    <div class="search-wrap">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
      <input type="search" name="search" placeholder="Search name, email, message…" value="<?= htmlspecialchars($search) ?>">
    </div>
    <button type="submit" class="btn btn-secondary btn-sm">Search</button>
    <?php if ($search || $status_f): ?>
      <a href="enquiries.php" class="btn btn-ghost btn-sm">Clear</a>
    <?php endif; ?>
  </form>
</div>

<!-- Table -->
<div class="card">
  <div class="table-wrap">
    <?php if (empty($enquiries)): ?>
      <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75"/></svg>
        <h3>No enquiries found</h3>
        <p>Try adjusting your filters or search query.</p>
      </div>
    <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Name / Company</th>
          <th>Email</th>
          <th>Message Preview</th>
          <th>Status</th>
          <th>Date</th>
          <th style="width:80px">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($enquiries as $enq):
          $sc = ['new'=>'badge-new','read'=>'badge-read','replied'=>'badge-replied','archived'=>'badge-archived'];
          $cls = $sc[$enq['status']] ?? 'badge-read';
          $row_style = $enq['status'] === 'new' ? 'font-weight:600;' : '';
        ?>
        <tr style="<?= $row_style ?>">
          <td class="text-muted text-sm"><?= $enq['id'] ?></td>
          <td class="td-name">
            <?= htmlspecialchars($enq['name']) ?>
            <?php if ($enq['company']): ?><br><span class="text-muted text-sm"><?= htmlspecialchars($enq['company']) ?></span><?php endif; ?>
          </td>
          <td class="td-email"><?= htmlspecialchars($enq['email']) ?></td>
          <td>
            <span style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;font-size:.81rem;max-width:320px">
              <?= htmlspecialchars($enq['message']) ?>
            </span>
          </td>
          <td><span class="badge <?= $cls ?>"><?= $enq['status'] ?></span></td>
          <td class="td-date"><?= date('d M Y', strtotime($enq['created_at'])) ?><br>
            <span style="font-size:.72rem;color:var(--muted)"><?= date('H:i', strtotime($enq['created_at'])) ?></span>
          </td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="enquiries.php?view=<?= $enq['id'] ?><?= $status_f?'&status='.$status_f:'' ?><?= $search?'&search='.urlencode($search):'' ?>"
                 class="btn btn-ghost btn-icon btn-sm" title="View">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              </a>
              <form method="POST" action="enquiries.php" onsubmit="return confirm('Delete this enquiry?')" style="display:inline">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $enq['id'] ?>">
                <button type="submit" class="btn btn-ghost btn-icon btn-sm" title="Delete" style="color:#dc2626">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                </button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>

  <!-- Pagination -->
  <?php if ($total_pages > 1): ?>
  <div class="pagination" style="padding:16px 24px">
    <?php
    $base = 'enquiries.php?'.http_build_query(array_filter(['status'=>$status_f,'search'=>$search]));
    if ($page > 1): ?><a href="<?= $base ?>&page=<?= $page-1 ?>" class="page-btn">&larr;</a><?php endif; ?>
    <?php for($i=1;$i<=$total_pages;$i++): ?>
      <a href="<?= $base ?>&page=<?= $i ?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
    <?php endfor; ?>
    <?php if ($page < $total_pages): ?><a href="<?= $base ?>&page=<?= $page+1 ?>" class="page-btn">&rarr;</a><?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
