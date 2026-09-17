<?php
require_once __DIR__ . '/../includes/db.php';

$detailId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$article = null;

if ($detailId > 0) {
    $stmt = $pdo->prepare(
        'SELECT news_id, category, title, content, event_date, cover_image, created_at
         FROM news WHERE news_id = :id AND is_published = 1'
    );
    $stmt->execute([':id' => $detailId]);
    $article = $stmt->fetch();
}

if ($article) {
    $page_title = $article['title'];
    $active_page = 'news';
    require_once __DIR__ . '/../includes/i18n.php';
    require_once __DIR__ . '/../includes/header.php';
    $cover = $article['cover_image'] ?: '/assets/images/news-placeholder.jpg';
    ?>
<section class="section">
  <div class="container" style="max-width:720px;">
    <p><a href="news.php"><?= hug_t('news.back') ?></a></p>
    <article class="card" style="overflow:hidden;">
      <img src="<?= htmlspecialchars($cover) ?>" alt="<?= htmlspecialchars($article['title']) ?>" style="width:100%;max-height:320px;object-fit:cover;">
      <div class="card-body">
        <span class="date"><?= $article['event_date'] ? date('d M Y', strtotime($article['event_date'])) : htmlspecialchars($article['category']) ?></span>
        <h1 style="margin:.5rem 0 1rem;font-size:1.75rem;"><?= htmlspecialchars($article['title']) ?></h1>
        <div style="line-height:1.75;color:var(--ink-soft);white-space:pre-wrap;"><?= nl2br(htmlspecialchars($article['content'])) ?></div>
      </div>
    </article>
  </div>
</section>
    <?php
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$news = $pdo->query(
    "SELECT news_id, category, title, content, event_date, cover_image FROM news
     WHERE is_published = 1 ORDER BY COALESCE(event_date, created_at) DESC"
)->fetchAll();

$page_title_key = 'page.news';
$active_page = 'news';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="section-head">
      <h2><?= hug_t('news.title') ?></h2>
      <p><?= hug_t('news.lead') ?></p>
    </div>
    <div class="grid-3">
      <?php foreach ($news as $n): ?>
      <a class="card" href="news.php?id=<?= (int)$n['news_id'] ?>" style="text-decoration:none;color:inherit;">
        <img src="<?= htmlspecialchars($n['cover_image'] ?: '/assets/images/news-placeholder.jpg') ?>" alt="<?= htmlspecialchars($n['title']) ?>">
        <div class="card-body">
          <span class="date"><?= $n['event_date'] ? date('d M Y', strtotime($n['event_date'])) : htmlspecialchars($n['category']) ?></span>
          <h3><?= htmlspecialchars($n['title']) ?></h3>
          <p><?= htmlspecialchars(mb_substr($n['content'], 0, 100)) ?>...</p>
        </div>
      </a>
      <?php endforeach; ?>
      <?php if (!$news): ?>
        <p>ยังไม่มีข่าวสารในขณะนี้</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
