<?php
/**
 * auth_check.php — วางไว้บนสุดของทุกหน้าในโฟลเดอร์ admin/
 * ตรวจสอบว่ามีการเข้าสู่ระบบแล้วหรือยัง ถ้ายังให้เด้งกลับไปหน้า login
 */
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'] ?? '';
if ($role === 'member') {
    header('Location: /public/account.php');
    exit;
}

// ตัวช่วยจำกัดสิทธิ์เฉพาะ admin (เรียกใช้เมื่อจำเป็น)
function require_admin_role(): void
{
    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
    }
}
