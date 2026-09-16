<?php
require_once __DIR__ . '/../includes/auth_helpers.php';
hug_require_member();

$page_title = 'บัญชีของฉัน';
$active_page = '';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="section">
  <div class="container" style="max-width:640px;">
    <div class="section-head">
      <h2>สวัสดี, <?= htmlspecialchars($_SESSION['full_name']) ?></h2>
      <p>พื้นที่สมาชิก — ติดตามข่าวสารและติดต่อศูนย์</p>
    </div>
    <div class="card">
      <div class="card-body">
        <p>ยินดีต้อนรับสู่ระบบสมาชิกฮักเนอร์สซิ่งโฮม</p>
        <ul style="list-style:none;padding:0;margin:20px 0;display:grid;gap:10px;">
          <li><a class="btn btn-ghost" href="news.php">ข่าวสารและกิจกรรม</a></li>
          <li><a class="btn btn-ghost" href="gallery.php">แกลเลอรี</a></li>
          <li><a class="btn btn-ghost" href="contact.php">ติดต่อ / นัดเยี่ยมชม</a></li>
        </ul>
        <a class="btn btn-rose" href="/public/logout.php">ออกจากระบบ</a>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
