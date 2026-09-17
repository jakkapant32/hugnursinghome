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

$page_title_key = 'page.contact';
$active_page = 'contact';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../includes/site_settings.php';
require_once __DIR__ . '/../includes/header.php';
$site = hug_load_site_settings($pdo);
$lineUrl = $site['line_url'];
$phoneTel = hug_phone_tel($site['contact_phone']);
?>

<section class="section">
  <div class="container">
    <div class="section-head">
      <h2><?= hug_t('contact.title') ?></h2>
      <p><?= hug_t('contact.lead') ?></p>
    </div>

    <div class="contact-grid">
      <div>
        <div class="contact-info-item">
          <div class="icon"><?= hug_icon('pin', 20) ?></div>
          <div><h3><?= hug_t('contact.addr') ?></h3><p><?= htmlspecialchars($site['contact_address_detail']) ?></p></div>
        </div>
        <div class="contact-info-item">
          <div class="icon"><?= hug_icon('phone', 20) ?></div>
          <div><h3><?= hug_t('contact.phone') ?></h3><p><a href="tel:<?= htmlspecialchars($phoneTel) ?>"><?= htmlspecialchars($site['contact_phone']) ?></a> (<?= htmlspecialchars($site['contact_hours']) ?>)</p></div>
        </div>
        <div class="contact-info-item">
          <div class="icon"><?= hug_icon('mail', 20) ?></div>
          <div><h3><?= hug_t('contact.email') ?></h3><p><a href="mailto:<?= htmlspecialchars($site['contact_email']) ?>"><?= htmlspecialchars($site['contact_email']) ?></a></p></div>
        </div>
        <div class="contact-info-item">
          <a href="<?= htmlspecialchars($lineUrl) ?>" class="line-contact-icon" target="_blank" rel="noopener noreferrer" aria-label="<?= htmlspecialchars(hug_t('contact.line')) ?>">
            <?= hug_line_icon(28) ?>
          </a>
          <div>
            <h3><?= hug_t('contact.line') ?></h3>
            <p>
              <a href="<?= htmlspecialchars($lineUrl) ?>" class="line-contact-link" target="_blank" rel="noopener noreferrer">
                <?= hug_t('contact.line_hint') ?>
              </a>
            </p>
          </div>
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
          <button type="submit" class="btn btn-primary"><?= hug_t('contact.submit') ?></button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
