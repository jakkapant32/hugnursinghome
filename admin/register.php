<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_admin_role();

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['password_confirm'] ?? '';

    if ($full_name === '') {
        $error = 'กรุณากรอกชื่อ-นามสกุล';
    } elseif ($username === '' || !preg_match('/^[a-zA-Z0-9._-]{3,50}$/', $username)) {
        $error = 'ชื่อผู้ใช้ต้องมี 3–50 ตัวอักษร (a-z, 0-9, . _ -)';
    } elseif (strlen($password) < 8) {
        $error = 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร';
    } elseif ($password !== $confirm) {
        $error = 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'รูปแบบอีเมลไม่ถูกต้อง';
    } else {
        $check = $pdo->prepare('SELECT 1 FROM users WHERE username = :u LIMIT 1');
        $check->execute([':u' => $username]);
        if ($check->fetch()) {
            $error = 'ชื่อผู้ใช้นี้ถูกใช้แล้ว';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                'INSERT INTO users (username, password_hash, full_name, email, role, is_active)
                 VALUES (:username, :hash, :full_name, :email, :role, 1)'
            );
            $stmt->execute([
                ':username'  => $username,
                ':hash'      => $hash,
                ':full_name' => $full_name,
                ':email'     => $email !== '' ? $email : null,
                ':role'      => 'staff',
            ]);
            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>สร้างบัญชีเจ้าหน้าที่ | ฮักเนอร์สซิ่งโฮม</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;600;700&family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="login-shell">
  <div class="login-card login-card-wide">
    <?php require __DIR__ . '/../includes/auth_brand.php'; ?>
    <h2>สร้างบัญชีเจ้าหน้าที่</h2>
    <p class="auth-lead">สำหรับ admin สร้างบัญชีเจ้าหน้าที่ (staff)</p>

    <?php if ($success): ?>
      <p class="success-text">สร้างบัญชี staff เรียบร้อยแล้ว</p>
      <a class="btn btn-primary" href="dashboard.php">กลับแดชบอร์ด</a>
    <?php else: ?>
      <?php if ($error): ?>
        <p class="error-text"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <form method="post" novalidate>
        <div class="form-field">
          <label for="full_name">ชื่อ-นามสกุล</label>
          <input type="text" id="full_name" name="full_name" required
                 value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
        </div>
        <div class="form-field">
          <label for="email">อีเมล (ไม่บังคับ)</label>
          <input type="email" id="email" name="email"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-field">
          <label for="username">ชื่อผู้ใช้</label>
          <input type="text" id="username" name="username" required autocomplete="username"
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
        <div class="form-field">
          <label for="password">รหัสผ่าน</label>
          <input type="password" id="password" name="password" required autocomplete="new-password" minlength="8">
        </div>
        <div class="form-field">
          <label for="password_confirm">ยืนยันรหัสผ่าน</label>
          <input type="password" id="password_confirm" name="password_confirm" required autocomplete="new-password">
        </div>
        <button type="submit" class="btn btn-primary">สร้างบัญชี</button>
      </form>
    <?php endif; ?>

    <p class="auth-foot"><a href="dashboard.php">← กลับแดชบอร์ด</a></p>
    <p class="auth-foot auth-foot-muted">ญาติ/สมาชิก: <a href="/public/register.php">สมัครที่หน้าเว็บ</a></p>
  </div>
</div>
</body>
</html>
