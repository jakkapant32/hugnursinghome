<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../includes/upload_helpers.php';

$active = 'residents';
$errors = [];
$editRow = null;

// ---------- ลบข้อมูล ----------
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM residents WHERE resident_id = :id")->execute([':id' => (int)$_GET['delete']]);
    header('Location: manage_residents.php');
    exit;
}

// ---------- บันทึกข้อมูล (เพิ่ม/แก้ไข) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = $_POST['resident_id'] ?? '';
    $prefix       = trim($_POST['prefix'] ?? '');
    $full_name    = trim($_POST['full_name'] ?? '');
    $gender       = $_POST['gender'] ?? 'ชาย';
    $birth_date   = $_POST['birth_date'] ?: null;
    $phone        = trim($_POST['phone'] ?? '');
    $guardian     = trim($_POST['guardian_name'] ?? '');
    $guardianTel  = trim($_POST['guardian_phone'] ?? '');
    $admitted     = $_POST['admitted_date'] ?: date('Y-m-d');
    $status       = $_POST['status'] ?? 'กำลังรับบริการ';
    $health_notes = trim($_POST['health_notes'] ?? '');

    $existingPhoto = null;
    if ($id) {
        $row = $pdo->prepare('SELECT photo_path FROM residents WHERE resident_id = :id');
        $row->execute([':id' => (int)$id]);
        $existingPhoto = $row->fetchColumn() ?: null;
    }
    $photo_path = hug_resolve_image_path(
        $_FILES['image_file'] ?? null,
        'residents',
        $_POST['photo_path'] ?? '',
        is_string($existingPhoto) ? $existingPhoto : null,
        $errors
    );

    if ($full_name === '') $errors[] = 'กรุณากรอกชื่อ-นามสกุล';

    if (!$errors) {
        if ($id) {
            $stmt = $pdo->prepare(
                "UPDATE residents SET prefix=:prefix, full_name=:full_name, gender=:gender,
                 birth_date=:birth_date, phone=:phone, guardian_name=:guardian_name,
                 guardian_phone=:guardian_phone, admitted_date=:admitted_date, status=:status,
                 health_notes=:health_notes, photo_path=:photo_path WHERE resident_id=:id"
            );
            $stmt->execute([
                ':prefix'=>$prefix, ':full_name'=>$full_name, ':gender'=>$gender,
                ':birth_date'=>$birth_date, ':phone'=>$phone, ':guardian_name'=>$guardian,
                ':guardian_phone'=>$guardianTel, ':admitted_date'=>$admitted, ':status'=>$status,
                ':health_notes'=>$health_notes, ':photo_path'=>$photo_path ?: null, ':id'=>$id,
            ]);
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO residents (prefix, full_name, gender, birth_date, phone,
                 guardian_name, guardian_phone, admitted_date, status, health_notes, photo_path, created_by)
                 VALUES (:prefix,:full_name,:gender,:birth_date,:phone,:guardian_name,
                 :guardian_phone,:admitted_date,:status,:health_notes,:photo_path,:created_by)"
            );
            $stmt->execute([
                ':prefix'=>$prefix, ':full_name'=>$full_name, ':gender'=>$gender,
                ':birth_date'=>$birth_date, ':phone'=>$phone, ':guardian_name'=>$guardian,
                ':guardian_phone'=>$guardianTel, ':admitted_date'=>$admitted, ':status'=>$status,
                ':health_notes'=>$health_notes, ':photo_path'=>$photo_path ?: null,
                ':created_by'=>$_SESSION['user_id'],
            ]);
        }
        header('Location: manage_residents.php');
        exit;
    }
}

// ---------- โหลดข้อมูลสำหรับแก้ไข ----------
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM residents WHERE resident_id = :id");
    $stmt->execute([':id' => (int)$_GET['id']]);
    $editRow = $stmt->fetch();
}

