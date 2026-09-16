<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/icons.php';

$active = 'staff';
$errors = [];
$editRow = null;

if (isset($_GET['delete'])) {
    $pdo->prepare('DELETE FROM staff WHERE staff_id = :id')->execute([':id' => (int)$_GET['delete']]);
    header('Location: manage_staff.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = $_POST['staff_id'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $position  = trim($_POST['position'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $shift     = $_POST['shift'] ?? 'ยืดหยุ่น';
    $hired     = $_POST['hired_date'] ?: null;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($full_name === '') $errors[] = 'กรุณากรอกชื่อ-นามสกุล';
    if ($position === '') $errors[] = 'กรุณากรอกตำแหน่ง';

    if (!$errors) {
        if ($id) {
            $stmt = $pdo->prepare(
                'UPDATE staff SET full_name=:name, position=:pos, phone=:phone, shift=:shift,
                 hired_date=:hired, is_active=:active WHERE staff_id=:id'
            );
            $stmt->execute([
                ':name' => $full_name, ':pos' => $position, ':phone' => $phone ?: null,
                ':shift' => $shift, ':hired' => $hired, ':active' => $is_active, ':id' => $id,
            ]);
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO staff (full_name, position, phone, shift, hired_date, is_active)
                 VALUES (:name, :pos, :phone, :shift, :hired, :active)'
            );
            $stmt->execute([
                ':name' => $full_name, ':pos' => $position, ':phone' => $phone ?: null,
                ':shift' => $shift, ':hired' => $hired, ':active' => $is_active,
            ]);
        }
        header('Location: manage_staff.php');
        exit;
    }
}

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM staff WHERE staff_id = :id');
    $stmt->execute([':id' => (int)$_GET['id']]);
    $editRow = $stmt->fetch();
}

$staff = $pdo->query('SELECT * FROM staff ORDER BY created_at DESC')->fetchAll();
$shifts = ['เช้า', 'บ่าย', 'ดึก', 'ยืดหยุ่น'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>พนักงาน | Hug Nursing Home</title>
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="admin-shell">
  <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>
  <main class="admin-main">
    <?php $topbar_title = 'จัดการพนักงาน'; require __DIR__ . '/../includes/admin_topbar.php'; ?>
    <div class="admin-content">
      <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><h3><?= $editRow ? 'แก้ไขพนักงาน' : 'เพิ่มพนักงาน' ?></h3></div>
        <?php foreach ($errors as $e): ?><p class="error-text"><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
        <form method="post">
          <input type="hidden" name="staff_id" value="<?= htmlspecialchars($editRow['staff_id'] ?? '') ?>">
          <div class="form-grid">
            <div class="form-field"><label>ชื่อ-นามสกุล</label>
              <input type="text" name="full_name" required value="<?= htmlspecialchars($editRow['full_name'] ?? '') ?>"></div>
            <div class="form-field"><label>ตำแหน่ง</label>
              <input type="text" name="position" required value="<?= htmlspecialchars($editRow['position'] ?? '') ?>"></div>
            <div class="form-field"><label>เบอร์โทร</label>
              <input type="text" name="phone" value="<?= htmlspecialchars($editRow['phone'] ?? '') ?>"></div>
            <div class="form-field"><label>กะ</label>
              <select name="shift">
                <?php foreach ($shifts as $s): ?>
                <option value="<?= $s ?>" <?= (($editRow['shift'] ?? '') === $s) ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
              </select></div>
            <div class="form-field"><label>วันที่เริ่มงาน</label>
              <input type="date" name="hired_date" value="<?= htmlspecialchars($editRow['hired_date'] ?? '') ?>"></div>
            <div class="form-field" style="display:flex;align-items:center;gap:8px;padding-top:24px;">
              <input type="checkbox" name="is_active" id="is_active" <?= !isset($editRow) || !empty($editRow['is_active']) ? 'checked' : '' ?>>
              <label for="is_active" style="margin:0;">ปฏิบัติงานอยู่</label>
            </div>
          </div>
          <button type="submit" class="btn btn-primary" style="width:auto;"><?= $editRow ? 'บันทึก' : 'เพิ่ม' ?></button>
          <?php if ($editRow): ?><a href="manage_staff.php" class="btn" style="background:var(--line);">ยกเลิก</a><?php endif; ?>
        </form>
      </div>
      <div class="panel">
        <div class="panel-head"><h3>รายชื่อพนักงาน</h3></div>
        <table class="data-table">
          <thead><tr><th>ชื่อ</th><th>ตำแหน่ง</th><th>กะ</th><th>สถานะ</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($staff as $s): ?>
            <tr>
              <td><?= htmlspecialchars($s['full_name']) ?></td>
              <td><?= htmlspecialchars($s['position']) ?></td>
              <td><?= htmlspecialchars($s['shift']) ?></td>
              <td><span class="badge <?= $s['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $s['is_active'] ? 'ทำงาน' : 'ไม่ active' ?></span></td>
              <td>
                <a class="icon-btn" href="?id=<?= (int)$s['staff_id'] ?>"><?= hug_icon('edit', 16) ?></a>
                <a class="icon-btn icon-btn-danger" href="?delete=<?= (int)$s['staff_id'] ?>" onclick="return confirm('ลบ?');"><?= hug_icon('trash', 16) ?></a>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$staff): ?><tr><td colspan="5">ยังไม่มีข้อมูลพนักงาน</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
</body>
</html>
