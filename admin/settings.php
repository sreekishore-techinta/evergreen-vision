<?php
require_once __DIR__ . '/includes/auth_check.php';

$pdo   = db();
$flash = '';

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('UPDATE site_settings SET setting_val=? WHERE setting_key=?');
    foreach ($_POST as $key => $val) {
        if (strpos($key, 'setting_') !== 0) continue;
        $real_key = substr($key, 8);
        $stmt->execute([sanitize((string)$val), $real_key]);
    }
    log_activity('settings_update', 'Site settings updated.');
    $flash = 'Settings saved successfully.';
}

// Fetch all settings grouped
$rows = $pdo->query('SELECT setting_key, setting_val, label, group_name, sort_order FROM site_settings ORDER BY group_name, sort_order')->fetchAll();
$groups = [];
foreach ($rows as $r) { $groups[$r['group_name']][] = $r; }

$group_labels = [
    'general'       => ['General', 'company name and tagline'],
    'contact'       => ['Contact Info', 'email and phone'],
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

$textarea_keys         = ['meta_description'];
$location_managed_keys = ['contact_address', 'contact_maps_url'];

// Fetch location settings
$loc_rows = $pdo->query("SELECT setting_key, setting_val FROM site_settings WHERE setting_key IN ('contact_address','contact_maps_url')")->fetchAll();
$loc_settings = [];
foreach ($loc_rows as $r) { $loc_settings[$r['setting_key']] = $r['setting_val']; }

// Auto-seed contact_maps_url if not present
if (!array_key_exists('contact_maps_url', $loc_settings)) {
    $pdo->exec("INSERT IGNORE INTO site_settings (setting_key, setting_val, label, group_name, sort_order) VALUES ('contact_maps_url','https://maps.google.com/maps?q=SIDCO+Industrial+Estate,+N.K.+Road,+Thanjavur+613006&t=&z=15&ie=UTF8&iwloc=&output=embed','Google Maps Embed URL','contact',5)");
    $loc_settings['contact_maps_url'] = 'https://maps.google.com/maps?q=SIDCO+Industrial+Estate,+N.K.+Road,+Thanjavur+613006&t=&z=15&ie=UTF8&iwloc=&output=embed';
    $rows = $pdo->query('SELECT setting_key, setting_val, label, group_name, sort_order FROM site_settings ORDER BY group_name, sort_order')->fetchAll();
    $groups = [];
    foreach ($rows as $r) { $groups[$r['group_name']][] = $r; }
}

$cur_address  = $loc_settings['contact_address']  ?? 'No. 2, Tholilpettai, SIDCO Industrial Estate, N.K. Road, Thanjavur (613006), Tamil Nadu';
$cur_maps_url = $loc_settings['contact_maps_url'] ?? '';

$page_title  = 'Settings';
$breadcrumbs = [['Settings', '']];
include __DIR__ . '/includes/header.php';
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Site Settings</h1>
    <p class="page-subtitle">Manage your site content, contact details, SEO, and location information.</p>
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
    if ($group_key === 'contact') {
        $settings = array_filter($settings, fn($s) => !in_array($s['setting_key'], $location_managed_keys, true));
    }
    if (empty($settings)) continue;
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
          $field_key   = 'setting_' . $s['setting_key'];
          $is_textarea = in_array($s['setting_key'], $textarea_keys, true);
        ?>
        <div class="form-group <?= $is_textarea ? 'grid-span-2' : '' ?>" <?= $is_textarea ? 'style="grid-column:1/-1"' : '' ?>>
          <label class="form-label" for="<?= $field_key ?>"><?= htmlspecialchars($s['label']) ?></label>
          <?php if ($is_textarea): ?>
            <textarea id="<?= $field_key ?>" name="<?= $field_key ?>" class="form-control" rows="3"><?= htmlspecialchars($s['setting_val'] ?? '') ?></textarea>
          <?php elseif (str_contains($s['setting_key'], '_url') || str_contains($s['setting_key'], 'email')): ?>
            <input type="text" id="<?= $field_key ?>" name="<?= $field_key ?>" class="form-control" value="<?= htmlspecialchars($s['setting_val'] ?? '') ?>" placeholder="<?= str_contains($s['setting_key'],'email') ? 'email@example.com' : 'https://' ?>">
          <?php else: ?>
            <input type="text" id="<?= $field_key ?>" name="<?= $field_key ?>" class="form-control" value="<?= htmlspecialchars($s['setting_val'] ?? '') ?>">
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>

  <!-- ===================== LOCATION & ADDRESS SECTION ===================== -->
  <div class="card settings-section" style="margin-bottom:20px;border:2px solid var(--green-200)">
    <div class="card-header" style="background:linear-gradient(to right,#f0fdf4,#f9fafb)">
      <div class="card-title" style="color:var(--green-800)">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="var(--green-700)" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
        Location &amp; Address
        <span class="text-muted text-sm" style="font-weight:400;margin-left:4px">— factory address and Google Maps embed</span>
      </div>
    </div>
    <div class="card-body">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

        <!-- Left: fields -->
        <div style="display:flex;flex-direction:column;gap:16px">

          <div class="form-group">
            <label class="form-label" for="setting_contact_address" style="font-weight:600;display:flex;align-items:center;gap:6px">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="var(--green-600)" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
              Factory &amp; Office Address
            </label>
            <textarea id="setting_contact_address" name="setting_contact_address" class="form-control" rows="4" style="resize:vertical"><?= htmlspecialchars($cur_address) ?></textarea>
            <div style="font-size:.75rem;color:var(--muted);margin-top:4px">Displayed on the Contact page address card and in the footer.</div>
          </div>

          <div class="form-group">
            <label class="form-label" for="setting_contact_maps_url" style="font-weight:600;display:flex;align-items:center;gap:6px">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="var(--green-600)" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z"/></svg>
              Google Maps Embed URL
            </label>
            <input type="text" id="setting_contact_maps_url" name="setting_contact_maps_url" class="form-control"
                   value="<?= htmlspecialchars($cur_maps_url) ?>"
                   placeholder="https://maps.google.com/maps?q=...&amp;output=embed"
                   oninput="updateMapPreview(this.value)">
            <div style="font-size:.75rem;color:var(--muted);margin-top:4px">
              To get this URL: open Google Maps &rarr; find location &rarr; Share &rarr; Embed a map &rarr; copy only the <code>src="..."</code> value.
            </div>
          </div>

          <div>
            <div style="font-size:.76rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Quick Preset:</div>
            <button type="button" class="btn btn-ghost btn-sm" onclick="setLocationPreset()">
              &#x1F4CD; Thanjavur Factory (Default)
            </button>
          </div>
        </div>

        <!-- Right: live preview -->
        <div>
          <div style="font-size:.8rem;font-weight:700;color:var(--ink);margin-bottom:8px;display:flex;align-items:center;gap:6px">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="var(--green-600)" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.641 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Live Map Preview
          </div>
          <div style="border-radius:14px;overflow:hidden;border:2px solid var(--green-200);box-shadow:0 4px 12px rgba(0,0,0,.07);background:#e2e8f0;position:relative;height:310px">
            <iframe id="map-preview-iframe" title="Location Map Preview"
                    src="<?= htmlspecialchars($cur_maps_url) ?>"
                    style="width:100%;height:100%;border:0;display:<?= $cur_maps_url ? 'block' : 'none' ?>"
                    loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            <div id="map-no-url" style="display:<?= $cur_maps_url ? 'none' : 'flex' ?>;position:absolute;inset:0;align-items:center;justify-content:center;flex-direction:column;gap:8px;color:var(--muted);text-align:center;padding:20px">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:40px;height:40px;opacity:.4"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
              <span style="font-size:.83rem;font-weight:600">Paste a Google Maps embed URL above<br>to see a live map preview here</span>
            </div>
          </div>
          <div style="margin-top:8px;font-size:.72rem;color:var(--muted);text-align:center">Preview updates automatically as you paste the URL</div>
        </div>

      </div>
    </div>
  </div>

  <div style="display:flex;gap:10px;margin-top:4px;margin-bottom:32px">
    <button type="submit" class="btn btn-primary">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      Save All Settings
    </button>
    <button type="reset" class="btn btn-secondary">Reset Changes</button>
  </div>
</form>

<style>
@media (max-width: 768px) {
  div[style*="grid-template-columns:1fr 1fr"] { grid-template-columns: 1fr !important; }
}
</style>
<script>
var mapDebounce = null;
function updateMapPreview(url) {
  clearTimeout(mapDebounce);
  mapDebounce = setTimeout(function() {
    var iframe  = document.getElementById('map-preview-iframe');
    var noUrl   = document.getElementById('map-no-url');
    var trimmed = (url || '').trim();
    if (trimmed) {
      iframe.src = trimmed;
      iframe.style.display = 'block';
      noUrl.style.display  = 'none';
    } else {
      iframe.src = '';
      iframe.style.display = 'none';
      noUrl.style.display  = 'flex';
    }
  }, 600);
}
function setLocationPreset() {
  document.getElementById('setting_contact_address').value =
    'No. 2, Tholilpettai, SIDCO Industrial Estate, N.K. Road, Thanjavur (613006), Tamil Nadu';
  var murl = 'https://maps.google.com/maps?q=SIDCO+Industrial+Estate,+N.K.+Road,+Thanjavur+613006&t=&z=15&ie=UTF8&iwloc=&output=embed';
  document.getElementById('setting_contact_maps_url').value = murl;
  updateMapPreview(murl);
}
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
