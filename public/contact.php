<?php
require_once __DIR__ . '/../includes/db.php';

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '') $errors[] = 'กรุณากรอกชื่อ';
    if ($message === '') $errors[] = 'กรุณากรอกข้อความ';
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'รูปแบบอีเมลไม่ถูกต้อง';

    if (!$errors) {
        $stmt = $pdo->prepare(
            "INSERT INTO contact_messages (sender_name, phone, email, subject, message)
             VALUES (:name, :phone, :email, :subject, :message)"
        );
        $stmt->execute([
            ':name' => $name, ':phone' => $phone, ':email' => $email,
            ':subject' => $subject, ':message' => $message,
        ]);
        $success = true;
    }
}

$page_title = 'ติดต่อเรา';
$active_page = 'contact';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="section-head">
      <h2>ติดต่อเรา</h2>
      <p>สอบถามข้อมูลเพิ่มเติมหรือนัดหมายเข้าเยี่ยมชมศูนย์ได้ทุกช่องทาง</p>
    </div>

    <div class="contact-grid">
      <div>
        <div class="contact-info-item">
          <div class="icon"><?= hug_icon('pin', 20) ?></div>
          <div><h3>ที่อยู่</h3><p>123 ถนนมิตรภาพ ตำบลในเมือง อำเภอเมือง จังหวัดขอนแก่น 40000</p></div>
        </div>
        <div class="contact-info-item">
          <div class="icon"><?= hug_icon('phone', 20) ?></div>
          <div><h3>โทรศัพท์</h3><p><a href="tel:0430000000">043-000-000</a> (ทุกวัน 09:00–17:00 น.)</p></div>
        </div>
        <div class="contact-info-item">
          <div class="icon"><?= hug_icon('mail', 20) ?></div>
          <div><h3>อีเมล</h3><p><a href="mailto:info@hugnursinghome.com">info@hugnursinghome.com</a></p></div>
        </div>
        <div class="map-frame">
          <iframe src="https://maps.google.com/maps?q=Khon%20Kaen&t=&z=13&ie=UTF8&iwloc=&output=embed"
            width="100%" height="100%" style="border:0;" loading="lazy" title="แผนที่ศูนย์ฮักเนอร์สซิ่งโฮม"></iframe>
        </div>
      </div>

      <div>
        <?php if ($success): ?>
          <p class="alert-success">ส่งข้อความเรียบร้อยแล้ว ทีมงานจะติดต่อกลับโดยเร็วที่สุด ขอบคุณค่ะ</p>
        <?php endif; ?>
        <?php if ($errors): ?>
          <div class="alert-error"><?php foreach ($errors as $e): ?><div><?= htmlspecialchars($e) ?></div><?php endforeach; ?></div>
        <?php endif; ?>

        <form method="post" novalidate>
          <div class="form-field">
            <label for="name">ชื่อ-นามสกุล</label>
            <input type="text" id="name" name="name" required>
          </div>
          <div class="form-field">
            <label for="phone">เบอร์โทรศัพท์</label>
            <input type="tel" id="phone" name="phone">
          </div>
          <div class="form-field">
            <label for="email">อีเมล</label>
            <input type="email" id="email" name="email">
          </div>
          <div class="form-field">
            <label for="subject">หัวข้อ</label>
            <input type="text" id="subject" name="subject">
          </div>
          <div class="form-field">
            <label for="message">ข้อความ</label>
            <textarea id="message" name="message" rows="5" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary">ส่งข้อความ</button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
