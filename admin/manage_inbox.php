<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/icons.php';

$active = 'inbox';

if (isset($_GET['read'])) {
    $pdo->prepare('UPDATE contact_messages SET is_read = 1 WHERE message_id = :id')
        ->execute([':id' => (int)$_GET['read']]);
    header('Location: manage_inbox.php?id=' . (int)$_GET['read']);
    exit;
}

$view = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM contact_messages WHERE message_id = :id');
    $stmt->execute([':id' => (int)$_GET['id']]);
    $view = $stmt->fetch();
    if ($view && !$view['is_read']) {
        $pdo->prepare('UPDATE contact_messages SET is_read = 1 WHERE message_id = :id')
            ->execute([':id' => (int)$view['message_id']]);
        $view['is_read'] = 1;
    }
}

$messages = $pdo->query(
    'SELECT message_id, sender_name, subject, phone, email, is_read, created_at
     FROM contact_messages ORDER BY is_read ASC, created_at DESC LIMIT 100'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ข้อความติดต่อ | Hug Nursing Home</title>
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="admin-shell">
  <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>
  <main class="admin-main">
    <?php $topbar_title = 'ข้อความจากหน้าเว็บ'; require __DIR__ . '/../includes/admin_topbar.php'; ?>
    <div class="admin-content">
      <div class="form-grid" style="align-items:start;">
        <div class="panel">
          <div class="panel-head"><h3>กล่องข้อความ</h3></div>
          <table class="data-table">
            <thead><tr><th>จาก</th><th>หัวข้อ</th><th>วันที่</th><th></th></tr></thead>
            <tbody>
              <?php foreach ($messages as $m): ?>
              <tr style="<?= $m['is_read'] ? '' : 'font-weight:600;' ?>">
                <td><?= htmlspecialchars($m['sender_name']) ?></td>
                <td><?= htmlspecialchars($m['subject'] ?: '(ไม่มีหัวข้อ)') ?></td>
                <td><?= date('d M Y', strtotime($m['created_at'])) ?></td>
                <td><a class="icon-btn" href="?id=<?= (int)$m['message_id'] ?>"><?= hug_icon('edit', 16) ?></a></td>
              </tr>
              <?php endforeach; ?>
              <?php if (!$messages): ?><tr><td colspan="4">ยังไม่มีข้อความ</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
        <div class="panel">
          <div class="panel-head"><h3>รายละเอียด</h3></div>
          <?php if ($view): ?>
            <p><strong><?= htmlspecialchars($view['sender_name']) ?></strong></p>
            <p style="font-size:.9rem;color:var(--muted);">
              <?= htmlspecialchars($view['phone'] ?? '') ?>
              <?= $view['email'] ? ' · ' . htmlspecialchars($view['email']) : '' ?>
            </p>
            <p style="font-size:.9rem;"><?= date('d M Y H:i', strtotime($view['created_at'])) ?></p>
            <?php if ($view['subject']): ?><p><strong>หัวข้อ:</strong> <?= htmlspecialchars($view['subject']) ?></p><?php endif; ?>
            <div style="margin-top:16px;padding:14px;background:var(--cream);border-radius:10px;white-space:pre-wrap;"><?= htmlspecialchars($view['message']) ?></div>
          <?php else: ?>
            <p style="color:var(--muted);">เลือกข้อความจากรายการ</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </main>
</div>
</body>
</html>
