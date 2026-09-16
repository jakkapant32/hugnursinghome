<?php
/**
 * ประมวลผลฟอร์ม login
 * $auth_mode = 'staff' (เจ้าหน้าที่) | 'admin' (ผู้ดูแลระบบเท่านั้น)
 * คืนค่า string error หรือ null ถ้า login สำเร็จ (มี redirect แล้ว)
 */
function auth_process_login(PDO $pdo, string $auth_mode): ?string
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return null;
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        return 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน';
    }

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :u AND is_active = 1 LIMIT 1');
    $stmt->execute([':u' => $username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
    }

    $role = $user['role'] ?? 'staff';

    if ($auth_mode === 'admin' && $role !== 'admin') {
        return 'บัญชีเจ้าหน้าที่ใช้หน้าเข้าสู่ระบบ User ที่ /public/login.php';
    }

    if ($auth_mode === 'staff' && !in_array($role, ['staff', 'admin'], true)) {
        return 'คุณไม่มีสิทธิ์เข้าใช้งาน';
    }

    session_regenerate_id(true);
    $_SESSION['user_id']   = $user['user_id'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['role']      = $role;

    $pdo->prepare('UPDATE users SET last_login_at = NOW() WHERE user_id = :id')
        ->execute([':id' => $user['user_id']]);

    header('Location: /admin/dashboard.php');
    exit;
}
