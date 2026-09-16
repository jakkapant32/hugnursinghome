<?php
/**
 * db.php — การเชื่อมต่อ PostgreSQL (Render) ผ่าน PDO
 */

$configFile = __DIR__ . '/db.config.php';
if (!is_readable($configFile)) {
    die('ไม่พบไฟล์ includes/db.config.php — คัดลอกจาก db.config.example.php แล้วใส่ค่าจาก Data.txt');
}

$cfg = require $configFile;

$DB_HOST = $cfg['host'] ?? 'localhost';
$DB_PORT = (int)($cfg['port'] ?? 5432);
$DB_NAME = $cfg['dbname'] ?? '';
$DB_USER = $cfg['user'] ?? '';
$DB_PASS = $cfg['password'] ?? '';
$sslmode = $cfg['sslmode'] ?? 'require';

$dsn = "pgsql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};sslmode={$sslmode}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    die('เชื่อมต่อฐานข้อมูลไม่สำเร็จ: ' . $e->getMessage());
}
