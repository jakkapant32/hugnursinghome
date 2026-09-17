<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/icons.php';

$active = 'search';
$q = trim($_GET['q'] ?? '');
$residents = $news = $messages = $records = [];

if ($q !== '') {
    $like = '%' . $q . '%';

    $stmt = $pdo->prepare(
        "SELECT resident_id, full_name, phone, status FROM residents
         WHERE full_name ILIKE :q OR phone ILIKE :q OR guardian_name ILIKE :q
         ORDER BY full_name LIMIT 30"
    );
    $stmt->execute([':q' => $like]);
    $residents = $stmt->fetchAll();

    $stmt = $pdo->prepare(
        "SELECT news_id, category, title, event_date FROM news
         WHERE title ILIKE :q OR content ILIKE :q ORDER BY created_at DESC LIMIT 20"
    );
    $stmt->execute([':q' => $like]);
    $news = $stmt->fetchAll();

    $stmt = $pdo->prepare(
        'SELECT message_id, sender_name, subject, created_at, is_read FROM contact_messages
         WHERE sender_name ILIKE :q OR subject ILIKE :q OR message ILIKE :q
         ORDER BY created_at DESC LIMIT 20'
    );
    $stmt->execute([':q' => $like]);
    $messages = $stmt->fetchAll();

    $stmt = $pdo->prepare(
        'SELECT sr.record_id, sr.record_date, sr.detail, r.full_name
         FROM service_records sr JOIN residents r ON r.resident_id = sr.resident_id
         WHERE sr.detail ILIKE :q OR r.full_name ILIKE :q
         ORDER BY sr.record_date DESC LIMIT 20'
    );
    $stmt->execute([':q' => $like]);
    $records = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ค้นหา | Hug Nursing Home</title>
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="admin-shell">
  <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>
  <main class="admin-main">
    <?php $topbar_title = 'ค้นหาข้อมูล'; require __DIR__ . '/../includes/admin_topbar.php'; ?>
    <div class="admin-content">
      <div class="panel" style="margin-bottom:20px;">
        <form method="get" style="display:flex;gap:10px;">
          <input type="search" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="ค้นหาชื่อผู้รับบริการ ข่าว ข้อความ บันทึกบริการ..."
                 style="flex:1;padding:12px 14px;border:1px solid var(--line);border-radius:10px;">
          <button type="submit" class="btn btn-primary" style="width:auto;padding:12px 24px;">ค้นหา</button>
        </form>
      </div>

      <?php if ($q !== ''): ?>
      <div class="form-grid" style="align-items:start;">
        <div class="panel">
          <h3>ผู้รับบริการ (<?= count($residents) ?>)</h3>
          <ul style="list-style:none;padding:0;margin:12px 0 0;font-size:.92rem;">
            <?php foreach ($residents as $r): ?>
            <li style="padding:8px 0;border-bottom:1px solid var(--line);">
              <a href="manage_residents.php?id=<?= (int)$r['resident_id'] ?>"><?= htmlspecialchars($r['full_name']) ?></a>
              <span style="color:var(--muted);"> — <?= htmlspecialchars($r['status']) ?></span>
            </li>
            <?php endforeach; ?>
            <?php if (!$residents): ?><li>ไม่พบ</li><?php endif; ?>
          </ul>
        </div>
        <div class="panel">
          <h3>ข่าว/กิจกรรม (<?= count($news) ?>)</h3>
          <ul style="list-style:none;padding:0;margin:12px 0 0;font-size:.92rem;">
            <?php foreach ($news as $n): ?>
            <li style="padding:8px 0;border-bottom:1px solid var(--line);">
              <a href="manage_news.php?id=<?= (int)$n['news_id'] ?>"><?= htmlspecialchars($n['title']) ?></a>
            </li>
            <?php endforeach; ?>
            <?php if (!$news): ?><li>ไม่พบ</li><?php endif; ?>
          </ul>
        </div>
        <div class="panel">
          <h3>ข้อความติดต่อ (<?= count($messages) ?>)</h3>
          <ul style="list-style:none;padding:0;margin:12px 0 0;font-size:.92rem;">
            <?php foreach ($messages as $m): ?>
            <li style="padding:8px 0;border-bottom:1px solid var(--line);">
              <a href="manage_inbox.php?id=<?= (int)$m['message_id'] ?>"><?= htmlspecialchars($m['sender_name']) ?></a>
            </li>
            <?php endforeach; ?>
            <?php if (!$messages): ?><li>ไม่พบ</li><?php endif; ?>
          </ul>
        </div>
        <div class="panel">
          <h3>บันทึกบริการ (<?= count($records) ?>)</h3>
          <ul style="list-style:none;padding:0;margin:12px 0 0;font-size:.92rem;">
            <?php foreach ($records as $rec): ?>
            <li style="padding:8px 0;border-bottom:1px solid var(--line);">
              <a href="manage_service_records.php?id=<?= (int)$rec['record_id'] ?>"><?= htmlspecialchars($rec['full_name']) ?></a>
              <span style="color:var(--muted);"> — <?= date('d M Y', strtotime($rec['record_date'])) ?></span>
            </li>
            <?php endforeach; ?>
            <?php if (!$records): ?><li>ไม่พบ</li><?php endif; ?>
          </ul>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </main>
</div>
</body>
</html>
