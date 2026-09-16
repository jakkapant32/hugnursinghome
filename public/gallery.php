<?php
require_once __DIR__ . '/../includes/db.php';

$images = $pdo->query(
    "SELECT image_path, caption FROM gallery ORDER BY uploaded_at DESC LIMIT 24"
)->fetchAll();

$page_title = 'แกลเลอรี';
$active_page = 'gallery';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="section-head">
      <h2>แกลเลอรีภาพกิจกรรม</h2>
      <p>ภาพบรรยากาศและกิจกรรมต่าง ๆ ภายในศูนย์</p>
    </div>
    <div class="gallery-grid">
      <?php foreach ($images as $img): ?>
        <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="<?= htmlspecialchars($img['caption'] ?? '') ?>">
      <?php endforeach; ?>
      <?php if (!$images): ?>
        <p>ยังไม่มีรูปภาพในแกลเลอรี</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
