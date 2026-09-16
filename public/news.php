<?php
require_once __DIR__ . '/../includes/db.php';

$news = $pdo->query(
    "SELECT news_id, category, title, content, event_date, cover_image FROM news
     WHERE is_published = 1 ORDER BY COALESCE(event_date, created_at) DESC"
)->fetchAll();

$page_title = 'ข่าวสารและกิจกรรม';
$active_page = 'news';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="section-head">
      <h2>ข่าวสารและกิจกรรม</h2>
      <p>ติดตามความเคลื่อนไหวและกิจกรรมล่าสุดของศูนย์</p>
    </div>
    <div class="grid-3">
      <?php foreach ($news as $n): ?>
      <div class="card">
        <img src="<?= htmlspecialchars($n['cover_image'] ?: '/assets/images/news-placeholder.jpg') ?>" alt="<?= htmlspecialchars($n['title']) ?>">
        <div class="card-body">
          <span class="date"><?= $n['event_date'] ? date('d M Y', strtotime($n['event_date'])) : htmlspecialchars($n['category']) ?></span>
          <h3><?= htmlspecialchars($n['title']) ?></h3>
          <p><?= htmlspecialchars(mb_substr($n['content'], 0, 100)) ?>...</p>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if (!$news): ?>
        <p>ยังไม่มีข่าวสารในขณะนี้</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
