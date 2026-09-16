<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!empty($_SESSION['user_id'])) {
    require_once __DIR__ . '/../includes/auth_helpers.php';
    hug_redirect_after_login($_SESSION['role']);
}

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
                ':role'      => 'member',
            ]);
            $success = true;
        }
    }
}

$page_title = 'สมัครสมาชิก';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="auth-section">
  <div class="auth-card auth-card-wide">
    <?php require __DIR__ . '/../includes/auth_brand.php'; ?>
    <h2>สมัครสมาชิก</h2>
    <p class="auth-lead">สำหรับญาติและผู้ติดตามข้อมูลผู้สูงอายุ</p>

    <?php if ($success): ?>
      <p class="alert-success">สมัครสมาชิกเรียบร้อยแล้ว</p>
      <a class="btn btn-green btn-lg" href="login.php" style="width:100%;">ไปหน้าเข้าสู่ระบบ</a>
    <?php else: ?>
      <?php if ($error): ?>
        <p class="alert-error"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>
      <form method="post" class="auth-form" novalidate>
        <div class="form-field">
          <label for="full_name">ชื่อ-นามสกุล</label>
          <input type="text" id="full_name" name="full_name" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
        </div>
        <div class="form-field">
          <label for="email">อีเมล</label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-field">
          <label for="username">ชื่อผู้ใช้</label>
          <input type="text" id="username" name="username" required autocomplete="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
        <div class="form-field">
          <label for="password">รหัสผ่าน</label>
          <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
        </div>
        <div class="form-field">
          <label for="password_confirm">ยืนยันรหัสผ่าน</label>
          <input type="password" id="password_confirm" name="password_confirm" required autocomplete="new-password">
        </div>
        <button type="submit" class="btn btn-green btn-lg" style="width:100%;">สมัครสมาชิก</button>
      </form>
    <?php endif; ?>

    <p class="auth-foot">มีบัญชีแล้ว? <a href="login.php">เข้าสู่ระบบ</a></p>
    <p class="auth-foot auth-foot-muted">เจ้าหน้าที่: <a href="/admin/register.php">สมัครบัญชีเจ้าหน้าที่</a></p>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
