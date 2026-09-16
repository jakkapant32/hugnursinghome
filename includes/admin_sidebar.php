<?php
require_once __DIR__ . '/icons.php';
$active = $active ?? '';

$nav = [
    ['dashboard', 'dashboard.php', 'home', 'หน้าหลัก'],
    ['residents', 'manage_residents.php', 'elder', 'ข้อมูลผู้รับบริการ'],
    ['staff', 'manage_staff.php', 'users', 'พนักงาน'],
    ['services', 'manage_services.php', 'service', 'บริการ'],
    ['activities', 'manage_activities.php', 'calendar', 'กิจกรรม'],
    ['news', 'manage_news.php', 'news', 'ข่าวสาร'],
    ['gallery', 'manage_gallery.php', 'image', 'แกลเลอรี'],
    ['reports', 'reports.php', 'chart', 'รายงาน'],
    ['settings', 'settings.php', 'settings', 'ตั้งค่า'],
];
?>
<aside class="admin-sidebar">
  <div class="admin-brand">
    <span class="mark"><?= hug_icon('heart', 20) ?></span>
    Hug Nursing Home
  </div>
  <ul class="admin-nav">
    <?php foreach ($nav as [$key, $href, $icon, $label]): ?>
    <li>
      <a href="<?= htmlspecialchars($href) ?>" class="<?= $active === $key ? 'active' : '' ?>">
        <span class="nav-ico"><?= hug_icon($icon, 18) ?></span>
        <?= htmlspecialchars($label) ?>
      </a>
    </li>
    <?php endforeach; ?>
  </ul>
</aside>
