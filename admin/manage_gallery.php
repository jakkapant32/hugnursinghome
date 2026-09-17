<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../includes/upload_helpers.php';

$active = 'gallery';
$errors = [];
$editRow = null;

if (isset($_GET['delete'])) {
    $pdo->prepare('DELETE FROM gallery WHERE gallery_id = :id')->execute([':id' => (int)$_GET['delete']]);
    header('Location: manage_gallery.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id         = $_POST['gallery_id'] ?? '';
    $album_name = trim($_POST['album_name'] ?? '');
    $existingPath = null;
    if ($id) {
        $row = $pdo->prepare('SELECT image_path FROM gallery WHERE gallery_id = :id');
        $row->execute([':id' => (int)$id]);
        $existingPath = $row->fetchColumn() ?: null;
    }
    $image_path = hug_resolve_image_path(
        $_FILES['image_file'] ?? null,
        'gallery',
        $_POST['image_path'] ?? '',
        is_string($existingPath) ? $existingPath : null,
        $errors
    );
    $caption    = trim($_POST['caption'] ?? '');
    $news_id    = $_POST['news_id'] !== '' ? (int)$_POST['news_id'] : null;

    if ($album_name === '') $errors[] = 'กรุณากรอกชื่ออัลบั้ม';
    if ($image_path === '') $errors[] = 'อัปโหลดรูปหรือระบุ URL/path';

    if (!$errors) {
        if ($id) {
            $stmt = $pdo->prepare(
                'UPDATE gallery SET album_name=:album, image_path=:path, caption=:caption, news_id=:news
                 WHERE gallery_id=:id'
            );
            $stmt->execute([
                ':album' => $album_name, ':path' => $image_path, ':caption' => $caption ?: null,
                ':news' => $news_id, ':id' => $id,
            ]);
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO gallery (album_name, image_path, caption, news_id, uploaded_by)
                 VALUES (:album, :path, :caption, :news, :uid)'
            );
            $stmt->execute([
                ':album' => $album_name, ':path' => $image_path, ':caption' => $caption ?: null,
                ':news' => $news_id, ':uid' => $_SESSION['user_id'],
            ]);
        }
        header('Location: manage_gallery.php');
        exit;
    }
}

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM gallery WHERE gallery_id = :id');
    $stmt->execute([':id' => (int)$_GET['id']]);
    $editRow = $stmt->fetch();
}

$items = $pdo->query('SELECT * FROM gallery ORDER BY uploaded_at DESC')->fetchAll();
$newsList = $pdo->query('SELECT news_id, title FROM news ORDER BY created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>แกลเลอรี | Hug Nursing Home</title>
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="admin-shell">
  <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>
  <main class="admin-main">
    <?php $topbar_title = 'จัดการแกลเลอรี'; require __DIR__ . '/../includes/admin_topbar.php'; ?>
    <div class="admin-content">
      <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><h3><?= $editRow ? 'แก้ไขรูปภาพ' : 'เพิ่มรูปภาพ' ?></h3></div>
        <?php foreach ($errors as $e): ?><p class="error-text"><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="gallery_id" value="<?= htmlspecialchars($editRow['gallery_id'] ?? '') ?>">
          <div class="form-grid">
            <div class="form-field">
              <label>ชื่ออัลบั้ม</label>
              <input type="text" name="album_name" required value="<?= htmlspecialchars($editRow['album_name'] ?? '') ?>">
            </div>
            <div class="form-field">
              <label>อัปโหลดรูป</label>
              <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp">
            </div>
            <div class="form-field">
              <label>หรือ URL / path</label>
              <input type="text" name="image_path" value="<?= htmlspecialchars($editRow['image_path'] ?? '') ?>"
                     placeholder="https://... หรือ /assets/uploads/...">
            </div>
            <div class="form-field">
              <label>คำอธิบาย</label>
              <input type="text" name="caption" value="<?= htmlspecialchars($editRow['caption'] ?? '') ?>">
            </div>
            <?php
            $uploadPreviewSrc = $editRow['image_path'] ?? '';
            require __DIR__ . '/../includes/admin_image_preview.php';
            ?>
            <div class="form-field">
              <label>ผูกกับข่าว/กิจกรรม (ไม่บังคับ)</label>
              <select name="news_id">
                <option value="">— ไม่ระบุ —</option>
                <?php foreach ($newsList as $n): ?>
                <option value="<?= (int)$n['news_id'] ?>" <?= (($editRow['news_id'] ?? '') == $n['news_id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($n['title']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <button type="submit" class="btn btn-primary" style="width:auto;"><?= $editRow ? 'บันทึก' : 'เพิ่มรูป' ?></button>
          <?php if ($editRow): ?><a href="manage_gallery.php" class="btn" style="background:var(--line);">ยกเลิก</a><?php endif; ?>
        </form>
      </div>
      <div class="panel">
        <div class="panel-head"><h3>รายการรูปภาพ</h3></div>
        <table class="data-table">
          <thead>
            <tr><th>ตัวอย่าง</th><th>อัลบั้ม</th><th>คำอธิบาย</th><th>วันที่</th><th></th></tr>
          </thead>
          <tbody>
            <?php foreach ($items as $g): ?>
            <tr>
              <td><img src="<?= htmlspecialchars($g['image_path']) ?>" alt="" style="width:64px;height:48px;object-fit:cover;border-radius:6px;"></td>
              <td><?= htmlspecialchars($g['album_name']) ?></td>
              <td><?= htmlspecialchars($g['caption'] ?? '') ?></td>
              <td><?= date('d M Y', strtotime($g['uploaded_at'])) ?></td>
              <td>
                <a class="icon-btn" href="?id=<?= (int)$g['gallery_id'] ?>" title="แก้ไข"><?= hug_icon('edit', 16) ?></a>
                <a class="icon-btn icon-btn-danger" href="?delete=<?= (int)$g['gallery_id'] ?>" title="ลบ"
                   onclick="return confirm('ลบรูปนี้?');"><?= hug_icon('trash', 16) ?></a>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$items): ?><tr><td colspan="5">ยังไม่มีรูปในแกลเลอรี</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
<script src="/assets/js/admin-image-preview.js"></script>
</body>
</html>
