<?php
require_once __DIR__ . '/includes/auth_check.php';

// ── Fetch dashboard data ───────────────────────────────────
$pdo = db();

// Enquiry stats
$enq_stats = [
    'total'    => 0,
    'new'      => 0,
    'replied'  => 0,
    'archived' => 0,
];
$rows = $pdo->query(
    "SELECT status, COUNT(*) AS cnt FROM contact_enquiries GROUP BY status"
)->fetchAll();
foreach ($rows as $r) {
    $enq_stats[$r['status']] = (int)$r['cnt'];
    $enq_stats['total'] += (int)$r['cnt'];
}

// Products count
$products_total  = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$products_active = (int)$pdo->query('SELECT COUNT(*) FROM products WHERE is_active=1')->fetchColumn();

// Recent enquiries (last 5)
$recent_enquiries = $pdo->query(
    "SELECT id, name, email, company, message, status, created_at
     FROM contact_enquiries ORDER BY created_at DESC LIMIT 5"
)->fetchAll();

// Last 7 days chart
$chart_raw = $pdo->query(
    "SELECT DATE(created_at) AS day, COUNT(*) AS cnt
     FROM contact_enquiries
     WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
     GROUP BY DATE(created_at)"
)->fetchAll(PDO::FETCH_KEY_PAIR);

$chart_labels = [];
$chart_values = [];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $chart_labels[] = date('D', strtotime($d));
    $chart_values[] = (int)($chart_raw[$d] ?? 0);
}
$chart_max = max(max($chart_values), 1);

// Recent activity log
$activity = $pdo->query(
    "SELECT l.action, l.description, l.created_at, u.full_name, u.username
     FROM admin_activity_log l LEFT JOIN admin_users u ON u.id = l.admin_id
     ORDER BY l.created_at DESC LIMIT 8"
)->fetchAll();

$page_title  = 'Dashboard';
$breadcrumbs = [];
include __DIR__ . '/includes/header.php';
?>

<!-- Page header -->
<div class="page-header">
  <div>
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Welcome back, <?= htmlspecialchars($current_admin['full_name'] ?: $current_admin['username']) ?>. Here's what's happening.</p>
  </div>
  <div style="display:flex;gap:10px;flex-wrap:wrap">
    <a href="enquiries.php" class="btn btn-primary">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
      View Enquiries
    </a>
  </div>
</div>

<!-- Stat Cards -->
<div class="stats-grid">
  <div class="stat-card green">
    <div class="stat-top">
      <div class="stat-icon">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
      </div>
      <span class="stat-trend up">Total</span>
    </div>
    <div class="stat-value"><?= $enq_stats['total'] ?></div>
    <div class="stat-label">All Enquiries</div>
  </div>

  <div class="stat-card amber">
    <div class="stat-top">
      <div class="stat-icon">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
      </div>
      <span class="stat-trend flat">Unread</span>
    </div>
    <div class="stat-value"><?= $enq_stats['new'] ?></div>
    <div class="stat-label">New Enquiries</div>
  </div>

  <div class="stat-card blue">
    <div class="stat-top">
      <div class="stat-icon">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
      </div>
      <span class="stat-trend flat">—</span>
    </div>
    <div class="stat-value"><?= $products_active ?></div>
    <div class="stat-label">Active Products</div>
  </div>

  <div class="stat-card purple">
    <div class="stat-top">
      <div class="stat-icon">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <span class="stat-trend up">Done</span>
    </div>
    <div class="stat-value"><?= $enq_stats['replied'] ?></div>
    <div class="stat-label">Replied</div>
  </div>
</div>

<!-- Chart + Activity -->
<div style="display:grid;grid-template-columns:1fr 360px;gap:20px;margin-bottom:24px" class="chart-row">

  <!-- Enquiries chart -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
        Enquiries — Last 7 Days
      </div>
    </div>
    <div class="card-body">
      <div class="chart-bar-wrap">
        <?php foreach ($chart_values as $val): ?>
          <div>
            <div class="chart-bar" style="height:<?= ($val / $chart_max * 100) ?>%;" title="<?= $val ?> enquiries"></div>
          </div>
        <?php endforeach; ?>
      </div>
      <div style="display:flex;gap:8px">
        <?php foreach ($chart_labels as $label): ?>
          <div class="chart-bar-label" style="flex:1"><?= $label ?></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Activity log -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Recent Activity
      </div>
    </div>
    <div class="card-body" style="padding-top:8px;padding-bottom:8px">
      <?php if (empty($activity)): ?>
        <div class="empty-state" style="padding:24px 0">
          <p>No activity yet.</p>
        </div>
      <?php else: ?>
        <?php foreach ($activity as $act): ?>
          <div class="activity-item">
            <div class="activity-dot"></div>
            <div>
              <div class="activity-text">
                <strong><?= htmlspecialchars($act['full_name'] ?: $act['username'] ?: 'System') ?></strong>
                — <?= htmlspecialchars(ucwords(str_replace('_',' ',$act['action']))) ?>
              </div>
              <div class="activity-time"><?= date('d M, H:i', strtotime($act['created_at'])) ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

</div>

<!-- Recent Enquiries table -->
<div class="card">
  <div class="card-header">
    <div class="card-title">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
      Recent Enquiries
    </div>
    <a href="enquiries.php" class="btn btn-secondary btn-sm">View All</a>
  </div>
  <div class="table-wrap">
    <?php if (empty($recent_enquiries)): ?>
      <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75"/></svg>
        <h3>No enquiries yet</h3>
        <p>When customers submit the contact form, they'll appear here.</p>
      </div>
    <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Message</th>
          <th>Status</th>
          <th>Date</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recent_enquiries as $enq): ?>
        <tr>
          <td class="text-muted text-sm">#<?= $enq['id'] ?></td>
          <td class="td-name"><?= htmlspecialchars($enq['name']) ?>
            <?php if ($enq['company']): ?><br><span class="text-muted text-sm"><?= htmlspecialchars($enq['company']) ?></span><?php endif; ?>
          </td>
          <td class="td-email"><?= htmlspecialchars($enq['email']) ?></td>
          <td style="max-width:280px">
            <span style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;font-size:.82rem">
              <?= htmlspecialchars($enq['message']) ?>
            </span>
          </td>
          <td>
            <?php
            $sc = ['new'=>'badge-new','read'=>'badge-read','replied'=>'badge-replied','archived'=>'badge-archived'];
            $cls = $sc[$enq['status']] ?? 'badge-read';
            ?>
            <span class="badge <?= $cls ?>"><?= $enq['status'] ?></span>
          </td>
          <td class="td-date"><?= date('d M Y', strtotime($enq['created_at'])) ?></td>
          <td>
            <a href="enquiries.php?view=<?= $enq['id'] ?>" class="btn btn-ghost btn-sm btn-icon" title="View">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>

<style>
@media(max-width:900px){.chart-row{grid-template-columns:1fr!important}}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
