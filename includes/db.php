<?php
/**
 * db.php — PostgreSQL ผ่าน PDO (ไฟล์ config ในเครื่อง หรือ env บน Render)
 */

/** Host แบบ dpg-xxx-a ใช้ได้แค่ในเครือข่าย Render — นอกนั้นต้องใช้ *.virginia-postgres.render.com */
function hug_resolve_pg_host(string $host): string
{
    $host = trim($host);
    if ($host === '' || str_contains($host, '.')) {
        return $host;
    }
    if (preg_match('/^dpg-[a-z0-9]+(-a)?$/i', $host)) {
        $suffix = getenv('PGHOST_SUFFIX') ?: 'virginia-postgres.render.com';
        return $host . '.' . $suffix;
    }
    return $host;
}

function hug_config_from_url(string $url): ?array
{
    $parts = parse_url($url);
    if (!$parts || !in_array($parts['scheme'] ?? '', ['postgresql', 'postgres'], true)) {
        return null;
    }
    return [
        'host'     => hug_resolve_pg_host($parts['host'] ?? 'localhost'),
        'port'     => (int)($parts['port'] ?? 5432),
        'dbname'   => ltrim($parts['path'] ?? '', '/'),
        'user'     => $parts['user'] ?? '',
        'password' => isset($parts['pass']) ? rawurldecode($parts['pass']) : '',
        'sslmode'  => getenv('DB_SSLMODE') ?: 'require',
    ];
}

function hug_db_config(): array
{
    $configFile = __DIR__ . '/db.config.php';
    if (is_readable($configFile)) {
        $cfg = require $configFile;
        $cfg['host'] = hug_resolve_pg_host($cfg['host'] ?? '');
        return $cfg;
    }

    foreach (['DATABASE_URL', 'DATABASE_EXTERNAL_URL', 'EXTERNAL_DATABASE_URL', 'INTERNAL_DATABASE_URL'] as $key) {
        $url = getenv($key);
        if ($url && ($cfg = hug_config_from_url($url))) {
            return $cfg;
        }
    }

    if (getenv('PGHOST') || getenv('DB_HOST')) {
        return [
            'host'     => hug_resolve_pg_host(getenv('PGHOST') ?: getenv('DB_HOST')),
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
