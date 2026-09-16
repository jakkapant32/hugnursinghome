<?php
/**
 * สร้างตารางและ seed ข้อมูลตั้งต้นบน PostgreSQL
 * รัน: php database/setup.php
 */
require_once __DIR__ . '/../includes/db.php';

function runSqlFile(PDO $pdo, string $path): void
{
    if (!is_readable($path)) {
        throw new RuntimeException("ไม่พบไฟล์: {$path}");
    }
    $pdo->exec(file_get_contents($path));
    echo 'OK: ' . basename($path) . PHP_EOL;
}

try {
    runSqlFile($pdo, __DIR__ . '/schema.postgresql.sql');

    $hasServices = (int) $pdo->query('SELECT COUNT(*) FROM services')->fetchColumn();
    if ($hasServices === 0) {
        runSqlFile($pdo, __DIR__ . '/seed.postgresql.sql');
    } else {
        $pdo->exec(
            "INSERT INTO users (username, password_hash, full_name, email, role)
             VALUES ('admin', '\$2y\$10\$7UrjIBRN3VNHoR3ErvdZ7eGWR2eJYvFMaPP.LQoQ72XIg2.uchx4C', 'ผู้ดูแลระบบ', 'admin@hugnursinghome.com', 'admin')
             ON CONFLICT (username) DO NOTHING"
        );
        echo 'OK: admin user (skip duplicate seed)' . PHP_EOL;
    }

    echo PHP_EOL . 'ตั้งค่าฐานข้อมูลเสร็จแล้ว' . PHP_EOL;
    echo 'เข้าระบบแอดมิน: admin / Admin@1234' . PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, 'ผิดพลาด: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
