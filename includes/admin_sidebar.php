<?php
require_once __DIR__ . '/icons.php';
$active = $active ?? '';
$isAdmin = (($_SESSION['role'] ?? '') === 'admin');
$navItems = require __DIR__ . '/admin_nav.php';
?>
<aside class="admin-sidebar">
  <div class="admin-brand">
    <span class="mark"><?= hug_icon('heart', 20) ?></span>
    Hug Nursing Home
  </div>
  <ul class="admin-nav">
    <?php foreach ($navItems as [$key, $href, $icon, $label, $adminOnly]): ?>
      <?php if ($adminOnly && !$isAdmin) continue; ?>
    <li>
      <a href="<?= htmlspecialchars($href) ?>" class="<?= $active === $key ? 'active' : '' ?>">
        <span class="nav-ico"><?= hug_icon($icon, 18) ?></span>
        <?= htmlspecialchars($label) ?>
      </a>
    </li>
    <?php endforeach; ?>
  </ul>
</aside>
