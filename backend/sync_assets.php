<?php
/**
 * sync_assets.php - One-time asset sync utility
 * Run: https://yourdomain.com/backend/sync_assets.php?token=ev_sync_2025
 * DELETE THIS FILE FROM SERVER AFTER RUNNING.
 */

$token = $_GET["token"] ?? "";
if ($token !== "ev_sync_2025") {
    http_response_code(403);
    die("Access denied. Pass ?token=ev_sync_2025 to run.");
}

$root = dirname(__DIR__);

$pairs = [
    [$root . "/public/uploads/slides/",   $root . "/uploads/slides/"],
    [$root . "/public/uploads/products/", $root . "/uploads/products/"],
    [$root . "/src/assets/",              $root . "/uploads/slides/"],
];

$results = [];

foreach ($pairs as [$src_dir, $dst_dir]) {
    if (!is_dir($src_dir)) {
        $results[] = "SKIP (not found): $src_dir";
        continue;
    }
    if (!is_dir($dst_dir)) {
        mkdir($dst_dir, 0755, true);
        $results[] = "MKDIR: $dst_dir";
    }
    $files = glob($src_dir . "*.{jpg,jpeg,png,webp,gif}", GLOB_BRACE);
    foreach ($files as $src) {
        $fname = basename($src);
        $dst   = $dst_dir . $fname;
        if (!file_exists($dst)) {
            $results[] = copy($src, $dst) ? "COPY OK: $fname" : "COPY FAIL: $fname";
        } else {
            $results[] = "EXIST (skip): $fname";
        }
    }
}

header("Content-Type: text/plain");
echo "Evergreen Asset Sync — " . date("Y-m-d H:i:s") . "\n";
echo str_repeat("=", 60) . "\n";
foreach ($results as $line) echo $line . "\n";
echo str_repeat("=", 60) . "\n";
echo "Done. DELETE this file from the server now.\n";
