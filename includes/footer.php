<?php
if (!function_exists('hug_t')) {
    require_once __DIR__ . '/i18n.php';
}
require_once __DIR__ . '/icons.php';
if (!isset($pdo)) {
    require_once __DIR__ . '/db.php';
}
require_once __DIR__ . '/site_settings.php';
$site = hug_load_site_settings($pdo);
$lineUrl = $site['line_url'];
$phoneTel = hug_phone_tel($site['contact_phone']);
?>
<footer class="site-footer">
  <div class="container footer-grid">
    <div>
      <div class="f-logo">
        <span class="logo-mark mark"><?= hug_icon('heart', 22) ?></span>
        <span class="brand-text logo-text"><?= hug_t('site.brand_name') ?></span>
      </div>
      <p><?= hug_current_lang() === 'en' ? hug_t('footer.desc') : htmlspecialchars($site['footer_description']) ?></p>
    </div>
    <div>
      <h4><?= hug_t('footer.menu') ?></h4>
      <ul>
        <li><a href="about.php"><?= hug_t('nav.about') ?></a></li>
        <li><a href="services.php"><?= hug_t('nav.services') ?></a></li>
        <li><a href="news.php"><?= hug_t('nav.news') ?></a></li>
        <li><a href="gallery.php"><?= hug_t('nav.gallery') ?></a></li>
      </ul>
    </div>
    <div>
      <h4><?= hug_t('footer.services') ?></h4>
      <ul>
        <li><a href="services.php"><?= hug_t('footer.svc_health') ?></a></li>
        <li><a href="services.php"><?= hug_t('footer.svc_physio') ?></a></li>
        <li><a href="services.php"><?= hug_t('footer.svc_nutrition') ?></a></li>
        <li><a href="services.php"><?= hug_t('footer.svc_24h') ?></a></li>
      </ul>
    </div>
    <div>
      <h4><?= hug_t('footer.contact') ?></h4>
      <ul>
        <li><?= htmlspecialchars($site['contact_address']) ?></li>
        <li><a href="tel:<?= htmlspecialchars($phoneTel) ?>"><?= hug_t('footer.phone_prefix') ?> <?= htmlspecialchars($site['contact_phone']) ?></a></li>
        <li><a href="mailto:<?= htmlspecialchars($site['contact_email']) ?>"><?= htmlspecialchars($site['contact_email']) ?></a></li>
        <li>
          <a href="<?= htmlspecialchars($lineUrl) ?>" class="footer-line-link" target="_blank" rel="noopener noreferrer">
            <?= hug_line_icon(18) ?> <?= hug_t('footer.line') ?>
          </a>
        </li>
        <li><?= htmlspecialchars($site['contact_hours']) ?></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">&copy; <?= date('Y') + 543 ?> <?= hug_t('site.brand_name') ?> <?= hug_t('footer.rights') ?></div>
</footer>
<a href="<?= htmlspecialchars($lineUrl) ?>" class="line-fab" target="_blank" rel="noopener noreferrer" aria-label="แชทกับเราทาง Line">
  <?= hug_line_icon(32) ?>
</a>
<script src="/assets/js/prefs.js"></script>
<script src="/assets/js/main.js"></script>
</body>
</html>
