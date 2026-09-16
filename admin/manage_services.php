<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/icons.php';

$active = 'services';
$errors = [];
$editRow = null;

if (isset($_GET['delete'])) {
    $pdo->prepare('DELETE FROM services WHERE service_id = :id')->execute([':id' => (int)$_GET['delete']]);
    header('Location: manage_services.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = $_POST['service_id'] ?? '';
    $title        = trim($_POST['title'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $icon         = trim($_POST['icon'] ?? 'heart');
    $sort_order   = (int)($_POST['sort_order'] ?? 0);
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    if ($title === '') $errors[] = 'กรุณากรอกชื่อบริการ';

    if (!$errors) {
        if ($id) {
            $stmt = $pdo->prepare(
                'UPDATE services SET title=:t, description=:d, icon=:i, sort_order=:o, is_published=:p
                 WHERE service_id=:id'
            );
            $stmt->execute([':t'=>$title, ':d'=>$description, ':i'=>$icon, ':o'=>$sort_order, ':p'=>$is_published, ':id'=>$id]);
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO services (title, description, icon, sort_order, is_published)
                 VALUES (:t, :d, :i, :o, :p)'
            );
            $stmt->execute([':t'=>$title, ':d'=>$description, ':i'=>$icon, ':o'=>$sort_order, ':p'=>$is_published]);
        }
        header('Location: manage_services.php');
        exit;
    }
}

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM services WHERE service_id = :id');
    $stmt->execute([':id' => (int)$_GET['id']]);
    $editRow = $stmt->fetch();
}

$services = $pdo->query('SELECT * FROM services ORDER BY sort_order ASC, service_id ASC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>บริการ | Hug Nursing Home</title>
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="admin-shell">
  <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>
  <main class="admin-main">
    <?php $topbar_title = 'จัดการบริการ'; require __DIR__ . '/../includes/admin_topbar.php'; ?>
    <div class="admin-content">
      <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><h3><?= $editRow ? 'แก้ไขบริการ' : 'เพิ่มบริการ' ?></h3></div>
        <?php foreach ($errors as $e): ?><p class="error-text"><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
        <form method="post">
          <input type="hidden" name="service_id" value="<?= htmlspecialchars($editRow['service_id'] ?? '') ?>">
          <div class="form-grid">
            <div class="form-field"><label>ชื่อบริการ</label>
              <input type="text" name="title" required value="<?= htmlspecialchars($editRow['title'] ?? '') ?>"></div>
            <div class="form-field"><label>ลำดับแสดงผล</label>
              <input type="number" name="sort_order" value="<?= htmlspecialchars($editRow['sort_order'] ?? '0') ?>"></div>
            <div class="form-field"><label>ชื่อไอคอน (heart, calendar, …)</label>
              <input type="text" name="icon" value="<?= htmlspecialchars($editRow['icon'] ?? 'heart') ?>"></div>
            <div class="form-field" style="display:flex;align-items:center;gap:8px;padding-top:24px;">
              <input type="checkbox" name="is_published" id="pub" <?= !isset($editRow) || !empty($editRow['is_published']) ? 'checked' : '' ?>>
              <label for="pub" style="margin:0;">เผยแพร่หน้าเว็บ</label>
            </div>
          </div>
          <div class="form-field"><label>รายละเอียด</label>
            <textarea name="description" rows="3"><?= htmlspecialchars($editRow['description'] ?? '') ?></textarea></div>
          <button type="submit" class="btn btn-primary" style="width:auto;"><?= $editRow ? 'บันทึก' : 'เพิ่ม' ?></button>
          <?php if ($editRow): ?><a href="manage_services.php" class="btn" style="background:var(--line);">ยกเลิก</a><?php endif; ?>
        </form>
      </div>
      <div class="panel">
        <table class="data-table">
          <thead><tr><th>ลำดับ</th><th>ชื่อ</th><th>เผยแพร่</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($services as $s): ?>
            <tr>
              <td><?= (int)$s['sort_order'] ?></td>
              <td><?= htmlspecialchars($s['title']) ?></td>
              <td><span class="badge <?= $s['is_published'] ? 'badge-active' : 'badge-inactive' ?>"><?= $s['is_published'] ? 'ใช่' : 'ไม่' ?></span></td>
              <td>
                <a class="icon-btn" href="?id=<?= (int)$s['service_id'] ?>"><?= hug_icon('edit', 16) ?></a>
                <a class="icon-btn icon-btn-danger" href="?delete=<?= (int)$s['service_id'] ?>" onclick="return confirm('ลบ?');"><?= hug_icon('trash', 16) ?></a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
</body>
</html>
