<?php
require_once __DIR__ . '/includes/auth_check.php';

$pdo   = db();
$flash = '';

// ── Handle save ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('UPDATE site_settings SET setting_val=? WHERE setting_key=?');
    foreach ($_POST as $key => $val) {
        if (strpos($key, 'setting_') !== 0) continue;
        $real_key = substr($key, 8); // strip "setting_" prefix
        $stmt->execute([sanitize((string)$val), $real_key]);
    }
    log_activity('settings_update', 'Site settings updated.');
    $flash = 'Settings saved successfully.';
}

// ── Fetch all settings grouped ────────────────────────────────
$rows = $pdo->query(
    'SELECT setting_key, setting_val, label, group_name, sort_order
     FROM site_settings ORDER BY group_name, sort_order'
)->fetchAll();

$groups = [];
foreach ($rows as $r) {
    $groups[$r['group_name']][] = $r;
}

$group_labels = [
    'general'       => ['General', 'company name and tagline'],
    'contact'       => ['Contact Info', 'email, phone, address'],
    'social'        => ['Social Media', 'social profile links'],
    'seo'           => ['SEO & Analytics', 'meta tags and tracking'],
    'notifications' => ['Notifications', 'site enquiry alert email'],
];

$group_icons = [
    'general'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>',
    'contact'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>',
    'social'        => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>',
    'seo'           => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>',
    'notifications' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>',
];

// Textarea keys
$textarea_keys = ['contact_address', 'meta_description'];

$page_title  = 'Settings';
$breadcrumbs = [['Settings', '']];
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Site Settings</h1>
    <p class="page-subtitle">Manage your site content, contact details, and SEO information.</p>
  </div>
</div>

<?php if ($flash): ?>
<div class="alert alert-success">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  <?= htmlspecialchars($flash) ?>
</div>
<?php endif; ?>

<form method="POST" action="settings.php">
  <?php foreach ($groups as $group_key => $settings):
    [$gl, $gsub] = $group_labels[$group_key] ?? [ucfirst($group_key), ''];
    $icon = $group_icons[$group_key] ?? '';
  ?>
  <div class="card settings-section" style="margin-bottom:20px">
    <div class="card-header">
      <div class="card-title">
        <?= $icon ?>
        <?= $gl ?>
        <span class="text-muted text-sm" style="font-weight:400;margin-left:4px">— <?= $gsub ?></span>
      </div>
    </div>
    <div class="card-body">
      <div class="grid-2">
        <?php foreach ($settings as $s):
          $field_key = 'setting_' . $s['setting_key'];
          $is_textarea = in_array($s['setting_key'], $textarea_keys, true);
        ?>
        <div class="form-group <?= $is_textarea ? 'grid-span-2' : '' ?>" <?= $is_textarea ? 'style="grid-column:1/-1"' : '' ?>>
          <label class="form-label" for="<?= $field_key ?>"><?= htmlspecialchars($s['label']) ?></label>
          <?php if ($is_textarea): ?>
            <textarea id="<?= $field_key ?>" name="<?= $field_key ?>" class="form-control" rows="3"><?= htmlspecialchars($s['setting_val'] ?? '') ?></textarea>
          <?php elseif (str_contains($s['setting_key'], '_url') || str_contains($s['setting_key'], 'email')): ?>
            <input type="text" id="<?= $field_key ?>" name="<?= $field_key ?>" class="form-control"
                   value="<?= htmlspecialchars($s['setting_val'] ?? '') ?>"
                   placeholder="<?= str_contains($s['setting_key'],'email') ? 'email@example.com' : 'https://' ?>">
          <?php else: ?>
            <input type="text" id="<?= $field_key ?>" name="<?= $field_key ?>" class="form-control"
                   value="<?= htmlspecialchars($s['setting_val'] ?? '') ?>">
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>

  <div style="display:flex;gap:10px;margin-top:4px;margin-bottom:32px">
    <button type="submit" class="btn btn-primary">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      Save All Settings
    </button>
    <button type="reset" class="btn btn-secondary">Reset Changes</button>
  </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
