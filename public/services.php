<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/content_i18n.php';

$services = $pdo->query(
    "SELECT title, description, icon FROM services WHERE is_published = 1 ORDER BY sort_order ASC"
)->fetchAll();

$page_title_key = 'page.services';
$active_page = 'services';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="section-head">
      <h2><?= hug_t('services.title') ?></h2>
      <p><?= hug_t('services.lead') ?></p>
    </div>
    <div class="grid-3">
      <?php foreach ($services as $s): ?>
      <?php $s = hug_localize_service($s); ?>
      <div class="card">
        <div class="card-body" style="text-align:center;">
          <div class="icon"><?= hug_icon('heart', 22) ?></div>
          <h3><?= htmlspecialchars($s['title']) ?></h3>
          <p><?= htmlspecialchars($s['description']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
