<?php
require_once __DIR__ . '/i18n.php';
require_once __DIR__ . '/icons.php';
$active_page = $active_page ?? '';
if (!empty($page_title_key)) {
    $page_title = hug_t($page_title_key);
}
$lang = hug_current_lang();
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($page_title) ? htmlspecialchars($page_title) . ' | ' : '' ?><?= hug_t('site.brand_sub') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;600;700&family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/style.css">
<script>
(function(){
  var t=localStorage.getItem('hug-theme');
  if(t==='dark') document.documentElement.setAttribute('data-theme','dark');
  var z=parseFloat(localStorage.getItem('hug-zoom')||'1');
  if(z&&z!==1){
    document.documentElement.dataset.zoom=String(z);
    document.documentElement.style.fontSize=(16*z)+'px';
  }
})();
</script>
</head>
<body>

<header class="site-header">
  <nav class="nav container">
    <a href="index.php" class="brand">
      <span class="mark logo-mark"><?= hug_icon('heart', 22) ?></span>
      <span class="brand-text"><?= hug_t('site.brand_name') ?><small><?= hug_t('site.brand_sub') ?></small></span>
    </a>
    <button type="button" class="nav-toggle" aria-label="เปิดเมนู"><?= hug_icon('menu', 24) ?></button>
    <ul class="nav-links">
      <li><a href="index.php" class="<?= $active_page === 'home' ? 'active' : '' ?>"><?= hug_t('nav.home') ?></a></li>
      <li><a href="about.php" class="<?= $active_page === 'about' ? 'active' : '' ?>"><?= hug_t('nav.about') ?></a></li>
      <li><a href="services.php" class="<?= $active_page === 'services' ? 'active' : '' ?>"><?= hug_t('nav.services') ?></a></li>
      <li><a href="news.php" class="<?= $active_page === 'news' ? 'active' : '' ?>"><?= hug_t('nav.news') ?></a></li>
      <li><a href="gallery.php" class="<?= $active_page === 'gallery' ? 'active' : '' ?>"><?= hug_t('nav.gallery') ?></a></li>
      <li><a href="contact.php" class="<?= $active_page === 'contact' ? 'active' : '' ?>"><?= hug_t('nav.contact') ?></a></li>
    </ul>
    <div class="nav-actions">
      <a href="/admin/login.php" class="btn btn-green"><?= hug_t('nav.admin_login') ?></a>
      <div class="nav-menu-dropdown" id="nav-menu-dropdown">
        <button type="button" class="nav-menu-trigger" id="nav-menu-trigger"
                aria-expanded="false" aria-controls="nav-menu-panel"
                aria-label="<?= htmlspecialchars(hug_t('prefs.menu_open')) ?>">
          <?= hug_icon('settings', 20) ?>
        </button>
        <div class="nav-menu-panel" id="nav-menu-panel" role="menu" hidden>
          <p class="nav-menu-title"><?= hug_t('prefs.label') ?></p>
          <div class="nav-menu-row">
            <span class="nav-menu-label"><?= hug_t('prefs.theme') ?></span>
            <button type="button" class="pref-btn" id="pref-theme" aria-pressed="false"
                    data-title-dark="<?= htmlspecialchars(hug_t('prefs.theme_dark')) ?>"
                    data-title-light="<?= htmlspecialchars(hug_t('prefs.theme_light')) ?>"
                    title="<?= htmlspecialchars(hug_t('prefs.theme')) ?>">
              <span class="pref-icon pref-icon-light"><?= hug_icon('sun', 18) ?></span>
              <span class="pref-icon pref-icon-dark"><?= hug_icon('moon', 18) ?></span>
            </button>
          </div>
          <div class="nav-menu-row">
            <span class="nav-menu-label"><?= hug_t('prefs.lang') ?></span>
            <div class="lang-switch" role="group" aria-label="<?= htmlspecialchars(hug_t('prefs.lang')) ?>">
              <a href="<?= htmlspecialchars(hug_lang_url('th')) ?>" class="lang-chip<?= $lang === 'th' ? ' active' : '' ?>" hreflang="th">TH</a>
              <a href="<?= htmlspecialchars(hug_lang_url('en')) ?>" class="lang-chip<?= $lang === 'en' ? ' active' : '' ?>" hreflang="en">EN</a>
            </div>
          </div>
          <div class="nav-menu-row">
            <span class="nav-menu-label"><?= hug_t('prefs.zoom_reset') ?></span>
            <div class="zoom-group" role="group" aria-label="Zoom">
              <button type="button" class="pref-btn pref-btn-text" id="pref-zoom-out" title="<?= htmlspecialchars(hug_t('prefs.zoom_out')) ?>">A−</button>
              <button type="button" class="pref-btn pref-btn-text" id="pref-zoom-reset" title="<?= htmlspecialchars(hug_t('prefs.zoom_reset')) ?>"><span id="pref-zoom-label">100%</span></button>
              <button type="button" class="pref-btn pref-btn-text" id="pref-zoom-in" title="<?= htmlspecialchars(hug_t('prefs.zoom_in')) ?>">A+</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</header>
