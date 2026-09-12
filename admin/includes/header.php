<?php
// Usage: include after auth_check.php
// $page_title must be set before including this file
$page_title  = $page_title  ?? 'Admin';
$breadcrumbs = $breadcrumbs ?? [];

// ADMIN_URL and BASE_URL are defined in backend/config/config.php (included via auth_check.php)
$assets_url = ADMIN_URL . '/assets';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title><?= htmlspecialchars($page_title) ?> — Evergreen Admin</title>
  <link rel="icon" href="<?= BASE_URL ?>/public/favicon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="<?= $assets_url ?>/css/admin.css">
</head>
<body>
<div class="admin-shell">

<?php include __DIR__ . '/sidebar.php'; ?>

<!-- Top Header -->
<header class="admin-header">
  <div class="header-left">
    <button class="header-btn menu-toggle" id="menuToggle" aria-label="Toggle sidebar">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
      </svg>
    </button>
    <div class="breadcrumb">
      <a href="<?= ADMIN_URL ?>/dashboard.php">Admin</a>
      <?php foreach ($breadcrumbs as $bc): ?>
        <span style="opacity:.4">›</span>
        <?php if (!empty($bc[1])): ?>
          <a href="<?= htmlspecialchars($bc[1]) ?>"><?= htmlspecialchars($bc[0]) ?></a>
        <?php else: ?>
          <span><?= htmlspecialchars($bc[0]) ?></span>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="header-right">
    <div class="header-admin-badge">
      <div class="dot"></div>
      <?= htmlspecialchars($current_admin['full_name'] ?: $current_admin['username']) ?>
    </div>
  </div>
</header>

<main class="admin-main">
<div class="page-content">
