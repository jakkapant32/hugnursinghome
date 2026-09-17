<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/icons.php';

$active = 'settings';
$error = '';
$success = '';

$stmt = $pdo->prepare('SELECT user_id, username, full_name, email, role FROM users WHERE user_id = :id');
$stmt->execute([':id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $new_pass  = $_POST['new_password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    if ($full_name === '') {
        $error = 'กรุณากรอกชื่อ-นามสกุล';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'รูปแบบอีเมลไม่ถูกต้อง';
    } elseif ($new_pass !== '' && strlen($new_pass) < 8) {
        $error = 'รหัสผ่านใหม่ต้องมีอย่างน้อย 8 ตัวอักษร';
    } elseif ($new_pass !== '' && $new_pass !== $confirm) {
        $error = 'ยืนยันรหัสผ่านไม่ตรงกัน';
    } else {
        if ($new_pass !== '') {
            $hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $upd = $pdo->prepare('UPDATE users SET full_name=:n, email=:e, password_hash=:h WHERE user_id=:id');
            $upd->execute([':n'=>$full_name, ':e'=>$email ?: null, ':h'=>$hash, ':id'=>$_SESSION['user_id']]);
        } else {
            $upd = $pdo->prepare('UPDATE users SET full_name=:n, email=:e WHERE user_id=:id');
            $upd->execute([':n'=>$full_name, ':e'=>$email ?: null, ':id'=>$_SESSION['user_id']]);
        }
        $_SESSION['full_name'] = $full_name;
        $success = 'บันทึกการตั้งค่าเรียบร้อยแล้ว';
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $user = $stmt->fetch();
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ตั้งค่า | Hug Nursing Home</title>
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="admin-shell">
  <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>
  <main class="admin-main">
    <?php $topbar_title = 'ตั้งค่าบัญชี'; require __DIR__ . '/../includes/admin_topbar.php'; ?>
    <div class="admin-content">
      <div class="panel" style="max-width:480px;">
        <div class="panel-head"><h3>โปรไฟล์ของฉัน</h3></div>
        <?php if ($error): ?><p class="error-text"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success-text"><?= htmlspecialchars($success) ?></p><?php endif; ?>
        <form method="post">
          <div class="form-field"><label>ชื่อผู้ใช้</label>
            <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled></div>
          <div class="form-field"><label>บทบาท</label>
            <input type="text" value="<?= htmlspecialchars($user['role']) ?>" disabled></div>
          <div class="form-field"><label>ชื่อ-นามสกุล</label>
            <input type="text" name="full_name" required value="<?= htmlspecialchars($user['full_name']) ?>"></div>
          <div class="form-field"><label>อีเมล</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>"></div>
          <hr style="border:none;border-top:1px solid var(--line);margin:20px 0;">
          <div class="form-field"><label>รหัสผ่านใหม่ (เว้นว่างถ้าไม่เปลี่ยน)</label>
            <input type="password" name="new_password" autocomplete="new-password"></div>
          <div class="form-field"><label>ยืนยันรหัสผ่านใหม่</label>
            <input type="password" name="confirm_password" autocomplete="new-password"></div>
          <button type="submit" class="btn btn-primary" style="width:auto;">บันทึก</button>
        </form>
      </div>
    </div>
  </main>
</div>
</body>
</html>
