<?php
/**
 * db.php — PostgreSQL ผ่าน PDO (ไฟล์ config ในเครื่อง หรือ env บน Render)
 */

function hug_db_config(): array
{
    $configFile = __DIR__ . '/db.config.php';
    if (is_readable($configFile)) {
        return require $configFile;
    }

    $url = getenv('DATABASE_URL') ?: getenv('INTERNAL_DATABASE_URL');
    if ($url) {
        $parts = parse_url($url);
        if ($parts && ($parts['scheme'] ?? '') === 'postgresql') {
            return [
                'host'     => $parts['host'] ?? 'localhost',
                'port'     => (int)($parts['port'] ?? 5432),
                'dbname'   => ltrim($parts['path'] ?? '', '/'),
                'user'     => $parts['user'] ?? '',
                'password' => $parts['pass'] ?? '',
                'sslmode'  => 'require',
            ];
        }
    }

    if (getenv('PGHOST') || getenv('DB_HOST')) {
        return [
            'host'     => getenv('PGHOST') ?: getenv('DB_HOST'),
            'port'     => (int)(getenv('PGPORT') ?: getenv('DB_PORT') ?: 5432),
            'dbname'   => getenv('PGDATABASE') ?: getenv('DB_NAME'),
            'user'     => getenv('PGUSER') ?: getenv('DB_USER'),
            'password' => getenv('PGPASSWORD') ?: getenv('DB_PASS'),
            'sslmode'  => getenv('DB_SSLMODE') ?: 'require',
        ];
    }

    die('ไม่พบการตั้งค่าฐานข้อมูล — ใส่ includes/db.config.php หรือ DATABASE_URL บน Render');
}

$cfg = hug_db_config();

$dsn = sprintf(
    'pgsql:host=%s;port=%d;dbname=%s;sslmode=%s',
    $cfg['host'],
    (int)($cfg['port'] ?? 5432),
    $cfg['dbname'],
    $cfg['sslmode'] ?? 'require'
);

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $cfg['user'], $cfg['password'], $options);
} catch (PDOException $e) {
    die('เชื่อมต่อฐานข้อมูลไม่สำเร็จ: ' . $e->getMessage());
}
