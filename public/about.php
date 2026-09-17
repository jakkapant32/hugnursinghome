<?php
$page_title_key = 'page.about';
$active_page = 'about';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="section-head">
      <h2><?= hug_t('about.title') ?></h2>
      <p><?= hug_t('about.lead') ?></p>
    </div>

    <div class="grid-2" style="margin-bottom:56px;">
      <div>
        <h3><?= hug_t('about.history_title') ?></h3>
        <p><?= hug_t('about.history_body') ?></p>
      </div>
      <div>
        <h3><?= hug_t('about.vision_title') ?></h3>
        <p><?= hug_t('about.vision_body') ?></p>
      </div>
    </div>

    <div class="section-head">
      <h2><?= hug_t('about.mission_heading') ?></h2>
    </div>
    <div class="grid-3">
      <div class="card"><div class="card-body">
        <h3><?= hug_t('about.m1_title') ?></h3>
        <p><?= hug_t('about.m1_body') ?></p>
      </div></div>
      <div class="card"><div class="card-body">
        <h3><?= hug_t('about.m2_title') ?></h3>
        <p><?= hug_t('about.m2_body') ?></p>
      </div></div>
      <div class="card"><div class="card-body">
        <h3><?= hug_t('about.m3_title') ?></h3>
        <p><?= hug_t('about.m3_body') ?></p>
      </div></div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
