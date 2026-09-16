<?php
require_once __DIR__ . '/icons.php';
$active_page = $active_page ?? '';
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($page_title) ? htmlspecialchars($page_title) . ' | ' : '' ?>ฮักเนอร์สซิ่งโฮม</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;600;700&family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<header class="site-header">
  <nav class="nav container">
    <a href="index.php" class="brand">
      <span class="mark logo-mark"><?= hug_icon('heart', 22) ?></span>
      <span class="brand-text">ฮักเนอร์สซิ่งโฮม<small>HUG NURSING HOME</small></span>
    </a>
    <button type="button" class="nav-toggle" aria-label="เปิดเมนู"><?= hug_icon('menu', 24) ?></button>
    <ul class="nav-links">
      <li><a href="index.php" class="<?= $active_page === 'home' ? 'active' : '' ?>">หน้าแรก</a></li>
      <li><a href="about.php" class="<?= $active_page === 'about' ? 'active' : '' ?>">เกี่ยวกับเรา</a></li>
      <li><a href="services.php" class="<?= $active_page === 'services' ? 'active' : '' ?>">บริการของเรา</a></li>
      <li><a href="news.php" class="<?= $active_page === 'news' ? 'active' : '' ?>">ข่าวสาร</a></li>
      <li><a href="gallery.php" class="<?= $active_page === 'gallery' ? 'active' : '' ?>">แกลเลอรี</a></li>
      <li><a href="contact.php" class="<?= $active_page === 'contact' ? 'active' : '' ?>">ติดต่อเรา</a></li>
    </ul>
    <div class="nav-actions">
      <a href="login.php" class="btn btn-ghost">เข้าสู่ระบบ</a>
      <a href="register.php" class="btn btn-green">ลงทะเบียน</a>
    </div>
  </nav>
</header>
