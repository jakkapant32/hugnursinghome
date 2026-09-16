<?php
require_once __DIR__ . '/../includes/db.php';

$services = $pdo->query(
    "SELECT title, description, icon FROM services WHERE is_published = 1 ORDER BY sort_order ASC LIMIT 5"
)->fetchAll();

$latestNews = $pdo->query(
    "SELECT news_id, category, title, event_date, cover_image FROM news
     WHERE is_published = 1 ORDER BY COALESCE(event_date, created_at) DESC LIMIT 3"
)->fetchAll();

$page_title = 'หน้าแรก';
$active_page = 'home';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../includes/header.php';

$heroImg = '/assets/images/hero-care.jpg';
?>

<section class="hero">
  <div class="hero-photo">
    <img src="<?= htmlspecialchars($heroImg) ?>" alt="พยาบาลดูแลผู้สูงอายุด้วยรอยยิ้ม" loading="eager"
         onerror="this.src='https://images.pexels.com/photos/18271866/pexels-photo-18271866.jpeg?auto=compress&cs=tinysrgb&w=1600'">
  </div>
  <div class="container">
    <div class="hero-inner hero-copy">
      <h1>ดูแลด้วยใจ<span class="line2 accent">ห่วงใยเหมือนคนในครอบครัว</span></h1>
      <p class="lead">ศูนย์ดูแลผู้สูงอายุฮักเนอร์สซิ่งโฮม จังหวัดขอนแก่น</p>
      <p class="sub">ทีมพยาบาลวิชาชีพและผู้ช่วยดูแลประจำการตลอด 24 ชั่วโมง พร้อมสภาพแวดล้อมที่ปลอดภัย สะอาด และอบอุ่นเหมือนอยู่บ้าน</p>
      <div class="hero-actions hero-cta">
        <a href="services.php" class="btn btn-green btn-lg">ดูบริการทั้งหมด</a>
        <a href="contact.php" class="btn btn-rose btn-lg">นัดเข้าเยี่ยมชม</a>
      </div>
      <div class="hero-stats">
        <div><strong>52</strong><span>ผู้สูงอายุที่เราดูแล</span></div>
        <div><strong>18</strong><span>พยาบาลและผู้ช่วย</span></div>
        <div><strong>24 ชม.</strong><span>ดูแลไม่มีวันหยุด</span></div>
      </div>
    </div>
  </div>
</section>

<section class="section section-alt services" id="services">
  <div class="container">
    <div class="section-head">
      <h2>บริการของเรา</h2>
      <p>เราพร้อมดูแลผู้สูงอายุอย่างครบวงจร ตั้งแต่สุขภาพกาย จิตใจ ไปจนถึงกิจวัตรประจำวัน</p>
    </div>
    <div class="services-strip">
      <?php foreach ($services as $s): ?>
      <div class="service-tile">
        <div class="icon"><?= hug_icon('heart', 22) ?></div>
        <h3><?= htmlspecialchars($s['title']) ?></h3>
        <p><?= htmlspecialchars($s['description']) ?></p>
      </div>
      <?php endforeach; ?>
      <?php if (!$services): ?>
        <p style="grid-column:1/-1;text-align:center;color:var(--ink-soft);">กำลังอัปเดตข้อมูลบริการ</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="section" id="news">
  <div class="container">
    <div class="section-head">
      <h2>กิจกรรมและข่าวสาร</h2>
      <p>ความเคลื่อนไหวล่าสุดจากศูนย์ดูแลผู้สูงอายุฮักเนอร์สซิ่งโฮม</p>
    </div>
    <div class="grid-3">
      <?php foreach ($latestNews as $n): ?>
      <a class="card" href="news.php?id=<?= (int)$n['news_id'] ?>">
        <img src="<?= htmlspecialchars($n['cover_image'] ?: '/assets/images/news-placeholder.jpg') ?>" alt="<?= htmlspecialchars($n['title']) ?>">
        <div class="card-body">
          <span class="tag"><?= htmlspecialchars($n['category'] ?: 'ข่าวสาร') ?></span>
          <h3><?= htmlspecialchars($n['title']) ?></h3>
          <?php if ($n['event_date']): ?>
          <p class="card-date"><?= date('j F Y', strtotime($n['event_date'])) ?></p>
          <?php endif; ?>
        </div>
      </a>
      <?php endforeach; ?>
      <?php if (!$latestNews): ?>
        <p style="grid-column:1/-1;text-align:center;color:var(--ink-soft);">ยังไม่มีข่าวสารในขณะนี้</p>
      <?php endif; ?>
    </div>
    <div class="center"><a class="btn btn-ghost btn-lg" href="news.php">ดูข่าวทั้งหมด</a></div>
  </div>
</section>

<section class="cta" id="contact">
  <div class="container">
    <h2>อยากให้เราดูแลคนที่คุณรักใช่ไหม</h2>
    <p>นัดเข้าชมสถานที่ได้ทุกวัน เวลา 09.00–17.00 น. หรือโทรสอบถามค่าบริการและห้องว่างได้ทันที</p>
    <div class="hero-cta" style="justify-content:center">
      <a class="btn btn-green btn-lg" href="tel:0430000000">โทร 043-000-000</a>
      <a class="btn btn-ghost btn-lg" href="contact.php">กรอกแบบฟอร์มนัดเยี่ยมชม</a>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
