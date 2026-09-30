<?php
/**
 * One-time patch: update site_settings labels for notifications.
 * Run once via browser: yourdomain.com/backend/patch_settings.php
 * DELETE this file from the server immediately after running.
 */
require_once __DIR__ . '/config/helpers.php';

$pdo = db();

$updates = [
    // [new_label, new_val (null = keep existing), setting_key]
    ['Analytics Tracking ID (GA4)',        null,                          'google_analytics'],
    ['Site Enquiry Notification Email',    'info@evergreenindustry.com',  'enquiry_notify_email'],
];

$results = [];
foreach ($updates as [$label, $val, $key]) {
    if ($val !== null) {
        $stmt = $pdo->prepare(
            'UPDATE site_settings SET label=?, setting_val=? WHERE setting_key=?'
        );
        $ok = $stmt->execute([$label, $val, $key]);
    } else {
        $stmt = $pdo->prepare(
            'UPDATE site_settings SET label=? WHERE setting_key=?'
        );
        $ok = $stmt->execute([$label, $key]);
    }
    $results[] = ($ok && $stmt->rowCount() > 0)
        ? "✓ Updated [{$key}] label → \"{$label}\""
        : "⚠ No rows changed for [{$key}] (may not exist yet)";
}

echo '<pre style="font-family:monospace;padding:20px">';
echo "<strong>patch_settings.php results</strong>\n\n";
foreach ($results as $r) { echo $r . "\n"; }
echo "\n<strong style='color:red'>⚠ DELETE this file from the server now!</strong>";
echo '</pre>';
