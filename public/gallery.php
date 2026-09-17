<?php
require_once __DIR__ . '/../includes/db.php';

$images = $pdo->query(
    "SELECT image_path, caption FROM gallery ORDER BY uploaded_at DESC LIMIT 24"
)->fetchAll();

$page_title_key = 'page.gallery';
$active_page = 'gallery';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="section-head">
      <h2><?= hug_t('gallery.title') ?></h2>
      <p><?= hug_t('gallery.lead') ?></p>
    </div>
    <div class="gallery-grid">
      <?php foreach ($images as $img): ?>
        <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="<?= htmlspecialchars($img['caption'] ?? '') ?>">
      <?php endforeach; ?>
      <?php if (!$images): ?>
        <p><?= hug_t('gallery.empty') ?></p>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
