document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('form input[type="file"][name="image_file"]').forEach(function (input) {
    input.addEventListener('change', function () {
      var form = input.closest('form');
      if (!form) return;
      var preview = form.querySelector('[data-upload-preview]');
      if (!preview) return;

      var file = input.files && input.files[0];
      if (!file || !file.type.match(/^image\//)) {
        return;
      }

      if (preview._blobUrl) {
        URL.revokeObjectURL(preview._blobUrl);
        preview._blobUrl = null;
      }

      var url = URL.createObjectURL(file);
      preview._blobUrl = url;
      preview.src = url;
      preview.style.display = 'block';
      preview.hidden = false;

      var hint = form.querySelector('[data-upload-preview-hint]');
      if (hint) {
        hint.textContent = 'ตัวอย่างจากไฟล์ที่เลือก (กดบันทึกเพื่ออัปโหลดจริง)';
      }
    });
  });
});
