<?php
$page_title_key = 'page.about';
$active_page = 'about';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="section-head">
      <h2><?= hug_t('about.title') ?></h2>
      <p><?= hug_t('about.lead') ?></p>
    </div>

    <div class="grid-2" style="margin-bottom:56px;">
      <div>
        <h3>ประวัติความเป็นมา</h3>
        <p>ฮักเนอร์สซิ่งโฮมก่อตั้งขึ้นจากความตั้งใจของทีมพยาบาลและผู้เชี่ยวชาญด้านผู้สูงอายุ
        ที่ต้องการสร้างพื้นที่ปลอดภัยและอบอุ่นสำหรับผู้สูงอายุในจังหวัดขอนแก่นและใกล้เคียง
        ให้ได้รับการดูแลอย่างมีคุณภาพ เสมือนอยู่กับครอบครัวของตนเอง</p>
      </div>
      <div>
        <h3>วิสัยทัศน์</h3>
        <p>เป็นศูนย์ดูแลผู้สูงอายุที่ได้รับความไว้วางใจอันดับต้น ๆ ของภาคอีสาน
        ด้วยมาตรฐานการดูแลที่ปลอดภัย ทันสมัย และเปี่ยมด้วยความเอาใจใส่</p>
      </div>
    </div>

    <div class="section-head">
      <h2>พันธกิจ</h2>
    </div>
    <div class="grid-3">
      <div class="card"><div class="card-body">
        <h3>คุณภาพการดูแล</h3>
        <p>ให้บริการดูแลสุขภาพและความเป็นอยู่ของผู้สูงอายุด้วยทีมงานมืออาชีพ</p>
      </div></div>
      <div class="card"><div class="card-body">
        <h3>ความปลอดภัย</h3>
        <p>จัดสภาพแวดล้อมและระบบดูแลที่ปลอดภัยตลอด 24 ชั่วโมง</p>
      </div></div>
      <div class="card"><div class="card-body">
        <h3>ความอบอุ่นใจ</h3>
        <p>สื่อสารกับครอบครัวผู้รับบริการอย่างสม่ำเสมอ เพื่อความสบายใจของทุกฝ่าย</p>
      </div></div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
