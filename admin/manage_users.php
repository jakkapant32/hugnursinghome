<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/icons.php';
require_admin_role();

$active = 'users';
$errors = [];
$editRow = null;
$roles = ['admin', 'staff', 'member'];

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id === (int)$_SESSION['user_id']) {
        $errors[] = 'ไม่สามารถลบบัญชีของตนเอง';
    } else {
        $pdo->prepare('DELETE FROM users WHERE user_id = :id')->execute([':id' => $id]);
        header('Location: manage_users.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = $_POST['user_id'] ?? '';
    $username  = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $role      = $_POST['role'] ?? 'staff';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $new_pass  = $_POST['new_password'] ?? '';

    if ($full_name === '') $errors[] = 'กรุณากรอกชื่อ-นามสกุล';
    if ($username === '') $errors[] = 'กรุณากรอกชื่อผู้ใช้';
    if (!in_array($role, $roles, true)) $errors[] = 'บทบาทไม่ถูกต้อง';
    if ($new_pass !== '' && strlen($new_pass) < 8) $errors[] = 'รหัสผ่านใหม่ต้องมีอย่างน้อย 8 ตัวอักษร';

    if (!$errors) {
        if ($id) {
            if ((int)$id === (int)$_SESSION['user_id'] && $role !== 'admin') {
                $errors[] = 'ไม่สามารถลดสิทธิ์ admin ของตนเอง';
            } elseif ((int)$id === (int)$_SESSION['user_id'] && !$is_active) {
                $errors[] = 'ไม่สามารถปิดใช้งานบัญชีของตนเอง';
            } else {
                $dup = $pdo->prepare('SELECT 1 FROM users WHERE username = :u AND user_id != :id');
                $dup->execute([':u' => $username, ':id' => $id]);
                if ($dup->fetch()) {
                    $errors[] = 'ชื่อผู้ใช้ซ้ำ';
                } else {
                    if ($new_pass !== '') {
                        $hash = password_hash($new_pass, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare(
                            'UPDATE users SET username=:u, full_name=:n, email=:e, role=:r, is_active=:a, password_hash=:h
                             WHERE user_id=:id'
                        );
                        $stmt->execute([
                            ':u' => $username, ':n' => $full_name, ':e' => $email ?: null,
                            ':r' => $role, ':a' => $is_active, ':h' => $hash, ':id' => $id,
                        ]);
                    } else {
                        $stmt = $pdo->prepare(
                            'UPDATE users SET username=:u, full_name=:n, email=:e, role=:r, is_active=:a WHERE user_id=:id'
                        );
                        $stmt->execute([
                            ':u' => $username, ':n' => $full_name, ':e' => $email ?: null,
                            ':r' => $role, ':a' => $is_active, ':id' => $id,
                        ]);
                    }
                    header('Location: manage_users.php');
                    exit;
                }
            }
        } else {
            if ($new_pass === '') $errors[] = 'กรุณาตั้งรหัสผ่านสำหรับผู้ใช้ใหม่';
            else {
                $dup = $pdo->prepare('SELECT 1 FROM users WHERE username = :u');
                $dup->execute([':u' => $username]);
                if ($dup->fetch()) $errors[] = 'ชื่อผู้ใช้ซ้ำ';
                else {
                    $stmt = $pdo->prepare(
                        'INSERT INTO users (username, password_hash, full_name, email, role, is_active)
                         VALUES (:u, :h, :n, :e, :r, :a)'
                    );
                    $stmt->execute([
                        ':u' => $username,
                        ':h' => password_hash($new_pass, PASSWORD_DEFAULT),
                        ':n' => $full_name,
                        ':e' => $email ?: null,
                        ':r' => $role,
                        ':a' => $is_active,
                    ]);
                    header('Location: manage_users.php');
                    exit;
                }
            }
        }
    }
}

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT user_id, username, full_name, email, role, is_active, last_login_at, created_at FROM users WHERE user_id = :id');
    $stmt->execute([':id' => (int)$_GET['id']]);
    $editRow = $stmt->fetch();
}

$users = $pdo->query(
    'SELECT user_id, username, full_name, email, role, is_active, last_login_at FROM users ORDER BY role, username'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>บัญชีผู้ใช้ | Hug Nursing Home</title>
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="admin-shell">
  <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>
  <main class="admin-main">
    <?php $topbar_title = 'จัดการบัญชีและสิทธิ์'; require __DIR__ . '/../includes/admin_topbar.php'; ?>
    <div class="admin-content">
      <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><h3><?= $editRow ? 'แก้ไขผู้ใช้' : 'เพิ่มผู้ใช้ระบบ' ?></h3></div>
        <?php foreach ($errors as $e): ?><p class="error-text"><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
        <form method="post">
          <input type="hidden" name="user_id" value="<?= htmlspecialchars($editRow['user_id'] ?? '') ?>">
          <div class="form-grid">
            <div class="form-field"><label>ชื่อผู้ใช้</label>
              <input type="text" name="username" required value="<?= htmlspecialchars($editRow['username'] ?? '') ?>"></div>
            <div class="form-field"><label>ชื่อ-นามสกุล</label>
              <input type="text" name="full_name" required value="<?= htmlspecialchars($editRow['full_name'] ?? '') ?>"></div>
            <div class="form-field"><label>อีเมล</label>
              <input type="email" name="email" value="<?= htmlspecialchars($editRow['email'] ?? '') ?>"></div>
            <div class="form-field"><label>บทบาท</label>
              <select name="role">
                <?php foreach ($roles as $r): ?>
                <option value="<?= $r ?>" <?= (($editRow['role'] ?? '') === $r) ? 'selected' : '' ?>><?= $r ?></option>
                <?php endforeach; ?>
              </select></div>
            <div class="form-field"><label>รหัสผ่าน<?= $editRow ? ' (ใหม่ — ว่างถ้าไม่เปลี่ยน)' : '' ?></label>
              <input type="password" name="new_password" autocomplete="new-password" <?= $editRow ? '' : 'required minlength="8"' ?>></div>
            <div class="form-field" style="display:flex;align-items:center;gap:8px;padding-top:24px;">
              <input type="checkbox" name="is_active" id="ua" <?= !isset($editRow) || !empty($editRow['is_active']) ? 'checked' : '' ?>>
              <label for="ua" style="margin:0;">เปิดใช้งาน</label>
            </div>
          </div>
          <button type="submit" class="btn btn-primary" style="width:auto;">บันทึก</button>
          <?php if ($editRow): ?><a href="manage_users.php" class="btn" style="background:var(--line);">ยกเลิก</a><?php endif; ?>
        </form>
      </div>
      <div class="panel">
        <table class="data-table">
          <thead>
            <tr><th>ผู้ใช้</th><th>ชื่อ</th><th>บทบาท</th><th>สถานะ</th><th>เข้าล่าสุด</th><th></th></tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
              <td><?= htmlspecialchars($u['username']) ?></td>
              <td><?= htmlspecialchars($u['full_name']) ?></td>
              <td><span class="badge badge-active"><?= htmlspecialchars($u['role']) ?></span></td>
              <td><span class="badge <?= $u['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $u['is_active'] ? 'ใช้งาน' : 'ปิด' ?></span></td>
              <td><?= $u['last_login_at'] ? date('d M Y H:i', strtotime($u['last_login_at'])) : '—' ?></td>
              <td>
                <a class="icon-btn" href="?id=<?= (int)$u['user_id'] ?>"><?= hug_icon('edit', 16) ?></a>
                <?php if ((int)$u['user_id'] !== (int)$_SESSION['user_id']): ?>
                <a class="icon-btn icon-btn-danger" href="?delete=<?= (int)$u['user_id'] ?>" onclick="return confirm('ลบผู้ใช้นี้?');"><?= hug_icon('trash', 16) ?></a>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
</body>
</html>
