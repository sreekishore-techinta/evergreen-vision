<?php
// ============================================================
// ONE-TIME INSTALLER — run once, then DELETE this file
// URL: yourdomain.com/backend/install.php
// ============================================================

// Basic security — only allow if no admin users exist yet
require_once __DIR__ . '/config/db.php';

$pdo = db();

// Check if already installed
try {
    $count = $pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
    if ((int)$count > 0) {
        die('<h2 style="font-family:sans-serif;color:#dc2626">Already installed. Delete this file immediately.</h2>');
    }
} catch (PDOException) {
    // Table doesn't exist yet — proceed with install
}

$errors = [];
$done   = [];

$sql_blocks = [

"CREATE TABLE IF NOT EXISTS `admin_users` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`        VARCHAR(60)  NOT NULL,
  `email`           VARCHAR(120) NOT NULL,
  `password_hash`   VARCHAR(255) NOT NULL,
  `full_name`       VARCHAR(120) NOT NULL DEFAULT '',
  `avatar`          VARCHAR(255) NOT NULL DEFAULT '',
  `role`            ENUM('superadmin','admin','editor') NOT NULL DEFAULT 'admin',
  `last_login`      DATETIME DEFAULT NULL,
  `is_active`       TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_username` (`username`),
  UNIQUE KEY `uq_email`    (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

"CREATE TABLE IF NOT EXISTS `contact_enquiries` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(120) NOT NULL,
  `company`     VARCHAR(120) NOT NULL DEFAULT '',
  `email`       VARCHAR(120) NOT NULL,
  `phone`       VARCHAR(30)  NOT NULL DEFAULT '',
  `message`     TEXT         NOT NULL,
  `status`      ENUM('new','read','replied','archived') NOT NULL DEFAULT 'new',
  `ip_address`  VARCHAR(45)  NOT NULL DEFAULT '',
  `user_agent`  VARCHAR(255) NOT NULL DEFAULT '',
  `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_status`     (`status`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

"CREATE TABLE IF NOT EXISTS `categories` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100) NOT NULL,
  `slug`        VARCHAR(100) NOT NULL,
  `description` TEXT,
  `sort_order`  INT          NOT NULL DEFAULT 0,
  `is_active`   TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

"CREATE TABLE IF NOT EXISTS `products` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`           VARCHAR(200) NOT NULL,
  `slug`           VARCHAR(200) NOT NULL,
  `category_id`    INT UNSIGNED NULL DEFAULT NULL,
  `category`       VARCHAR(100) NOT NULL DEFAULT '',
  `description`    TEXT,
  `features`       TEXT,
  `applications`   TEXT,
  `image_url`      VARCHAR(255) NOT NULL DEFAULT '',
  `image_path`     VARCHAR(255) NOT NULL DEFAULT '',
  `certifications` VARCHAR(255) NOT NULL DEFAULT '',
  `is_featured`    TINYINT(1)   NOT NULL DEFAULT 0,
  `is_active`      TINYINT(1)   NOT NULL DEFAULT 1,
  `sort_order`     INT          NOT NULL DEFAULT 0,
  `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_slug`      (`slug`),
  INDEX `idx_category_id`   (`category_id`),
  INDEX `idx_is_active`     (`is_active`),
  INDEX `idx_is_featured`   (`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

"CREATE TABLE IF NOT EXISTS `site_settings` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_val` TEXT,
  `label`       VARCHAR(150) NOT NULL DEFAULT '',
  `group_name`  VARCHAR(60)  NOT NULL DEFAULT 'general',
  `sort_order`  INT          NOT NULL DEFAULT 0,
  `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_key` (`setting_key`),
  INDEX `idx_group` (`group_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

"CREATE TABLE IF NOT EXISTS `admin_activity_log` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id`    INT UNSIGNED NOT NULL,
  `action`      VARCHAR(100) NOT NULL,
  `description` TEXT,
  `ip_address`  VARCHAR(45)  NOT NULL DEFAULT '',
  `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_admin_id`   (`admin_id`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

];

foreach ($sql_blocks as $sql) {
    try { $pdo->exec($sql); $done[] = "✓ " . substr(trim($sql), 0, 60) . "…"; }
    catch (PDOException $e) { $errors[] = "✗ " . $e->getMessage(); }
}

// Seed admin user
$hash = password_hash('Admin@1234', PASSWORD_BCRYPT, ['cost' => 12]);
try {
    $pdo->prepare(
        "INSERT IGNORE INTO admin_users (username, email, password_hash, full_name, role)
         VALUES ('admin', 'admin@evergreenindustry.com', ?, 'Super Admin', 'superadmin')"
    )->execute([$hash]);
    $done[] = "✓ Admin user created (admin / Admin@1234)";
} catch (PDOException $e) { $errors[] = "✗ Admin user: " . $e->getMessage(); }

// Seed settings
$settings = [
    ['company_name',        'EVERGREENINDUSTRY',                    'Company Name',          'general', 1],
    ['tagline',             'Sustainable Packaging Solutions',       'Tagline',               'general', 2],
    ['contact_email',       'hello@evergreenindustry.com',           'Contact Email',         'contact', 1],
    ['contact_phone',       '+91 00000 00000',                       'Contact Phone',         'contact', 2],
    ['contact_address',     'India · Serving businesses globally',   'Address',               'contact', 3],
    ['whatsapp_number',     '',                                      'WhatsApp Number',       'contact', 4],
    ['facebook_url',        '',                                      'Facebook URL',          'social',  1],
    ['instagram_url',       '',                                      'Instagram URL',         'social',  2],
    ['linkedin_url',        '',                                      'LinkedIn URL',          'social',  3],
    ['twitter_url',         '',                                      'Twitter/X URL',         'social',  4],
    ['meta_title',          'EVERGREENINDUSTRY | Sustainable Packaging', 'Meta Title',        'seo',     1],
    ['meta_description',    'Biodegradable and compostable packaging solutions.', 'Meta Description', 'seo', 2],
    ['google_analytics',    '',                                      'Google Analytics ID',   'seo',     3],
    ['enquiry_notify_email','admin@evergreenindustry.com',           'Notify Email',          'notifications', 1],
];
$ins = $pdo->prepare("INSERT IGNORE INTO site_settings (setting_key, setting_val, label, group_name, sort_order) VALUES (?,?,?,?,?)");
foreach ($settings as $s) { try { $ins->execute($s); } catch(PDOException) {} }
$done[] = "✓ Site settings seeded";

// Seed sample categories
$cats = [['Carry Bags','carry-bags',1],['Waste Bags','waste-bags',2],['Produce Packaging','produce-packaging',3],['Raw Materials','raw-materials',4],['Lifestyle','lifestyle',5]];
$insc = $pdo->prepare("INSERT IGNORE INTO categories (name, slug, sort_order) VALUES (?,?,?)");
foreach ($cats as $c) { try { $insc->execute($c); } catch(PDOException) {} }
$done[] = "✓ Sample categories seeded";

?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Install — Evergreen</title>
<style>body{font-family:system-ui,sans-serif;max-width:640px;margin:60px auto;padding:20px;background:#f0faf1;color:#0e2617}
h1{color:#166534}ul{line-height:2}.ok{color:#15803d}.err{color:#dc2626}
.box{background:#fff;border:1px solid #bbf7d0;border-radius:12px;padding:24px;margin:20px 0}
.warn{background:#fef9c3;border:1px solid #fde047;border-radius:12px;padding:20px;margin-top:20px;font-weight:600}</style>
</head>
<body>
<h1>🌿 Evergreen Install</h1>
<?php if ($errors): ?>
  <div class="box" style="border-color:#fecaca">
    <h3 class="err">Errors:</h3>
    <ul><?php foreach($errors as $e): ?><li class="err"><?= htmlspecialchars($e) ?></li><?php endforeach ?></ul>
  </div>
<?php endif; ?>
<div class="box">
  <h3 class="ok">Completed:</h3>
  <ul><?php foreach($done as $d): ?><li class="ok"><?= htmlspecialchars($d) ?></li><?php endforeach ?></ul>
</div>
<div class="warn">
  ⚠️ DELETE this file immediately after installation!<br>
  <code>Delete: backend/install.php</code><br><br>
  Then login at: <strong>/admin/login.php</strong><br>
  Username: <strong>admin</strong> &nbsp; Password: <strong>Admin@1234</strong>
</div>
</body>
</html>
