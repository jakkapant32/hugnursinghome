<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../includes/site_settings.php';

require_admin_role();

$active = 'site';
$error = '';
$success = '';
$settings = hug_load_site_settings($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $line = trim($_POST['line_url'] ?? '');
    $email = trim($_POST['contact_email'] ?? '');

    if (trim($_POST['site_name'] ?? '') === '') {
        $error = 'กรุณากรอกชื่อศูนย์';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'รูปแบบอีเมลไม่ถูกต้อง';
    } elseif ($line !== '' && !preg_match('#^https://line\.me/#i', $line)) {
        $error = 'ลิงก์ Line ควรขึ้นต้นด้วย https://line.me/';
    } else {
        hug_save_site_settings($pdo, [
            'site_name' => $_POST['site_name'] ?? '',
            'footer_description' => $_POST['footer_description'] ?? '',
            'contact_address' => $_POST['contact_address'] ?? '',
            'contact_address_detail' => $_POST['contact_address_detail'] ?? '',
            'contact_phone' => $_POST['contact_phone'] ?? '',
            'contact_email' => $email,
            'contact_hours' => $_POST['contact_hours'] ?? '',
            'line_url' => $line,
        ]);
        $settings = hug_load_site_settings($pdo);
        $success = 'บันทึกข้อมูล Footer และช่องทางติดต่อแล้ว';
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ตั้งค่าเว็บไซต์ | Hug Nursing Home</title>
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="admin-shell">
  <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>
  <main class="admin-main">
    <?php $topbar_title = 'Footer & ติดต่อเรา'; require __DIR__ . '/../includes/admin_topbar.php'; ?>
    <div class="admin-content">
      <div class="panel">
        <div class="panel-head"><h3>แก้ไขส่วนล่างเว็บ (Footer) และหน้าติดต่อเรา</h3></div>
        <p style="color:var(--muted);font-size:.9rem;margin:0 0 16px;">เฉพาะ Admin · ข้อมูลนี้แสดงที่ Footer ทุกหน้าและบล็อกติดต่อในหน้าเว็บ</p>
        <?php if ($error): ?><p class="error-text"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success-text"><?= htmlspecialchars($success) ?></p><?php endif; ?>
        <form method="post">
          <div class="form-field">
            <label for="site_name">ชื่อศูนย์ (Footer / ลิขสิทธิ์)</label>
            <input type="text" id="site_name" name="site_name" required value="<?= htmlspecialchars($settings['site_name']) ?>">
          </div>
          <div class="form-field">
            <label for="footer_description">คำอธิบายใต้โลโก้ (Footer)</label>
            <textarea id="footer_description" name="footer_description" rows="3"><?= htmlspecialchars($settings['footer_description']) ?></textarea>
          </div>
          <div class="form-field">
            <label for="contact_address">ที่อยู่ (สั้น — Footer)</label>
            <textarea id="contact_address" name="contact_address" rows="2"><?= htmlspecialchars($settings['contact_address']) ?></textarea>
          </div>
          <div class="form-field">
            <label for="contact_address_detail">ที่อยู่ (เต็ม — หน้าติดต่อเรา)</label>
            <textarea id="contact_address_detail" name="contact_address_detail" rows="2"><?= htmlspecialchars($settings['contact_address_detail']) ?></textarea>
          </div>
          <div class="form-field">
            <label for="contact_phone">โทรศัพท์</label>
            <input type="text" id="contact_phone" name="contact_phone" value="<?= htmlspecialchars($settings['contact_phone']) ?>">
          </div>
          <div class="form-field">
            <label for="contact_email">อีเมล</label>
            <input type="email" id="contact_email" name="contact_email" value="<?= htmlspecialchars($settings['contact_email']) ?>">
          </div>
          <div class="form-field">
            <label for="contact_hours">เวลาเปิดทำการ</label>
            <input type="text" id="contact_hours" name="contact_hours" value="<?= htmlspecialchars($settings['contact_hours']) ?>">
          </div>
          <div class="form-field">
            <label for="line_url">ลิงก์ Line Official</label>
            <input type="url" id="line_url" name="line_url" value="<?= htmlspecialchars($settings['line_url']) ?>" placeholder="https://line.me/ti/p/...">
          </div>
          <button type="submit" class="btn btn-primary" style="width:auto;">บันทึก</button>
        </form>
      </div>
    </div>
  </main>
</div>
</body>
</html>
