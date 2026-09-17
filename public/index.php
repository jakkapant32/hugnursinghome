<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/content_i18n.php';

$services = $pdo->query(
    "SELECT title, description, icon FROM services WHERE is_published = 1 ORDER BY sort_order ASC LIMIT 5"
)->fetchAll();

$latestNews = $pdo->query(
    "SELECT news_id, category, title, event_date, cover_image FROM news
     WHERE is_published = 1 ORDER BY COALESCE(event_date, created_at) DESC LIMIT 3"
)->fetchAll();

$page_title_key = 'page.home';
$active_page = 'home';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../includes/header.php';

$heroImg = '/assets/images/hero-care.jpg';
?>

<section class="hero">
  <div class="hero-photo">
    <img src="<?= htmlspecialchars($heroImg) ?>" alt="<?= htmlspecialchars(hug_t('home.hero_alt')) ?>" loading="eager"
         onerror="this.src='https://images.pexels.com/photos/18271866/pexels-photo-18271866.jpeg?auto=compress&cs=tinysrgb&w=1600'">
  </div>
  <div class="container">
    <div class="hero-inner hero-copy">
      <h1><?= hug_t('home.hero_title_1') ?><span class="line2 accent"><?= hug_t('home.hero_title_2') ?></span></h1>
      <p class="lead"><?= hug_t('home.hero_lead') ?></p>
      <p class="sub"><?= hug_t('home.hero_sub') ?></p>
      <div class="hero-actions hero-cta">
        <a href="services.php" class="btn btn-green btn-lg"><?= hug_t('home.cta_services') ?></a>
        <a href="contact.php" class="btn btn-rose btn-lg"><?= hug_t('home.cta_visit') ?></a>
      </div>
      <div class="hero-stats">
        <div><strong>52</strong><span><?= hug_t('home.stat_residents') ?></span></div>
        <div><strong>18</strong><span><?= hug_t('home.stat_staff') ?></span></div>
        <div><strong><?= hug_t('home.stat_hours_val') ?></strong><span><?= hug_t('home.stat_hours') ?></span></div>
      </div>
    </div>
  </div>
</section>

<section class="section section-alt services" id="services">
  <div class="container">
    <div class="section-head">
      <h2><?= hug_t('home.services_title') ?></h2>
      <p><?= hug_t('home.services_lead') ?></p>
    </div>
    <div class="services-strip">
      <?php foreach ($services as $s): ?>
      <?php $s = hug_localize_service($s); ?>
      <div class="service-tile">
        <div class="icon"><?= hug_icon('heart', 22) ?></div>
        <h3><?= htmlspecialchars($s['title']) ?></h3>
        <p><?= htmlspecialchars($s['description']) ?></p>
      </div>
      <?php endforeach; ?>
      <?php if (!$services): ?>
        <p style="grid-column:1/-1;text-align:center;color:var(--ink-soft);"><?= hug_t('home.services_empty') ?></p>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="section" id="news">
  <div class="container">
    <div class="section-head">
      <h2><?= hug_t('home.news_title') ?></h2>
      <p><?= hug_t('home.news_lead') ?></p>
    </div>
    <div class="grid-3">
      <?php foreach ($latestNews as $n): ?>
      <?php $n = hug_localize_news_card($n); ?>
      <a class="card" href="news.php?id=<?= (int)$n['news_id'] ?>">
        <img src="<?= htmlspecialchars($n['cover_image'] ?: '/assets/images/news-placeholder.jpg') ?>" alt="<?= htmlspecialchars($n['title']) ?>">
        <div class="card-body">
          <span class="tag"><?= htmlspecialchars($n['category'] ?: hug_t('category.ข่าวสาร')) ?></span>
          <h3><?= htmlspecialchars($n['title']) ?></h3>
          <?php if ($n['event_date']): ?>
          <p class="card-date"><?= hug_format_display_date($n['event_date']) ?></p>
          <?php endif; ?>
        </div>
      </a>
      <?php endforeach; ?>
      <?php if (!$latestNews): ?>
        <p style="grid-column:1/-1;text-align:center;color:var(--ink-soft);"><?= hug_t('home.news_empty') ?></p>
      <?php endif; ?>
    </div>
    <div class="center"><a class="btn btn-ghost btn-lg" href="news.php"><?= hug_t('home.news_all') ?></a></div>
  </div>
</section>

<section class="cta" id="contact">
  <div class="container">
    <h2><?= hug_t('home.cta_title') ?></h2>
    <p><?= hug_t('home.cta_lead') ?></p>
    <div class="hero-cta" style="justify-content:center">
      <a class="btn btn-green btn-lg" href="tel:0430000000"><?= hug_t('home.cta_call') ?></a>
      <a class="btn btn-ghost btn-lg" href="contact.php"><?= hug_t('home.cta_form') ?></a>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