// ---------- ค้นหา ----------
$keyword = trim($_GET['q'] ?? '');
if ($keyword !== '') {
    $stmt = $pdo->prepare("SELECT * FROM residents WHERE full_name LIKE :kw ORDER BY created_at DESC");
    $stmt->execute([':kw' => "%{$keyword}%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM residents ORDER BY created_at DESC");
}
$residents = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ข้อมูลผู้รับบริการ | Hug Nursing Home</title>
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="admin-shell">
  <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>

  <main class="admin-main">
    <?php $topbar_title = 'ข้อมูลผู้รับบริการ'; require __DIR__ . '/../includes/admin_topbar.php'; ?>

    <div class="admin-content">

      <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><h3><?= $editRow ? 'แก้ไขข้อมูลผู้รับบริการ' : 'เพิ่มผู้รับบริการใหม่' ?></h3></div>

        <?php foreach ($errors as $e): ?><p class="error-text"><?= htmlspecialchars($e) ?></p><?php endforeach; ?>

        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="resident_id" value="<?= htmlspecialchars($editRow['resident_id'] ?? '') ?>">
          <div class="form-grid">
            <div class="form-field">
              <label>คำนำหน้า</label>
              <input type="text" name="prefix" value="<?= htmlspecialchars($editRow['prefix'] ?? '') ?>" placeholder="นาย / นาง / นางสาว">
            </div>
            <div class="form-field">
              <label>ชื่อ-นามสกุล</label>
              <input type="text" name="full_name" required value="<?= htmlspecialchars($editRow['full_name'] ?? '') ?>">
            </div>
            <div class="form-field">
              <label>เพศ</label>
              <select name="gender">
                <option value="ชาย" <?= (($editRow['gender'] ?? '')==='ชาย')?'selected':'' ?>>ชาย</option>
                <option value="หญิง" <?= (($editRow['gender'] ?? '')==='หญิง')?'selected':'' ?>>หญิง</option>
              </select>
            </div>
            <div class="form-field">
              <label>วันเกิด</label>
              <input type="date" name="birth_date" value="<?= htmlspecialchars($editRow['birth_date'] ?? '') ?>">
            </div>
            <div class="form-field">
              <label>เบอร์โทรศัพท์</label>
              <input type="text" name="phone" value="<?= htmlspecialchars($editRow['phone'] ?? '') ?>">
            </div>
            <div class="form-field">
              <label>วันที่เข้ารับบริการ</label>
              <input type="date" name="admitted_date" value="<?= htmlspecialchars($editRow['admitted_date'] ?? date('Y-m-d')) ?>">
            </div>
            <div class="form-field">
              <label>ชื่อผู้ติดต่อ/ญาติ</label>
              <input type="text" name="guardian_name" value="<?= htmlspecialchars($editRow['guardian_name'] ?? '') ?>">
            </div>
            <div class="form-field">
              <label>เบอร์โทรผู้ติดต่อ</label>
              <input type="text" name="guardian_phone" value="<?= htmlspecialchars($editRow['guardian_phone'] ?? '') ?>">
            </div>
            <div class="form-field">
              <label>สถานะ</label>
              <select name="status">
                <?php foreach (['กำลังรับบริการ','พักการรับบริการ','สิ้นสุดการรับบริการ'] as $st): ?>
                  <option value="<?= $st ?>" <?= (($editRow['status'] ?? '')===$st)?'selected':'' ?>><?= $st ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-field"><label>อัปโหลดรูปผู้รับบริการ</label>
              <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp"></div>
            <div class="form-field"><label>หรือ URL / path</label>
              <input type="text" name="photo_path" value="<?= htmlspecialchars($editRow['photo_path'] ?? '') ?>"></div>
            <?php
            $uploadPreviewSrc = $editRow['photo_path'] ?? '';
            $uploadPreviewMax = 120;
            require __DIR__ . '/../includes/admin_image_preview.php';
            ?>
          </div>
          <div class="form-field">
            <label>บันทึกด้านสุขภาพ / ข้อควรระวัง</label>
            <textarea name="health_notes" rows="3"><?= htmlspecialchars($editRow['health_notes'] ?? '') ?></textarea>
          </div>
          <button type="submit" class="btn btn-primary" style="width:auto;"><?= $editRow ? 'บันทึกการแก้ไข' : 'เพิ่มข้อมูล' ?></button>
          <?php if ($editRow): ?><a href="manage_residents.php" class="btn" style="background:var(--line);">ยกเลิก</a><?php endif; ?>
        </form>
      </div>

      <div class="panel">
        <div class="panel-head">
          <h3>รายชื่อผู้รับบริการ</h3>
          <form method="get" style="display:flex; gap:8px;">
            <input type="text" name="q" placeholder="ค้นหาชื่อ..." value="<?= htmlspecialchars($keyword) ?>"
                   style="padding:8px 12px; border:1px solid var(--line); border-radius:8px;">
            <button type="submit" class="btn btn-primary" style="padding:8px 16px; width:auto;">ค้นหา</button>
          </form>
        </div>
        <table class="data-table">
          <thead>
            <tr><th>#</th><th>ชื่อ-นามสกุล</th><th>เพศ</th><th>อายุ</th><th>วันที่เข้ารับบริการ</th><th>สถานะ</th><th>จัดการ</th></tr>
          </thead>
          <tbody>
            <?php foreach ($residents as $i => $r): ?>
            <?php $age = $r['birth_date'] ? floor((time() - strtotime($r['birth_date'])) / 31556926) : '-'; ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><?= htmlspecialchars($r['prefix'] . ' ' . $r['full_name']) ?></td>
              <td><?= htmlspecialchars($r['gender']) ?></td>
              <td><?= $age ?></td>
              <td><?= date('d M Y', strtotime($r['admitted_date'])) ?></td>
              <td><span class="badge <?= $r['status']==='กำลังรับบริการ' ? 'badge-active' : 'badge-inactive' ?>"><?= htmlspecialchars($r['status']) ?></span></td>
              <td>
                <a class="icon-btn" href="?id=<?= $r['resident_id'] ?>" title="แก้ไข"><?= hug_icon('edit', 16) ?></a>
                <a class="icon-btn icon-btn-danger" href="?delete=<?= $r['resident_id'] ?>" title="ลบ"
                   onclick="return confirm('ยืนยันการลบข้อมูลนี้?');"><?= hug_icon('trash', 16) ?></a>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$residents): ?>
              <tr><td colspan="7">ไม่พบข้อมูล</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </main>
</div>
<script src="/assets/js/admin-image-preview.js"></script>
</body>
</html>
