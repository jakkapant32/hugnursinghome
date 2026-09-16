<?php
/** กิจกรรม = ข่าวหมวด "กิจกรรม" — ใช้ฟอร์มเดียวกับ manage_news แต่กรองหมวด */
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/icons.php';

$active = 'activities';
$category = 'กิจกรรม';
$errors = [];
$editRow = null;

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM news WHERE news_id = :id AND category = 'กิจกรรม'")->execute([':id' => (int)$_GET['delete']]);
    header('Location: manage_activities.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = $_POST['news_id'] ?? '';
    $title        = trim($_POST['title'] ?? '');
    $content      = trim($_POST['content'] ?? '');
    $cover_image  = trim($_POST['cover_image'] ?? '');
    $event_date   = $_POST['event_date'] ?: null;
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    if ($title === '') $errors[] = 'กรุณากรอกชื่อกิจกรรม';
    if ($content === '') $errors[] = 'กรุณากรอกรายละเอียด';

    if (!$errors) {
        if ($id) {
            $stmt = $pdo->prepare(
                "UPDATE news SET title=:t, content=:body, cover_image=:img, event_date=:ed,
                 is_published=:p, category='กิจกรรม' WHERE news_id=:id"
            );
            $stmt->execute([
                ':t'=>$title, ':body'=>$content, ':img'=>$cover_image ?: null,
                ':ed'=>$event_date, ':p'=>$is_published, ':id'=>$id,
            ]);
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO news (category, title, content, cover_image, event_date, is_published, created_by)
                 VALUES ('กิจกรรม', :t, :body, :img, :ed, :p, :uid)"
            );
            $stmt->execute([
                ':t'=>$title, ':body'=>$content, ':img'=>$cover_image ?: null,
                ':ed'=>$event_date, ':p'=>$is_published, ':uid'=>$_SESSION['user_id'],
            ]);
        }
        header('Location: manage_activities.php');
        exit;
    }
}

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE news_id = :id AND category = 'กิจกรรม'");
    $stmt->execute([':id' => (int)$_GET['id']]);
    $editRow = $stmt->fetch();
}

$items = $pdo->query("SELECT * FROM news WHERE category = 'กิจกรรม' ORDER BY event_date DESC NULLS LAST, created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>กิจกรรม | Hug Nursing Home</title>
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="admin-shell">
  <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>
  <main class="admin-main">
    <?php $topbar_title = 'จัดการกิจกรรม'; require __DIR__ . '/../includes/admin_topbar.php'; ?>
    <div class="admin-content">
      <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><h3><?= $editRow ? 'แก้ไขกิจกรรม' : 'เพิ่มกิจกรรม' ?></h3></div>
        <?php foreach ($errors as $e): ?><p class="error-text"><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
        <form method="post">
          <input type="hidden" name="news_id" value="<?= htmlspecialchars($editRow['news_id'] ?? '') ?>">
          <div class="form-grid">
            <div class="form-field"><label>วันที่จัดกิจกรรม</label>
              <input type="date" name="event_date" value="<?= htmlspecialchars($editRow['event_date'] ?? '') ?>"></div>
            <div class="form-field" style="grid-column:1/-1;"><label>ชื่อกิจกรรม</label>
              <input type="text" name="title" required value="<?= htmlspecialchars($editRow['title'] ?? '') ?>"></div>
            <div class="form-field" style="grid-column:1/-1;"><label>รูปปก</label>
              <input type="text" name="cover_image" value="<?= htmlspecialchars($editRow['cover_image'] ?? '') ?>"></div>
            <div class="form-field" style="grid-column:1/-1;"><label>รายละเอียด</label>
              <textarea name="content" rows="4" required><?= htmlspecialchars($editRow['content'] ?? '') ?></textarea></div>
            <div class="form-field" style="display:flex;align-items:center;gap:8px;">
              <input type="checkbox" name="is_published" id="pub" <?= !isset($editRow) || !empty($editRow['is_published']) ? 'checked' : '' ?>>
              <label for="pub" style="margin:0;">เผยแพร่หน้าเว็บ</label>
            </div>
          </div>
          <button type="submit" class="btn btn-primary" style="width:auto;"><?= $editRow ? 'บันทึก' : 'เพิ่ม' ?></button>
          <?php if ($editRow): ?><a href="manage_activities.php" class="btn" style="background:var(--line);">ยกเลิก</a><?php endif; ?>
        </form>
      </div>
      <div class="panel">
        <table class="data-table">
          <thead><tr><th>กิจกรรม</th><th>วันที่</th><th>เผยแพร่</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($items as $n): ?>
            <tr>
              <td><?= htmlspecialchars($n['title']) ?></td>
              <td><?= $n['event_date'] ? date('d M Y', strtotime($n['event_date'])) : '—' ?></td>
              <td><span class="badge <?= $n['is_published'] ? 'badge-active' : 'badge-inactive' ?>"><?= $n['is_published'] ? 'ใช่' : 'ไม่' ?></span></td>
              <td>
                <a class="icon-btn" href="?id=<?= (int)$n['news_id'] ?>"><?= hug_icon('edit', 16) ?></a>
                <a class="icon-btn icon-btn-danger" href="?delete=<?= (int)$n['news_id'] ?>" onclick="return confirm('ลบ?');"><?= hug_icon('trash', 16) ?></a>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$items): ?><tr><td colspan="4">ยังไม่มีกิจกรรม</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
</body>
</html>
