<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../includes/upload_helpers.php';

$active = 'news';
$errors = [];
$editRow = null;
$categories = ['ข่าวสาร', 'กิจกรรม', 'ประกาศ'];

if (isset($_GET['delete'])) {
    $pdo->prepare('DELETE FROM news WHERE news_id = :id')->execute([':id' => (int)$_GET['delete']]);
    header('Location: manage_news.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = $_POST['news_id'] ?? '';
    $category     = $_POST['category'] ?? 'ข่าวสาร';
    $title        = trim($_POST['title'] ?? '');
    $content      = trim($_POST['content'] ?? '');
    $existingCover = null;
    if ($id) {
        $row = $pdo->prepare('SELECT cover_image FROM news WHERE news_id = :id');
        $row->execute([':id' => (int)$id]);
        $existingCover = $row->fetchColumn() ?: null;
    }
    $cover_image = hug_resolve_image_path(
        $_FILES['image_file'] ?? null,
        'news',
        $_POST['cover_image'] ?? '',
        is_string($existingCover) ? $existingCover : null,
        $errors
    );
    $event_date   = $_POST['event_date'] ?: null;
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    if ($title === '') $errors[] = 'กรุณากรอกหัวข้อ';
    if ($content === '') $errors[] = 'กรุณากรอกเนื้อหา';
    if (!in_array($category, $categories, true)) $errors[] = 'หมวดหมู่ไม่ถูกต้อง';

    if (!$errors) {
        if ($id) {
            $stmt = $pdo->prepare(
                'UPDATE news SET category=:c, title=:t, content=:body, cover_image=:img,
                 event_date=:ed, is_published=:p WHERE news_id=:id'
            );
            $stmt->execute([
                ':c'=>$category, ':t'=>$title, ':body'=>$content,
                ':img'=>$cover_image ?: null, ':ed'=>$event_date, ':p'=>$is_published, ':id'=>$id,
            ]);
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO news (category, title, content, cover_image, event_date, is_published, created_by)
                 VALUES (:c, :t, :body, :img, :ed, :p, :uid)'
            );
            $stmt->execute([
                ':c'=>$category, ':t'=>$title, ':body'=>$content,
                ':img'=>$cover_image ?: null, ':ed'=>$event_date, ':p'=>$is_published,
                ':uid'=>$_SESSION['user_id'],
            ]);
        }
        header('Location: manage_news.php');
        exit;
    }
}

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM news WHERE news_id = :id');
    $stmt->execute([':id' => (int)$_GET['id']]);
    $editRow = $stmt->fetch();
}

$items = $pdo->query('SELECT * FROM news ORDER BY created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ข่าวสาร | Hug Nursing Home</title>
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="admin-shell">
  <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>
  <main class="admin-main">
    <?php $topbar_title = 'จัดการข่าวสาร'; require __DIR__ . '/../includes/admin_topbar.php'; ?>
    <div class="admin-content">
      <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><h3><?= $editRow ? 'แก้ไขข่าว' : 'เพิ่มข่าว' ?></h3></div>
        <?php foreach ($errors as $e): ?><p class="error-text"><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="news_id" value="<?= htmlspecialchars($editRow['news_id'] ?? '') ?>">
          <div class="form-grid">
            <div class="form-field"><label>หมวด</label>
              <select name="category">
                <?php foreach ($categories as $c): ?>
                <option value="<?= $c ?>" <?= (($editRow['category'] ?? '') === $c) ? 'selected' : '' ?>><?= $c ?></option>
                <?php endforeach; ?>
              </select></div>
            <div class="form-field"><label>วันที่กิจกรรม/ข่าว</label>
              <input type="date" name="event_date" value="<?= htmlspecialchars($editRow['event_date'] ?? '') ?>"></div>
            <div class="form-field" style="grid-column:1/-1;"><label>หัวข้อ</label>
              <input type="text" name="title" required value="<?= htmlspecialchars($editRow['title'] ?? '') ?>"></div>
            <div class="form-field"><label>อัปโหลดรูปปก</label>
              <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp"></div>
            <div class="form-field"><label>หรือ URL / path</label>
              <input type="text" name="cover_image" value="<?= htmlspecialchars($editRow['cover_image'] ?? '') ?>"
                     placeholder="https://... หรือ /assets/uploads/..."></div>
            <?php
            $uploadPreviewSrc = $editRow['cover_image'] ?? '';
            require __DIR__ . '/../includes/admin_image_preview.php';
            ?>
            <div class="form-field" style="grid-column:1/-1;"><label>เนื้อหา</label>
              <textarea name="content" rows="5" required><?= htmlspecialchars($editRow['content'] ?? '') ?></textarea></div>
            <div class="form-field" style="display:flex;align-items:center;gap:8px;">
              <input type="checkbox" name="is_published" id="pub" <?= !isset($editRow) || !empty($editRow['is_published']) ? 'checked' : '' ?>>
              <label for="pub" style="margin:0;">เผยแพร่</label>
            </div>
          </div>
          <button type="submit" class="btn btn-primary" style="width:auto;"><?= $editRow ? 'บันทึก' : 'เพิ่ม' ?></button>
          <?php if ($editRow): ?><a href="manage_news.php" class="btn" style="background:var(--line);">ยกเลิก</a><?php endif; ?>
        </form>
      </div>
      <div class="panel">
        <table class="data-table">
          <thead><tr><th>หมวด</th><th>หัวข้อ</th><th>วันที่</th><th>เผยแพร่</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($items as $n): ?>
            <tr>
              <td><?= htmlspecialchars($n['category']) ?></td>
              <td><?= htmlspecialchars($n['title']) ?></td>
              <td><?= $n['event_date'] ? date('d M Y', strtotime($n['event_date'])) : '—' ?></td>
              <td><span class="badge <?= $n['is_published'] ? 'badge-active' : 'badge-inactive' ?>"><?= $n['is_published'] ? 'ใช่' : 'ไม่' ?></span></td>
              <td>
                <a class="icon-btn" href="?id=<?= (int)$n['news_id'] ?>"><?= hug_icon('edit', 16) ?></a>
                <a class="icon-btn icon-btn-danger" href="?delete=<?= (int)$n['news_id'] ?>" onclick="return confirm('ลบ?');"><?= hug_icon('trash', 16) ?></a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
<script src="/assets/js/admin-image-preview.js"></script>
</body>
</html>
