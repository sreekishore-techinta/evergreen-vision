<?php
// ============================================================
// LIVE SERVER DIAGNOSTIC — DELETE IMMEDIATELY AFTER USE
// URL: https://ghostwhite-hawk-667350.hostingersite.com/backend/live_diag.php
// ============================================================
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/html; charset=utf-8');
?><!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Live Diagnostic</title>
<style>
  body{font-family:monospace;background:#0f172a;color:#e2e8f0;padding:24px;font-size:13px}
  .ok{color:#4ade80}.err{color:#f87171}.warn{color:#fbbf24}
  h2{color:#34d399;margin:20px 0 8px}
  pre{background:#1e293b;padding:12px;border-radius:8px;white-space:pre-wrap;word-break:break-all}
  a{color:#60a5fa}
</style>
</head>
<body>
<h1 style="color:#34d399">🌿 Evergreen — Live Server Diagnostic</h1>

<?php

$host    = 'localhost';
$dbname  = 'u910074219_evergreen_bio';
$user    = 'u910074219_evergreen_bio';
$pass    = 'Techinta@2026';
$charset = 'utf8mb4';

echo "<h2>1. PHP & Server Info</h2><pre>";
echo "PHP Version : " . PHP_VERSION . "\n";
echo "Server      : " . ($_SERVER['SERVER_SOFTWARE'] ?? 'unknown') . "\n";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'unknown') . "\n";
echo "Script Path : " . __FILE__ . "\n";
echo "</pre>";

// ── Check config files exist ──────────────────────────────────────────────
echo "<h2>2. Config Files</h2><pre>";
$configFile  = __DIR__ . '/config/config.php';
$dbFile      = __DIR__ . '/config/db.php';
$helpersFile = __DIR__ . '/config/helpers.php';
echo "config.php  : " . ($configFile)  . " → " . (file_exists($configFile)  ? "<span class='ok'>EXISTS</span>" : "<span class='err'>MISSING</span>") . "\n";
echo "db.php      : " . ($dbFile)      . " → " . (file_exists($dbFile)      ? "<span class='ok'>EXISTS</span>" : "<span class='err'>MISSING</span>") . "\n";
echo "helpers.php : " . ($helpersFile) . " → " . (file_exists($helpersFile) ? "<span class='ok'>EXISTS</span>" : "<span class='err'>MISSING</span>") . "\n";
echo "</pre>";

// ── Try DB connection ─────────────────────────────────────────────────────
echo "<h2>3. Database Connection</h2><pre>";
echo "Host   : $host\nDB Name: $dbname\nUser   : $user\n\n";

$pdo = null;
try {
    // First try without selecting DB
    $dsn0 = "mysql:host=$host;charset=$charset";
    $pdo0 = new PDO($dsn0, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "<span class='ok'>✓ MySQL connection OK (root level)</span>\n";

    // Try creating the DB if it doesn't exist
    $pdo0->exec("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<span class='ok'>✓ Database '$dbname' exists / created</span>\n";

    // Now connect to the specific DB
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "<span class='ok'>✓ Connected to '$dbname' successfully</span>\n";
} catch (PDOException $e) {
    echo "<span class='err'>✗ CONNECTION FAILED: " . htmlspecialchars($e->getMessage()) . "</span>\n";
    echo "\nPossible fixes:\n";
    echo "• Check DB host — on Hostinger it's usually 'localhost'\n";
    echo "• Verify DB name, user and password in cPanel → Databases\n";
    echo "• Make sure the DB user is assigned to the DB in cPanel\n";
    echo "</pre>";
    exit;
}
echo "</pre>";

// ── Check tables ──────────────────────────────────────────────────────────
echo "<h2>4. Tables</h2><pre>";
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
if (empty($tables)) {
    echo "<span class='warn'>⚠ No tables found — run migration first</span>\n";
    echo "Visit: <a href='/backend/run_migrate.php'>/backend/run_migrate.php</a>\n";
} else {
    foreach ($tables as $t) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
        echo "<span class='ok'>✓</span> $t  ($count rows)\n";
    }
}
echo "</pre>";

// ── Check required columns ────────────────────────────────────────────────
if (in_array('products', $tables)) {
    echo "<h2>5. Products Table Columns</h2><pre>";
    $cols = $pdo->query("SHOW COLUMNS FROM products")->fetchAll(PDO::FETCH_COLUMN);
    $required = ['id','name','slug','category','category_id','image_url','image_path','is_active','sort_order'];
    foreach ($required as $col) {
        $has = in_array($col, $cols);
        echo ($has ? "<span class='ok'>✓</span>" : "<span class='err'>✗ MISSING</span>") . " $col\n";
    }
    // Add missing columns
    $missing = array_diff($required, $cols);
    if (!empty($missing)) {
        echo "\n<span class='warn'>Adding missing columns...</span>\n";
        $addMap = [
            'category_id' => "ALTER TABLE products ADD COLUMN IF NOT EXISTS category_id INT UNSIGNED NULL DEFAULT NULL",
            'image_path'  => "ALTER TABLE products ADD COLUMN IF NOT EXISTS image_path VARCHAR(255) NOT NULL DEFAULT ''",
        ];
        foreach ($missing as $m) {
            if (isset($addMap[$m])) {
                try { $pdo->exec($addMap[$m]); echo "<span class='ok'>✓ Added: $m</span>\n"; }
                catch (Exception $ex) { echo "<span class='err'>✗ Could not add $m: " . htmlspecialchars($ex->getMessage()) . "</span>\n"; }
            }
        }
    }
    echo "</pre>";
}

// ── Test the exact API queries ─────────────────────────────────────────────
echo "<h2>6. API Query Test</h2><pre>";

// Test categories query
try {
    $rows = $pdo->query(
        "SELECT c.*, COUNT(p.id) AS product_count
         FROM categories c
         LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1
         WHERE c.is_active = 1
         GROUP BY c.id ORDER BY c.sort_order ASC"
    )->fetchAll();
    echo "<span class='ok'>✓ categories query OK — " . count($rows) . " categories</span>\n";
    foreach ($rows as $r) echo "  [{$r['id']}] {$r['name']} ({$r['product_count']} products)\n";
} catch (PDOException $e) {
    echo "<span class='err'>✗ categories query FAILED: " . htmlspecialchars($e->getMessage()) . "</span>\n";
}

echo "\n";

// Test products query
try {
    $rows = $pdo->query("SELECT id, name, category, image_url, is_active FROM products WHERE is_active=1 LIMIT 5")->fetchAll();
    echo "<span class='ok'>✓ products query OK — " . count($rows) . " active products (showing max 5)</span>\n";
    foreach ($rows as $r) echo "  [{$r['id']}] {$r['name']} | {$r['category']}\n";
} catch (PDOException $e) {
    echo "<span class='err'>✗ products query FAILED: " . htmlspecialchars($e->getMessage()) . "</span>\n";
}
echo "</pre>";

// ── Try running the helpers.php include to catch PHP syntax errors ─────────
echo "<h2>7. helpers.php Include Test</h2><pre>";
try {
    ob_start();
    // Use output buffering to catch any output helpers.php might emit
    @include_once __DIR__ . '/config/helpers.php';
    $out = ob_get_clean();
    echo "<span class='ok'>✓ helpers.php included without fatal errors</span>\n";
    if ($out) echo "Output from helpers: " . htmlspecialchars($out) . "\n";
} catch (Throwable $e) {
    ob_end_clean();
    echo "<span class='err'>✗ helpers.php ERROR: " . htmlspecialchars($e->getMessage()) . "</span>\n";
    echo "File: " . $e->getFile() . " line " . $e->getLine() . "\n";
}
echo "</pre>";

// ── Check CORS config ─────────────────────────────────────────────────────
echo "<h2>8. CORS / Config Check</h2><pre>";
if (defined('ALLOWED_ORIGINS')) {
    echo "<span class='ok'>✓ ALLOWED_ORIGINS defined</span>\n";
    echo "Origins: " . implode(', ', ALLOWED_ORIGINS) . "\n";
} else {
    echo "<span class='warn'>⚠ ALLOWED_ORIGINS not defined (config.php not loaded or error)</span>\n";
}
echo "</pre>";

echo "<h2>✅ Diagnostic Complete</h2>";
echo "<p><strong style='color:#f87171'>⚠ DELETE this file immediately: backend/live_diag.php</strong></p>";
echo "<p>Next steps:</p><ul style='line-height:2'>";
echo "<li>If tables are missing: <a href='/backend/run_migrate.php'>Run migration</a></li>";
echo "<li>If migration not uploaded: upload backend/run_migrate.php first</li>";
echo "<li>After all is green: delete live_diag.php and run_migrate.php</li>";
echo "</ul>";
?>
</body>
</html>
