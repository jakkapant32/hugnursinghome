<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth_helpers.php';

if (!empty($_SESSION['user_id'])) {
    hug_redirect_after_login($_SESSION['role']);
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
            hug_redirect_after_login($user['role']);
        }
        $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
    }
}

$page_title = 'เข้าสู่ระบบ';
$active_page = '';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="auth-section">
  <div class="auth-card">
    <?php require __DIR__ . '/../includes/auth_brand.php'; ?>
    <h2>เข้าสู่ระบบ</h2>
    <p class="auth-lead">สำหรับสมาชิก ญาติผู้สูงอายุ และเจ้าหน้าที่ศูนย์</p>

    <?php if ($error): ?>
      <p class="alert-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" class="auth-form" novalidate>
      <div class="form-field">
        <label for="username">ชื่อผู้ใช้</label>
        <input type="text" id="username" name="username" required autofocus autocomplete="username">
      </div>
      <div class="form-field">
        <label for="password">รหัสผ่าน</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">
      </div>
      <button type="submit" class="btn btn-green btn-lg" style="width:100%;">เข้าสู่ระบบ</button>
    </form>

    <p class="auth-foot">ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a></p>
    <p class="auth-foot auth-foot-muted">เจ้าหน้าที่/แอดมิน: <a href="/admin/login.php">เข้าสู่ระบบหลังบ้าน</a></p>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
