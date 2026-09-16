<?php
require_once __DIR__ . '/icons.php';
?>
<div class="admin-topbar">
  <h2 style="margin:0;"><?= htmlspecialchars($topbar_title ?? 'แดชบอร์ด') ?></h2>
  <div class="who">
    <div class="avatar"><?= hug_icon('user-badge', 18) ?></div>
    <span><?= htmlspecialchars($_SESSION['full_name']) ?> · <a href="logout.php">ออกจากระบบ</a></span>
  </div>
</div>
