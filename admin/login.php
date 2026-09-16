<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth_helpers.php';

if (!empty($_SESSION['user_id'])) {
    if (($_SESSION['role'] ?? '') === 'member') {
        header('Location: /public/account.php');
        exit;
    }
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน';
    } else {
        $user = hug_attempt_login($pdo, $username, $password);
        if ($user) {
            if ($user['role'] === 'member') {
                session_unset();
                session_destroy();
                session_start();
                $error = 'บัญชีสมาชิกให้เข้าที่หน้าเว็บ: /public/login.php';
            } else {
                header('Location: dashboard.php');
                exit;
            }
        } else {
            $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>เข้าสู่ระบบหลังบ้าน | ฮักเนอร์สซิ่งโฮม</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;600;700&family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="login-shell">
  <div class="login-card">
    <?php require __DIR__ . '/../includes/auth_brand.php'; ?>
    <h2>เข้าสู่ระบบหลังบ้าน</h2>
    <p class="auth-lead">สำหรับเจ้าหน้าที่และผู้ดูแลระบบ (admin / staff)</p>

    <?php if ($error): ?>
      <p class="error-text"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" novalidate>
      <div class="form-field">
        <label for="username">ชื่อผู้ใช้</label>
        <input type="text" id="username" name="username" required autofocus autocomplete="username">
      </div>
      <div class="form-field">
        <label for="password">รหัสผ่าน</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">
      </div>
      <button type="submit" class="btn btn-primary">เข้าสู่ระบบ</button>
    </form>

    <p class="auth-foot">เจ้าหน้าที่ใหม่? <a href="register.php">สมัครบัญชีเจ้าหน้าที่</a></p>
    <p class="auth-foot auth-foot-muted">สมาชิก/ญาติ: <a href="/public/login.php">เข้าสู่ระบบหน้าเว็บ</a></p>
  </div>
</div>
</body>
</html>
