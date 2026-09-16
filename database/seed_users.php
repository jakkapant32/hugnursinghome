<?php
/**
 * สร้าง/อัปเดตบัญชี admin และ user ตัวอย่าง
 * รัน: php database/seed_users.php
 */
require_once __DIR__ . '/../includes/db.php';

try {
    $pdo->exec("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
    $pdo->exec("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin', 'staff', 'member'))");
} catch (Throwable $e) {
    // ข้ามถ้ารันแล้ว
}

$accounts = [
    [
        'username'  => 'admin',
        'password'  => 'Admin@2026',
        'full_name' => 'ผู้ดูแลระบบ',
        'email'     => 'admin@hugnursinghome.com',
        'role'      => 'admin',
    ],
    [
        'username'  => 'staff01',
        'password'  => 'Staff@2026',
        'full_name' => 'เจ้าหน้าที่ สมใจ ใจดี',
        'email'     => 'staff01@hugnursinghome.com',
        'role'      => 'staff',
    ],
    [
        'username'  => 'member01',
        'password'  => 'Member@2026',
        'full_name' => 'ญาติ มานะ ใจดี',
        'email'     => 'member01@hugnursinghome.com',
        'role'      => 'member',
    ],
];

$sql = 'INSERT INTO users (username, password_hash, full_name, email, role, is_active)
        VALUES (:username, :hash, :full_name, :email, :role, 1)
        ON CONFLICT (username) DO UPDATE SET
          password_hash = EXCLUDED.password_hash,
          full_name = EXCLUDED.full_name,
          email = EXCLUDED.email,
          role = EXCLUDED.role,
          is_active = 1';

$stmt = $pdo->prepare($sql);

echo "บัญชีที่พร้อมใช้งาน:\n\n";

foreach ($accounts as $acc) {
    $stmt->execute([
        ':username'  => $acc['username'],
        ':hash'      => password_hash($acc['password'], PASSWORD_DEFAULT),
        ':full_name' => $acc['full_name'],
        ':email'     => $acc['email'],
        ':role'      => $acc['role'],
    ]);
    echo sprintf(
        "  [%s] %s\n  ชื่อผู้ใช้: %s\n  รหัสผ่าน: %s\n\n",
        match ($acc['role']) { 'admin' => 'Admin', 'staff' => 'Staff', default => 'Member' },
        $acc['full_name'],
        $acc['username'],
        $acc['password']
    );
}

echo "เข้าสู่ระบบ: http://localhost:8080/admin/login.php\n";
