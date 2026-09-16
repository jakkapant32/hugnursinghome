<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/icons.php';

// ---- สรุปข้อมูลสำหรับ Dashboard ----
$totalResidents = $pdo->query("SELECT COUNT(*) FROM residents WHERE status = 'กำลังรับบริการ'")->fetchColumn();
$totalStaff     = $pdo->query("SELECT COUNT(*) FROM staff WHERE is_active = 1")->fetchColumn();
$totalMale      = $pdo->query("SELECT COUNT(*) FROM residents WHERE gender = 'ชาย' AND status = 'กำลังรับบริการ'")->fetchColumn();
$totalFemale    = $pdo->query("SELECT COUNT(*) FROM residents WHERE gender = 'หญิง' AND status = 'กำลังรับบริการ'")->fetchColumn();
$upcomingEvents = $pdo->query("SELECT COUNT(*) FROM news WHERE category = 'กิจกรรม' AND event_date >= CURRENT_DATE")->fetchColumn();

$recentResidents = $pdo->query(
    "SELECT resident_id, full_name, gender, admitted_date, status
     FROM residents ORDER BY created_at DESC LIMIT 5"
)->fetchAll();

$recentActivities = $pdo->query(
    "SELECT title, event_date FROM news WHERE category = 'กิจกรรม'
     ORDER BY event_date DESC LIMIT 5"
)->fetchAll();

$active = 'dashboard';
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>แดชบอร์ด | Hug Nursing Home</title>
<link rel="stylesheet" href="/assets/css/admin.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
</head>
<body>
<div class="admin-shell">
  <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>

  <main class="admin-main">
    <?php $topbar_title = 'แดชบอร์ด'; require __DIR__ . '/../includes/admin_topbar.php'; ?>

    <div class="admin-content">

      <div class="stat-row">
        <div class="stat-card">
          <div class="icon"><?= hug_icon('elder', 22) ?></div>
          <div><div class="label">ผู้สูงอายุที่ดูแล</div><div class="value"><?= (int)$totalResidents ?> คน</div></div>
        </div>
        <div class="stat-card">
          <div class="icon amber"><?= hug_icon('users', 22) ?></div>
          <div><div class="label">พนักงานทั้งหมด</div><div class="value"><?= (int)$totalStaff ?> คน</div></div>
        </div>
        <div class="stat-card">
          <div class="icon rose"><?= hug_icon('calendar', 22) ?></div>
          <div><div class="label">กิจกรรมที่จะถึง</div><div class="value"><?= (int)$upcomingEvents ?> กิจกรรม</div></div>
        </div>
        <div class="stat-card">
          <div class="icon icon-dark"><?= hug_icon('check', 22) ?></div>
          <div><div class="label">สถานะระบบ</div><div class="value" style="font-size:1rem;">ปกติ</div></div>
        </div>
      </div>

      <div class="form-grid" style="align-items:start;">
        <div class="panel">
          <div class="panel-head"><h3>สัดส่วนผู้รับบริการแยกตามเพศ</h3></div>
          <canvas id="genderChart" height="180"></canvas>
        </div>

        <div class="panel">
          <div class="panel-head"><h3>กิจกรรมล่าสุด</h3></div>
          <ul style="list-style:none; padding:0; margin:0; font-size:.92rem;">
            <?php foreach ($recentActivities as $a): ?>
              <li style="padding:9px 0; border-bottom:1px solid var(--line); display:flex; justify-content:space-between;">
                <span><?= htmlspecialchars($a['title']) ?></span>
                <span style="color:var(--muted);"><?= $a['event_date'] ? date('d M Y', strtotime($a['event_date'])) : '' ?></span>
              </li>
            <?php endforeach; ?>
            <?php if (!$recentActivities): ?><li>ยังไม่มีกิจกรรม</li><?php endif; ?>
          </ul>
        </div>
      </div>

      <div class="panel" style="margin-top:20px;">
        <div class="panel-head">
          <h3>ข้อมูลผู้รับบริการล่าสุด</h3>
          <a href="manage_residents.php" class="btn btn-primary" style="padding:8px 16px;">+ เพิ่มผู้รับบริการ</a>
        </div>
        <table class="data-table">
          <thead>
            <tr><th>ชื่อ-นามสกุล</th><th>เพศ</th><th>วันที่เข้ารับบริการ</th><th>สถานะ</th><th></th></tr>
          </thead>
          <tbody>
            <?php foreach ($recentResidents as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['full_name']) ?></td>
              <td><?= htmlspecialchars($r['gender']) ?></td>
              <td><?= date('d M Y', strtotime($r['admitted_date'])) ?></td>
              <td><span class="badge <?= $r['status']==='กำลังรับบริการ' ? 'badge-active' : 'badge-pending' ?>"><?= htmlspecialchars($r['status']) ?></span></td>
              <td><a class="icon-btn" href="manage_residents.php?id=<?= (int)$r['resident_id'] ?>" title="แก้ไข"><?= hug_icon('edit', 16) ?></a></td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$recentResidents): ?>
              <tr><td colspan="5">ยังไม่มีข้อมูลผู้รับบริการ</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </main>
</div>

<script>
new Chart(document.getElementById('genderChart'), {
  type: 'doughnut',
  data: {
    labels: ['ชาย (<?= (int)$totalMale ?>)', 'หญิง (<?= (int)$totalFemale ?>)'],
    datasets: [{
      data: [<?= (int)$totalMale ?>, <?= (int)$totalFemale ?>],
      backgroundColor: ['#1B9E6F', '#E05572'],
      borderWidth: 0
    }]
  },
  options: { plugins: { legend: { position: 'bottom' } }, cutout: '65%' }
});
</script>
</body>
</html>
