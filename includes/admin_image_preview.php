<?php
/** ตัวอย่างรูปในฟอร์มแอดมิน — ใช้คู่กับ admin-image-preview.js */
$uploadPreviewSrc = $uploadPreviewSrc ?? '';
$uploadPreviewMax = (int) ($uploadPreviewMax ?? 160);
$hasSrc = $uploadPreviewSrc !== '';
?>
<div class="form-field" style="grid-column:1/-1;">
  <p data-upload-preview-hint style="font-size:13px;color:var(--muted);margin:0 0 6px;">
    <?= $hasSrc ? 'รูปที่บันทึกอยู่ — เลือกไฟล์ใหม่เพื่อดูตัวอย่างก่อนบันทึก' : 'ตัวอย่างรูปจะแสดงเมื่อเลือกไฟล์จากเครื่อง' ?>
  </p>
  <img data-upload-preview src="<?= $hasSrc ? htmlspecialchars($uploadPreviewSrc) : '' ?>" alt=""
       style="max-width:<?= $uploadPreviewMax ?>px;border-radius:8px;object-fit:cover;<?= $hasSrc ? '' : 'display:none;' ?>">
</div>
