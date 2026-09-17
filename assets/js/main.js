document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.querySelector('.nav-toggle');
  var links = document.querySelector('.nav-links');

  if (toggle && links) {
    toggle.addEventListener('click', function () {
      links.classList.toggle('show');
    });
  }

  var menuDropdown = document.getElementById('nav-menu-dropdown');
  var menuTrigger = document.getElementById('nav-menu-trigger');
  var menuPanel = document.getElementById('nav-menu-panel');

  function closeNavMenu() {
    if (!menuDropdown || !menuTrigger || !menuPanel) return;
    menuDropdown.classList.remove('is-open');
    menuTrigger.setAttribute('aria-expanded', 'false');
    menuPanel.hidden = true;
  }

  function openNavMenu() {
    if (!menuDropdown || !menuTrigger || !menuPanel) return;
    menuDropdown.classList.add('is-open');
    menuTrigger.setAttribute('aria-expanded', 'true');
    menuPanel.hidden = false;
  }

  if (menuTrigger && menuPanel && menuDropdown) {
    menuTrigger.addEventListener('click', function (e) {
      e.stopPropagation();
      if (menuDropdown.classList.contains('is-open')) {
        closeNavMenu();
      } else {
        openNavMenu();
      }
    });

    document.addEventListener('click', function (e) {
      if (!menuDropdown.contains(e.target)) {
        closeNavMenu();
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        closeNavMenu();
      }
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
