<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/icons.php';

$active = 'records';
$errors = [];
$editRow = null;

if (isset($_GET['delete'])) {
    $pdo->prepare('DELETE FROM service_records WHERE record_id = :id')->execute([':id' => (int)$_GET['delete']]);
    header('Location: manage_service_records.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = $_POST['record_id'] ?? '';
    $resident_id = (int)($_POST['resident_id'] ?? 0);
    $service_id  = $_POST['service_id'] !== '' ? (int)$_POST['service_id'] : null;
    $record_date = $_POST['record_date'] ?: date('Y-m-d');
    $detail      = trim($_POST['detail'] ?? '');

    if ($resident_id <= 0) $errors[] = 'กรุณาเลือกผู้รับบริการ';

    if (!$errors) {
        if ($id) {
            $stmt = $pdo->prepare(
                'UPDATE service_records SET resident_id=:rid, service_id=:sid, record_date=:d, detail=:det
                 WHERE record_id=:id'
            );
            $stmt->execute([
                ':rid' => $resident_id, ':sid' => $service_id, ':d' => $record_date,
                ':det' => $detail ?: null, ':id' => $id,
            ]);
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO service_records (resident_id, service_id, record_date, detail, recorded_by)
                 VALUES (:rid, :sid, :d, :det, :uid)'
            );
            $stmt->execute([
                ':rid' => $resident_id, ':sid' => $service_id, ':d' => $record_date,
                ':det' => $detail ?: null, ':uid' => $_SESSION['user_id'],
            ]);
        }
        header('Location: manage_service_records.php');
        exit;
    }
}

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM service_records WHERE record_id = :id');
    $stmt->execute([':id' => (int)$_GET['id']]);
    $editRow = $stmt->fetch();
}

$filterResident = (int)($_GET['resident_id'] ?? 0);
$filterDate = trim($_GET['date'] ?? '');

$sql = 'SELECT sr.*, r.full_name AS resident_name, s.title AS service_title, u.full_name AS recorder_name
        FROM service_records sr
        JOIN residents r ON r.resident_id = sr.resident_id
        LEFT JOIN services s ON s.service_id = sr.service_id
        LEFT JOIN users u ON u.user_id = sr.recorded_by
        WHERE 1=1';
$params = [];
if ($filterResident > 0) {
    $sql .= ' AND sr.resident_id = :rid';
    $params[':rid'] = $filterResident;
}
if ($filterDate !== '') {
    $sql .= ' AND sr.record_date = :dt';
    $params[':dt'] = $filterDate;
}
$sql .= ' ORDER BY sr.record_date DESC, sr.created_at DESC LIMIT 200';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$records = $stmt->fetchAll();

$residents = $pdo->query("SELECT resident_id, full_name FROM residents ORDER BY full_name")->fetchAll();
$services = $pdo->query('SELECT service_id, title FROM services ORDER BY sort_order, title')->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>บันทึกการให้บริการ | Hug Nursing Home</title>
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="admin-shell">
  <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>
  <main class="admin-main">
    <?php $topbar_title = 'บันทึกการให้บริการรายวัน'; require __DIR__ . '/../includes/admin_topbar.php'; ?>
    <div class="admin-content">
      <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><h3><?= $editRow ? 'แก้ไขบันทึก' : 'เพิ่มบันทึกการให้บริการ' ?></h3></div>
        <?php foreach ($errors as $e): ?><p class="error-text"><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
        <form method="post">
          <input type="hidden" name="record_id" value="<?= htmlspecialchars($editRow['record_id'] ?? '') ?>">
          <div class="form-grid">
            <div class="form-field"><label>ผู้รับบริการ</label>
              <select name="resident_id" required>
                <option value="">— เลือก —</option>
                <?php foreach ($residents as $r): ?>
                <option value="<?= (int)$r['resident_id'] ?>" <?= (($editRow['resident_id'] ?? '') == $r['resident_id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($r['full_name']) ?>
                </option>
                <?php endforeach; ?>
              </select></div>
            <div class="form-field"><label>ประเภทบริการ</label>
              <select name="service_id">
                <option value="">— ทั่วไป —</option>
                <?php foreach ($services as $s): ?>
                <option value="<?= (int)$s['service_id'] ?>" <?= (($editRow['service_id'] ?? '') == $s['service_id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($s['title']) ?>
                </option>
                <?php endforeach; ?>
              </select></div>
            <div class="form-field"><label>วันที่</label>
              <input type="date" name="record_date" required value="<?= htmlspecialchars($editRow['record_date'] ?? date('Y-m-d')) ?>"></div>
          </div>
          <div class="form-field"><label>รายละเอียด</label>
            <textarea name="detail" rows="3"><?= htmlspecialchars($editRow['detail'] ?? '') ?></textarea></div>
          <button type="submit" class="btn btn-primary" style="width:auto;">บันทึก</button>
          <?php if ($editRow): ?><a href="manage_service_records.php" class="btn" style="background:var(--line);">ยกเลิก</a><?php endif; ?>
        </form>
      </div>
      <div class="panel">
        <div class="panel-head">
          <h3>รายการบันทึก</h3>
          <form method="get" style="display:flex;gap:8px;flex-wrap:wrap;">
            <select name="resident_id" style="padding:8px;border:1px solid var(--line);border-radius:8px;">
              <option value="0">ผู้รับบริการทั้งหมด</option>
              <?php foreach ($residents as $r): ?>
              <option value="<?= (int)$r['resident_id'] ?>" <?= $filterResident === (int)$r['resident_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($r['full_name']) ?>
              </option>
              <?php endforeach; ?>
            </select>
            <input type="date" name="date" value="<?= htmlspecialchars($filterDate) ?>" style="padding:8px;border:1px solid var(--line);border-radius:8px;">
            <button type="submit" class="btn btn-primary" style="width:auto;padding:8px 14px;">กรอง</button>
          </form>
        </div>
        <table class="data-table">
          <thead><tr><th>วันที่</th><th>ผู้รับบริการ</th><th>บริการ</th><th>รายละเอียด</th><th>บันทึกโดย</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($records as $rec): ?>
            <tr>
              <td><?= date('d M Y', strtotime($rec['record_date'])) ?></td>
              <td><?= htmlspecialchars($rec['resident_name']) ?></td>
              <td><?= htmlspecialchars($rec['service_title'] ?? '—') ?></td>
              <td><?= htmlspecialchars(mb_strimwidth($rec['detail'] ?? '', 0, 60, '…')) ?></td>
              <td><?= htmlspecialchars($rec['recorder_name'] ?? '—') ?></td>
              <td>
                <a class="icon-btn" href="?id=<?= (int)$rec['record_id'] ?>"><?= hug_icon('edit', 16) ?></a>
                <a class="icon-btn icon-btn-danger" href="?delete=<?= (int)$rec['record_id'] ?>" onclick="return confirm('ลบ?');"><?= hug_icon('trash', 16) ?></a>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$records): ?><tr><td colspan="6">ยังไม่มีบันทึก</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
</body>
</html>
