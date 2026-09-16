document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.querySelector('.nav-toggle');
  var links = document.querySelector('.nav-links');
  var actions = document.querySelector('.nav-actions');

  if (toggle) {
    toggle.addEventListener('click', function () {
      if (links) links.classList.toggle('show');
      if (actions) actions.classList.toggle('show');
    });
  }

  var contactForm = document.querySelector('form[method="post"]');
  if (contactForm && contactForm.querySelector('#message')) {
    contactForm.addEventListener('submit', function (e) {
      var name = contactForm.querySelector('#name');
      if (name && name.value.trim() === '') {
        e.preventDefault();
        alert('กรุณากรอกชื่อ-นามสกุล');
        name.focus();
      }
    });
  }
});
