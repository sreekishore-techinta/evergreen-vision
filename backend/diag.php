<?php
// ============================================================
// DIAGNOSTIC — DELETE AFTER USE
// URL: http://localhost/evergreen-vision/backend/diag.php
// ============================================================
header('Content-Type: text/html; charset=utf-8');
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<pre style='font:13px monospace;padding:20px;background:#0f172a;color:#e2e8f0;min-height:100vh'>";
echo "<span style='color:#4ade80;font-size:1.1em'>🌿 Evergreen Backend Diagnostic</span>\n";
echo str_repeat("─", 60) . "\n\n";

// 1. PHP version
echo "PHP Version : " . PHP_VERSION . "\n";
echo "Server Root : " . ($_SERVER['DOCUMENT_ROOT'] ?? 'unknown') . "\n\n";

// 2. Config file
$configPath = __DIR__ . '/config/config.php';
echo "config.php  : " . ($configPath) . "\n";
echo "Exists      : " . (file_exists($configPath) ? "✓ YES" : "✗ NO") . "\n";

// 3. DB config
$dbPath = __DIR__ . '/config/db.php';
echo "db.php      : $dbPath\n";
echo "Exists      : " . (file_exists($dbPath) ? "✓ YES" : "✗ NO") . "\n\n";

// 4. Load and test DB
try {
    require_once $configPath;
    require_once $dbPath;
    echo "DB_HOST     : " . DB_HOST . "\n";
    echo "DB_PORT     : " . DB_PORT . "\n";
    echo "DB_NAME     : " . DB_NAME . "\n";
    echo "DB_USER     : " . DB_USER . "\n\n";

    $pdo = db();
    echo "<span style='color:#4ade80'>✓ DB CONNECTION SUCCESS</span>\n\n";

    // 5. Check tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables in '" . DB_NAME . "':\n";
    if (empty($tables)) {
        echo "  <span style='color:#f87171'>⚠ NO TABLES FOUND — database is empty!</span>\n";
        echo "  → Run the installer: <a style='color:#60a5fa' href='/evergreen-vision/backend/install.php'>/evergreen-vision/backend/install.php</a>\n";
    } else {
        foreach ($tables as $t) {
            $count = $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
            echo "  ✓ $t  ($count rows)\n";
        }
    }

    // 6. Check categories table specifically
    echo "\n";
    if (in_array('categories', $tables)) {
        $cats = $pdo->query("SELECT id, name, slug FROM categories")->fetchAll();
        echo "Categories (" . count($cats) . "):\n";
        foreach ($cats as $c) echo "  [{$c['id']}] {$c['name']} → {$c['slug']}\n";
    } else {
        echo "<span style='color:#f87171'>✗ 'categories' table MISSING</span>\n";
        echo "  → Run migrate: <a style='color:#60a5fa' href='/evergreen-vision/backend/run_migrate.php'>/evergreen-vision/backend/run_migrate.php</a>\n";
    }

    // 7. Check products
    echo "\n";
    if (in_array('products', $tables)) {
        $count = $pdo->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn();
        echo "Active products: $count\n";
        // Check for category_id column
        $cols = $pdo->query("SHOW COLUMNS FROM products")->fetchAll(PDO::FETCH_COLUMN);
        echo "Products columns: " . implode(', ', $cols) . "\n";
    } else {
        echo "<span style='color:#f87171'>✗ 'products' table MISSING</span>\n";
    }

    // 8. Test the actual API endpoint logic
    echo "\n" . str_repeat("─", 60) . "\n";
    echo "Testing categories API query...\n";
    $rows = $pdo->query(
        "SELECT c.*, COUNT(p.id) AS product_count
         FROM categories c
         LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1
         WHERE c.is_active = 1
         GROUP BY c.id
         ORDER BY c.sort_order ASC, c.name ASC"
    )->fetchAll();
    echo "<span style='color:#4ade80'>✓ categories query OK — " . count($rows) . " rows returned</span>\n";

    echo "\nTesting products API query...\n";
    $prods = $pdo->query("SELECT id, name, category, category_id FROM products WHERE is_active=1 LIMIT 5")->fetchAll();
    echo "<span style='color:#4ade80'>✓ products query OK — " . count($prods) . " rows (showing max 5)</span>\n";
    foreach ($prods as $p) echo "  [{$p['id']}] {$p['name']} | cat: {$p['category']} | cat_id: {$p['category_id']}\n";

} catch (PDOException $e) {
    echo "<span style='color:#f87171'>✗ DB ERROR: " . htmlspecialchars($e->getMessage()) . "</span>\n\n";
    echo "POSSIBLE CAUSES:\n";
    echo "  1. MySQL is not running — start it in XAMPP Control Panel\n";
    echo "  2. Database '" . (defined('DB_NAME') ? DB_NAME : 'ever_bio') . "' does not exist\n";
    echo "  3. Wrong credentials in backend/config/db.php\n\n";
    echo "FIX:\n";
    echo "  1. Open XAMPP Control Panel → Start MySQL\n";
    echo "  2. Open phpMyAdmin → Create database 'ever_bio'\n";
    echo "  3. Then visit: <a style='color:#60a5fa' href='/evergreen-vision/backend/install.php'>/evergreen-vision/backend/install.php</a>\n";
    echo "  4. Then visit: <a style='color:#60a5fa' href='/evergreen-vision/backend/seed_products.php'>/evergreen-vision/backend/seed_products.php</a>\n";
} catch (Throwable $e) {
    echo "<span style='color:#f87171'>✗ PHP ERROR: " . htmlspecialchars($e->getMessage()) . "</span>\n";
    echo "File: " . $e->getFile() . " line " . $e->getLine() . "\n";
}

echo "\n" . str_repeat("─", 60) . "\n";
echo "LINKS:\n";
echo "  Install DB  : <a style='color:#60a5fa' href='/evergreen-vision/backend/install.php'>backend/install.php</a>\n";
echo "  Run Migrate : <a style='color:#60a5fa' href='/evergreen-vision/backend/run_migrate.php'>backend/run_migrate.php</a>\n";
echo "  Seed Products: <a style='color:#60a5fa' href='/evergreen-vision/backend/seed_products.php'>backend/seed_products.php</a>\n";
echo "</pre>";
