<?php
// ============================================================
// Database Configuration
// ============================================================
define('DB_HOST',    'localhost');
define('DB_PORT',    '3306');
define('DB_NAME',    'u910074219_evergreen_bio');
define('DB_USER',    'u910074219_evergreen_bio');
define('DB_PASS',    'Techinta@2026');
define('DB_CHARSET', 'utf8mb4');

/**
 * Returns a singleton PDO connection.
 */
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        $configs = [
            ['host' => DB_HOST, 'port' => DB_PORT, 'name' => DB_NAME, 'user' => DB_USER, 'pass' => DB_PASS],
            ['host' => 'localhost', 'port' => '3306', 'name' => 'ever_bio', 'user' => 'root', 'pass' => ''],
            ['host' => 'localhost', 'port' => '3306', 'name' => 'evergreen_db', 'user' => 'root', 'pass' => ''],
        ];

        foreach ($configs as $cfg) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                    $cfg['host'], $cfg['port'], $cfg['name'], DB_CHARSET
                );
                $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], $options);
                break;
            } catch (PDOException $e) {
                // Try next configuration
            }
        }

        if ($pdo === null) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
            exit;
        }
    }
    return $pdo;
}
