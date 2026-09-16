<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/icons.php';

$active = 'reports';

$residentsActive = (int)$pdo->query("SELECT COUNT(*) FROM residents WHERE status = 'กำลังรับบริการ'")->fetchColumn();
$residentsAll    = (int)$pdo->query('SELECT COUNT(*) FROM residents')->fetchColumn();
$staffActive     = (int)$pdo->query('SELECT COUNT(*) FROM staff WHERE is_active = 1')->fetchColumn();
$newsPub         = (int)$pdo->query('SELECT COUNT(*) FROM news WHERE is_published = 1')->fetchColumn();
$galleryCount    = (int)$pdo->query('SELECT COUNT(*) FROM gallery')->fetchColumn();
$messagesUnread  = (int)$pdo->query('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0')->fetchColumn();
$messagesAll     = (int)$pdo->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();

$byGender = $pdo->query(
    "SELECT gender, COUNT(*) AS cnt FROM residents WHERE status = 'กำลังรับบริการ' GROUP BY gender"
)->fetchAll(PDO::FETCH_KEY_PAIR);

$recentMessages = $pdo->query(
    'SELECT sender_name, subject, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 8'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>รายงาน | Hug Nursing Home</title>
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="admin-shell">
  <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>
  <main class="admin-main">
    <?php $topbar_title = 'รายงานสรุป'; require __DIR__ . '/../includes/admin_topbar.php'; ?>
    <div class="admin-content">
      <div class="stat-row">
        <div class="stat-card">
          <div class="icon"><?= hug_icon('elder', 22) ?></div>
          <div><div class="label">ผู้รับบริการ (active)</div><div class="value"><?= $residentsActive ?> คน</div></div>
        </div>
        <div class="stat-card">
          <div class="icon amber"><?= hug_icon('users', 22) ?></div>
          <div><div class="label">พนักงาน</div><div class="value"><?= $staffActive ?> คน</div></div>
        </div>
        <div class="stat-card">
          <div class="icon rose"><?= hug_icon('news', 22) ?></div>
          <div><div class="label">ข่าวเผยแพร่</div><div class="value"><?= $newsPub ?> รายการ</div></div>
        </div>
        <div class="stat-card">
          <div class="icon icon-dark"><?= hug_icon('mail', 22) ?></div>
          <div><div class="label">ข้อความใหม่</div><div class="value"><?= $messagesUnread ?> / <?= $messagesAll ?></div></div>
        </div>
      </div>
      <div class="form-grid" style="align-items:start;">
        <div class="panel">
          <div class="panel-head"><h3>สรุปข้อมูล</h3></div>
          <ul style="list-style:none;padding:0;margin:0;font-size:.93rem;">
            <li style="padding:8px 0;border-bottom:1px solid var(--line);">ผู้รับบริการทั้งหมดในระบบ: <strong><?= $residentsAll ?></strong></li>
            <li style="padding:8px 0;border-bottom:1px solid var(--line);">ชาย (กำลังรับบริการ): <strong><?= (int)($byGender['ชาย'] ?? 0) ?></strong></li>
            <li style="padding:8px 0;border-bottom:1px solid var(--line);">หญิง (กำลังรับบริการ): <strong><?= (int)($byGender['หญิง'] ?? 0) ?></strong></li>
            <li style="padding:8px 0;">รูปในแกลเลอรี: <strong><?= $galleryCount ?></strong></li>
          </ul>
        </div>
        <div class="panel">
          <div class="panel-head"><h3>ข้อความติดต่อล่าสุด</h3></div>
          <ul style="list-style:none;padding:0;margin:0;font-size:.92rem;">
            <?php foreach ($recentMessages as $m): ?>
            <li style="padding:9px 0;border-bottom:1px solid var(--line);">
              <strong><?= htmlspecialchars($m['sender_name']) ?></strong>
              — <?= htmlspecialchars($m['subject'] ?: '(ไม่มีหัวข้อ)') ?>
              <span style="color:var(--muted);display:block;font-size:.82rem;"><?= date('d M Y H:i', strtotime($m['created_at'])) ?></span>
            </li>
            <?php endforeach; ?>
            <?php if (!$recentMessages): ?><li>ยังไม่มีข้อความ</li><?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
  </main>
</div>
</body>
</html>
